<?php

namespace Pixelant\PxaSurvey\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 * @author Andriy Oprysko
 */

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaSurvey\Domain\Model\Answer;
use Pixelant\PxaSurvey\Domain\Model\UserAnswer;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class UserAnswerTest
 * @package Pixelant\PxaSurvey\Tests\Unit\Domain\Model
 */
class UserAnswerTest extends UnitTestCase
{
    /**
     * @var UserAnswer
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new UserAnswer();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getCustomValueReturnsInitialValueForString()
    {
        $this->assertSame(
            '',
            $this->subject->getCustomValue()
        );
    }

    /**
     * @test
     */
    public function setCustomValueForStringSetsCustomValue()
    {
        $value = 'value';
        $this->subject->setCustomValue($value);

        $this->assertEquals(
            $value,
            $this->subject->getCustomValue()
        );
    }

    /**
     * @test
     */
    public function getQuestionReturnsInitialValueForQuestion()
    {
        $this->assertEquals(
            null,
            $this->subject->getQuestion()
        );
    }

    /**
     * @test
     */
    public function setQuestionForQuestionSetsQuestion()
    {
        $questionFixture = new \Pixelant\PxaSurvey\Domain\Model\Question();
        $this->subject->setQuestion($questionFixture);

        $this->assertEquals(
            $questionFixture,
            $this->subject->getQuestion()
        );
    }

    /**
     * @test
     */
    public function getAnswerReturnsInitialValueForAnswer()
    {
        $answers = new ObjectStorage();

        $this->assertEquals(
            $answers,
            $this->subject->getAnswers()
        );
    }

    /**
     * @test
     */
    public function addAnswerToObjectStorageHoldingAnswers()
    {
        $answerFixture = new Answer();
        $this->subject->addAnswer($answerFixture);

        $objectStorage = new ObjectStorage();
        $objectStorage->attach($answerFixture);

        $this->assertEquals(
            $objectStorage,
            $this->subject->getAnswers()
        );
    }

    /**
     * @test
     */
    public function removeAnswerFromObjectStorageHoldingAnswers()
    {
        $answerFixture = new Answer();
        $this->subject->addAnswer($answerFixture);
        $this->subject->removeAnswer($answerFixture);

        $objectStorage = new ObjectStorage();
        $objectStorage->attach($answerFixture);
        $objectStorage->detach($answerFixture);

        $this->assertEquals(
            $objectStorage,
            $this->subject->getAnswers()
        );
    }

    /**
     * @test
     */
    public function getFrontendUserReturnInitialValue()
    {
        $this->assertEquals(
            null,
            $this->subject->getFrontendUser()
        );
    }

    /**
     * @test
     */
    public function frontendUserCanBeSet()
    {
        $frontendUser = new FrontendUser();
        $this->subject->setFrontendUser($frontendUser);

        $this->assertSame(
            $frontendUser,
            $this->subject->getFrontendUser()
        );
    }
}
