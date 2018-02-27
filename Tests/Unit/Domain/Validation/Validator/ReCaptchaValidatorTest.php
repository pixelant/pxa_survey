<?php

namespace Pixelant\PxaSurvey\Tests\Unit\Domain\Validation\Validator;

use GuzzleHttp\Psr7\Response;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaSurvey\Domain\Model\Question;
use Pixelant\PxaSurvey\Domain\Model\Survey;
use Pixelant\PxaSurvey\Domain\Validation\Validator\ReCaptchaValidator;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Extbase\Error\Result;

/**
 * Class ReCaptchaValidatorTest
 * @package Pixelant\PxaSurvey\Tests\Unit\Domain\Validation\Validator
 */
class ReCaptchaValidatorTest extends UnitTestCase
{
    /**
     * @var ReCaptchaValidator|\Nimut\TestingFramework\MockObject\AccessibleMockObjectInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $subject = null;

    /**
     * Setup
     */
    public function setUp()
    {
        $this->subject = $this->getAccessibleMock(
            RecaptchaValidator::class,
            ['getRequestFactory', 'localize'],
            [],
            '',
            false
        );

        $_GET['g-recaptcha-response'] = 'test';
    }

    /**
     * @test
     */
    public function isReCaptchaDisabledValidatorReturnTrue()
    {
        $settings = [
            'protectWithReCaptcha' => 0
        ];
        $survey = new Survey();

        $this->subject->_set('settings', $settings);

        $this->assertTrue($this->subject->isValid($survey));
    }

    /**
     * @test
     */
    public function isReCaptchaEnabledItWillTryToValidate()
    {
        $settings = [
            'protectWithReCaptcha' => 1,
            'showAllQuestions' => 1,
            'recaptcha' => [
                'siteKey' => '123',
                'siteSecret' => '321'
            ]
        ];
        $survey = new Survey();

        $this->subject->_set('settings', $settings);

        $mockedRequestFactory = $this->createPartialMock(RequestFactory::class, ['request']);
        $mockedResponse = $this->createMock(Response::class);

        $this->subject
            ->expects($this->once())
            ->method('getRequestFactory')
            ->willReturn($mockedRequestFactory);

        $mockedRequestFactory
            ->expects($this->once())
            ->method('request')
            ->willReturn($mockedResponse);

        $mockedResult = $this->createPartialMock(Result::class, ['forProperty']);
        $this->subject->_set('result', $mockedResult);

        $mockedResult2 = $this->createPartialMock(Result::class, ['addError']);
        $mockedResult2->expects($this->once())->method('addError');
        $mockedResult
            ->expects($this->once())
            ->method('forProperty')
            ->willReturn($mockedResult2);
        // Will fail
        $this->assertFalse($this->subject->isValid($survey));
    }

    /**
     * @test
     */
    public function stepByStepModeWillCheckReCaptchaOnlyForFirstQuestion()
    {
        $settings = [
            'protectWithReCaptcha' => 1,
            'showAllQuestions' => 0,
            'recaptcha' => [
                'siteKey' => '123',
                'siteSecret' => '321'
            ]
        ];
        $survey = new Survey();
        for ($i = 1; $i <= 3; $i++) {
            $question = new Question();
            $question->_setProperty('uid', $i);
            $survey->addQuestion($question);
        }

        $this->subject->_set('settings', $settings);

        $mockedRequestFactory = $this->createPartialMock(RequestFactory::class, ['request']);
        $mockedResponse = $this->createPartialMock(Response::class, ['getStatusCode', 'getBody']);

        $this->subject
            ->expects($this->once())
            ->method('getRequestFactory')
            ->willReturn($mockedRequestFactory);

        $mockedRequestFactory
            ->expects($this->once())
            ->method('request')
            ->willReturn($mockedResponse);

        $mockedResponse
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);

        $answer = json_encode(['success' => true]);
        $mockedResponse
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($answer);

        for ($i = 1; $i <= 3; $i++) {
            $_POST['tx_pxasurvey_survey']['currentQuestion'] = $i;
            /// Valid for all cases
            $this->assertTrue($this->subject->isValid($survey));
        }
    }

    public function tearDown()
    {
        unset($this->subject);
    }
}
