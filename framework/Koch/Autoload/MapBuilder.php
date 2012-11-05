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

namespace . $classname, '\\');
            }
        }

        return $classes;
    }

    /**
     * Writes the classmap array to file
     *
     * @param array  $classmap Array containing the classname to file relation.
     * @param string $mapfile  Path to the classmap file to be written.
     */
    public static function writeMapFile($classmap, $mapfile)
    {
        $mapArray = var_export($classmap, true);

        $content = sprintf("<?php\n// Autoloader Classmap generated by Koch Framework.\nreturn %s;\n?>", $mapArray);

        return (bool) file_put_contents($mapfile, $content);
    }
}
