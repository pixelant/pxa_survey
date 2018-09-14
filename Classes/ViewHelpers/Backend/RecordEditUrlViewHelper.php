<?php

namespace Pixelant\PxaSurvey\ViewHelpers\Backend;

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class RecordEditUrlViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * Path to translation file
     *
     * @var string
     */
    public static $LL = 'LLL:EXT:pxa_survey/Resources/Private/Language/locallang_be.xlf:';

    /**
     * Initialize arguments
     *
     * @api
     */
    public function initializeArguments()
    {
        $this->registerArgument('uid', 'int', 'Record uid', true);
        $this->registerArgument('table', 'string', 'Table name', false, 'tx_pxasurvey_domain_model_survey');
    }

    /**
     * BE view helper for translate
     *
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $table = $arguments['table'];
        $uid = $arguments['uid'];

        $urlParameters = [
            'edit[' . $table . '][' . $uid . ']' => 'edit',
            'returnUrl' => GeneralUtility::getIndpEnv('REQUEST_URI')
        ];
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);

        return (string)$uriBuilder->buildUriFromRoute('record_edit', $urlParameters);
    }
}
