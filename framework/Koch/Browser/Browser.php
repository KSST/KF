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
 * Koch Framework - Class for acquiring Browser information.
 */
class Browser
{
    const BROWSER_IE = 'Internet Explorer';
    const BROWSER_FIREFOX = 'Firefox';
    const BROWSER_OPERA = 'Opera';
    const BROWSER_CHROME = 'Google Chrome';
    const BROWSER_SAFARI = 'Safari';

    const TYPE_BOT = 'bot';
    const TYPE_BROWSER = 'browser';

    const SYSTEM_MOBIL = 'mobil';
    const SYSTEM_CONSOLE = 'console';

    /**
     * user agent
     * @var string
     */
    protected $userAgent;

    /**
     * browser name
     * @var string
     */
    protected $name;

    /**
     * browser type
     * @var string
     */
    protected $type;

    /**
     * browser sub typ
     * @var string
     */
    protected $subtype;

    /**
     * browser version
     * @var string
     */
    protected $version;

    /**
     * browser major version
     * @var string
     */
    protected $majorVersion;

    /**
     * browser minor version
     * @var string
     */
    protected $minorVersion;

    /**
     * browser release version
     * @var string
     */
    protected $releaseVersion;

    /**
     * browser build version
     * @var string
     */
    protected $buildVersion;

    /**
     * operating system
     * @var string
     */
    protected $os;

    /**
     * operating system name
     * @var string
     */
    protected $osName;

    /**
     * operating system type
     * @var string
     */
    protected $osType;

    /**
     * operating system sub type
     * @var string
     */
    protected $osSubType;

    /**
     * engine
     * @var string
     */
    protected $engine;

    /**
     * engine version
     * @var string
     */
    protected $engineVersion;

    /**
     * Constructor
     *
     * Configure the User Agent from a user agent string.
     * @param string          $userAgentString The user agent string.
     * @param UserAgentParser $userAgentParser The parser used to parse the string.
     */
    public function __construct($userAgentString = null, UserAgentParser $userAgentParser = null)
    {
        if ($userAgentParser == null) {
            $userAgentParser = new UserAgentParser();
        }
        $this->setUserAgentString($userAgentString);
        $this->fromArray($userAgentParser->parse($userAgentString));
    }

    public function getBrowserInfo()
    {
        $aBrowser = array();

        $aBrowser['name'] = $this->name;
        $aBrowser['version'] = $this->version;
        $aBrowser['engine'] = $this->engine . ' ' . $this->engineVersion;
        $aBrowser['os'] = $this->os . ' ' . $this->osName;

        return $aBrowser;
    }

    /**
     * Section: boolean return methods for checking the browser type.
     */

    /**
     * isBot
     * @return bool
     */
    public function isBot()
    {
        return (bool) ($this->type === self::TYPE_BOT);
    }

    /**
     * isIE
     * @return bool
     */
    public function isIE()
    {
        return (bool) ($this->name === self::BROWSER_IE);
    }

    /**
     * isFirefox
     * @return bool
     */
    public function isFirefox()
    {
        return (bool) ($this->name === self::BROWSER_FIREFOX);
    }

    /**
     * isOpera
     * @return bool
     */
    public function isOpera()
    {
        return (bool) ($this->name === self::BROWSER_OPERA);
    }

    /**
     * isChrome
     * @return bool
     */
    public function isChrome()
    {
        return (bool) ($this->name === self::BROWSER_CHROME);
    }

    /**
     * isSafari
     * @return bool
     */
    public function isSafari()
    {
        return (bool) ($this->name === self::BROWSER_SAFARI);
    }

    /**
     * isMobilSystem
     * @return bool
     */
    public function isMobilSystem()
    {
        return (bool) ($this->osType === self::SYSTEM_MOBIL);
    }

    /**
     * isConsoleSystem
     * @return bool
     */
    public function isConsoleSystem()
    {
        return (bool) ($this->osType === self::SYSTEM_CONSOLE);
    }

    // --------------- BROWSER ---------------

    /**
     * Get Browser name
     *
     * @return string the browser name
     */
    public function getBrowserName()
    {
        return $this->name;
    }

    /**
     * Set Browser name
     * @param string The browser name
     */
    public function setBrowserName($name)
    {
        $this->name = $name;
    }

    /**
     * Get Browser type (bot, browser...)
     *
     * @return string the browser type
     */
    public function getBrowserType()
    {
        return $this->type;
    }

    /**
     * Set Browser type
     * @param string The browser type.
     */
    public function setBrowserType($name)
    {
        $this->type = $name;
    }

    /**
     * Get Browser sub type (validator, pda...)
     *
     * @return string the browser sub type
     */
    public function getBrowserTypeSub()
    {
        return $this->subtype;
    }

    /**
     * Set Browser sub type
     * @param string  the browser sub type;
     */
    public function setBrowserTypeSub($name)
    {
        $this->subtype = $name;
    }

    /**
     * Get Browser version
     *
     * @return string the browser version
     */
    public function getBrowserVersion()
    {
        return $this->version;
    }

    /**
     * Set Browser version
     * @param string  the browser version;
     */
    public function setBrowserVersion($version)
    {
        $this->version = $version;
    }

    /**
     * Get Browser version major
     *
     * @return string the browser version major
     */
    public function getBrowserVersionMajor()
    {
        return $this->majorVersion;
    }

    /**
     * Set Browser version major
     * @param string  the browser version major;
     */
    public function setBrowserVersionMajor($version)
    {
        $this->majorVersion = $version;
    }

