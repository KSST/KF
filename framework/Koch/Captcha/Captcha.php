<?php

/**
 * Koch Framework
 * Jens-André Koch © 2005 - onwards
 *
 * This file is part of "Koch Framework".
 *
 * License: GNU/GPL v2 or any later version, see LICENSE file.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace Koch\Captcha;

/**
 * Koch Framework - just a simple Captcha Class.
 *
 * A Captcha is a acronym for "Completely Automated Public Turing-Test to
 * Tell Computers and Humans Apart". It is type of a challenge-response test
 * to determine whether or not the user is human.
 *
 * The Purpose of Captcha's is to prevent bots from using various types of
 * computing services, like posting to guestbooks, boards or register email
 * accounts. The bot-generated spam could be reduced by requiring that the
 * (unrecognized) sender passes that CAPTCHA test before the service is delivered.
 *
 * Remember: Captchas are deafeatable!
 * It's a matter of artificial intelligence and pattern recognition.
 * @link http://www.cs.sfu.ca/~mori/research/gimpy/ Greg Mori's - Breaking Captchas
 * @link http://www.pwntcha.net/test.html PwnTcha : Test the Captcha - Strength
 * @link http://captcha.megaleecher.net/ Megaleecher Captcha Kill Pro Class
 */
class Captcha
{
    /**
     * @var int image height
     */
    public $image_height = 40;

    /**
     * @var int image width
     */
    public $image_width = 140;

    /**
     * @var array $fonts Available Fonts.
     */
    private $fonts = array();

    /*
     * @var string The selected font for the captcha.
     */
    public $font;

    /**
     * @var array List of font folders.
     */
    public $font_folders = array();

    /**
     * @var resource The captcha image.
     */
    public $captcha;

    /**
     * Constructor.
     *
     */
    public function __construct($options = array())
    {
        if (extension_loaded('gd') === false) {
            throw new \Koch\Exception\Exception(_('GD Library missing.'));
        }

        $this->setOptions($options);
    }

    /**
     * Options
     *
     * "captcha_dir"
     * "font"
     * "fonts"
     *
     * @param type $options
     */
    public function setOptions($options)
    {
        $this->options = $options;

        if (isset($options['font']) === true) {
            $this->font = $options['font'];
        } else {
            if (isset($options['fontfolders']) === true) {
                $this->setFontFolder($options['fontfolders']);
            }

            // set frameworks font folder as default
            $this->font_folders[] = realpath(__DIR__ . '/fonts');

            // pick a random font from the fonts dir
            $this->font = $this->getRandomFont($this->font_folders);
        }
    }

    /**
     * Set one or more font folders.
     *
     * @param string|array $folders
     */
    public function setFontFolder($folders)
    {
        // folder might be string
        $folders = (array) $folders;

        foreach ($folders as $folder) {
            $this->font_folders[] = $folder;
        }
    }

    public function getFontFolders()
    {
        return $this->font_folders;
    }

    /**
     * Set the captcha font to use.
     *
     * @param string $font Full path to a font.ttf.
     */
    public function setFont($font)
    {
        $this->font = $font;
    }

    /**
     * Returns a random font from the font files directory.
     *
     * @param string $fonts_dir The directory where the font files reside.
     */
    public function getRandomFont($fonts_dir = null)
    {
        // use default font dir, if nothing was set
        if ($fonts_dir === null) {
            $fonts_dir[] = realpath(__DIR__ . '/fonts');
        }

        // random select one of multiple font folders
        if (is_array($fonts_dir) === true) {
            $fonts_dir = $fonts_dir[array_rand($fonts_dir)];
        }

        // build the fonts array by detecting all font files
        $iterator = new \DirectoryIterator($fonts_dir);

        foreach ($iterator as $file) {
            // add font files (*.ttf) to the array
            if ($file->isFile() and (strrchr($file->getPathname(), '.') == '.ttf')) {
                $this->fonts[] = $file->getPathname();
            }
        }

        // return a random font file
        return $this->fonts[array_rand($this->fonts)];
    }

