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
 * UserAnswer
 */
class UserAnswer extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * customValue
     *
     * @var string
     */
    protected $customValue = '';

    /**
     * question
     *
     * @var \Pixelant\PxaSurvey\Domain\Model\Question
     */
    protected $question = null;

    /**
     * answer
     *
     * @var \Pixelant\PxaSurvey\Domain\Model\Answer
     */
    protected $answer = null;

    /**
     * Returns the customValue
     *
     * @return string $customValue
     */
    public function getCustomValue()
    {
        return $this->customValue;
    }

    /**
     * Sets the customValue
     *
     * @param string $customValue
     * @return void
     */
    public function setCustomValue($customValue)
    {
        $this->customValue = $customValue;
    }

    /**
     * Returns the question
     *
     * @return \Pixelant\PxaSurvey\Domain\Model\Question $question
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Sets the question
     *
     * @param \Pixelant\PxaSurvey\Domain\Model\Question $question
     * @return void
     */
    public function setQuestion(\Pixelant\PxaSurvey\Domain\Model\Question $question)
    {
        $this->question = $question;
    }

    /**
     * Returns the answer
     *
     * @return \Pixelant\PxaSurvey\Domain\Model\Answer $answer
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * Sets the answer
     *
     * @param \Pixelant\PxaSurvey\Domain\Model\Answer $answer
     * @return void
     */
    public function setAnswer(\Pixelant\PxaSurvey\Domain\Model\Answer $answer)
    {
        $this->answer = $answer;
    }
}
