<?php

/**
 * Koch Framework
 *
 * SPDX-FileCopyrightText: 2005-2024 Jens A. Koch
 * SPDX-License-Identifier: MIT
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

 namespace Koch\Autoload;

 use Koch\Functions\Functions;

 /**
  * Koch Framework - Class for building class maps for the autoloader.
  *
  * A class map is a relationship of classname to filename.
  * The class map is used by the autoloader as a lookup table
  * to determine the file to load.
  */
 class MapBuilder
 {
     /**
      * Builds a class map file.
      *
      * @param string[] $dirs    One or multiple directories to scan for PHP files.
      * @param string   $mapfile Path to the classmap file to be written.
      *
      * @return bool True, if map file written, false otherwise.
      */
     public static function build($dirs, $mapfile)
     {
         $classmap = [];
         $files    = [];

         $dirs = (array) $dirs;
         foreach ($dirs as $dir) {
             $files += Functions::globRecursive($dir . '/*.php');
         }

         foreach ($files as $file) {
             $classnames = self::extractClassnames($file);
             foreach ($classnames as $classname) {
                 $classmap[$classname] = $file;
             }
         }

         return self::writeMapFile($classmap, $mapfile);
     }

     /**
      * Extract classname and namespace of the given file.
      *
      * @param string $file The file to extract the classname from.
      *
      * @return array Found classnames.
      */
     public static function extractClassnames($file)
     {
         if (is_file($file) === false) {
             throw new \InvalidArgumentException('File ' . $file . ' does not exist.');
         }

         // tokenize the content of the file
         $contents = file_get_contents($file);
         $tokens   = token_get_all($contents);

         $classes            = [];
         $namespace          = '';
         $totaNumberOfTokens = count($tokens);

         for ($i = 0, $max = $totaNumberOfTokens; $i < $max; ++$i) {
             $token = $tokens[$i];

             if (is_string($token)) {
                 continue;
             }

             $classname = '';

             if ($token[0] === T_NAMESPACE) {
                 $namespace = '';

                 // extract the namespace
                 while (($tok = $tokens[++$i]) && is_array($tok)) {
                     if (in_array($tok[0], [T_STRING, T_NS_SEPARATOR], true)) {
                         $namespace .= $tok[1];
                     }
                 }

                 $namespace .= '\\';
             }

             if (($token[0] === T_CLASS) || ($token[0] === T_INTERFACE)) {
                 // extract the classname
                 while (($tok = $tokens[++$i]) && is_array($tok)) {
                     if (T_STRING === $tok[0]) {
                         $classname .= $tok[1];
                     } elseif ($classname !== '' && T_WHITESPACE === $tok[0]) {
                         break;
                     }
                 }

                 $classes[] = ltrim($namespace . $classname, '\\');
             }
         }

         return $classes;
     }

     /**
      * Writes the classmap array to file.
      *
      * @param array  $classmap Array containing the classname to file relation.
      * @param string $mapfile  Path to the classmap file to be written.
      */
     public static function writeMapFile($classmap, $mapfile)
     {
         $mapArray = var_export($classmap, true);

         $content = sprintf("<?php\n// Autoloader Classmap generated by Koch Framework.\nreturn %s;\n", $mapArray);

         return (bool) file_put_contents($mapfile, $content);
     }
 }