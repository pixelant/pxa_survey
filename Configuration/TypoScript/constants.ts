
plugin.tx_pxasurvey_survey {
    view {
        # cat=plugin.tx_pxasurvey_survey/file; type=string; label=Path to template root (FE)
        templateRootPath = EXT:pxa_survey/Resources/Private/Templates/
        # cat=plugin.tx_pxasurvey_survey/file; type=string; label=Path to template partials (FE)
        partialRootPath = EXT:pxa_survey/Resources/Private/Partials/
        # cat=plugin.tx_pxasurvey_survey/file; type=string; label=Path to template layouts (FE)
        layoutRootPath = EXT:pxa_survey/Resources/Private/Layouts/
    }
    persistence {
        # cat=plugin.tx_pxasurvey_survey//a; type=string; label=Default storage PID
        storagePid =
    }

    #customcategory=tx_pxasurvey_survey=LLL:EXT:pxa_survey/Resources/Private/Language/locallang_be.xlf:editor.category_name
    settings {
        # customsubcategory=recaptcha=LLL:EXT:pxa_survey/Resources/Private/Language/locallang_be.xlf:editor.recaptcha_title
        recaptcha {
            #cat=tx_pxasurvey_survey/recaptcha/010; type=string; label=LLL:EXT:pxa_survey/Resources/Private/Language/locallang_be.xlf:editor.recaptcha.site_key
            siteKey =

            #cat=tx_pxasurvey_survey/recaptcha/020; type=string; label=LLL:EXT:pxa_survey/Resources/Private/Language/locallang_be.xlf:editor.recaptcha.site_secret
            siteSecret =
        }
    }
}

module.tx_pxasurvey_surveyanalysis {
    view {
        # cat=module.tx_pxasurvey_surveyanalysis/file; type=string; label=Path to template root (BE)
        templateRootPath = EXT:pxa_survey/Resources/Private/Backend/Templates/
        # cat=module.tx_pxasurvey_surveyanalysis/file; type=string; label=Path to template partials (BE)
        partialRootPath = EXT:pxa_survey/Resources/Private/Backend/Partials/
        # cat=module.tx_pxasurvey_surveyanalysis/file; type=string; label=Path to template layouts (BE)
        layoutRootPath = EXT:pxa_survey/Resources/Private/Backend/Layouts/
    }
    persistence {
        # cat=module.tx_pxasurvey_surveyanalysis//a; type=string; label=Default storage PID
        storagePid =
    }
}
