<?php

namespace Pixelant\PxaSurvey\Utility;

use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Lang\LanguageService;

/**
 * Main utility
 */
class SurveyMainUtility
{
    /**
     * Store in session with such name
     */
    const SESSION_KEY = 'pxa_survey_answers';

    /**
     * Path to translation file
     *
     * @var string
     */
    public static $LL = 'LLL:EXT:pxa_survey/Resources/Private/Language/locallang_be.xlf:';

    /**
     * Extension configuration
     *
     * @var array
     */
    protected static $extensionConfiguration;

    /**
     * Get extension configuration
     *
     * @return array
     */
    public static function getExtensionConfiguration(): array
    {
        if (self::$extensionConfiguration === null) {
            self::$extensionConfiguration = unserialize(
                $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['pxa_survey']
            );
        }

        return self::$extensionConfiguration ?? [];
    }

    /**
     * Add single answer to session
     *
     * @param int $surveyUid
     * @param array $answer (Question Uid => Value)
     */
    public static function addAnswerToSessionData(int $surveyUid, array $answer)
    {
        $data = self::getTSFE()->fe_user->getKey('ses', self::SESSION_KEY) ?? [];
        if (!is_array($data[$surveyUid])) {
            $data[$surveyUid] = [];
        }

        $data[$surveyUid] += $answer;
        self::storeDataInSession($data);
    }

    /**
     * Get answers from session
     *
     * @param int $surveyUid
     * @return array
     */
    public static function getAnswerSessionData(int $surveyUid): array
    {
        $data = self::getTSFE()->fe_user->getKey('ses', self::SESSION_KEY) ?? [];

        return $data[$surveyUid] ?? [];
    }

    /**
     * Save data in session
     *
     * @param array $data
     */
    public static function storeDataInSession(array $data)
    {
        self::getTSFE()->fe_user->setKey('ses', self::SESSION_KEY, $data);
    }

    /**
     * Clean session for survey
     */
    public static function clearAnswersSessionData(int $surveyUid)
    {
        $data = self::getAnswerSessionData($surveyUid);

        if (isset($data[$surveyUid])) {
            unset($data[$surveyUid]);
        }

        self::storeDataInSession($data);
    }

    /**
     * Translate function
     *
     * @param string $key
     * @param array $arguments
     * @return string
     */
    public static function translate(string $key, array $arguments = []): string
    {
        if (TYPO3_MODE !== 'BE') {
            return '';
        }

        $label = self::getLanguageService()->sL(self::$LL . $key);

        if (!empty($arguments)) {
            $label = vsprintf(
                $label,
                $arguments
            );
        }

        return $label;
    }

    /**
     * @return LanguageService
     */
    public static function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

    /**
     * @return TypoScriptFrontendController
     */
    public static function getTSFE(): TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }
}