    /**
     * Generates a random string in requested $string_length for usage with captcha
     *
     * @param $length The length of the captcha string.
     */
    public function generateRandomString($length)
    {
        // Excluded-Chars: 0, 1, 7, I, O
        // why? because they are too simple to recognize even when effects applied upon.)
        $excludeChars = array(48, 49, 55, 73, 79);

        $string = '';

        while (mb_strlen($string) < $length) {
            // a random char between 48 and 122
            $random = mt_rand(48, 122);

            // not the excluded chars and only special chars segments
            if (in_array($random, $excludeChars) == false and
                    ( ($random >= 50 && $random <= 57)   // ASCII 48->57:  numbers   0-9
                    | ($random >= 65 && $random <= 90))  // ASCII 65->90:  uppercase A-Z
                    | ($random >= 97 && $random <= 122)  // ASCII 97->122: lowercase a-z
            ) {
                // adds a random char to the string
                $string .= chr($random);
            }
        }

        return $string;
    }

    /**
     * Generates the captcha image
     */
    public function generateCaptchaImage()
    {
        // a random captcha string
        $string_length = rand(3, 6);
        $captcha_string = $this->generateRandomString($string_length);

        // set string class (user needs set this to session)
        $this->captchaString = $captcha_string;

        $this->captcha = imagecreatetruecolor($this->image_width, $this->image_height);

        /**
         * Switch between Captcha Types
         */
        // rand(1,2)
        switch (1) {
            // captcha with some effects
            case 1:
                /**
                 *  Create Background-Color from random RGB colors
                 */
                $background_color = imagecolorallocate($this->captcha, rand(100, 255), rand(100, 255), rand(0, 255));

                /**
                 * Background Fill Effects
                 */
                switch (rand(1, 2)) {
                    // Solid Fill
                    case 1:
                        imagefill($this->captcha, 0, 0, $background_color);
                        break;
                    // Gradient Fill
                    case 2:
                        $rd = mt_rand(0, 100);
                        $gr = mt_rand(0, 100);
                        $bl = mt_rand(0, 100);
                        for ($i = 0; $i <= $this->image_height; $i++) {
                            $g = imagecolorallocate($this->captcha, $rd+=2, $gr+=2, $bl+=2);
                            imageline($this->captcha, 0, $i, $this->image_width, $i, $g);
                        }
                        break;
                }

                /**
                 * Create Text-Color from random RGB colors
                 */
                $textcolor = imagecolorallocate(
                    $this->captcha,
                    mt_rand(50, 240),
                    mt_rand(50, 240),
                    mt_rand(0, 255)
                );

                // add some noise
                for ($i = 1; $i <= 4; $i++) {
                    imageellipse(
                        $this->captcha,
                        mt_rand(1, 200),
                        mt_rand(1, 50),
                        mt_rand(50, 100),
                        mt_rand(12, 25),
                        $textcolor
                    );
                }

                for ($i = 1; $i <= 4; $i++) {
                    imageellipse(
                        $this->captcha,
                        mt_rand(1, 200),
                        mt_rand(1, 50),
                        mt_rand(50, 100),
                        mt_rand(12, 25),
                        $background_color
                    );
                }

                /**
                 * Process the Captcha String charwise, so that each character has a random font-effect.
                 */
                for ($i = 0; $i < $string_length; $i++) {
                    /**
                     * Font Rotation Effect
                     */
                    switch (mt_rand(1, 2)) {
                        case 1: // Clock-Rotation (->)
                            $angle = mt_rand(0, 15);
                            break;
                        case 2: // Counter-Rotation (<-)
                            $angle = mt_rand(345, 360);
                            break;
                    }

                    $defaultSize = min($this->image_width, $this->image_height * 2) / strlen($captcha_string);
                    $spacing = (int) ($this->image_width * 0.9 / strlen($captcha_string));

                    /**
                     * Font Size
                     */
                    $size = $defaultSize / 10 * mt_rand(12, 15);

                    /**
                     * Determine cordinates X and Y
                     *
                     * This is done using the bounding box of a text via imageftbbox.
                     */
                    $bbox = imageftbbox($size, $angle, $this->font, $captcha_string[$i]);
                    $x = $spacing / 4 + $i * $spacing + 2;
                    /*
                     * @todo $height is undefined
                     */
                    $y = /*$height / */ 2 + ($bbox[2] - $bbox[5]) / 4;
                    #$x = $bbox[0] + (imagesx($this->captcha) / 2) - ($bbox[4] / 2) - 5;
                    #$y = $bbox[1] + (imagesy($this->captcha) / 2) - ($bbox[5] / 2) - 5;
                    unset($bbox);

                    /**
                     * Font Color
                     */
                    $color = imagecolorallocate($this->captcha, mt_rand(0, 160), mt_rand(0, 160), mt_rand(0, 160));

                    /**
                     * Finally: Add the CHAR from the captcha string to the image
                     */
                    imagettftext($this->captcha, $size, $angle, $x, $y, $color, $this->font, $captcha_string[$i]);
                }

                // add interlacing
                // $this->interlace($captcha);

                // add image rotation
                /**
                  if (function_exists('imagerotate')) {
                  #$im2 = imagerotate($captcha,rand(-20,30),$background_color);
                  // imagedestroy($captcha);
                  // $captcha = $im2;
                  }
                 */
                break;

            /* case '2': // a very simple captcha

              // apply a white background
              $white = ImageColorAllocate($captcha, 255, 255, 255);
              imagefill($captcha, 1, 1, $white );

              // loop through $captcha_str and apply a random font-effect to every char
              for ($i=0; $i >= $string_length; $i++) {
              imagettftext($captcha, rand(28,35),
              rand(-5,5),
              25+($i*17),
              38,$text_color,
              $this->font, $captcha_string{$i});
              }
              break; */
        }

        return $this->render('base64');
    }

