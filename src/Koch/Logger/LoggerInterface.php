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

namespace Koch\Logger;

/**
 * Interface for the Logger Adapters.
 *
 * Implements the PSR-3 standard.
 *
 * @link https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md
 */
interface LoggerInterface
{
    /**
     * System is unusable.
     *
     * @param string $message
     */
    public function emergency($message, array $context = []);

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     */
    public function alert($message, array $context = []);

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     */
    public function critical($message, array $context = []);

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     */
    public function error($message, array $context = []);

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     */
    public function warning($message, array $context = []);

    /**
     * Normal but significant events.
     *
     * @param string $message
     */
    public function notice($message, array $context = []);

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     */
    public function info($message, array $context = []);

    /**
     * Detailed debug information.
     *
     * @param string $message
     */
    public function debug($message, array $context = []);

    /**
     * Logs with an arbitrary level.
     *
     * @param string $message
     */
    public function log(mixed $level, $message, array $context = []);
}
