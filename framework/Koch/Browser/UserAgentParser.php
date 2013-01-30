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

namespace Koch\Browser;

/**
 * Koch Framework - The Class provides a User Agent Parser.
 */
class UserAgentParser
{
    const TYPE_UNKNOWN = 'unknown';

    /**
     * Parse a user agent string.
     *
     * @param  string $userAgentString Defaults to $_SERVER['USER_AGENT'], if empty.
     * @return array
     */
    public function parse($userAgentString = null)
    {
        if ($userAgentString === null) {
            $userAgentString = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
        }

        return $this->doParse($userAgentString);
    }

    /**
     * Detect quickly informations from the user agent string.
     *
     * @param  string $userAgentString => user agent string.
     * @return array $information     => user agent informations directly in array.
     */
    public function doParse($userAgentString)
    {
        $userAgent = array(
            'string' => $this->cleanUserAgentString($userAgentString),
            'browser_name' => null,
            'browser_type' => null,
            'browser_type_sub' => null,
            'browser_version' => null,
            'browser_version_major' => null,
            'browser_version_minor' => null,
            'browser_version_release' => null,
            'browser_version_build' => null,
            'operating_system' => null,
            'operating_system_name' => null,
            'operating_system_type' => null,
            'operating_system_type_sub' => null,
            'engine' => null,
            'engine_version' => null
        );

        $userAgent['user_agent'] = $userAgent['string'];

        if (empty($userAgent['string'])) {
            return $userAgent;
        }

        // Parse Browser
        $found = false;
        $tmp_array = array();

        foreach ($this->getListBrowsers() as $name => $elements) {
            // read browser
            $exprReg = $elements['search'];
            foreach ($exprReg as $expr) {
                if (preg_match($expr, $userAgent['string'], $tmp_array)) {
                    $userAgent['browser_name'] = $name;

                    $userAgent['browser_type'] = $elements['type'];
                    if (isset($elements['subtype']) === true) {
                        $userAgent['browser_type_sub'] = $elements['subtype'];
                    }
                    $found = true;

                    // read version
                    if ($elements['vparam'] !== null) {
                        $pattern = '';
                        $pv = $elements['vparam'];
                        $pattern = '|.+\s' . $pv . '([0-9a-z\+\-\.]+).*|';
                        $userAgent['browser_version'] = preg_replace($pattern, '$1', $userAgent['string']);
                        $tVer = preg_split("/\./", $userAgent['browser_version']);
                        $userAgent['browser_version_major'] = $tVer[0];
                        $userAgent['browser_version_minor'] = $tVer[1];
                        $userAgent['browser_version_release'] = isset($tVer[2]) ? $tVer[2] : null;
                        $userAgent['browser_version_build'] = isset($tVer[3]) ? $tVer[3] : null;
                    } else {
                        $userAgent['browser_version'] = self::TYPE_UNKNOWN;
                    }

                    // read engine
                    if ($elements['engine'] !== null) {
                        $userAgent['engine'] = $elements['engine'];
                    } else {
                        $userAgent['engine'] = self::TYPE_UNKNOWN;
                    }

                    // read engine version
                    $pattern = '';
                    if ($elements['eparam'] !== null) {
                        $pe = $elements['eparam'];
                        $pattern = '|.+\s' . $pe . '([0-9\.]+)(.*).*|';
                        $userAgent['engine_version'] = preg_replace($pattern, '$1', $userAgent['string']);
                    } else {
                        $userAgent['engine_version'] = self::TYPE_UNKNOWN;
                    }
                }
            }
        }

        if (false === $found) {
            $userAgent['browser_type'] = self::TYPE_UNKNOWN;
        }

        // Parse Operating System
        $found = false;
        $tmp_array = array();
        foreach ($this->getListOperatingSystems() as $name => $elements) {
            $exprReg = $elements['search'];

            foreach ($exprReg as $expr) {
                if (preg_match($expr, $userAgent['string'], $tmp_array)) {
                    $userAgent['operating_system'] = $name;
                    if ($tmp_array !== null && isset($tmp_array[1])) {
                        if ($elements['subsearch'] !== null) {
                            foreach ($elements['subsearch'] as $sub => $expr) {
                                if (preg_match($expr, $tmp_array[1])) {
                                    $userAgent['operating_system_name'] = $sub;
                                }
                            }
                        }
                        if ($userAgent['operating_system_name'] === null) {
                            $userAgent['operating_system_name'] = (string) $tmp_array[1];
                        }
                    } elseif (isset($elements['addsearch']) === true) {
                        foreach ($elements['addsearch'] as $sub => $expr) {
                            if (preg_match($expr, $userAgent['string'])) {
                                $userAgent['operating_system_name'] = $sub;
                            }
                        }
                    }
                    if ($elements['type'] !== null) {
                        $userAgent['operating_system_type'] = $elements['type'];
                    } else {
                        $userAgent['operating_system_type'] = self::TYPE_UNKNOWN;
                    }

                    if (isset($elements['subtype']) === true) {
                        $userAgent['operating_system_type_sub'] = $elements['subtype'];
                    }

                    $found = true;
                }
            }
        }

        if (false === $found) {
            $userAgent['operating_system_type'] = self::TYPE_UNKNOWN;
        }

        return $userAgent;
    }

    /**
     * Make user agent string lowercase, and replace browser aliases.
     *
     * @param string $userAgentString The dirty user agent string.
     */
    public function cleanUserAgentString($userAgentString)
    {
        //clean up the string
        $userAgentString = trim(strtolower($userAgentString));

        //replace browser names with their aliases
        #$userAgentString = strtr($userAgentString, $this->getListBrowserAliases());

        //replace engine names with their aliases
        #$userAgentString = strtr($userAgentString, $this->getListEngineAliases());

        return $userAgentString;
    }

    /**
     * Get browsers list
     *
     * @return Array of browsers
     */
    protected function getListBrowsers()
    {
        $aList = array();

        include __DIR__ . '/UserAgents/Bot.php';

        foreach ($bot as $name => $row) {
            $aList[$name] = $row;
        }

        include __DIR__ . '/UserAgents/Browser.php';

        foreach ($browser as $name => $row) {
            $aList[$name] = $row;
        }

        /*
        include __DIR__ . '/UserAgents/Mobile.php';
        foreach ($mobile as $name => $row) {
            $aList[$name] = $row;
        }

        include __DIR__ . '/UserAgents/Console.php';
        foreach ($console as $name => $row) {
            $aList[$name] = $row;
        }
        */

        return $aList;
    }

    /**
     * Get operating system list
     *
     * @return array The operating system.
     */
    protected function getListOperatingSystems()
    {
        $aList = array();

        include __DIR__ . '/UserAgents/Os.php';

        foreach ($os as $name => $row) {
            $aList[$name] = $row;
        }

        return $aList;
    }
}
