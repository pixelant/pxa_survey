<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function () {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Pixelant.PxaSurvey',
            'Survey',
            'Simple Survey'
        );

        if (TYPO3_MODE === 'BE') {
            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
                'Pixelant.PxaSurvey',
                'web', // Make module a submodule of 'web'
                'surveyanalysis', // Submodule key
                '', // Position
                [
                    'SurveyAnalysis' => 'main',

                ],
                [
                    'access' => 'user,group',
                    'icon' => 'EXT:pxa_survey/Resources/Public/Icons/user_mod_surveyanalysis.svg',
                    'labels' => 'LLL:EXT:pxa_survey/Resources/Private/Language/locallang_surveyanalysis.xlf',
                ]
            );
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
    }
);
