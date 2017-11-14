<?php
defined('TYPO3_MODE') || die;

call_user_func(function () {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'Pixelant.PxaSurvey',
        'Survey',
        'Simple Survey'
    );
    $pluginSignature = 'pxasurvey_survey';

    // exclude some fields
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'recursive,pages';

    // add flexform
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
        $pluginSignature,
        'FILE:EXT:pxa_survey/Configuration/FlexForms/FlexformSurvey.xml'
    );
});
