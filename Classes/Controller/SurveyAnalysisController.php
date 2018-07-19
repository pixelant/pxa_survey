<?php

namespace Pixelant\PxaSurvey\Controller;

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

use Pixelant\PxaSurvey\Domain\Model\Answer;
use Pixelant\PxaSurvey\Domain\Model\Question;
use Pixelant\PxaSurvey\Domain\Model\Survey;
use Pixelant\PxaSurvey\Domain\Model\UserAnswer;
use Pixelant\PxaSurvey\Utility\SurveyMainUtility;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;

/**
 * Class SurveyAnalysisController
 * @package Pixelant\PxaSurvey\Controller
 */
class SurveyAnalysisController extends AbstractController
{
    /**
     * BackendTemplateContainer
     *
     * @var BackendTemplateView
     */
    protected $view;

    /**
     * Backend Template Container
     *
     * @var BackendTemplateView
     */
    protected $defaultViewObjectName = BackendTemplateView::class;

    /**
     * Current page
     *
     * @var int
     */
    protected $pid = 0;

    /**
     * Initialize
     */
    public function initializeAction()
    {
        $this->pid = (int)GeneralUtility::_GET('id');
    }

    /**
     * Main action
     */
    public function mainAction()
    {
        if ($this->pid) {
            $surveys = $this->surveyRepository->findByPid($this->pid);
        }

        $this->view->assign('surveys', $surveys ?? []);
    }

    /**
     * Display analysis for survey
     *
     * @param Survey $survey
     */
    public function seeAnalysisAction(Survey $survey)
    {
        $data = $this->generateAnalysisData($survey);

        $this->view->assign('dataJson', json_encode($data));
        $this->view->assign('data', $data);
    }

    /**
     * Export data as csv file
     *
     * @param Survey $survey
     */
    public function exportCsvAction(Survey $survey)
    {
        $data = $this->generateAnalysisData($survey);

        $lines = [
            [$survey->getName() . ($survey->getTitle() ? (' (' . $survey->getTitle() . ')') : '')]
        ];

        foreach ($data as $questionData) {
            $lines[] = []; // empty line
            $lines[] = [$questionData['label']];


            $lines[] = []; // empty line
            $lines[] = [
                SurveyMainUtility::translate('module.answers'),
                SurveyMainUtility::translate('module.percentages'),
                SurveyMainUtility::translate('module.count'),
            ];

            foreach ($questionData['questionData'] as $questionAnswerData) {
                $lines[] = [
                    $questionAnswerData['label'],
                    $questionAnswerData['percents'] . ' %',
                    $questionAnswerData['count']
                ];
            }
            $lines[] = [
                '',
                '',
                SurveyMainUtility::translate('module.total_answers', [$questionData['allAnswersCount']])
            ];

            $lines[] = []; // empty line
        }

        $fileName = str_replace(' ', '_', $survey->getName()) . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $fileName,
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];
        foreach ($headers as $header => $headerValue) {
            /** @noinspection PhpUndefinedMethodInspection */
            $this->response->setHeader($header, $headerValue);
        }
        /** @noinspection PhpUndefinedMethodInspection */
        $this->response->sendHeaders();

        $output = fopen('php://output', 'w');
        foreach ($lines as $singleLine) {
            fputcsv($output, $singleLine);
        }
        fclose($output);

        exit(0);
    }

    /**
     * Set up view
     *
     * @param ViewInterface $view
     */
    protected function initializeView(ViewInterface $view)
    {
        /** @var BackendTemplateView $view */
        parent::initializeView($view);
        if ($this->view->getModuleTemplate() !== null) {
            $this->createButtons();
        }
    }

    /**
     * Add menu buttons
     *
     * @return void
     */
    protected function createButtons()
    {
        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);
        $buttonBar = $this->view->getModuleTemplate()->getDocHeaderComponent()->getButtonBar();

        $button = $buttonBar->makeLinkButton()
            ->setHref($this->buildNewSurveyUrl())
            ->setTitle(SurveyMainUtility::translate('module.new_survey'))
            ->setIcon($iconFactory->getIcon('actions-document-new', Icon::SIZE_SMALL));

        $buttonBar->addButton($button, ButtonBar::BUTTON_POSITION_LEFT);
    }

    /**
     * Generate url to create new survey
     *
     * @return string
     */
    protected function buildNewSurveyUrl(): string
    {
        $url = BackendUtility::getModuleUrl('record_edit', [
            'edit[tx_pxasurvey_domain_model_survey][' . $this->pid . ']' => 'new',
            'returnUrl' => GeneralUtility::getIndpEnv('REQUEST_URI')
        ]);

        return $url;
    }
}
