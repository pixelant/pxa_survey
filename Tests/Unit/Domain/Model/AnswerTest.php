<?php
namespace Pixelant\PxaSurvey\Tests\Unit\Domain\Model;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaSurvey\Domain\Model\Answer;

/**
 * Test case.
 *
 * @author Andriy Oprysko 
 */

/**
 * Class AnswerTest
 * @package Pixelant\PxaSurvey\Tests\Unit\Domain\Model
 */
class AnswerTest extends UnitTestCase
{
    /**
     * @var Answer
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new Answer();
    }

    protected function tearDown()
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function getTextReturnsInitialValueForString()
    {
        $this->assertEquals(
            '',
            $this->subject->getText()
        );
    }

    /**
     * @test
     */
    public function setTextForStringSetsText()
    {
        $text = 'text';
        $this->subject->setText($text);

        $this->assertEquals(
            $text,
            $this->subject->getText()
        );
    }
}
