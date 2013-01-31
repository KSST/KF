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

namespace Koch\Session;

/**
 * Interface for Session
 *
 * Compatible with the SessionHandlerInterface of PHP 5.4.
 * @link http://php.net/manual/ro/class.sessionhandlerinterface.php
 */
interface SessionInterface
{
    public function open($path, $name);
    public function close();
    public function read($id);
    public function write($id, $data);
    public function destroy($id);
    public function gc($maxlifetime);
}
