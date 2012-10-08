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
    public function sessionOpen();
    public function sessionClose();
    public function sessionRead($id);
    public function sessionWrite($id, $data);
    public function sessionDestroy($id);
    public function sessionGc($maxlifetime);
}
