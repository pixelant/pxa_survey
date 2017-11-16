<?php

namespace Pixelant\PxaSurvey\Tests\Unit\Domain\Model;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaSurvey\Domain\Model\Question;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Test case.
 *
 * @author Andriy Oprysko
 */

/**
 * Class SurveyTest
 * @package Pixelant\PxaSurvey\Tests\Unit\Domain\Model
 */
class SurveyTest extends UnitTestCase
{
    /**
     * @var \Pixelant\PxaSurvey\Domain\Model\Survey
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \Pixelant\PxaSurvey\Domain\Model\Survey();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getNameReturnsInitialValueForString()
    {
        $this->assertSame(
            '',
            $this->subject->getName()
        );
    }

    /**
     * @test
     */
    public function setNameForStringSetsName()
    {
        $name = 'name';
        $this->subject->setName($name);

        $this->assertEquals(
            $name,
            $this->subject->getName()
        );
    }

    /**
     * @test
     */
    public function getTitleReturnsInitialValueForString()
    {
        $this->assertSame(
            '',
            $this->subject->getTitle()
        );
    }

    /**
     * @test
     */
    public function setTitleForStringSetsTitle()
    {
        $title = 'title';
        $this->subject->setTitle($title);

        $this->assertEquals(
            $title,
            $this->subject->getTitle()
        );
    }

    /**
     * @test
     */
    public function getDescriptionReturnsInitialValueForString()
    {
        $this->assertSame(
            '',
            $this->subject->getDescription()
        );
    }

    /**
     * @test
     */
    public function setDescriptionForStringSetsDescription()
    {
        $description = 'description';

        $this->subject->setDescription($description);

        $this->assertEquals(
            $description,
            $this->subject->getDescription()
        );
    }

    /**
     * @test
     */
    public function getQuestionsReturnsInitialValueForQuestion()
    {
        $newObjectStorage = new ObjectStorage();
        $this->assertEquals(
            $newObjectStorage,
            $this->subject->getQuestions()
        );
    }

    /**
     * @test
     */
    public function setQuestionsForObjectStorageContainingQuestionSetsQuestions()
    {
        $question = new Question();
        $objectStorageHoldingExactlyOneQuestions = new ObjectStorage();
        $objectStorageHoldingExactlyOneQuestions->attach($question);
        $this->subject->setQuestions($objectStorageHoldingExactlyOneQuestions);

        $this->assertAttributeEquals(
            $objectStorageHoldingExactlyOneQuestions,
            'questions',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function addQuestionToObjectStorageHoldingQuestions()
    {
        $question = new Question();
        $questionsObjectStorageMock = $this->getMockBuilder(ObjectStorage::class)
            ->setMethods(['attach'])
            ->disableOriginalConstructor()
            ->getMock();

        $questionsObjectStorageMock->expects($this->once())->method('attach')->with($this->equalTo($question));
        $this->inject($this->subject, 'questions', $questionsObjectStorageMock);

        $this->subject->addQuestion($question);
    }

    /**
     * @test
     */
    public function removeQuestionFromObjectStorageHoldingQuestions()
    {
        $question = new Question();
        $questionsObjectStorageMock = $this->getMockBuilder(ObjectStorage::class)
            ->setMethods(['detach'])
            ->disableOriginalConstructor()
            ->getMock();

        $questionsObjectStorageMock->expects($this->once())->method('detach')->with($this->equalTo($question));
        $this->inject($this->subject, 'questions', $questionsObjectStorageMock);

        $this->subject->removeQuestion($question);
    }
}
