<?php

/**
 * Koch Framework
 * Jens-André Koch © 2005 - onwards.
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

namespace Koch\Form\Elements;

use Koch\Form\FormElementInterface;

class SecurityToken extends Hidden implements FormElementInterface
{
    public function __construct()
    {
        parent::__construct();

        $this->isRequired();
        $this->setValidator('NotEmpty');
        $this->initCsrfValidator();
    }

    public function initCsrfValidator()
    {
        $session = $this->getSession();

        if ($session->hash !== null) {
            $validHash = $session->hash;
        } else {
            $validHash = null;
        }

        $this->addValidator('Identical', true, [$validHash]);

        return $this;
    }

    // getHash
    // setHashToSession
    // getHashFromSession
    // compare

    public function render()
    {
    }
}
