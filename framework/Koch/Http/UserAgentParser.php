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

namespace Koch\Http;

/**
 * Koch Framework - User Agent Parser
 *
 * @category    Koch
 * @package     Tools
 * @subpackage  Browser
 */
class UserAgentParser
{
    const TYPE_UNKNOW = 'unknown';

    /**
     *  Parse a user agent string.
     *
     *  @param  (String) $userAgentString - defaults to $_SERVER['USER_AGENT'] if empty
     *  @return Array(
     *                'user_agent'     => 'mozilla/5.0 (windows; u;...))',
     *                'browser_name'     => 'firefox',
     *                'browser_type'     => 'browser',
     *                'browser_type_sub'     => '',
     *                'browser_version'  => '3.6',
     *                'browser_version_major'  => '3',
     *                'browser_version_minor'  => '6',
     *                'browser_version_release'  => '15',
     *                'browser_version_build'  => '',
     *                'operating_system' => 'windows'
     *                'operating_system_name' => 'xp'
     *                'operating_system_typ' => 'os'
     *                'operating_system_typ_sub' => 'os'
     *                'engine' => 'gecko'
     *                'engine_version' => '1.9.2.15'
     *               );
     */
    public function parse($userAgentString = null)
    {
        if (!$userAgentString) {
            $userAgentString = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
        }

        // parse quickly with medium accuracy
        $informations = $this->doParse($userAgentString);

        // debug
        #var_dump( $informations );

        return $informations;
    }

    /**
     *  Detect quickly informations from the user agent string.
     *
     *  @param  (String) $userAgentString => user agent string.
     *  @return (Array)  $information     => user agent informations directly in array.
     */
    public function doParse($userAgentString)
    {
        $userAgent = array(
            'string' => $this->cleanUserAgentString($userAgentString),
            'browser_name' => null,
            'browser_typ' => null,
            'browser_typ_sub' => null,
            'browser_version' => null,
            'browser_version_major' => null,
            'browser_version_minor' => null,
            'browser_version_release' => null,
            'browser_version_build' => null,
            'operating_system' => null,
            'operating_system_name' => null,
            'operating_system_typ' => null,
            'operating_system_typ_sub' => null,
            'engine' => null,
            'engine_version' => null
        );

        $userAgent['user_agent'] = $userAgent['string'];

        if (empty($userAgent['string'])) {
            return $userAgent;
        }

        // --------------- Parse Browser ---------------
        $found = false;
        $tmp_array = array();

        foreach ($this->getListBrowsers() as $name => $elements) {
            // ----- read browser ----
            $exprReg = $elements['search'];
            foreach ($exprReg as $expr) {
                if (preg_match($expr, $userAgent['string'], $tmp_array)) {
                    $userAgent['browser_name'] = $name;

                    $userAgent['browser_typ'] = $elements['type'];
                    if (isset($elements['subtype']) === true) {
                        $userAgent['browser_typ_sub'] = $elements['subtype'];
                    }
                    $found = true;

                    // ----- read version ----
                    if ($elements['vparam'] !== null) {
                        $pattern = '';
                        $pv = $elements['vparam'];
                        $pattern = '|.+\s'.$pv.'([0-9a-z\+\-\.]+).*|';
                        $userAgent['browser_version'] = preg_replace($pattern, '$1', $userAgent['string']);
                        $tVer = preg_split("/\./", $userAgent['browser_version']);
                        $userAgent['browser_version_major'] = $tVer[0];
                        $userAgent['browser_version_minor'] = $tVer[1];
                        if( isset($tVer[2])) $userAgent['browser_version_release'] = $tVer[2];
                        if( isset($tVer[3])) $userAgent['browser_version_build'] = $tVer[3];
                    } else {
                        $userAgent['browser_version'] = self::TYPE_UNKNOW;
                    }

                    // ----- read engine ----
                    if ($elements['engine'] !== null) {
                        $userAgent['engine'] = $elements['engine'];
                    } else {
                        $userAgent['engine'] = self::TYPE_UNKNOW;
                    }

                    // ----- read engine version -----
                    $pattern = '';
                    if ($elements['eparam'] !== null) {
                        $pe = $elements['eparam'];
                        $pattern = '|.+\s'.$pe.'([0-9\.]+)(.*).*|';
                        $userAgent['engine_version'] = preg_replace($pattern, '$1', $userAgent['string']);
                    } else {
                        $userAgent['engine_version'] = self::TYPE_UNKNOW;
                    }
                }
            }
        }

        if (false === $found) {
            $userAgent['browser_typ'] = self::TYPE_UNKNOW;
        }

        // --------------- Parse Operating System ---------------
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
                        $userAgent['operating_system_typ'] = $elements['type'];
                    } else {
                        $userAgent['operating_system_typ'] = self::TYPE_UNKNOW;
                    }

                    if (isset($elements['subtype']) === true) {
                        $userAgent['operating_system_typ_sub'] = $elements['subtype'];
                    }

                    $found = true;
                }
            }
        }

        if (false === $found) {
            $userAgent['operating_system_typ'] = self::TYPE_UNKNOW;
        }

        return $userAgent;
    }

    /**
     *  Make user agent string lowercase, and replace browser aliases.
     *
     *  @param String $userAgentString => the dirty user agent string.
     *  @param String $userAgentString => the clean user agent string.
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

        foreach ($bot as $name =>$row) {
            $aList[$name] = $row;
        }

        include __DIR__ . '/UserAgents/Browser.php';

        foreach ($browser as $name =>$row) {
            $aList[$name] = $row;
        }

/*
        include __DIR__ . '/UserAgents/Mobile.php';
        foreach ($mobile as $name =>$row) {
            $aList[$name] = $row;
        }

        include __DIR__ . '/UserAgents/Console.php';
        foreach ($console as $name =>$row) {
            $aList[$name] = $row;
        }
*/

        return $aList;
    }

    /**
     *  Get operating system list
     *
     *  @return array => the operating system.
     */
    protected function getListOperatingSystems()
    {
        $aList = array();

        include __DIR__ . '/UserAgents/Os.php';

        foreach ($os as $name =>$row) {
            $aList[$name] = $row;
        }

        return $aList;
    }
}
