<?php

namespace Pixelant\PxaSurvey\Tests\Unit\Controller;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaSurvey\Controller\SurveyController;
use Pixelant\PxaSurvey\Domain\Model\Answer;
use Pixelant\PxaSurvey\Domain\Model\Question;
use Pixelant\PxaSurvey\Domain\Model\Survey;
use Pixelant\PxaSurvey\Domain\Model\UserAnswer;
use Pixelant\PxaSurvey\Domain\Repository\AnswerRepository;
use Pixelant\PxaSurvey\Domain\Repository\UserAnswerRepository;
use Pixelant\PxaSurvey\Utility\SurveyMainUtility;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Test case.
 *
 * @author Andriy Oprysko
 */
class SurveyControllerTest extends UnitTestCase
{
    /**
     * @test
     */
    public function getQuestionFromSurveyByUidReturnQuestion()
    {
        $subject = $this->getAccessibleMock(
            SurveyController::class,
            ['redirect', 'forward', 'addFlashMessage'],
            [],
            '',
            false
        );
        $survey = new Survey();

        $question1 = new Question();
        $question1->_setProperty('uid', 1);

        $question2 = new Question();
        $question2->_setProperty('uid', 2);

        $survey->addQuestion($question1);
        $survey->addQuestion($question2);

        $this->assertSame(
            $question2,
            $subject->_call('getQuestionFromSurveyByUid', $survey, 2)
        );
    }

    /**
     * @test
     */
    public function setDataForUserAnswerWithAnswerObjectWillSetCorrectData()
    {
        $subject = $this->getAccessibleMock(
            SurveyController::class,
            ['redirect', 'forward', 'addFlashMessage'],
            [],
            '',
            false
        );

        $mockedUserAnswer = $this->createPartialMock(UserAnswer::class, ['addAnswer']);
        $mockedAnswer = new Answer();
        $mockedAnswerRepository = $this->createPartialMock(AnswerRepository::class, ['findByUid']);
        $answerData = '__object--12';

        $this->inject($subject, 'answerRepository', $mockedAnswerRepository);
        $mockedAnswerRepository->expects($this->once())->method('findByUid')->with($this->equalTo(12))->willReturn($mockedAnswer);

        $mockedUserAnswer->expects($this->once())->method('addAnswer')->with($mockedAnswer);

        $subject->_call('setUserAnswerFromRequestData', $mockedUserAnswer, $answerData);
    }

    /**
     * @test
     */
    public function setDataForUserAnswerWithStringWillSetCorrectData()
    {
        $subject = $this->getAccessibleMock(
            SurveyController::class,
            ['redirect', 'forward', 'addFlashMessage'],
            [],
            '',
            false
        );

        $mockedUserAnswer = $this->createPartialMock(UserAnswer::class, ['setCustomValue']);
        $answerData = 'custom string';

        $mockedUserAnswer->expects($this->once())->method('setCustomValue')->with($answerData);

        $subject->_call('setUserAnswerFromRequestData', $mockedUserAnswer, $answerData);
    }

