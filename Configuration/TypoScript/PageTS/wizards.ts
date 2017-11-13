mod {
    wizards.newContentElement.wizardItems.plugins {
        elements {
            survey {
                icon = EXT:pxa_survey/Resources/Public/Icons/user_plugin_survey.svg
                title = LLL:EXT:pxa_survey/Resources/Private/Language/locallang_db.xlf:tx_pxa_survey_domain_model_survey
                description = LLL:EXT:pxa_survey/Resources/Private/Language/locallang_db.xlf:tx_pxa_survey_domain_model_survey.description
                tt_content_defValues {
                    CType = list
                    list_type = pxasurvey_survey
                }
            }
        }
        show = *
    }
}