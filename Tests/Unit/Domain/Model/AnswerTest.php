<?php
namespace Pixelant\PxaSurvey\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 * @author Andriy Oprysko 
 */
class AnswerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \Pixelant\PxaSurvey\Domain\Model\Answer
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \Pixelant\PxaSurvey\Domain\Model\Answer();
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
}
