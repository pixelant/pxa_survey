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
use Pixelant\PxaSurvey\ViewHelpers\Backend\TranslateViewHelper as Translate;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Lang\LanguageService;

/**
 * Class SurveyAnalysisController
 * @package Pixelant\PxaSurvey\Controller
 */
class SurveyAnalysisController extends ActionController
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
     * Survey Repository
     *
     * @var \Pixelant\PxaSurvey\Domain\Repository\SurveyRepository
     * @inject
     */
    protected $surveyRepository = null;

    /**
     * User Answer Repository
     *
     * @var \Pixelant\PxaSurvey\Domain\Repository\UserAnswerRepository
     * @inject
     */
    protected $userAnswerRepository = null;

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
     * Generate data for Charts.js
     *
     * @param Survey $survey
     * @return array
     */
    protected function generateAnalysisData(Survey $survey): array
    {
        $data = [];

        /** @var Question $question */
        foreach ($survey->getQuestions() as $question) {
            $questionData = [];
            $allAnswersCount = 0;

            /** @noinspection PhpUndefinedMethodInspection */
            $userAnswers = $this->userAnswerRepository->findByQuestion($question);

            /** @var UserAnswer $userAnswer */
            foreach ($userAnswers as $userAnswer) {
                // if check box or radio
                if ($userAnswer->getAnswers()->count() > 0) {
                    /** @var Answer $answer */
                    foreach ($userAnswer->getAnswers() as $answer) {
                        if (!is_array($questionData[$answer->getUid()])) {
                            $questionData[$answer->getUid()] = [
                                'label' => $answer->getText(),
                                'count' => 1
                            ];
                        } else {
                            $questionData[$answer->getUid()]['count'] += 1;
                        }

                        $allAnswersCount++;
                    }
                } elseif (!empty($userAnswer->getCustomValue())) { // custom value
                    $identifier = GeneralUtility::shortMD5($userAnswer->getCustomValue());

                    if (!is_array($questionData[$identifier])) {
                        $questionData[$identifier] = [
                            'label' => $userAnswer->getCustomValue(),
                            'count' => 1
                        ];
                    } else {
                        $questionData[$identifier]['count'] += 1;
                    }

                    $allAnswersCount++;
                }
            }

            // add to data array
            $data[$question->getUid()] = [
                'questionData' => $this->calculatePercentsForQuestionData($questionData, $allAnswersCount),
                'labelChart' => $this->translate('module.percentages'),
                'label' => $question->getText(),
                'allAnswersCount' => $allAnswersCount
            ];
        }

        return $data;
    }

    /**
     * Count in percents user answers
     *
     * @param array $questionData
     * @param int $allAnswersCount
     * @return array
     */
    protected function calculatePercentsForQuestionData(array $questionData, int $allAnswersCount): array
    {
        foreach ($questionData as &$questionItem) {
            $questionItem['percents'] = round($questionItem['count'] / $allAnswersCount, 3) * 100;
        }

        return $questionData;
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
            ->setTitle($this->getLanguageService()->sL(Translate::$LL . 'module.new_survey'))
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

    /**
     * Translate function
     *
     * @param string $key
     * @param array $arguments
     * @return string
     */
    protected function translate(string $key, array $arguments = []): string
    {
        $label = $this->getLanguageService()->sL(Translate::$LL . $key);

        if (!empty($arguments)) {
            $label = vsprintf(
                $label,
                $arguments
            );
        }

        return $label;
    }

    /**
     * @return LanguageService
     */
    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
