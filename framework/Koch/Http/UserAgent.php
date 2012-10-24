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

namespace Koch\Http;

/**
 * Koch Framework - Class for acquiring Browser information.
 *
 * @category    Koch
 * @package     Tools
 * @subpackage  Browser
 */
class UserAgent
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
     * usrer agent
     * @var string
     */
    protected $userAgentString;

    /**
     * browser name
     * @var string
     */
    protected $browserName;

    /**
     * browser typ
     * @var string
     */
    protected $browserTyp;

    /**
     * browser sub typ
     * @var string
     */
    protected $browserTypSub;

    /**
     * browser version
     * @var string
     */
    protected $browserVersion;

    /**
     * browser major version
     * @var string
     */
    protected $browserVersionMajor;

    /**
     * browser minor version
     * @var string
     */
    protected $browserVersionMinor;

    /**
     * browser release version
     * @var string
     */
    protected $browserVersionRelease;

    /**
     * browser build version
     * @var string
     */
    protected $browserVersionBuild;

    /**
     * operating system
     * @var string
     */
    protected $operatingSystem;

    /**
     * operating system name
     * @var string
     */
    protected $operatingSystemName;

    /**
     * operating system typ
     * @var string
     */
    protected $operatingSystemTyp;

    /**
     * operating system sub typ
     * @var string
     */
    protected $operatingSystemTypSub;

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
     *  Configure the User Agent from a user agent string.
     *  @param  String  $userAgentString => the user agent string.
     *  @param  UserAgentParser  $userAgentParser => the parser used to parse the string.
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

        $aBrowser['name'] = $this->getBrowserName();
        $aBrowser['version'] = $this->getBrowserVersion();
        $aBrowser['engine'] = $this->getEngine() . ' ' . $this->getEngineVersion();
        $aBrowser['os'] = $this->getOperatingSystem() . ' ' . $this->getOperatingSystemName();

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
        return (bool) ($this->getBrowserTyp() == self::TYPE_BOT);
    }

    /**
     * isIE
     * @return bool
     */
    public function isIE()
    {
        return (bool) ($this->getBrowserName() == self::BROWSER_IE);
    }

    /**
     * isFirefox
     * @return bool
     */
    public function isFirefox()
    {
        return (bool) ($this->getBrowserName() == self::BROWSER_FIREFOX);
    }

    /**
     * isOpera
     * @return bool
     */
    public function isOpera()
    {
        return (bool) ($this->getBrowserName() == self::BROWSER_OPERA);
    }

    /**
     * isChrome
     * @return bool
     */
    public function isChrome()
    {
        return (bool) ($this->getBrowserName() == self::BROWSER_CHROME);
    }

    /**
     * isSafari
     * @return bool
     */
    public function isSafari()
    {
        return (bool) ($this->getBrowserName() == self::BROWSER_SAFARI);
    }

    /**
     * isMobilSystem
     * @return bool
     */
    public function isMobilSystem()
    {
        return (bool) ($this->getOperatingSystemTyp() == self::SYSTEM_MOBIL);
    }

    /**
     * isConsoleSystem
     * @return bool
     */
    public function isConsoleSystem()
    {
        return (bool) ($this->getOperatingSystemTyp() == self::SYSTEM_CONSOLE);
    }

    // --------------- BROWSER ---------------

    /**
     *  Get Browser name
     *
     *  @return String - the browser name
     */
    public function getBrowserName()
    {
        return $this->browserName;
    }

    /**
     *  Set Browser name
     *  @param String - the browser name;
     */
    public function setBrowserName($name)
    {
        $this->browserName = $name;
    }

    /**
     *  Get Browser typ (bot, browser...)
     *
     *  @return String - the browser typ
     */
    public function getBrowserTyp()
    {
        return $this->browserTyp;
    }

    /**
     *  Set Browser typ
     *  @param String - the browser typ;
     */
    public function setBrowserTyp($name)
    {
        $this->browserTyp = $name;
    }

    /**
     *  Get Browser sub typ (validator, pda...)
     *
     *  @return String - the browser sub typ
     */
    public function getBrowserTypSub()
    {
        return $this->browserTypSub;
    }

    /**
     *  Set Browser sub typ
     *  @param String - the browser sub typ;
     */
    public function setBrowserTypSub($name)
    {
        $this->browserTypSub = $name;
    }

    /**
     *  Get Browser version
     *
     *  @return String - the browser version
     */
    public function getBrowserVersion()
    {
        return $this->browserVersion;
    }

    /**
     *  Set Browser version
     *  @param String - the browser version;
     */
    public function setBrowserVersion($version)
    {
        $this->browserVersion = $version;
    }

    /**
     *  Get Browser version major
     *
     *  @return String - the browser version major
     */
    public function getBrowserVersionMajor()
    {
        return $this->browserVersionMajor;
    }

    /**
     *  Set Browser version major
     *  @param String - the browser version major;
     */
    public function setBrowserVersionMajor($version)
    {
        $this->browserVersionMajor = $version;
    }

    /**
     *  Get Browser version minor
     *
     *  @return String - the browser version minor
     */
    public function getBrowserVersionMinor()
    {
        return $this->browserVersionMinor;
    }

    /**
     *  Set Browser version minor
     *  @param String - the browser version minor;
     */
    public function setBrowserVersionMinor($version)
    {
        $this->browserVersionMinor = $version;
    }

    /**
     *  Get Browser version release
     *
     *  @return String - the browser version release
     */
    public function getBrowserVersionRelease()
    {
        return $this->browserVersionRelease;
    }

    /**
     *  Set Browser version release
     *  @param String - the browser version release;
     */
    public function setBrowserVersionRelease($value)
    {
        if ($value === null or empty($value)) {
            $value = 0;
        }
        $this->browserVersionRelease = $value;
    }

    /**
     *  Get Browser version build
     *
     *  @return String - the browser version build
     */
    public function getBrowserVersionBuild()
    {
        return $this->browserVersionBuild;
    }

    /**
     *  Set Browser version build
     *  @param String - the browser version build;
     */
    public function setBrowserVersionBuild($value)
    {
        if ($value === null or empty($value)) {
            $value = 0;
        }
        $this->browserVersionBuild = $value;
    }

    // --------------- OPERATING SYSTEM ---------------

    /**
     *  Get the operating system
     *
     *  @return String - the operating system
     */
    public function getOperatingSystem()
    {
        return $this->operatingSystem;
    }

    /**
     *  Set Operating System ( windows, linux ...)
     *  @param String - the operating system.
     */
    public function setOperatingSystem($os)
    {
        $this->operatingSystem = $os;
    }

    /**
     *  Get the operating system name ( vista, 2000, 7 ...)
     *
     *  @return String - the operating system name
     */
    public function getOperatingSystemName()
    {
        return $this->operatingSystemName;
    }

    /**
     *  Set Operating System typ ( os, mobile...)
     *  @param String - the operating system typ.
     */
    public function setOperatingSystemTyp($value)
    {
        $this->operatingSystemTyp = $value;
    }

    /**
     *  Get the operating system typ
     *  @return String - the operating system typ
     */
    public function getOperatingSystemTyp()
    {
        return $this->operatingSystemTyp;
    }

    /**
     *  Set Operating System sub typ ( device...)
     *  @param String - the operating system sub typ.
     */
    public function setOperatingSystemTypSub($value)
    {
        $this->operatingSystemTypSub = $value;
    }

    /**
     *  Get the operating system sub typ
     *
     *  @return String - the operating system sub typ
     */
    public function getOperatingSystemTypSub()
    {
        return $this->operatingSystemTypSub;
    }

    /**
     *  Set Operating System name
     *  @param String - the operating system name.
     */
    public function setOperatingSystemName($value)
    {
        $this->operatingSystemName = $value;
    }

    // --------------- ENGINE ---------------
    /**
     *  Get the Engine Name
     *  @return String the engine name
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     *  Set Engine name
     *  @param String - the engine name
     */
    public function setEngine($engine)
    {
        $this->engine = $engine;
    }

    /**
     *  Get the Engine version
     *  @return String the engine version
     */
    public function getEngineVersion()
    {
        return $this->engineVersion;
    }

    /**
     *  Set Engine version
     *  @param String - the engine version
     */
    public function setEngineVersion($version)
    {
        $this->engineVersion = $version;
    }

    // --------------- USER AGENT ---------------
    /**
     *  Get the User Agent String
     *  @return String the User Agent string
     */
    public function getUserAgentString()
    {
        return $this->userAgentString;
    }

    /**
     *  Set Engine name
     *  @param String - the engine name
     */
    public function setUserAgentString($userAgentString)
    {
        $this->userAgentString = $userAgentString;
    }

    // --------------- INFO ---------------
    public function __toString()
    {
        return $this->getFullName();
    }

    /**
     *  Returns a string combined browser name plus version
     *  @return browser name plus version
     */
    public function getFullName()
    {
        return $this->getBrowserName() . ' ' . $this->getBrowserVersion();
    }

    /**
     *  Convert the http user agent to an array.
     */
    public function toArray()
    {
        return array(
            'user_agent' => $this->getUserAgentString(),
            'browser_name' => $this->getBrowserName(),
            'browser_typ' => $this->getBrowserTyp(),
            'browser_typ_sub' => $this->getBrowserTypSub(),
            'browser_version' => $this->getBrowserVersion(),
            'browser_version_major' => $this->getBrowserVersionMajor(),
            'browser_version_minor' => $this->getBrowserVersionMinor(),
            'browser_version_release' => $this->getBrowserVersionRelease(),
            'browser_version_build' => $this->getBrowserVersionBuild(),
            'operating_system' => $this->getOperatingSystem(),
            'operating_system_name' => $this->getOperatingSystemName(),
            'operating_system_typ' => $this->getOperatingSystemTyp(),
            'operating_system_typ_sub' => $this->getOperatingSystemTypSub(),
            'engine' => $this->getEngine(),
            'engine_version' => $this->getEngineVersion()
        );
    }

    /**
     *  Configure the user agent from an input array.
     *  @param Array $data input data array
     */
    public function fromArray(array $data)
    {
        $this->setUserAgentString($data['user_agent']);
        $this->setBrowserName($data['browser_name']);
        $this->setBrowserTyp($data['browser_typ']);
        $this->setBrowserTypSub($data['browser_typ_sub']);
        $this->setBrowserVersion($data['browser_version']);
        $this->setBrowserVersionMajor($data['browser_version_major']);
        $this->setBrowserVersionMinor($data['browser_version_minor']);
        $this->setBrowserVersionRelease($data['browser_version_release']);
        $this->setBrowserVersionBuild($data['browser_version_build']);
        $this->setBrowserTyp($data['browser_typ']);
        $this->setOperatingSystem($data['operating_system']);
        $this->setOperatingSystemName($data['operating_system_name']);
        $this->setOperatingSystemTyp($data['operating_system_typ']);
        $this->setOperatingSystemTypSub($data['operating_system_typ_sub']);
        $this->setEngine($data['engine']);
        $this->setEngineVersion($data['engine_version']);
    }

    /**
     *  This method tells whether this User Agent is unknown or not.
     *  @return TRUE is the User Agent is unknown, FALSE otherwise.
     */
    public function isUnknown()
    {
        return empty($this->browserName);
    }
}
