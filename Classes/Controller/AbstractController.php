<?php
declare(strict_types=1);

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
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Class AbstractController
 * @package Pixelant\PxaSurvey\Controller
 */
abstract class AbstractController extends ActionController
{
    /**
     * Survey Repository
     *
     * @var \Pixelant\PxaSurvey\Domain\Repository\SurveyRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $surveyRepository = null;

    /**
     * User Answer Repository
     *
     * @var \Pixelant\PxaSurvey\Domain\Repository\UserAnswerRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $userAnswerRepository = null;

    /**
     * Answer Repository
     *
     * @var \Pixelant\PxaSurvey\Domain\Repository\AnswerRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $answerRepository = null;

    /**
     * Frontend User Repository
     *
     * @var \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $frontendUserRepository = null;

    /**
     * Generate data for Charts.js and FE show results
     *
     * @param Survey $survey
     * @return array
     */
    protected function generateAnalysisData(Survey $survey): array
    {
        $data = [];

        /** @var Question $question */
        foreach ($survey->getQuestions() as $question) {
            $questionData = [];
            $allAnswersCount = 0;

            /** @noinspection PhpUndefinedMethodInspection */
            $userAnswers = $this->userAnswerRepository->findByQuestion($question);

            /** @var UserAnswer $userAnswer */
            foreach ($userAnswers as $userAnswer) {
                // if check box or radio
                if ($userAnswer->getAnswers()->count() > 0) {
                    /** @var Answer $answer */
                    foreach ($userAnswer->getAnswers() as $answer) {
                        if (!is_array($questionData[$answer->getUid()])) {
                            $questionData[$answer->getUid()] = [
                                'label' => $answer->getText(),
                                'count' => 1
                            ];
                        } else {
                            $questionData[$answer->getUid()]['count'] += 1;
                        }

                        $allAnswersCount++;
                    }
                } elseif (!empty($userAnswer->getCustomValue())) { // custom value
                    $identifier = GeneralUtility::shortMD5($userAnswer->getCustomValue());

                    if (!is_array($questionData[$identifier])) {
                        $questionData[$identifier] = [
                            'label' => $userAnswer->getCustomValue(),
                            'count' => 1
                        ];
                    } else {
                        $questionData[$identifier]['count'] += 1;
                    }

                    $allAnswersCount++;
                }
            }

            // add to data array
            $data[$question->getUid()] = [
                'questionData' => $this->calculatePercentsForQuestionData($questionData, $allAnswersCount),
                'labelChart' => SurveyMainUtility::translate('module.percentages'),
                'label' => $question->getText(),
                'allAnswersCount' => $allAnswersCount
            ];
        }

        return $data;
    }

    /**
     * Count in percents user answers
     *
     * @param array $questionData
     * @param int $allAnswersCount
     * @return array
     */
    protected function calculatePercentsForQuestionData(array $questionData, int $allAnswersCount): array
    {
        foreach ($questionData as &$questionItem) {
            $questionItem['percents'] = (string)(round($questionItem['count'] / $allAnswersCount, 3) * 100);
        }

        return $questionData;
    }
}
