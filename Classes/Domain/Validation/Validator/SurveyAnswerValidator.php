<?php

namespace Pixelant\PxaSurvey\Domain\Validation\Validator;

use Pixelant\PxaSurvey\Domain\Model\Question;
use Pixelant\PxaSurvey\Domain\Model\Survey;
use Pixelant\PxaSurvey\Domain\Repository\QuestionRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\Validation\Error;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;
use TYPO3\CMS\Extbase\Validation\Validator\EmailAddressValidator;
use TYPO3\CMS\Extbase\Validation\Validator\NumberValidator;

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

        //Collect questions by custom filter type
        $questionsByUid = [];
        foreach($survey->getQuestions() as $question) {
            $questionsByUid[$question->getUid()] = $question;
        }

        if (is_array($arguments) && !empty($arguments['answers'])) {
            foreach ($arguments['answers'] as $questionUid => $answer) {

                // Validate if email field filled with email
                if($questionsByUid[$questionUid]->getAppendWithInput() === Question::INPUT_TYPE_EMAIL) {
                    $emailValidator = new EmailAddressValidator();
                    if($emailValidator->validate($answer[isset($answer['otherAnswer'])?'otherAnswer':'answer'])->hasErrors()) {
                        $this->result->forProperty('question-' . $questionUid)->addError(
                            new Error(
                                $this->translate('fe.error.email_required'),
                                1510659509775
                            )
                        );
                    }
                }

                // Validate if numeric field filled with number
                if($questionsByUid[$questionUid]->getAppendWithInput() === Question::INPUT_TYPE_NUMBER) {
                    //var_dump($answer);
                    //die('catched');
                    $numberValidator = new NumberValidator();
                    if($numberValidator->validate($answer[isset($answer['otherAnswer'])?'otherAnswer':'answer'])->hasErrors()) {
                        $this->result->forProperty('question-' . $questionUid)->addError(
                            new Error(
                                $this->translate('fe.error.number_required'),
                                1510659509776
                            )
                        );
                    }
                }

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
            $answers = is_array($arguments['answers']) ? $arguments['answers'] : [];
            if (empty($requiredQuestions)) {
                $answers = array_filter(
                    $answers,
                    function ($v) {
                        return !empty($v['answer']) || !empty($v['otherAnswer']);
                    }
                );
            }
            $givenAnswersFor = implode(',', array_keys($answers));

            if (empty($givenAnswersFor) && empty($requiredQuestions)) {
                $this->result->addError(
                    new Error(
                        $this->translate('fe.error.no_answers_at_all'),
                        1519726630388
                    )
                );

                $isValid = false;
            } else {
                foreach (GeneralUtility::intExplode(',', $requiredQuestions, true) as $requiredQuestionUid) {
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

        }
        //Check if email field was filled with email



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
        return $this->isAnswerEmpty($answer['answer'])
            && $this->isAnswerEmpty($answer['otherAnswer'])
            && GeneralUtility::inList(
                $requiredList,
                $questionUid
            );
    }

    /**
     * Check if answer is empty
     *
     * @param $answer
     * @return bool
     */
    protected function isAnswerEmpty($answer): bool
    {
        return is_string($answer)
            ? ($answer === '')
            : empty($answer);
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
