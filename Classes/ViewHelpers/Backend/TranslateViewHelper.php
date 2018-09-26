<?php

namespace Pixelant\PxaSurvey\ViewHelpers\Backend;

use Pixelant\PxaSurvey\Utility\SurveyMainUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * Class TranslateViewHelper
 * @package Pixelant\PxaSurvey\ViewHelpers\Backend
 */
class TranslateViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * Initialize arguments
     *
     * @api
     */
    public function initializeArguments()
    {
        $this->registerArgument('key', 'string', 'Key to translate', true);
        $this->registerArgument('arguments', 'array', 'Translate arguments', false, []);
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
        $key = $arguments['key'];
        $arguments = $arguments['arguments'];

        return SurveyMainUtility::translate($key, $arguments);
    }
}
