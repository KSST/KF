<?php

/**
 * Koch Framework
 * Jens A. Koch © 2005 - onwards
 *
 * SPDX-License-Identifier: MIT
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Koch\View\Helper;

/**
 * Class for Gravatar Service.
 *
 * This is a service class for accessing the Globally Recognized Avatars as provided
 * by http://www.gravatar.com.
 *
 * I give credits and thanks to the following classes, discussions and hints:
 *
 * 1) Gravatar Implementation Infos:
 *    http://en.gravatar.com/site/implement/php &  http://en.gravatar.com/site/implement/url
 *
 * 2) TalkPHP :
 *    http://www.talkphp.com/script-giveaway/1905-gravatar-wrapper-class.html
 *
 * @package     Koch
 * @subpackage  Libraries
 */
class Gravatar
{
    // Gravatar BASE URL
    private $gravatar_baseurl = 'http://www.gravatar.com/avatar/%s?&size=%s&rating=%s&default=%s';

    // Gravatar Ratings
    private $gravatar_ratings = array("g", "pg", "r", "x");

    // Gravatar Properties
    protected $gravatar_properties = array(
        "gravatar_id"	=> null,      // = md5(email)
        "default"		=> null,      // default avatar
        "size"			=> 80,        // default value = 80
        "rating"		=> null,      // rating
    );

    // Email
    public $email = '';

    // Turn on/off the use of cached Gravatars
    public $useCaching = true;

    /**
     *  Constructor
     */
    public function __construct($email = null, $rating = null, $size = null, $default = null, $nocaching = false)
    {
        $this->setEmail($email);
        $this->setRating($rating);
        $this->setSize($size);
        $this->setDefaultAvatar($default);

        if (true == $nocaching) {
            $this->disableCaching();
        }
    }

    /**
     *  setEmail
     *  1. convert email to lowercase
     *  2. set email to class
     *  3. set md5 of email as gravatar_id
     *
     * @param $email
     * @return $this
     */
    public function setEmail($email)
    {
        /* if (false === filter_var($email, FILTER_VALIDATE_EMAIL)) {
          throw new \InvalidArgumentException('Invalid value of $email: '.$email);
          } */

        $this->email = (string) strtolower($email);

        $this->gravatar_properties['gravatar_id'] = md5($this->email);

        return $this;
    }

    /**
     * setRating
     * $rating has to be one of the predefined elements from GRAVATAR_RATINGS array
     * otherwise it sets the rating to default [g]
     *
     * @param $rating
     * @return $this
     */
    public function setRating($rating = 'g')
    {
        $rating = strtolower($rating);

        if (in_array($rating, $this->gravatar_ratings) === true) {
            $this->gravatar_properties['rating'] = $rating;
        } else {
            $this->gravatar_properties['rating'] = 'g';
        }

        return $this;
    }

    /**
     *  setDefaultAvatar
     *
     *  sets a default avatar image
     */
    public function setDefaultAvatar($image_url)
    {
        $this->gravatar_properties['default'] = (string) urlencode($image_url);

        return $this;
    }

    /**
     *  setSize
     *
     *  Th maximum size for a Gravatar is 512px.
     *  This will make sure you set it between 16px and 512px.
     *  If not, Gravatar will return a size of 80px as default value.
     *
     * @param $size
     * @return $this
     */
    public function setSize($size)
    {
        $size = (int) $size;

        /*
          if (is_numeric($size) === false) {
          throw new \UnexpectedValueException('Value of $size must be numeric, is: '. $size);
          }

          if ($size < 16 || $size > 512) {
          throw new \OutOfRangeException('Value of $size should be between 16 and 512 (size of image in pixels), given: '.$size);
          } */

        $this->size = $size;

        return $this;
    }

    /**
     *  If you don't want to use cached gravatars, disable it.
     */
    public function disableCache()
    {
        $this->useCaching = false;

        return $this;
    }

    /**
     *  Construct a valid Gravatar URL
     */
    public function getGravatarURL()
    {
        $gravatar_url = (string) sprintf(
            $this->gravatar_baseurl,
            $this->gravatar_properties['gravatar_id'],
            $this->gravatar_properties['size'],
            $this->gravatar_properties['rating'],
            $this->gravatar_properties['default']
        );

        return $gravatar_url;
    }

    /**
     *  Constructs and output's the complete gravatar <img /> html-tag
     */
    public function getHTML()
    {
        // init html string variable
        $html  = '';

        // check for caching and construct html from cached gravatar url
        if (true == $this->useCaching) {
            // initialize cache class
            $cache = new GravatarCache(
                $this->getGravatarURL(),
                $this->gravatar_properties['gravatar_id'],
                $this->gravatar_properties['size'],
                $this->gravatar_properties['rating']
            );

            // getGravatar URL from cache
            $html .= '<img src="'. $cache->getGravatar() .'"';
        } else {
            // construct html for non-cached gravatar
            $html .= '<img src="'. $this->getGravatarURL() .'"';

            // add additional width and height
            if ($this->gravatar_properties['size'] !== null) {
                $html .= ' width="'.$this->gravatar_properties['size'].'"';
                $html .= ' height="'.$this->gravatar_properties['size'].'"';
            }
        }

        // add alt and title tags on both (cached, non-cached) html
        $html .= ' alt="Gravatar for '.$this->email.'" title="Gravatar for '.$this->email.'" />';

        return $html;
    }

    /**
     *  toString
     */
    public function __toString()
    {
        return $this->getHTML();
    }
}
