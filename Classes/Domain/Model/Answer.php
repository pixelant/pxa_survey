<?php
namespace Pixelant\PxaSurvey\Domain\Model;

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
 * Answer
 */
class Answer extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * text
     *
     * @var string
     */
    protected $text = '';

    /**
     * Returns the text
     *
     * @return string $text
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Sets the text
     *
     * @param string $text
     * @return void
     */
    public function setText($text)
    {
        $this->text = $text;
    }
}