    /**
     * @test
     * @dataProvider dataForSaveResultWillSaveUserResultsAndClearSession
     */
    public function saveResultWillSaveUserResultsAndClearSession($answerData, $multiple)
    {
        $subject = $this->getAccessibleMock(
            SurveyController::class,
            ['redirect', 'forward', 'addFlashMessage', 'getQuestionFromSurveyByUid', 'setUserAnswerFromRequestData', 'addSurveyToCookie', 'isUserLoggedIn'],
            [],
            '',
            false
        );
        $subject
            ->expects($this->once())
            ->method('isUserLoggedIn')
            ->willReturn(true); // Simulate login

        $mockedFeUserAuth = $this->createPartialMock(FrontendUserAuthentication::class, ['getKey', 'setKey']);

        $mockedTSFE = $this->getAccessibleMock(TypoScriptFrontendController::class, [], [], '', false);
        $mockedTSFE->fe_user = $mockedFeUserAuth;
        $GLOBALS['TSFE'] = $mockedTSFE;

        $mockedObjectManager = $this->createPartialMock(ObjectManager::class, ['get']);
        $mockedUserAnswer = $this->createPartialMock(UserAnswer::class, ['setFrontendUser', 'setQuestion']);
        $mockedUserAnswerRepository = $this->createPartialMock(UserAnswerRepository::class, ['add']);
        $mockedFrontendUserRepository = $this->createPartialMock(FrontendUserRepository::class, ['findByUid']);
        $mockedQuestion = $this->createMock(Question::class);

        $survey = new Survey();
        $survey->_setProperty('uid', 12);

        $this->inject($subject, 'objectManager', $mockedObjectManager);
        $this->inject($subject, 'frontendUserRepository', $mockedFrontendUserRepository);
        $this->inject($subject, 'userAnswerRepository', $mockedUserAnswerRepository);

        $mockedObjectManager->expects($this->once())->method('get')->with(UserAnswer::class)->willReturn($mockedUserAnswer);
        $mockedUserAnswer->expects($this->once())->method('setQuestion')->with($mockedQuestion);
        $mockedUserAnswerRepository->expects($this->once())->method('add')->with($mockedUserAnswer);

        $subject->expects($this->once())->method('getQuestionFromSurveyByUid')->with(
            $survey,
            1221
        )->willReturn($mockedQuestion);

        if ($multiple) {
            $subject->expects($this->exactly(3))->method('setUserAnswerFromRequestData');
        } else {
            $subject->expects($this->once())->method('setUserAnswerFromRequestData');
        }
        $subject->expects($this->once())->method('redirect')->with('finish', null, null, ['survey' => $survey]);
        $subject->expects($this->once())->method('addSurveyToCookie')->with($survey);

        $subject->_call('saveResultAndFinish', $survey, $answerData);

        unset($GLOBALS['TSFE']);
    }

    /**
     * @test
     */
    public function convertingRequestWillCreateUserAnswersArray()
    {
        $subject = $this->getAccessibleMock(
            SurveyController::class,
            ['redirect', 'forward', 'addFlashMessage', 'getQuestionFromSurveyByUid', 'setUserAnswerFromRequestData'],
            [],
            '',
            false
        );
        $answers = [
            12 => [
                'answer' => 'test12'
            ],
            14 => [
                'answer' => '',
                'otherAnswer' => 'other'
            ],
            16 => [
                'answer' => 'answer',
                'otherAnswer' => 'other-other'
            ]
        ];

        $mockedRequest = $this->createPartialMock(Request::class, ['hasArgument', 'getArgument']);
        $mockedRequest->expects($this->once())->method('hasArgument')->with($this->equalTo('answers'))->willReturn(true);
        $mockedRequest->expects($this->once())->method('getArgument')->with($this->equalTo('answers'))->willReturn($answers);

        $this->inject($subject, 'request', $mockedRequest);

        $expect = [
            12 => 'test12',
            14 => 'other',
            16 => 'answer'
        ];

        $this->assertEquals(
            $expect,
            $subject->_call('convertRequestToUserAnswersArray')
        );
    }

    /**
     * @test
     */
    public function getNextActionFirstTimeReturnFirstQuestion()
    {
        $subject = $this->getAccessibleMock(
            SurveyController::class,
            ['redirect', 'forward', 'addFlashMessage', 'getQuestionFromSurveyByUid', 'setUserAnswerFromRequestData'],
            [],
            '',
            false
        );

        $mockedFeUserAuth = $this->createPartialMock(FrontendUserAuthentication::class, ['getKey', 'setKey']);

        $mockedTSFE = $this->getAccessibleMock(TypoScriptFrontendController::class, [], [], '', false);
        $mockedTSFE->fe_user = $mockedFeUserAuth;
        $GLOBALS['TSFE'] = $mockedTSFE;

        $mockedFeUserAuth->expects($this->once())->method('getKey')->willReturn([]);

        $survey = new Survey();
        $survey->_setProperty('uid', 1);
        $question1 = new Question();
        $question2 = new Question();

        $survey->addQuestion($question1);
        $survey->addQuestion($question2);

        $this->assertSame(
            $question1,
            $subject->_call('getNextQuestion', $survey)
        );

        unset($GLOBALS['TSFE']);
    }

