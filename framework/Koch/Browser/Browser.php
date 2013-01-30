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
 * Class for acquiring Browser information.
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
    protected $browserType;

    /**
     * browser sub typ
     * @var string
     */
    protected $browserTypeSub;

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
     * operating system type
     * @var string
     */
    protected $operatingSystemType;

    /**
     * operating system sub type
     * @var string
     */
    protected $operatingSystemTypeSub;

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
        return (bool) ($this->getBrowserType() === self::TYPE_BOT);
    }

    /**
     * isIE
     * @return bool
     */
    public function isIE()
    {
        return (bool) ($this->getBrowserName() === self::BROWSER_IE);
    }

    /**
     * isFirefox
     * @return bool
     */
    public function isFirefox()
    {
        return (bool) ($this->getBrowserName() === self::BROWSER_FIREFOX);
    }

    /**
     * isOpera
     * @return bool
     */
    public function isOpera()
    {
        return (bool) ($this->getBrowserName() === self::BROWSER_OPERA);
    }

    /**
     * isChrome
     * @return bool
     */
    public function isChrome()
    {
        return (bool) ($this->getBrowserName() === self::BROWSER_CHROME);
    }

    /**
     * isSafari
     * @return bool
     */
    public function isSafari()
    {
        return (bool) ($this->getBrowserName() === self::BROWSER_SAFARI);
    }

    /**
     * isMobilSystem
     * @return bool
     */
    public function isMobilSystem()
    {
        return (bool) ($this->getOperatingSystemType() === self::SYSTEM_MOBIL);
    }

    /**
     * isConsoleSystem
     * @return bool
     */
    public function isConsoleSystem()
    {
        return (bool) ($this->getOperatingSystemType() === self::SYSTEM_CONSOLE);
    }

    // --------------- BROWSER ---------------

    /**
     * Get Browser name
     *
     * @return string the browser name
     */
    public function getBrowserName()
    {
        return $this->browserName;
    }

    /**
     * Set Browser name
     * @param string The browser name
     */
    public function setBrowserName($name)
    {
        $this->browserName = $name;
    }

    /**
     * Get Browser type (bot, browser...)
     *
     * @return string the browser type
     */
    public function getBrowserType()
    {
        return $this->browserType;
    }

    /**
     * Set Browser type
     * @param string The browser type.
     */
    public function setBrowserType($name)
    {
        $this->browserType = $name;
    }

    /**
     * Get Browser sub type (validator, pda...)
     *
     * @return string the browser sub type
     */
    public function getBrowserTypeSub()
    {
        return $this->browserTypeSub;
    }

    /**
     * Set Browser sub type
     * @param string  the browser sub type;
     */
    public function setBrowserTypeSub($name)
    {
        $this->browserTypeSub = $name;
    }

    /**
     * Get Browser version
     *
     * @return string the browser version
     */
    public function getBrowserVersion()
    {
        return $this->browserVersion;
    }

    /**
     * Set Browser version
     * @param string  the browser version;
     */
    public function setBrowserVersion($version)
    {
        $this->browserVersion = $version;
    }

    /**
     * Get Browser version major
     *
     * @return string the browser version major
     */
    public function getBrowserVersionMajor()
    {
        return $this->browserVersionMajor;
    }

    /**
     * Set Browser version major
     * @param string  the browser version major;
     */
    public function setBrowserVersionMajor($version)
    {
        $this->browserVersionMajor = $version;
    }

    /**
     * Get Browser version minor
     *
     * @return string the browser version minor
     */
    public function getBrowserVersionMinor()
    {
        return $this->browserVersionMinor;
    }

    /**
     * Set Browser version minor
     * @param string  the browser version minor;
     */
    public function setBrowserVersionMinor($version)
    {
        $this->browserVersionMinor = $version;
    }

    /**
     * Get Browser version release
     *
     * @return string the browser version release
     */
    public function getBrowserVersionRelease()
    {
        return $this->browserVersionRelease;
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
        $this->browserVersionRelease = $value;
    }

    /**
     * Get Browser version build
     *
     * @return string the browser version build
     */
    public function getBrowserVersionBuild()
    {
        return $this->browserVersionBuild;
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
        $this->browserVersionBuild = $value;
    }

    // --------------- OPERATING SYSTEM ---------------

    /**
     * Get the operating system
     *
     * @return string the operating system
     */
    public function getOperatingSystem()
    {
        return $this->operatingSystem;
    }

    /**
     * Set Operating System ( windows, linux ...)
     * @param string  the operating system.
     */
    public function setOperatingSystem($os)
    {
        $this->operatingSystem = $os;
    }

    /**
     * Get the operating system name ( vista, 2000, 7 ...)
     *
     * @return string the operating system name
     */
    public function getOperatingSystemName()
    {
        return $this->operatingSystemName;
    }

    /**
     * Set Operating System type ( os, mobile...)
     * @param string  the operating system type.
     */
    public function setOperatingSystemType($value)
    {
        $this->operatingSystemType = $value;
    }

    /**
     * Get the operating system type
     * @return string the operating system type
     */
    public function getOperatingSystemType()
    {
        return $this->operatingSystemType;
    }

    /**
     * Set Operating System sub type ( device...)
     * @param string  the operating system sub type.
     */
    public function setOperatingSystemTypeSub($value)
    {
        $this->operatingSystemTypeSub = $value;
    }

    /**
     * Get the operating system sub type
     *
     * @return string the operating system sub type
     */
    public function getOperatingSystemTypeSub()
    {
        return $this->operatingSystemTypeSub;
    }

    /**
     * Set Operating System name
     * @param string  the operating system name.
     */
    public function setOperatingSystemName($value)
    {
        $this->operatingSystemName = $value;
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
        return $this->userAgentString;
    }

    /**
     * Set Engine name
     * @param string  the engine name
     */
    public function setUserAgentString($userAgentString)
    {
        $this->userAgentString = $userAgentString;
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
        return $this->getBrowserName() . ' ' . $this->getBrowserVersion();
    }

    /**
     * Convert the http user agent to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'user_agent' => $this->getUserAgentString(),
            'browser_name' => $this->getBrowserName(),
            'browser_type' => $this->getBrowserType(),
            'browser_type_sub' => $this->getBrowserTypeSub(),
            'browser_version' => $this->getBrowserVersion(),
            'browser_version_major' => $this->getBrowserVersionMajor(),
            'browser_version_minor' => $this->getBrowserVersionMinor(),
            'browser_version_release' => $this->getBrowserVersionRelease(),
            'browser_version_build' => $this->getBrowserVersionBuild(),
            'operating_system' => $this->getOperatingSystem(),
            'operating_system_name' => $this->getOperatingSystemName(),
            'operating_system_type' => $this->getOperatingSystemType(),
            'operating_system_type_sub' => $this->getOperatingSystemTypeSub(),
            'engine' => $this->getEngine(),
            'engine_version' => $this->getEngineVersion()
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
     * @return TRUE is the User Agent is unknown, FALSE otherwise.
     */
    public function isUnknown()
    {
        return empty($this->browserName);
    }
}
