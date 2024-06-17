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

namespace Koch\Tests;

/**
 * An alternative base-class for PHPUnit test cases.
 *
 * The class provides additional functionality, like:
 * - temporary file and fixture path handling (create, purge)
 * - getting, setting and calling protected and private methods
 *   and properties (access manipulation) through reflection.
 * Meow. That's considered bad practice. Just shut up, ok?
 *
 * If your test class provides its own tearDown() method,
 * make sure to call the parent tearDown() method as well.
 * tearDown() cleans up temporary files and directories.
 */
class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * The list of paths to purge.
     *
     * @var array
     */
    private $purgePaths = [];

    /**
     * Calls a class or object method.
     *
     * @param object|string $class  The class name or object.
     * @param string        $method The method name.
     * @param array         $args   The method arguments.
     *
     * @return mixed The method result.
     */
    public function callMethod($class, $method, array $args = [])
    {
        $method = $this->findMethod($class, $method);
        $method->setAccessible(true);

        return $method->invokeArgs(
            is_object($class) ? $class : null,
            $args
        );
    }

    /**
     * Recursively copies a file or directory tree to another location.
     *
     * @param string $from    The source file system path.
     * @param string $to      The target file system path.
     * @param bool   $replace Replacing existing files?
     * @param bool   $purge   Automatically purge?
     *
     * @throws \Exception If the path could not be copied.
     */
    public function copyPath($from, $to, $replace = true, $purge = true)
    {
        if (false === file_exists($from)) {
            throw new \Exception('InvalidPath %s', $from);
        }

        if (is_dir($from)) {
            if (false === file_exists($to)) {
                if (false === @mkdir($to)) {
                    throw new \Exception(sprintf('The directory "%s" could not be created.', $to));
                }
            }

            if (false === ($dh = @opendir($from))) {
                throw new \Exception(sprintf('The directory "%s" could not be opened.', $from));
            }

            while (false !== ($item = readdir($dh))) {
                if (('.' === $item) || ('..' === $item)) {
                    continue;
                }

                $this->copyPath(
                    $from . DIRECTORY_SEPARATOR . $item,
                    $to . DIRECTORY_SEPARATOR . $item,
                    $replace,
                    false
                );
            }

            closedir($dh);
        } elseif (false === (file_exists($to) && (false === $replace))) {
            if (false === @copy($from, $to)) {
                throw new \Exception('Error %s', $to);
            }
        }

        if ($purge) {
            $this->purgePaths[] = $to;
        }
    }

    /**
     * Creates a temporary directory path that will be automatically purged
     * at the end of the test.
     *
     * @param string $name The directory name.
     *
     * @return string The directory path.
     *
     * @throws \Exception If the directory could not be created.
     */
    public function createDir($name = null)
    {
        unlink($dir = $this->createFile());

        if (false === mkdir($dir)) {
            throw new \Exception('Mkdir failed when trying to create dir %s', $dir);
        }

        if (null !== $name) {
            $dir .= DIRECTORY_SEPARATOR . $name;

            if (false === mkdir($dir)) {
                throw new \Exception('Mkdir failed when trying to create dir %s', $dir);
            }
        }

        return $dir;
    }

    /**
     * Creates a temporary file path that will be automatically purged at the
     * end of the test.
     *
     * @param string $name The file name.
     *
     * @return string The file path.
     *
     * @throws \Exception If the file could not be created.
     */
    public function createFile($name = null)
    {
        if (null === $name) {
            if (false === ($file = tempnam(sys_get_temp_dir(), 'tst'))) {
                throw new \Exception('Error %s', $name);
            }

            $this->purgePaths[] = $file;
        } else {
            $file = $this->createDir() . DIRECTORY_SEPARATOR . $name;
            if (false === touch($file)) {
                throw new \Exception('Create file error: %s', $file);
            }
        }

        return $file;
    }

    /**
     * Finds a class method and returns the ReflectionMethod instance.
     *
     * @param object|string $class The class name or object or a ReflectionClass.
     * @param string        $name  The method name.
     *
     * @return ReflectionMethod The method name.
     *
     * @throws ReflectionException If the method does not exist.
     */
    public function findMethod($class, $name)
    {
        $reflection = ($class instanceof \ReflectionClass) ? $class : new \ReflectionClass($class);

        while (false === $reflection->hasMethod($name)) {
            if (false === ($reflection = $reflection->getParentClass())) {
                throw new \ReflectionException(sprintf(
                    'The method "%s" does not exist in the class "%s".',
                    $name,
                    is_object($class) ? $class::class : $class
                ));
            }
        }

        return $reflection->getMethod($name);
    }

    /**
     * Finds a class property and returns the ReflectionProperty instance.
     *
     * @param object|string $class The class name or object or a ReflectionClass.
     * @param string        $name  The property name.
     *
     * @return ReflectionProperty The property instance.
     *
     * @throws ReflectionException If the property is not found.
     */
    public function findProperty($class, $name)
    {
        $reflection = ($class instanceof \ReflectionClass) ? $class : new \ReflectionClass($class);

        while (false === $reflection->hasProperty($name)) {
            if (false === ($reflection = $reflection->getParentClass())) {
                throw new \ReflectionException(sprintf(
                    'The property "%s" does not exist in the class "%s".',
                    $name,
                    is_object($class) ? $class::class : $class
                ));
            }
        }

        return $reflection->getProperty($name);
    }

    /**
     * Returns the value of the property.
     *
     * @param object|string $class The class name or object.
     * @param string        $name  The property name.
     *
     * @return mixed The value of the property.
     */
    public function getPropertyValue($class, $name)
    {
        $property = $this->findProperty($class, $name);
        $property->setAccessible(true);

        return $property->getValue(is_object($class) ? $class : null);
    }

    /**
     * Sets the value of the property.
     *
     * @param object|string $class The class name or object.
     * @param string        $name  The property name.
     */
    public function setPropertyValue($class, $name, mixed $value)
    {
        $property = $this->findProperty($class, $name);
        $property->setAccessible(true);
        $property->setValue(
            is_object($class) ? $class : null,
            $value
        );
    }

    /**
     * Recursively deletes a file or directory tree.
     *
     * @param string $path The file or directory path.
     *
     * @throws FileSystemException If the path could not be purged.
     */
    public function purgePath($path)
    {
        if (false === file_exists($path)) {
            throw new \Exception('InvalidPath %s', $from);
        }

        if (is_dir($path)) {
            if (false === ($dh = @opendir($path))) {
                throw new \Exception('Could not open dir %s', $path);
            }

            while (false !== ($item = readdir($dh))) {
                if (('.' === $item) || ('..' === $item)) {
                    continue;
                }

                $this->purgePath($path . DIRECTORY_SEPARATOR . $item);
            }

            closedir($dh);

            if (false === @rmdir($path)) {
                throw new \Exception('rmdir error %s', $path);
            }
        } else {
            if (false === @unlink($path)) {
                throw new \Exception('unlink error %s', $path);
            }
        }
    }

    /**
     * Purges the created paths.
     */
    protected function tearDown()
    {
        foreach ($this->purgePaths as $path) {
            if (file_exists($path)) {
                $this->purgePath($path);
            }
        }
    }
}
