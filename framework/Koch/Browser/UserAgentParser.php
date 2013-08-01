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

namespace Koch\Browser;

/**
 * The class provides a User Agent Parser.
 */
class UserAgentParser
{
    const TYPE_UNKNOWN = 'unknown';

    public function getBrowser()
    {
        static $browser = array();

        if (isset($browser) === false) {
            $browser = get_browser($_SERVER['HTTP_USER_AGENT']);
        }

        return $browser;
    }

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
     * @return array  $information     => user agent informations directly in array.
     */
    public function doParse($userAgentString)
    {
        $userAgent = array(
            'string' => trim(strtolower($userAgentString)),
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
        $matches = array();

        $list = $this->getBrowsersList();

        foreach ($list as $name => $elements) {
            // read browser

            foreach ($elements['regexp'] as $regexp) {
                if (preg_match($regexp, $userAgent['string'], $matches)) {
                    $userAgent['browser_name'] = $name;

                    $userAgent['browser_type'] = $elements['type'];
                    if (isset($elements['subtype']) === true) {
                        $userAgent['browser_type_sub'] = $elements['subtype'];
                    }
                    $found = true;

                    // read version
                    if ($elements['vparam'] !== null) {
                        $pattern = '|.+\s' . $elements['vparam'] . '([0-9a-z\+\-\.]+).*|';
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
                        $pattern = '|.+\s' . $elements['eparam'] . '([0-9\.]+)(.*).*|';
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
        $matches = array();
        $osList = $this->getListOperatingSystems();

        foreach ($osList as $name => $elements) {
            foreach ($elements['regexp'] as $regexp) {
                if (preg_match($regexp, $userAgent['string'], $matches)) {
                    $userAgent['operating_system'] = $name;
                    if ($matches !== null && isset($matches[1])) {
                        if ($elements['subsearch'] !== null) {
                            foreach ($elements['subsearch'] as $sub => $regexp) {
                                if (preg_match($regexp, $matches[1])) {
                                    $userAgent['operating_system_name'] = $sub;
                                }
                            }
                        }
                        if ($userAgent['operating_system_name'] === null) {
                            $userAgent['operating_system_name'] = (string) $matches[1];
                        }
                    } elseif (isset($elements['addsearch']) === true) {
                        foreach ($elements['addsearch'] as $sub => $regexp) {
                            if (preg_match($regexp, $userAgent['string'])) {
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
     * Get browsers list
     *
     * @return Array of browsers
     */
    protected function getBrowsersList()
    {
        $aList = array();

        $bot = include_once __DIR__ . '/UserAgents/Bot.php';

        // @todo use array_merge
        foreach ($bot as $name => $row) {
            $aList[$name] = $row;
        }

        $browser = include_once __DIR__ . '/UserAgents/Browser.php';

        foreach ($browser as $name => $row) {
            $aList[$name] = $row;
        }

        /*
        $mobile = include_once __DIR__ . '/UserAgents/Mobile.php';
        foreach ($mobile as $name => $row) {
            $aList[$name] = $row;
        }

        $console = include_once __DIR__ . '/UserAgents/Console.php';
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

        $os = include_once __DIR__ . '/UserAgents/Os.php';

        foreach ($os as $name => $row) {
            $aList[$name] = $row;
        }

        return $aList;
    }
}
