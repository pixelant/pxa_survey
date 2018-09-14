<?php

namespace Pixelant\PxaSurvey\Hooks;

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


use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Class ListTypeInfoPreviewHook
 * @package Pixelant\PxaSurvey\Hooks
 */
class ListTypeInfoPreviewHook
{
    /**
     * Render preview for plugin
     *
     * @param array $params
     * @return string
     */
    public function getExtensionSummary(array $params): string
    {
        $flexFormServiceClass = version_compare(TYPO3_version, '9.0', '<')
            ? 'TYPO3\\CMS\\Extbase\\Service\\FlexFormService'
            : 'TYPO3\\CMS\\Core\\Service\\FlexFormService';

        $flexFormService = GeneralUtility::makeInstance($flexFormServiceClass);
        $flexFormData = $flexFormService->convertFlexFormContentToArray($params['row']['pi_flexform'] ?? '');

        if (is_array($flexFormData['settings'])) {
            $surveyUid = (int)$flexFormData['settings']['survey'];
            $allowedActions = GeneralUtility::trimExplode(';', $flexFormData['switchableControllerActions']);
            list(, $action) = GeneralUtility::trimExplode('->', $allowedActions[0]);

            $surveyRow = BackendUtility::getRecord(
                'tx_pxasurvey_domain_model_survey',
                $surveyUid,
                'name'
            );

            $view = $this->getView();
            $view
                ->assign('settings', $flexFormData['settings'])
                ->assign('surveyRow', $surveyRow)
                ->assign('action', GeneralUtility::camelCaseToLowerCaseUnderscored($action));

            return $view->render();
        }

        return '';
    }

    /**
     * Initalize view
     *
     * @return StandaloneView
     */
    protected function getView(): StandaloneView
    {
        /** @var StandaloneView $view */
        $view = GeneralUtility::makeInstance(ObjectManager::class)->get(StandaloneView::class);
        $templatePath = GeneralUtility::getFileAbsFileName(
            'EXT:pxa_survey/Resources/Private/Templates/PageLayoutPreview/ListTypeInfoPreviewHook.html'
        );

        $view->setTemplatePathAndFilename($templatePath);

        return $view;
    }
}
