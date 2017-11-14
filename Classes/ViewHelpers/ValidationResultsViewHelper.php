<?php

namespace Pixelant\PxaSurvey\ViewHelpers;

use TYPO3\CMS\Extbase\Error\Result;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * Class ValidationResultsViewHelper
 * @package Pixelant\PxaSurvey\ViewHelpers
 */
class ValidationResultsViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * Initialize arguments
     *
     * @api
     */
    public function initializeArguments()
    {
        // @codingStandardsIgnoreStart
        $this->registerArgument('for', 'string', 'The name of the error name (e.g. argument name or property name). This can also be a property path (like blog.title), and will then only display the validation errors of that property.', true);
        // @codingStandardsIgnoreEnd
    }

    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $for = $arguments['for'];

        /** @noinspection PhpUndefinedMethodInspection */
        /** @var Result $validationResults */
        $validationResults = $renderingContext
            ->getControllerContext()
            ->getRequest()
            ->getOriginalRequestMappingResults();

        if ($validationResults !== null && $for !== '') {
            $validationResults = $validationResults->forProperty($for);
        }

        return $validationResults;
    }
}
