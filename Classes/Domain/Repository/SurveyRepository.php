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

use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * The repository for Surveys
 */
class SurveyRepository extends Repository
{
    /**
     * Find by storage
     *
     * @param int $pid
     * @return QueryResultInterface
     */
    public function findByPid(int $pid): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setStoragePageIds([$pid]);

        return $query->execute();
    }
}
