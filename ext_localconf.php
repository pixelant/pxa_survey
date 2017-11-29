<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function () {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Pixelant.PxaSurvey',
            'Survey',
            [
                'Survey' => 'show, answer, finish'
            ],
            // non-cacheable actions
            [
                'Survey' => 'show, answer, finish'
            ]
        );

        // wizards
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
            '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:pxa_survey/Configuration/TypoScript/PageTS/wizards.ts">'
        );
    }
);
