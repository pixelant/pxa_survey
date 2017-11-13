<?php
namespace Pixelant\PxaSurvey\Tests\Unit\Controller;

/**
 * Test case.
 *
 * @author Andriy Oprysko 
 */
class SurveyControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \Pixelant\PxaSurvey\Controller\SurveyController
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(\Pixelant\PxaSurvey\Controller\SurveyController::class)
            ->setMethods(['redirect', 'forward', 'addFlashMessage'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenSurveyToView()
    {
        $survey = new \Pixelant\PxaSurvey\Domain\Model\Survey();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('survey', $survey);

        $this->subject->showAction($survey);
    }
}
