<?php

namespace Pixelant\PxaSurvey\Domain\Repository;

/***
 *
 * This file is part of the "Simple Survey" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2017 Andriy Oprysko
 *
 ***/

use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * The repository for Surveys
 */
class UserAnswerRepository extends Repository
{
    /**
     * Initialize object
     */
    public function initializeObject()
    {
        /** @var $defaultQuerySettings Typo3QuerySettings */
        $defaultQuerySettings = $this->objectManager->get(Typo3QuerySettings::class);
        // don't add the pid constraint
        $defaultQuerySettings->setRespectStoragePage(false);

        $this->setDefaultQuerySettings($defaultQuerySettings);
    }
}
