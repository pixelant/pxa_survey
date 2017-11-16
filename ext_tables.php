<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function () {
        // Registre hook for plugin preview
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info']['pxasurvey_survey']['pxa_survey'] =
            \Pixelant\PxaSurvey\Hooks\ListTypeInfoPreviewHook::class . '->getExtensionSummary';

        if (TYPO3_MODE === 'BE') {
            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
                'Pixelant.PxaSurvey',
                'web', // Make module a submodule of 'web'
                'surveyanalysis', // Submodule key
                '', // Position
                [
                    'SurveyAnalysis' => 'main, seeAnalysis',

                ],
                [
                    'access' => 'user,group',
                    'icon' => 'EXT:pxa_survey/Resources/Public/Icons/user_mod_surveyanalysis.svg',
                    'labels' => 'LLL:EXT:pxa_survey/Resources/Private/Language/locallang_surveyanalysis.xlf',
                ]
            );

            $icons = [
                'ext-pxa-survey-wizard-icon' => 'user_plugin_survey.svg',
            ];

            /** @var \TYPO3\CMS\Core\Imaging\IconRegistry $iconRegistry */
            $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                \TYPO3\CMS\Core\Imaging\IconRegistry::class
            );

            foreach ($icons as $identifier => $path) {
                $iconRegistry->registerIcon(
                    $identifier,
                    \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
                    ['source' => 'EXT:pxa_survey/Resources/Public/Icons/' . $path]
                );
            }
        }

        $tables = [
            'tx_pxasurvey_domain_model_survey',
            'tx_pxasurvey_domain_model_question',
            'tx_pxasurvey_domain_model_answer'
        ];

        foreach ($tables as $table) {
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
                $table,
                'EXT:pxa_survey/Resources/Private/Language/locallang_csh_' . $table . '.xlf'
            );
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages($table);
        }
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages(
            'tx_pxasurvey_domain_model_useranswer'
        );
    }
);
