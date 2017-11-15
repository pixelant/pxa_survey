<?php

namespace Pixelant\PxaSurvey\ViewHelpers\Backend;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Lang\LanguageService;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class TranslateViewHelper extends AbstractViewHelper
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
    protected static function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
