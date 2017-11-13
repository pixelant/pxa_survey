<?php
namespace Pixelant\PxaSurvey\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 * @author Andriy Oprysko 
 */
class QuestionTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \Pixelant\PxaSurvey\Domain\Model\Question
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \Pixelant\PxaSurvey\Domain\Model\Question();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getTextReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getText()
        );
    }

    /**
     * @test
     */
    public function setTextForStringSetsText()
    {
        $this->subject->setText('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'text',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getTypeReturnsInitialValueForInt()
    {
        self::assertSame(
            0,
            $this->subject->getType()
        );
    }

    /**
     * @test
     */
    public function setTypeForIntSetsType()
    {
        $this->subject->setType(12);

        self::assertAttributeEquals(
            12,
            'type',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getAnswersReturnsInitialValueForAnswer()
    {
        $newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getAnswers()
        );
    }

    /**
     * @test
     */
    public function setAnswersForObjectStorageContainingAnswerSetsAnswers()
    {
        $answer = new \Pixelant\PxaSurvey\Domain\Model\Answer();
        $objectStorageHoldingExactlyOneAnswers = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneAnswers->attach($answer);
        $this->subject->setAnswers($objectStorageHoldingExactlyOneAnswers);

        self::assertAttributeEquals(
            $objectStorageHoldingExactlyOneAnswers,
            'answers',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function addAnswerToObjectStorageHoldingAnswers()
    {
        $answer = new \Pixelant\PxaSurvey\Domain\Model\Answer();
        $answersObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['attach'])
            ->disableOriginalConstructor()
            ->getMock();

        $answersObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($answer));
        $this->inject($this->subject, 'answers', $answersObjectStorageMock);

        $this->subject->addAnswer($answer);
    }

    /**
     * @test
     */
    public function removeAnswerFromObjectStorageHoldingAnswers()
    {
        $answer = new \Pixelant\PxaSurvey\Domain\Model\Answer();
        $answersObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['detach'])
            ->disableOriginalConstructor()
            ->getMock();

        $answersObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($answer));
        $this->inject($this->subject, 'answers', $answersObjectStorageMock);

        $this->subject->removeAnswer($answer);
    }
}