    /**
     * Render the Captcha Image on various ways
     *
     * @param  string $render_type Types: "file", "base64", "png". Defaults to html_embedded.
     * @return mixed  Renders the image directly or returns html string.
     */
    public function render($render_type = 'file')
    {
        switch ($render_type) {
            case 'png':
                // PNG direct via header
                header("Content-type: image/png");
                imagepng($this->captcha);
                imagedestroy($this->captcha);
                break;
            case 'base64':
                // get image via output buffer rendering
                ob_start();
                imagepng($this->captcha);
                $imagesource = ob_get_clean();
                imagedestroy($this->captcha);
                // output the image as inlined data
                return sprintf(
                    '<img alt="Embedded Captcha Image" src="data:image/png;base64,%s" />',
                    base64_encode($imagesource)
                );
                break;
            case 'file':
                // remove outdated captcha images
                $this->collectGarbage();
                $file = APPLICATION_PATH . $this->options['captcha_dir'] . '/' . $this->_id . '.png';
                // write png to file
                imagepng($this->captcha, $file);
                // return html img tag which points to the image file
                return sprintf(
                    '<img alt="Captcha Image" src="%s" />',
                    $file
                );
                break;
        }
    }

    /**
     * Garbage Collection
     * is performed in 10% of all calls to this method and
     * removes old captcha images from the captcha images directory.
     */
    public function collectGarbage()
    {
        // randomize (perform the garbage_collection in 10 % of all calls)
        if (mt_rand(0, 9) == 0) {
            // get file iterator
            $iterator = new \DirectoryIterator($this->options['captcha_dir']);
            foreach ($iterator as $file) {
                // and delete all png files
                if ($file->isFile() and (strrchr($file->getPathname(), '.') == '.png')) {
                    unlink($file->getPathname());
                }
            }
        }

        return true;
    }

    /**
     * Interlaces the Image
     * Interlacing means, that every 2th line is blacked or greyed out.
     *
     * @param $image The image to interlace.
     */
    private function interlace($image)
    {
        $imagex = imagesx($image);
        $imagey = imagesy($image);
        $black = imagecolorallocate($image, 255, 255, 255);
        for ($y = 0; $y < $imagey; $y += 2) {
            imageline($image, 0, $y, $imagex, $y, $black);
        }
    }
}
