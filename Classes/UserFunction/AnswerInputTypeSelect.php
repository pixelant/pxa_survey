<?php
declare(strict_types=1);

namespace Pixelant\PxaPmImporter\UserFunction;

use Pixelant\PxaPmImporter\Utility\ImportersRegistry;
use TYPO3\CMS\Backend\Form\FormDataProviderInterface;

/**
 * Class ConfigurationPathSelect
 * @package Pixelant\PxaPmImporter\UserFunction
 */
class ConfigurationPathSelect
{
    /**
     * Add importers configurations
     *
     * @param array $params
     * @param FormDataProviderInterface $formDataProvider
     */
    public function renderItems(array $params, FormDataProviderInterface $formDataProvider): void
    {
        $items = &$params['items'];
        $availableConfigurations = ImportersRegistry::getImportersAvailableConfigurations();
        foreach ($availableConfigurations as $extKey => $availableConfigurationFiles) {
            $items[] = [
                $extKey,
                '--div--'
            ];
            foreach ($availableConfigurationFiles as $file) {
                $items[] = [
                    pathinfo($file, PATHINFO_FILENAME),
                    $file
                ];
            }
        }
    }
}
