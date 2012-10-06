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
 * @category    Koch
 * @package     Core
 * @subpackage  Session
 */
interface SessionInterface
{
    public function session_open();
    public function session_close();
    public function session_read($id);
    public function session_write($id, $data);
    public function session_destroy($id);
    public function session_gc($maxlifetime);
}
