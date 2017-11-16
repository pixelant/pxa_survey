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

use Pixelant\PxaSurvey\Utility\SurveyMainUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
            SurveyMainUtility::translate('extension_info.name')
        );

        $flexformData = GeneralUtility::xml2array($params['row']['pi_flexform'] ?? '');

        if (is_array($flexformData)) {
            $additionalInfo = '';

            $settings = $flexformData['data']['sDEF']['lDEF'];
            $surveyUid = (int)$settings['settings.survey']['vDEF'];
            $showAllQuestions = (int)$settings['settings.showAllQuestions']['vDEF'] ? 'yes' : 'no';

            $surveyRow = BackendUtility::getRecord(
                'tx_pxasurvey_domain_model_survey',
                $surveyUid,
                'name'
            );

            if (is_array($surveyRow)) {
                $additionalInfo .= sprintf(
                    '<b>%s</b>: %s<br>',
                    SurveyMainUtility::translate('extension_info.survey'),
                    $surveyRow['name']
                );
            }

            $additionalInfo .= sprintf(
                '<b>%s</b>: %s<br>',
                SurveyMainUtility::translate('extension_info.show_all'),
                SurveyMainUtility::translate('extension_info.' . $showAllQuestions)
            );
        }

        return $header . (isset($additionalInfo) ? '<br><pre>' . $additionalInfo . '</pre>' : '');
    }
}