    /**
     * @test
     */
    public function getNextActionWillReturnNextQuestion()
    {
        $subject = $this->getAccessibleMock(
            SurveyController::class,
            ['redirect', 'forward', 'addFlashMessage', 'getQuestionFromSurveyByUid', 'setUserAnswerFromRequestData'],
            [],
            '',
            false
        );

        $mockedFeUserAuth = $this->createPartialMock(FrontendUserAuthentication::class, ['getKey', 'setKey']);

        $mockedTSFE = $this->getAccessibleMock(TypoScriptFrontendController::class, [], [], '', false);
        $mockedTSFE->fe_user = $mockedFeUserAuth;
        $GLOBALS['TSFE'] = $mockedTSFE;

        $answerData = [
            1 => [
                12 => 'test'
            ]
        ];

        $mockedFeUserAuth->expects($this->once())->method('getKey')->willReturn($answerData);

        $survey = new Survey();
        $survey->_setProperty('uid', 1);
        $question1 = new Question();
        $question1->_setProperty('uid', 12);

        $question2 = new Question();

        $survey->addQuestion($question1);
        $survey->addQuestion($question2);

        $this->assertSame(
            $question2,
            $subject->_call('getNextQuestion', $survey)
        );

        unset($GLOBALS['TSFE']);
    }

    /**
     * @test
     */
    public function surveyIsNotAllowedIfUserAnswerCountEqualsQuestionCount()
    {
        $subject = $this->getAccessibleMock(
            SurveyController::class,
            ['isUserLoggedIn'],
            [],
            '',
            false
        );
        $subject
            ->expects($this->once())
            ->method('isUserLoggedIn')
            ->willReturn(true); // Simulate login

        $mockedTSFE = $this->createMock(TypoScriptFrontendController::class);
        $feUser = new FrontendUser();

        $GLOBALS['TSFE'] = $mockedTSFE;

        $mockedFrontendUserRepository = $this->createPartialMock(FrontendUserRepository::class, ['findByUid']);
        $mockedFrontendUserRepository->expects($this->once())->method('findByUid')->willReturn($feUser);

        $mockedUserAnswerRepository = $this->createPartialMock(UserAnswerRepository::class, ['countGivenUserAnswer']);
        $mockedUserAnswerRepository->expects($this->once())->method('countGivenUserAnswer')->willReturn(2);

        $subject->_set('frontendUserRepository', $mockedFrontendUserRepository);
        $subject->_set('userAnswerRepository', $mockedUserAnswerRepository);

        $survey = new Survey();
        $survey->_setProperty('uid', 1);
        $survey->addQuestion(new Question());
        $survey->addQuestion(new Question());

        $this->assertFalse(
            $subject->_call('isSurveyAllowed', $survey)
        );

        unset($GLOBALS['TSFE']);
    }

    /**
     * @test
     */
    public function surveyIsAllowedIfUserAnswerCountLessThanQuestionCount()
    {
        $subject = $this->getAccessibleMock(
            SurveyController::class,
            ['isUserLoggedIn'],
            [],
            '',
            false
        );
        $subject
            ->expects($this->once())
            ->method('isUserLoggedIn')
            ->willReturn(true); // Simulate login

        $mockedTSFE = $this->createMock(TypoScriptFrontendController::class);
        $feUser = new FrontendUser();

        $GLOBALS['TSFE'] = $mockedTSFE;

        $mockedFrontendUserRepository = $this->createPartialMock(FrontendUserRepository::class, ['findByUid']);
        $mockedFrontendUserRepository->expects($this->once())->method('findByUid')->willReturn($feUser);

        $mockedUserAnswerRepository = $this->createPartialMock(UserAnswerRepository::class, ['countGivenUserAnswer']);
        $mockedUserAnswerRepository->expects($this->once())->method('countGivenUserAnswer')->willReturn(0);

        $subject->_set('frontendUserRepository', $mockedFrontendUserRepository);
        $subject->_set('userAnswerRepository', $mockedUserAnswerRepository);

        $survey = new Survey();
        $survey->_setProperty('uid', 1);
        $survey->addQuestion(new Question());
        $survey->addQuestion(new Question());

        $this->assertTrue(
            $subject->_call('isSurveyAllowed', $survey)
        );

        unset($GLOBALS['TSFE']);
    }

