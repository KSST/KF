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

namespace Koch\Autoload;

/**
 * Class for building a framework monolith.
 *
 * The class compiler assembles all framework files into one monolithic file.
 * 1) Includes
 * This is a performance strategy called "Include Tuning".
 * Files are merged, for a lower number of includes.
 * Use php/ext inclued() to check that.
 *
 * @todo detect dependencies, either with get_required_files() or extension inclued()
 *
 * 2) APC Compile Files
 */
class Compiler
{
    protected $monolith_filename = 'clansuite_monolith.php';

    /**
     * Wraps php tags around the content of the monolith
     */
    public static function empowerMonolith()
    {
        $content = '<?php ' . file_get_contents(self::$monolith_filename) . '?>';
        file_put_contents(self::$monolith_filename, $content);
    }

    /**
     * Combines all files to ONE file
     *
     * @return boolean True, on successful build.
     */
    public static function build()
    {
        // remove existing monolith
        if (is_file(self::$monolith_file) === true) {
            unlink(self::$monolith_file);
        }

        // this directory
        $directory = '.';

        $iterator = new DirectoryIterator($directory);

        foreach ($iterator as $phpfile) {
            // no dots, no dirs, not this file and not the target file
            if ($phpfile->isDot() === false and
               $phpfile->isDir() === false and
               $phpfile->getFilename() != basename($_SERVER['PHP_SELF']) and
               $phpfile->getFilename() != self::$monolith_file) {
                //echo 'Processing: ' . $phpfile . '<br>';

                // get file content
                $content = file_get_contents(self::$monolith_file);

                // apply string modification (strips unnessecary things off)
                $new_content = self::removeCommentsFromString($content);
                //$new_content = self::strip_php_tags($new_content);
                //$new_content = self::strip_empty_lines($new_content);

                // write the modified content to the monolith file
                file_put_contents(self::$monolith_file, $new_content, FILE_APPEND);
            }
        }

        //echo 'Monolith successfully build!';
        return true;
    }

    /**
     * Removes any comments from string
     *
     * @param $sourcecode The sourcecode string to clean up.
     * @return string The sourcecode string without comments.
     */
    public static function removeCommentsFromString($sourcecode)
    {
        // check if sourcecode is set
        if ($sourcecode === null) {
            return null;
        }

        // ensure token_get_all is available
        if (false === function_exists('token_get_all')) {
            return $sourcecode;
        }

        // tokenize the sourcecode
        $tokens = token_get_all($sourcecode);

        // init return var
        $stripped_sourcecode = '';

        // loop over all tokens
        foreach ($tokens as $token) {
            // if token is a string append to sourcecode
            if (is_string($token) === true) {
                $stripped_sourcecode .= $token;
            } else {
                $token_element = '';
                $text = '';

                // identify elements
                list($token_element, $text) = $token;

                // filter out comments
                if ($token_element != T_COMMENT and $token_element != T_DOC_COMMENT) {
                    // append only, if not comment or doc_comment
                    $stripped_sourcecode .= $text;
                }
            }
        }

        return $stripped_sourcecode;
    }

    /**
     * Strips new lines by replacing them with a single newline.
     *
     * @param $string sourcecode-string to clean up
     * @return string
     */
    public static function stripEmptyLines($string)
    {
        $string = preg_replace('/[\r\n]+[\s\t]*[\r\n]+/', "\n", $string);
        $string = preg_replace('/^[\s\t]*[\r\n]+/', '', $string);

        return $string;
    }

    /**
     * Strips all openening and closing PHP Tags.
     *
     * @param $string sourcecode-string to clean up
     * @return string
     */
    public static function stripPhpTags($string)
    {
        // remove php opening and closing tag from beginning and end
        $string = substr($string, strlen('<?php' . PHP_EOL));
        $string = substr($string, 0, -strlen('?>' . PHP_EOL));

        // remove php opening tag from whole string
        $string = str_replace('<?php', '', $string);

        return $string;
    }
}
