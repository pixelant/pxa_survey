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

use Pixelant\PxaSurvey\Utility\SurveyMainUtility as MainUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Service\FlexFormService;

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
        $header = sprintf(
            '<strong>%s</strong><br>',
            MainUtility::translate('extension_info.name')
        );

        $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
        $flexFormData = $flexFormService->convertFlexFormContentToArray($params['row']['pi_flexform'] ?? '');

        if (is_array($flexFormData['settings'])) {
            $surveyUid = (int)$flexFormData['settings']['survey'];
            $allowedActions = GeneralUtility::trimExplode(';', $flexFormData['switchableControllerActions']);
            list(, $action) = GeneralUtility::trimExplode('->', $allowedActions[0]);

            $additionalInfo = sprintf(
                '<b>%s</b>: %s<br>',
                MainUtility::translate('flexform.mode'),
                MainUtility::translate('flexform.mode.' . GeneralUtility::camelCaseToLowerCaseUnderscored($action))
            );

            $surveyRow = BackendUtility::getRecord(
                'tx_pxasurvey_domain_model_survey',
                $surveyUid,
                'name'
            );

            if (is_array($surveyRow)) {
                $additionalInfo .= sprintf(
                    '<b>%s</b>: %s<br>',
                    MainUtility::translate('extension_info.survey'),
                    $surveyRow['name']
                );
            }

            switch ($action) {
                case 'showResults':
                    break;
                default:
                    $additionalInfo .= $this->getPreviewForShowAction($flexFormData['settings']);
            }
        }

        return $header . (isset($additionalInfo) ? '<br><pre>' . $additionalInfo . '</pre>' : '');
    }

    /**
     * Get information for show action
     *
     * @param array $settings
     * @return string
     */
    protected function getPreviewForShowAction(array $settings): string
    {
        $preview = '';

        $checkboxes = [
            'show_all' => 'showAllQuestions',
            'multiple_participation' => 'allowMultipleAnswerOnSurvey'
        ];
        foreach ($checkboxes as $translationKey => $checkbox) {
            $label = (int)$settings[$checkbox] ? 'yes' : 'no';

            $preview .= sprintf(
                '<b>%s</b>: %s<br>',
                MainUtility::translate('extension_info.' . $translationKey),
                MainUtility::translate('extension_info.' . $label)
            );
        }

        return $preview;
    }
}
