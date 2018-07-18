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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;

/**
 * SurveyController
 */
class SurveyController extends AbstractController
{
    /**
     * action show
     *
     * @return void
     */
    public function showAction()
    {
        if ($surveyUid = (int)$this->settings['survey']) {
            $survey = $this->surveyRepository->findByUid($surveyUid);
        }

        if ($survey !== null && !$this->isSurveyAllowed($survey)) {
            $this->forward('finish', null, null, ['survey' => $survey, 'alreadyFinished' => true]);
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
            $this->forward('show');
        }
    }

    /**
     * After survey was finished
     *
     * @param Survey $survey
     * @param bool $alreadyFinished User already finished this survey and is not allowed take it again
     */
    public function finishAction(Survey $survey, bool $alreadyFinished = false)
    {
        $this->view
            ->assign('survey', $survey)
            ->assign('alreadyFinished', $alreadyFinished);
    }

    /**
     * Show survey results
     */
    public function showResultsAction()
    {
        $surveyUid = (int)$this->settings['survey'];
        /** @var Survey $survey */
        $survey = $this->surveyRepository->findByUid($surveyUid);

        $data = $this->generateAnalysisData($survey);

        $this->view
            ->assign('survey', $survey)
            ->assign('data', $data);
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
        $this->addSurveyToCookie($survey);

        $this->redirect('finish', null, null, ['survey' => $survey]);
    }

    /**
     * Wrapper function for testing
     *
     * @param Survey $survey
     */
    protected function addSurveyToCookie(Survey $survey)
    {
        SurveyMainUtility::addValueToListCookie(SurveyMainUtility::SURVEY_FINISHED_COOKIE_NAME, $survey->getUid());
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

    /**
     * Check if user can take survey
     *
     * @param Survey $survey
     * @return bool
     */
    protected function isSurveyAllowed(Survey $survey): bool
    {
        if ((int)$this->settings['allowMultipleAnswerOnSurvey'] === 1) {
            return true;
        }

        // Check by fe user
        if (SurveyMainUtility::getTSFE()->loginUser) {
            /** @var FrontendUser $frontendUser */
            $frontendUser = $this->frontendUserRepository->findByUid(
                SurveyMainUtility::getTSFE()->fe_user->user['uid']
            );
            $frontendUserAnswers = $this->userAnswerRepository->countGivenUserAnswer($survey, $frontendUser);
            $countQuestions = $survey->getQuestions()->count();

            if ($countQuestions > 0 && $frontendUserAnswers >= $countQuestions) {
                return false;
            }
        }

        // check by cookie
        $surveysFinished = $_COOKIE[SurveyMainUtility::SURVEY_FINISHED_COOKIE_NAME] ?? '';
        return !GeneralUtility::inList($surveysFinished, $survey->getUid());
    }
}
