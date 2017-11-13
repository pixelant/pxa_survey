<?php
namespace Pixelant\PxaSurvey\Controller;

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

/**
 * SurveyController
 */
class SurveyController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * surveyRepository
     *
     * @var \Pixelant\PxaSurvey\Domain\Repository\SurveyRepository
     * @inject
     */
    protected $surveyRepository = null;

    /**
     * action show
     *
     * @param \Pixelant\PxaSurvey\Domain\Model\Survey $survey
     * @return void
     */
    public function showAction(\Pixelant\PxaSurvey\Domain\Model\Survey $survey)
    {
        $this->view->assign('survey', $survey);
    }
}
