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

namespace Koch\Session\Storage;

use Koch\Session\AbstractSession;

/**
 * Koch Framwork - Class for a PHP native session storage.
 */
class PHPNativeSession extends AbstractSession
{
    public function close()
    {
        ;
    }

    public function write($id, $data)
    {
        ;
    }

    public function gc($maxlifetime)
    {
        ;
    }

    public function destroy($id)
    {
        ;
    }

    public function open($path, $name)
    {
        ;
    }

    public function read($id)
    {
        ;
    }
}
