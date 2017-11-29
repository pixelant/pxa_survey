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

use Pixelant\PxaSurvey\Domain\Model\Survey;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
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

    /**
     * Count user answers
     *
     * @param Survey $survey
     * @param FrontendUser $frontendUser
     * @return int
     */
    public function countGivenUserAnswer(Survey $survey, FrontendUser $frontendUser): int
    {
        return count($this->getUserAnswersUids($survey, $frontendUser));
    }

    /**
     * Get user answers for Frontend user
     *
     * @param Survey $survey
     * @param FrontendUser $frontendUser
     * @return array
     */
    public function getUserAnswersUids(Survey $survey, FrontendUser $frontendUser): array
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(
            'tx_pxasurvey_domain_model_useranswer'
        );

        return $queryBuilder
            ->select('tx_pxasurvey_domain_model_useranswer.uid')
            ->from('tx_pxasurvey_domain_model_useranswer')
            ->join(
                'tx_pxasurvey_domain_model_useranswer',
                'tx_pxasurvey_domain_model_question',
                'question',
                $queryBuilder->expr()->eq(
                    'tx_pxasurvey_domain_model_useranswer.question',
                    $queryBuilder->quoteIdentifier('question.uid')
                )
            )
            ->join(
                'question',
                'tx_pxasurvey_domain_model_survey',
                'survey',
                $queryBuilder->expr()->eq(
                    'question.survey',
                    $queryBuilder->quoteIdentifier('survey.uid')
                )
            )
            ->where(
                $queryBuilder->expr()->eq(
                    'tx_pxasurvey_domain_model_useranswer.frontend_user',
                    $queryBuilder->createNamedParameter($frontendUser->getUid(), \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->eq(
                    'survey.uid',
                    $queryBuilder->createNamedParameter($survey->getUid(), \PDO::PARAM_INT)
                )
            )
            ->groupBy('tx_pxasurvey_domain_model_useranswer.uid')
            ->execute()
            ->fetchAll();
    }
}
