<?php
namespace Pixelant\PxaSurvey\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 * @author Andriy Oprysko 
 */
class UserAnswerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \Pixelant\PxaSurvey\Domain\Model\UserAnswer
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \Pixelant\PxaSurvey\Domain\Model\UserAnswer();
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
        self::assertSame(
            '',
            $this->subject->getCustomValue()
        );
    }

    /**
     * @test
     */
    public function setCustomValueForStringSetsCustomValue()
    {
        $this->subject->setCustomValue('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'customValue',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getQuestionReturnsInitialValueForQuestion()
    {
        self::assertEquals(
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

        self::assertAttributeEquals(
            $questionFixture,
            'question',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getAnswerReturnsInitialValueForAnswer()
    {
        self::assertEquals(
            null,
            $this->subject->getAnswer()
        );
    }

    /**
     * @test
     */
    public function setAnswerForAnswerSetsAnswer()
    {
        $answerFixture = new \Pixelant\PxaSurvey\Domain\Model\Answer();
        $this->subject->setAnswer($answerFixture);

        self::assertAttributeEquals(
            $answerFixture,
            'answer',
            $this->subject
        );
    }
}
