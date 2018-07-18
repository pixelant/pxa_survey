plugin.tx_pxasurvey_survey {
    view {
        templateRootPaths {
            0 = EXT:pxa_survey/Resources/Private/Templates/
            1 = {$plugin.tx_pxasurvey_survey.view.templateRootPath}
        }
        partialRootPaths {
            0 = EXT:pxa_survey/Resources/Private/Partials/
            1 = {$plugin.tx_pxasurvey_survey.view.partialRootPath}
        }
        layoutRootPaths {
            0 = EXT:pxa_survey/Resources/Private/Layouts/
            1 = {$plugin.tx_pxasurvey_survey.view.layoutRootPath}
        }
    }
    persistence {
        storagePid = {$plugin.tx_pxasurvey_survey.persistence.storagePid}
        #recursive = 1
    }
    features {
        #skipDefaultArguments = 1
        # if set to 1, the enable fields are ignored in BE context
        ignoreAllEnableFieldsInBe = 0
        # Should be on by default, but can be disabled if all action in the plugin are uncached
        requireCHashArgumentForActionArguments = 1
    }
    mvc {
        #callDefaultActionIfActionCantBeResolved = 1
    }
}

# Module configuration
module.tx_pxasurvey_web_pxasurveysurveyanalysis {
    persistence {
        storagePid = {$module.tx_pxasurvey_surveyanalysis.persistence.storagePid}
    }
    view {
        templateRootPaths {
            0 = EXT:pxa_survey/Resources/Private/Templates/
            1 = {$module.tx_pxasurvey_surveyanalysis.view.templateRootPath}
        }
        partialRootPaths {
            0 = EXT:pxa_survey/Resources/Private/Partials/
            1 = {$module.tx_pxasurvey_surveyanalysis.view.partialRootPath}
        }
        layoutRootPaths {
            0 = EXT:pxa_survey/Resources/Private/Layouts/
            1 = {$module.tx_pxasurvey_surveyanalysis.view.layoutRootPath}
        }
    }
}

page {
    includeJSFooterlibs {
        pxa_survey = EXT:pxa_survey/Resources/Public/JavaScript/Survey.js
    }
    includeJSFooter {
        pxa_survey = EXT:pxa_survey/Resources/Public/JavaScript/pxa_survey.js
    }
    includeCSS {
        pxa_survey = EXT:pxa_survey/Resources/Public/Css/pxa_survey.css
    }
}
