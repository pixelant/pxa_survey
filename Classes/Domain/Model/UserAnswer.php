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
     * answers
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaSurvey\Domain\Model\Answer>
     */
    protected $answers = null;

    /**
     * @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
     */
    protected $frontendUser = null;

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

    /**
     * @return \TYPO3\CMS\Extbase\Domain\Model\FrontendUser|null
     */
    public function getFrontendUser()
    {
        return $this->frontendUser;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $frontendUser
     */
    public function setFrontendUser(\TYPO3\CMS\Extbase\Domain\Model\FrontendUser $frontendUser)
    {
        $this->frontendUser = $frontendUser;
    }
}
