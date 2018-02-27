<?php

namespace Pixelant\PxaSurvey\Domain\Validation\Validator;

use Pixelant\PxaSurvey\Domain\Model\Question;
use Pixelant\PxaSurvey\Domain\Model\Survey;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Error\Error;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

/**
 * Class ReCaptchaValidator
 * @package Pixelant\PxaSurvey\Domain\Validation\Validator
 */
class ReCaptchaValidator extends AbstractValidator
{

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     * @inject
     */
    protected $objectManager = null;

    /**
     * Extension settings
     *
     * @var array
     */
    protected $settings = [];

    /**
     * Initialize settings
     */
    public function initializeObject()
    {
        /** @var ConfigurationManagerInterface $configurationManager */
        $configurationManager = $this->objectManager->get(ConfigurationManagerInterface::class);

        $this->settings = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS
        );
    }

    /**
     * Check if reCAPTCHA is valid
     *
     * @param Survey $survey
     * @return bool
     */
    public function isValid($survey)
    {
        $isValid = true;
        if ($this->isReCaptchaRequired($survey)) {
            $isValid = false;
            $reCaptchaCode = GeneralUtility::_GP('g-recaptcha-response');

            if ($reCaptchaCode !== null && !empty($this->settings['recaptcha']['siteSecret'])) {
                $requestFactory = $this->getRequestFactory();
                /** @var ResponseInterface $response */
                $response = $requestFactory->request(
                    'https://www.google.com/recaptcha/api/siteverify',
                    'POST',
                    [
                        'form_params' => [
                            'response' => $reCaptchaCode,
                            'secret' => $this->settings['recaptcha']['siteSecret'],
                            'remoteip' => $_SERVER['REMOTE_ADDR']
                        ]
                    ]
                );
                if ($response->getStatusCode() === 200) {
                    $reCaptchaResult = json_decode($response->getBody(), true);
                    $isValid = (bool)$reCaptchaResult['success'];
                }
            }
        }

        if (!$isValid) {
            $this->result->forProperty('recaptcha')->addError(
                new Error(
                    $this->localize('fe.error.recaptcha'),
                    1512131546169
                )
            );
        }

        return $isValid;
    }

    /**
     * Check if we need to do recaptcha validation
     *
     * @param Survey $survey
     * @return bool
     */
    protected function isReCaptchaRequired(Survey $survey)
    {
        if ((int)$this->settings['protectWithReCaptcha'] === 1) {
            if ((int)$this->settings['showAllQuestions'] === 0) {
                $currentQuestionUid = (int)GeneralUtility::_POST('tx_pxasurvey_survey')['currentQuestion'];
                $survey->getQuestions()->rewind();

                /** @var Question $question */
                $question = $survey->getQuestions()->current();

                // If step by step, check recaptcha only for first question
                return $question->getUid() === $currentQuestionUid;
            }

            // reCAPTCHA for all questions is always required
            return true;
        }

        // reCAPTCHA disabled
        return false;
    }

    /**
     * @return RequestFactory
     */
    protected function getRequestFactory(): RequestFactory
    {
        return GeneralUtility::makeInstance(RequestFactory::class);
    }

    /**
     * Wrapper for localization
     *
     * @param string $key
     * @return NULL|string
     */
    protected function localize(string $key)
    {
        return LocalizationUtility::translate($key, 'PxaSurvey');
    }
}
