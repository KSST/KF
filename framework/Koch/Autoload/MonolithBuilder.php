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
 */

namespace Koch\Autoload;

/**
 * Koch Framework - Class for building a framework monolith.
 *
 * The class compiler assembles all framework files into one monolithic file.
 * In other words, all PHP scripts are joined/merged into a single scriptfile.
 * 1) Includes
 * This is a performance strategy called "Include Tuning".
 * Files are merged, for a lower number of includes.
 * Use php/ext inclued() to check that.
 *
 * @todo detect dependencies, either with get_required_files() or extension inclued()
 *
 * 2) APC Compile Files
 */
class MonolithBuilder
{
    protected static $monolithFile = '';

    public static function getMonolithFile()
    {
        self::$monolithFile = __DIR__ . '/../monolith.php';
    }

    /**
     * Wraps php tags around the content of the monolith and writes the monolith file.
     */
    public static function empowerMonolith()
    {
        $file = self::getMonolithFile();
        $content = '<?php ' . file_get_contents($file) . '?>';

        file_put_contents($file, $content);
    }

    /**
     * Combines all files to ONE file
     *
     * @return boolean True, on successful build.
     */
    public static function build()
    {
        $file = self::getMonolithFile();

        // remove existing monolith
        if (is_file($file) === true) {
            unlink($file);
        }

        // this directory
        $directory = '.';

        $iterator = new DirectoryIterator($directory);

        foreach ($iterator as $phpfile) {
            // no dots, no dirs, not this file and not the target file
            if ($phpfile->isDot() === false and
               $phpfile->isDir() === false and
               $phpfile->getFilename() != basename($_SERVER['PHP_SELF']) and
               $phpfile->getFilename() != $file) {
                //echo 'Processing: ' . $phpfile . '<br>';

                // get file content
                $content = file_get_contents($file);

                // apply string modification (strips unnessecary things off)
                $new_content = self::removeCommentsFromString($content);
                //$new_content = self::strip_php_tags($new_content);
                //$new_content = self::strip_empty_lines($new_content);

                // write the modified content to the monolith file
                file_put_contents($file, $new_content, FILE_APPEND);
            }
        }

        //echo 'Monolith successfully build!';
        return true;
    }

    /**
     * Removes any comments from string
     *
     * @param string $sourcecode The sourcecode string to clean up.
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
        $strippedSourcecode = '';

        // loop over all tokens
        foreach ($tokens as $token) {
            // if token is a string append to sourcecode
            if (is_string($token) === true) {
                $strippedSourcecode .= $token;
            } else {
                $tokenElement = '';
                $text = '';

                // identify elements
                list($tokenElement, $text) = $token;

                // filter out comments
                if ($tokenElement != T_COMMENT and $tokenElement != T_DOC_COMMENT) {
                    // append only, if not comment or doc_comment
                    $strippedSourcecode .= $text;
                }
            }
        }

        return $strippedSourcecode;
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
