<?php

namespace Pixelant\PxaSurvey\Utility;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

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
     * Name of cookie that keep list of finished surveys
     */
    const SURVEY_FINISHED_COOKIE_NAME = 'pxa_survey_finished';

    /**
     * Path to translation file
     *
     * @var string
     */
    public static $LL = 'LLL:EXT:pxa_survey/Resources/Private/Language/locallang_be.xlf:';

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
     * Add value to cookie list
     *
     * @param string $name
     * @param int $value
     */
    public static function addValueToListCookie(string $name, int $value)
    {
        $cookie = array_key_exists($name, $_COOKIE)
            ? GeneralUtility::intExplode(',', $_COOKIE[$name], true)
            : [];

        // If not in array yet
        if (!in_array($value, $cookie, true)) {
            $cookie[] = $value;
        }

        setcookie(
            $name,
            implode(',', $cookie),
            0,
            '/'
        );
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

    /**
     * Check if FE user is logged in
     *
     * @return bool
     */
    public static function isFrontendLogin(): bool
    {
        return GeneralUtility::makeInstance(Context::class)->getPropertyFromAspect('frontend.user', 'isLoggedIn', false);
    }
}
