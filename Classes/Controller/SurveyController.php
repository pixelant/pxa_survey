<?php

namespace Pixelant\PxaSurvey\Controller;

/***
 *
 * This file is part of the "Simple Survey" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2017 Andriy Oprysko
 *
 ***/

use Pixelant\PxaSurvey\Domain\Model\Answer;
use Pixelant\PxaSurvey\Domain\Model\Question;
use Pixelant\PxaSurvey\Domain\Model\Survey;
use Pixelant\PxaSurvey\Domain\Model\UserAnswer;
use Pixelant\PxaSurvey\Utility\SurveyMainUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * SurveyController
 */
class SurveyController extends ActionController
{
    /**
     * Survey Repository
     *
     * @var \Pixelant\PxaSurvey\Domain\Repository\SurveyRepository
     * @inject
     */
    protected $surveyRepository = null;

    /**
     * User Answer Repository
     *
     * @var \Pixelant\PxaSurvey\Domain\Repository\UserAnswerRepository
     * @inject
     */
    protected $userAnswerRepository = null;

    /**
     * Answer Repository
     *
     * @var \Pixelant\PxaSurvey\Domain\Repository\AnswerRepository
     * @inject
     */
    protected $answerRepository = null;

    /**
     * Frontend User Repository
     *
     * @var \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository
     * @inject
     */
    protected $frontendUserRepository = null;

    /**
     * action show
     *
     * @param Survey $survey
     * @return void
     */
    public function showAction(Survey $survey = null)
    {
        if ($survey === null && ($surveyUid = (int)$this->settings['survey'])) {
            $survey = $this->surveyRepository->findByUid($surveyUid);
        }

        if ($survey !== null && (int)$this->settings['showAllQuestions'] === 0) {
            $currentQuestion = $this->getNextQuestion($survey);
            $currentPosition = $survey->getQuestions()->getPosition($currentQuestion);
            $countAllQuestions = $survey->getQuestions()->count();

            $this->view->assignMultiple([
                'currentQuestion' => $currentQuestion,
                'currentPosition' => $currentPosition,
                'countAllQuestions' => $countAllQuestions,
                'progress' => round(($currentPosition - 1) / $countAllQuestions, 2) * 100
            ]);
        }

        $this->view->assign('survey', $survey);
    }

    /**
     * answer from user survey
     *
     * @param Survey $survey
     * @param Question $currentQuestion
     * @validate $survey \Pixelant\PxaSurvey\Domain\Validation\Validator\SurveyAnswerValidator
     */
    public function answerAction(Survey $survey, Question $currentQuestion = null)
    {
        $answers = $this->convertRequestToUserAnswersArray();

        if ((int)$this->settings['showAllQuestions']) {
            $this->saveResultAndFinish($survey, $answers);
        } else {
            // No answer given and question is not required
            if (empty($answers) && $currentQuestion !== null) {
                $answers = [$currentQuestion->getUid() => ''];
            }

            SurveyMainUtility::addAnswerToSessionData($survey->getUid(), $answers);

            // Show next question
            $this->forward('show', null, null, ['survey' => $survey]);
        }
    }

    /**
     * After survey was finished
     *
     * @param Survey $survey
     */
    public function finishAction(Survey $survey)
    {
        $this->view->assign('survey', $survey);
    }

    /**
     * Get answers from request
     *
     * @return array
     */
    protected function convertRequestToUserAnswersArray()
    {
        $answers = [];

        if ($this->request->hasArgument('answers')) {
            $requestAnswers = $this->request->getArgument('answers');

            /** @noinspection PhpWrongForeachArgumentTypeInspection */
            foreach ($requestAnswers as $questionUid => $requestAnswer) {
                $answers[$questionUid] = $requestAnswer['answer'] ?: $requestAnswer['otherAnswer'];
            }
        }

        return $answers;
    }

    /**
     * @param Survey $survey
     * @return Question|object
     */
    protected function getNextQuestion(Survey $survey)
    {
        $answers = SurveyMainUtility::getAnswerSessionData($survey->getUid());

        if (empty($answers)) {
            // Very first question
            $survey->getQuestions()->rewind();
            return $survey->getQuestions()->current();
        } else {
            // Last answered question uid
            $lastQuestionUid = (int)array_keys(array_reverse($answers, true))[0];

            /** @var Question $question */
            foreach ($survey->getQuestions() as $question) {
                if ($question->getUid() === $lastQuestionUid) {
                    $survey->getQuestions()->next();
                    $nextQuestion = $survey->getQuestions()->current();

                    if ($nextQuestion !== null) {
                        return $nextQuestion;
                    } else {
                        // Reached last question
                        $this->saveResultAndFinish($survey, $answers);
                    }
                }
            }
        }

        // Nothing was found
        // assume we need to start over again
        SurveyMainUtility::clearAnswersSessionData($survey->getUid());
        $survey->getQuestions()->rewind();

        return $survey->getQuestions()->current();
    }

    /**
     * Save user answers and finish
     *
     * @param Survey $survey
     * @param array $data
     */
    protected function saveResultAndFinish(Survey $survey, array $data)
    {
        foreach ($data as $questionUid => $answerData) {
            if (empty($answerData)) {
                continue;
            }

            /** @var UserAnswer $userAnswer */
            $userAnswer = $this->objectManager->get(UserAnswer::class);
            $userAnswer->setQuestion(
                $this->getQuestionFromSurveyByUid($survey, (int)$questionUid)
            );

            if (is_string($answerData)) {
                $this->setUserAnswerFromRequestData($userAnswer, $answerData);
            } elseif (is_array($answerData)) {
                foreach ($answerData as $answerSingleFromMultiple) {
                    $this->setUserAnswerFromRequestData($userAnswer, $answerSingleFromMultiple);
                }
            }

            if (SurveyMainUtility::getTSFE()->loginUser) {
                /** @var FrontendUser $frontendUser */
                $frontendUser = $this->frontendUserRepository->findByUid(
                    SurveyMainUtility::getTSFE()->fe_user->user['uid']
                );
                if ($frontendUser !== null) {
                    $userAnswer->setFrontendUser($frontendUser);
                }
            }

            $this->userAnswerRepository->add($userAnswer);
        }

        SurveyMainUtility::clearAnswersSessionData($survey->getUid());
        $this->redirect('finish', null, null, ['survey' => $survey]);
    }

    /**
     * Set data from user answer
     *
     * @param UserAnswer $userAnswer
     * @param string $answerData
     */
    protected function setUserAnswerFromRequestData(UserAnswer $userAnswer, string $answerData)
    {
        // Check if answer is option object
        if (StringUtility::beginsWith($answerData, '__object--')) {
            $answerUid = (int)substr($answerData, 10);
            /** @var Answer $answer */
            $answer = $this->answerRepository->findByUid($answerUid);
            if ($answer !== null) {
                $userAnswer->addAnswer($answer);
            }
        } else {
            $userAnswer->setCustomValue($answerData);
        }
    }

    /**
     * Get question from survey by uid
     *
     * @param Survey $survey
     * @param int $questionUid
     * @return null|Question
     */
    protected function getQuestionFromSurveyByUid(Survey $survey, int $questionUid)
    {
        /** @var Question $question */
        foreach ($survey->getQuestions() as $question) {
            if ($question->getUid() === $questionUid) {
                return $question;
            }
        }

        return null;
    }
}
