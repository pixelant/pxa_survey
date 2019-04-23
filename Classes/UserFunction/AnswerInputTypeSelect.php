<?php
declare(strict_types=1);

namespace Pixelant\PxaSurvey\UserFunction;

use Pixelant\PxaSurvey\Domain\Model\Question;
use TYPO3\CMS\Backend\Form\FormDataProviderInterface;

/**
 * Class ConfigurationPathSelect
 * @package Pixelant\PxaPmImporter\UserFunction
 */
class AnswerInputTypeSelect
{
    /**
     * Add importers configurations
     *
     * @param array $params
     * @param FormDataProviderInterface $formDataProvider
     */
    public function renderItems(array $params, FormDataProviderInterface $formDataProvider)
    {
        $ll = 'LLL:EXT:pxa_survey/Resources/Private/Language/locallang_db.xlf:';

        $type = isset($params['row']['type']) && is_array($params['row']['type']) && !empty($params['row']['type'])
            ? ((int)current($params['row']['type']))
            : 0;

        $defaultLabel = $type === Question::ANSWER_TYPE_INPUT
            ? 'tx_pxasurvey_domain_model_question.append.default'
            : 'tx_pxasurvey_domain_model_question.append.none';


        $items = [
            [
                $ll . $defaultLabel,
                \Pixelant\PxaSurvey\Domain\Model\Question::INPUT_TYPE_NONE
            ],
            [
                $ll . 'tx_pxasurvey_domain_model_question.append.input',
                \Pixelant\PxaSurvey\Domain\Model\Question::INPUT_TYPE_INPUT
            ],
            [
                $ll . 'tx_pxasurvey_domain_model_question.append.textarea',
                \Pixelant\PxaSurvey\Domain\Model\Question::INPUT_TYPE_TEXTAREA
            ],
            [
                $ll . 'tx_pxasurvey_domain_model_question.append.email',
                \Pixelant\PxaSurvey\Domain\Model\Question::INPUT_TYPE_EMAIL
            ]
        ];

        $params['items'] = $items;
    }
}
