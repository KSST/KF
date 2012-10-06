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

        $this->addValidator('Identical', true, array($validHash));

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