    /**
     * Get Browser version minor
     *
     * @return string the browser version minor
     */
    public function getBrowserVersionMinor()
    {
        return $this->minorVersion;
    }

    /**
     * Set Browser version minor
     * @param string  the browser version minor;
     */
    public function setBrowserVersionMinor($version)
    {
        $this->minorVersion = $version;
    }

    /**
     * Get Browser version release
     *
     * @return string the browser version release
     */
    public function getBrowserVersionRelease()
    {
        return $this->releaseVersion;
    }

    /**
     * Set Browser version release
     * @param string  the browser version release;
     */
    public function setBrowserVersionRelease($value)
    {
        if ($value === null or empty($value)) {
            $value = 0;
        }
        $this->releaseVersion = $value;
    }

    /**
     * Get Browser version build
     *
     * @return string the browser version build
     */
    public function getBrowserVersionBuild()
    {
        return $this->buildVersion;
    }

    /**
     * Set Browser version build
     * @param string  the browser version build;
     */
    public function setBrowserVersionBuild($value)
    {
        if ($value === null or empty($value)) {
            $value = 0;
        }
        $this->buildVersion = $value;
    }

    // --------------- OPERATING SYSTEM ---------------

    /**
     * Get the operating system
     *
     * @return string the operating system
     */
    public function getOperatingSystem()
    {
        return $this->os;
    }

    /**
     * Set Operating System ( windows, linux ...)
     * @param string  the operating system.
     */
    public function setOperatingSystem($os)
    {
        $this->os = $os;
    }

    /**
     * Get the operating system name ( vista, 2000, 7 ...)
     *
     * @return string the operating system name
     */
    public function getOperatingSystemName()
    {
        return $this->osName;
    }

    /**
     * Set Operating System type ( os, mobile...)
     * @param string  the operating system type.
     */
    public function setOperatingSystemType($value)
    {
        $this->osType = $value;
    }

    /**
     * Get the operating system type
     * @return string the operating system type
     */
    public function getOperatingSystemType()
    {
        return $this->osType;
    }

    /**
     * Set Operating System sub type ( device...)
     * @param string  the operating system sub type.
     */
    public function setOperatingSystemTypeSub($value)
    {
        $this->osSubType = $value;
    }

    /**
     * Get the operating system sub type
     *
     * @return string the operating system sub type
     */
    public function getOperatingSystemTypeSub()
    {
        return $this->osSubType;
    }

    /**
     * Set Operating System name
     * @param string  the operating system name.
     */
    public function setOperatingSystemName($value)
    {
        $this->osName = $value;
    }

    // --------------- ENGINE ---------------
    /**
     * Get the Engine Name
     * @return String the engine name
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     * Set Engine name
     * @param string  the engine name
     */
    public function setEngine($engine)
    {
        $this->engine = $engine;
    }

    /**
     * Get the Engine version
     * @return String the engine version
     */
    public function getEngineVersion()
    {
        return $this->engineVersion;
    }

    /**
     * Set Engine version
     * @param string  the engine version
     */
    public function setEngineVersion($version)
    {
        $this->engineVersion = $version;
    }

    /**
     * Get the User Agent String
     * @return String the User Agent string
     */
    public function getUserAgentString()
    {
        return $this->userAgent;
    }

    /**
     * Set Engine name
     * @param string  the engine name
     */
    public function setUserAgentString($userAgentString)
    {
        $this->userAgent = $userAgentString;
    }

    public function __toString()
    {
        return $this->getFullName();
    }

    /**
     * Returns a string combined browser name plus version
     * @return browser name plus version
     */
    public function getFullName()
    {
        return $this->name . ' ' . $this->version;
    }

    /**
     * Convert the http user agent to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'user_agent' => $this->userAgent,
            'browser_name' => $this->name,
            'browser_type' => $this->type,
            'browser_type_sub' => $this->subtype,
            'browser_version' => $this->version,
            'browser_version_major' => $this->majorVersion,
            'browser_version_minor' => $this->minorVersion,
            'browser_version_release' => $this->releaseVersion,
            'browser_version_build' => $this->buildVersion,
            'operating_system' => $this->os,
            'operating_system_name' => $this->osName,
            'operating_system_type' => $this->osType,
            'operating_system_type_sub' => $this->osSubType,
            'engine' => $this->engine,
            'engine_version' => $this->engineVersion
        );
    }

    /**
     * Configure the user agent from an input array.
     * @return array $data input data array
     */
    public function fromArray(array $data)
    {
        $this->setUserAgentString($data['user_agent']);
        $this->setBrowserName($data['browser_name']);
        $this->setBrowserType($data['browser_type']);
        $this->setBrowserTypeSub($data['browser_type_sub']);
        $this->setBrowserVersion($data['browser_version']);
        $this->setBrowserVersionMajor($data['browser_version_major']);
        $this->setBrowserVersionMinor($data['browser_version_minor']);
        $this->setBrowserVersionRelease($data['browser_version_release']);
        $this->setBrowserVersionBuild($data['browser_version_build']);
        $this->setBrowserType($data['browser_type']);
        $this->setOperatingSystem($data['operating_system']);
        $this->setOperatingSystemName($data['operating_system_name']);
        $this->setOperatingSystemType($data['operating_system_type']);
        $this->setOperatingSystemTypeSub($data['operating_system_type_sub']);
        $this->setEngine($data['engine']);
        $this->setEngineVersion($data['engine_version']);
    }

    /**
     * This method tells whether this User Agent is unknown or not.
     *
     * @return boolean true is the User Agent is unknown, false otherwise.
     */
    public function isUnknown()
    {
        return empty($this->name);
    }
}
