mod {
    wizards.newContentElement.wizardItems.plugins {
        elements {
            pxa_survey {
                iconIdentifier = ext-pxa-survey-wizard-icon
                title = LLL:EXT:pxa_survey/Resources/Private/Language/locallang_be.xlf:plugin.name
                description = LLL:EXT:pxa_survey/Resources/Private/Language/locallang_be.xlf:plugin.description
                tt_content_defValues {
                    CType = list
                    list_type = pxasurvey_survey
                }
            }
        }
    }
}