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
        $flexFormData = GeneralUtility::xml2array($params['row']['pi_flexform'] ?? '');
        $flexFormSettings = [];

        if (is_array($flexFormData['data']['sDEF']['lDEF'])) {
            foreach ($flexFormData['data'] as $sheet) {
                $rawSettings = $sheet['lDEF'];
                foreach ($rawSettings as $field => $rawSetting) {
                    $this->flexFormToArray($field, $rawSetting['vDEF'], $flexFormSettings);
                }
            }
        }

        $surveyRow = BackendUtility::getRecord(
            'tx_pxasurvey_domain_model_survey',
            $surveyUid = (int)$flexFormSettings['settings']['survey'],
            'name'
        );

        $view = $this->getView();
        $view
            ->assign('settings', $flexFormSettings['settings'])
            ->assign('surveyRow', $surveyRow);

        return $view->render();
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

    /**
     * Go through all settings and generate array
     *
     * @param string $field
     * @param mixed $value
     * @param array $settings
     * @return void
     */
    protected function flexFormToArray($field, $value, &$settings)
    {
        $fieldNameParts = GeneralUtility::trimExplode('.', $field);
        if (count($fieldNameParts) > 1) {
            $name = $fieldNameParts[0];
            unset($fieldNameParts[0]);
            if (!isset($settings[$name])) {
                $settings[$name] = [];
            }
            $this->flexFormToArray(implode('.', $fieldNameParts), $value, $settings[$name]);
        } else {
            $settings[$fieldNameParts[0]] = $value;
        }
    }
}
