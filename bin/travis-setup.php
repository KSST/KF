<?php

/**
 * Alter INI Setting and install PHP extensions required for testing by Travis CI.
 *
 * Usage: add to your .travis.yml
 * env:
 *   - OPCODE_CACHE=apc
 * before_script:
 *   php ./bin/travis-setup.php $OPCODE_CACHE
 */
$phpEnv = new PhpEnvironment();

if (isset($argv[1]) && 'APC' === strtoupper($argv[1])) {
    $phpEnv->installExtension('apc');
} else {
    $phpEnv->installExtension('xcache');
}

$phpEnv->installExtension('memcache');
$phpEnv->installExtension('memcached');

// enable short_open_tags for 5.3 and lower
if(version_compare(PHP_VERSION, '5.4.0', '<'))
{
    $phpEnv->iniSet('short_open_tag=On');
}

class PhpEnvironment
{
    /**
     * Holds build, configure and install instructions for PHP extensions.
     *
     * @var array Extensions to build keyed by extension name.
     */
    protected $extensions;
    protected $phpVersion;
    protected $phpIniFile;

    public function __construct()
    {
        $this->phpVersion = phpversion();

        $this->phpIniFile = php_ini_loaded_file();

        $this->extensions = array(
        'memcache' => array(
            'url'        => 'http://pecl.php.net/get/memcache-2.2.6.tgz',
            'php_version' => array(),
            'cfg'         => array('--enable-memcache'),
            'ini'         => array('extension=memcache.so'),
        ),
        'memcached' => array(
            'url'        => 'http://pecl.php.net/get/memcached-1.0.2.tgz',
            'php_version' => array(
                // memcached 1.0.2 does not build on PHP 5.4
                array('<', '5.4'),
            ),
            'cfg'         => array(),
            'ini'         => array('extension=memcached.so'),
        ),
        'apc' => array(
            'url'        => 'http://pecl.php.net/get/APC-3.1.9.tgz',
            'php_version' => array(
                // apc 3.1.9 causes a segfault on PHP 5.4
                array('<', '5.4'),
            ),
            'cfg'         => array(),
            'ini'         => array(
                'extension=apc.so',
                'apc.enabled=1',
                'apc.enable_cli=1',
                'apc.slam_defense=1',
            ),
        ),
        'xcache' => array(
            'url'        => 'http://xcache.lighttpd.net/pub/Releases/1.2.2/xcache-1.2.2.tar.gz',
            'php_version' => array(
                // xcache does not build with Travis CI (as of 2012-01-09)
                array('<', '5'),
            ),
            'cfg'         => array('--enable-xcache'),
            'ini'         => array(
                'extension=xcache.so',
                'xcache.cacher=false',
                'xcache.admin.enable_auth=0',
                'xcache.var_size=1M',
            ),
        ),
    );
    }

    /**
     * Install extension by given name.
     *
     * Uses configration retrieved as per `php_ini_loaded_file()`.
     *
     * @see http://php.net/php_ini_loaded_file
     * @param string $name The name of the extension to install.
     */
    public function installExtension($name)
    {
        if (isset($this->extensions[$name]) === true || array_key_exists($name, $this->extensions) === true) {
            $extension = $this->extensions[$name];

            echo "== extension: $name ==\n";

            foreach ($extension['php_version'] as $version) {
                if (false === version_compare($this->phpVersion, $version[1], $version[0])) {
                    printf(
                        "=> not installed, requires a PHP version %s %s (%s installed)\n",
                        $version[0],
                        $version[1],
                        $this->phpVersion
                    );

                    return;
                }
            }

            $this->system(sprintf("wget %s > /dev/null 2>&1", $extension['url']));
            $file = basename($extension['url']);

            $this->system(sprintf("tar -xzf %s > /dev/null 2>&1", $file));
            $folder = basename($file, ".tgz");
            $folder = basename($folder, ".tar.gz");

            $this->system(sprintf(
                'sh -c "cd %s && phpize && ./configure %s && make && sudo make install" > /dev/null 2>&1',
                $folder,
                implode(' ', $extension['cfg'])
            ));

            foreach ($extension['ini'] as $ini) {
                $this->system(sprintf("echo %s >> %s", $ini, $this->phpIniFile));
            }

            printf("=> installed (%s)\n", $folder);
        }
    }

    /**
     * Sets a php ini configuration option.
     *
     * @param string $option The directive to set, like "directive=1".
     */
    public function iniSet($option)
    {
        $this->system(sprintf("echo %s >> %s", $option, $this->phpIniFile));

        printf("=> new php.ini setting (%s)\n", $option);
    }

    /**
     * Executes given command, reports and exits in case it fails.
     *
     * @param string $command The command to execute.
     */
    private function system($command)
    {
        $statusCode = 0;
        system($command, $statusCode);
        if (1 === $statusCode) {
            printf("=> Command '%s' failed !", $command);

            exit(1);
        }
    }
}
