<?php

namespace Pixelant\PxaSurvey\Domain\Model;

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

/**
 * Question
 */
class Question extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * Type of answer for question radio boxes
     */
    const ANSWER_TYPE_RADIO = 1;

    /**
     * Type of answer for question checkboxes
     */
    const ANSWER_TYPE_CHECKBOXES = 2;

    /**
     * Type of answer for question custom input
     */
    const ANSWER_TYPE_INPUT = 3;

    /**
     * Free answer types
     */
    const INPUT_TYPE_NONE = 0;
    const INPUT_TYPE_INPUT = 1;
    const INPUT_TYPE_TEXTAREA = 2;
    const INPUT_TYPE_EMAIL = 3;
    const INPUT_TYPE_NUMBER = 4;

    /**
     * text
     *
     * @var string
     */
    protected $text = '';

    /**
     * type
     *
     * @var int
     */
    protected $type = 0;

    /**
     * @var int
     */
    protected $appendWithInput = 0;

    /**
     * @var bool
     */
    protected $required = false;

    /**
     * answers
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaSurvey\Domain\Model\Answer>
     * @cascade remove
     */
    protected $answers = null;

    /**
     * __construct
     */
    public function __construct()
    {
        //Do not remove the next line: It would break the functionality
        $this->initStorageObjects();
    }

    /**
     * Initializes all ObjectStorage properties
     * Do not modify this method!
     * It will be rewritten on each save in the extension builder
     * You may modify the constructor of this class instead
     *
     * @return void
     */
    protected function initStorageObjects()
    {
        $this->answers = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * Returns the text
     *
     * @return string $text
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Sets the text
     *
     * @param string $text
     * @return void
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * Returns the type
     *
     * @return int $type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the type
     *
     * @param int $type
     * @return void
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getAppendWithInput(): int
    {
        return $this->appendWithInput;
    }

    /**
     * @param int $appendWithInput
     */
    public function setAppendWithInput(int $appendWithInput)
    {
        $this->appendWithInput = $appendWithInput;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @param bool $required
     */
    public function setRequired(bool $required)
    {
        $this->required = $required;
    }

    /**
     * Adds a Answer
     *
     * @param \Pixelant\PxaSurvey\Domain\Model\Answer $answer
     * @return void
     */
    public function addAnswer(\Pixelant\PxaSurvey\Domain\Model\Answer $answer)
    {
        $this->answers->attach($answer);
    }

    /**
     * Removes a Answer
     *
     * @param \Pixelant\PxaSurvey\Domain\Model\Answer $answerToRemove The Answer to be removed
     * @return void
     */
    public function removeAnswer(\Pixelant\PxaSurvey\Domain\Model\Answer $answerToRemove)
    {
        $this->answers->detach($answerToRemove);
    }

    /**
     * Returns the answers
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaSurvey\Domain\Model\Answer> $answers
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * Sets the answers
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaSurvey\Domain\Model\Answer> $answers
     * @return void
     */
    public function setAnswers(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $answers)
    {
        $this->answers = $answers;
    }
}
