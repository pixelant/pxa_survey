<?php

namespace Pixelant\PxaSurvey\Domain\Validation\Validator;

use Pixelant\PxaSurvey\Domain\Model\Question;
use Pixelant\PxaSurvey\Domain\Model\Survey;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\Validation\Error;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

/**
 * Validate answers from user
 *
 * @package Pixelant\PxaSurvey\Domain\Validation
 */
class SurveyAnswerValidator extends AbstractValidator
{
    /**
     * @param Survey $survey
     * @return bool
     */
    public function isValid($survey)
    {
        $arguments = GeneralUtility::_POST('tx_pxasurvey_survey');
        $requiredQuestions = $this->getRequiredQuestionsList($survey);
        $isValid = true;

        if (is_array($arguments) && !empty($arguments['answers'])) {
            foreach ($arguments['answers'] as $questionUid => $answer) {
                // check if only one value is used
                if (!empty($answer['answer']) && !empty($answer['otherAnswer'])) {
                    $this->result->forProperty('question-' . $questionUid)->addError(
                        new Error(
                            $this->translate('fe.error.double_result'),
                            1510659341372
                        )
                    );

                    $isValid = false;
                } elseif ($this->isAnswerRequiredError($answer, $requiredQuestions, $questionUid)) {
                    $this->result->forProperty('question-' . $questionUid)->addError(
                        new Error(
                            $this->translate('fe.error.required'),
                            1510659509774
                        )
                    );
                }
            }
        } elseif ((int)$arguments['currentQuestion']
            && GeneralUtility::inList(
                $requiredQuestions,
                $arguments['currentQuestion']
            )
        ) {
            $this->result->forProperty('question-' . $arguments['currentQuestion'])->addError(
                new Error(
                    $this->translate('fe.error.required'),
                    1510659509774
                )
            );

            $isValid = false;
        }
        // check for missing answers
        if ((int)$arguments['showAllQuestions']) {
            $givenAnswersFor = empty($arguments['answers']) ? '' : implode(',', array_keys($arguments['answers']));

            foreach (explode(',', $requiredQuestions) as $requiredQuestionUid) {
                if (!GeneralUtility::inList($givenAnswersFor, $requiredQuestionUid)) {
                    $this->result->forProperty('question-' . $requiredQuestionUid)->addError(
                        new Error(
                            $this->translate('fe.error.required'),
                            1510659509774
                        )
                    );

                    $isValid = false;
                }
            }
        }

        return $isValid;
    }

    /**
     * Get list of required questions
     *
     * @param Survey $survey
     * @return string
     */
    protected function getRequiredQuestionsList(Survey $survey)
    {
        $requiredQuestion = [];

        /** @var Question $question */
        foreach ($survey->getQuestions() as $question) {
            if ($question->isRequired()) {
                $requiredQuestion[] = $question->getUid();
            }
        }

        return implode(',', $requiredQuestion);
    }

    /**
     * Check if required type error found
     * @param $answer
     * @param $requiredList
     * @param $questionUid
     * @return bool
     */
    protected function isAnswerRequiredError($answer, $requiredList, $questionUid)
    {
        return empty($answer['answer'])
                && empty($answer['otherAnswer'])
                && GeneralUtility::inList(
                    $requiredList,
                    $questionUid
                );
    }

    /**
     * Translate wrapper
     * @param string $key
     * @return string
     */
    protected function translate(string $key)
    {
        return LocalizationUtility::translate($key, 'PxaSurvey');
    }
}