    /**
     * @test
     */
    public function surveyIsAllowedIfNoLoginAndNotInCookies()
    {
        $subject = $this->getAccessibleMock(
            SurveyController::class,
            ['isUserLoggedIn'],
            [],
            '',
            false
        );
        $subject
            ->expects($this->once())
            ->method('isUserLoggedIn')
            ->willReturn(false); // Not logged in

        $_COOKIE[SurveyMainUtility::SURVEY_FINISHED_COOKIE_NAME] = '2,4';
        $mockedTSFE = $this->createMock(TypoScriptFrontendController::class);

        $GLOBALS['TSFE'] = $mockedTSFE;

        $survey = new Survey();
        $survey->_setProperty('uid', 1);

        $this->assertTrue(
            $subject->_call('isSurveyAllowed', $survey)
        );

        unset($GLOBALS['TSFE']);
    }

    /**
     * @test
     */
    public function surveyIsNotAllowedIfNoLoginAndInCookies()
    {
        $subject = $this->getAccessibleMock(
            SurveyController::class,
            ['isUserLoggedIn'],
            [],
            '',
            false
        );
        $subject
            ->expects($this->once())
            ->method('isUserLoggedIn')
            ->willReturn(false); // Not logged in

        $_COOKIE[SurveyMainUtility::SURVEY_FINISHED_COOKIE_NAME] = '2,4,1';
        $mockedTSFE = $this->createMock(TypoScriptFrontendController::class);

        $GLOBALS['TSFE'] = $mockedTSFE;

        $survey = new Survey();
        $survey->_setProperty('uid', 1);

        $this->assertFalse(
            $subject->_call('isSurveyAllowed', $survey)
        );

        unset($GLOBALS['TSFE']);
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function dataForSaveResultWillSaveUserResultsAndClearSession()
    {
        return [
            'single_answer_data' => [
                [
                    1221 => 'user answer'
                ],
                false // single
            ],
            'multiple_answer_data' => [
                [
                    1221 => [
                        12,
                        14,
                        16
                    ]
                ],
                true // multiple
            ]
        ];
    }

    /**
     * @test
     */
    public function includeReCaptchaScriptIfItsEnabled()
    {
        $subject = $this->getAccessibleMock(
            SurveyController::class,
            ['getPageRenderer'],
            [],
            '',
            false
        );
        $settings = [
            'protectWithReCaptcha' => 1,
            'recaptcha' => [
                'donNotIncludeJsApi' => 0,
                'siteKey' => 1,
                'siteSecret' => 123
            ]
        ];
        $mockedPageRenderer = $this->createMock(PageRenderer::class);
        $mockedPageRenderer->expects($this->once())
            ->method('addJsFile');

        $subject->_set('settings', $settings);
        $subject->expects($this->once())
            ->method('getPageRenderer')
            ->willReturn($mockedPageRenderer);

        $subject->initializeShowAction();
    }

    /**
     * @test
     */
    public function disableIncludingReCaptchaScriptWillNotIncludeScript()
    {
        $subject = $this->getAccessibleMock(
            SurveyController::class,
            ['getPageRenderer'],
            [],
            '',
            false
        );
        $settings = [
            'protectWithReCaptcha' => 1,
            'recaptcha' => [
                'donNotIncludeJsApi' => 1,
                'siteKey' => 1,
                'siteSecret' => 123
            ]
        ];

        $subject->_set('settings', $settings);
        $subject->expects($this->never())
            ->method('getPageRenderer');

        $subject->initializeShowAction();
    }

    /**
     * @test
     */
    public function incorrectSettingsWillNotIncludeScriptReCaptchaScript()
    {
        $subject = $this->getAccessibleMock(
            SurveyController::class,
            ['getPageRenderer'],
            [],
            '',
            false
        );
        $settings = [
            'protectWithReCaptcha' => 1,
            'recaptcha' => [
                'donNotIncludeJsApi' => 0,
                'siteKey' => '',
                'siteSecret' => ''
            ]
        ];

        $subject->_set('settings', $settings);
        $subject->expects($this->never())
            ->method('getPageRenderer');

        $subject->initializeShowAction();
    }
}
