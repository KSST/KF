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

namespace Koch\Exception;

use \Koch\Exception\Renderer\YellowScreenOfDeath;

class SmartyTemplateException extends \Exception
{
    public function __construct($exception)
    {
        $this->exception = $exception;
    }

    public function render($exception)
    {
        YellowScreenOfDeath::renderException(
            $this->exception->getMessage(),
            $this->exception->getTraceAsString(),
            $this->exception->getCode(),
            $this->exception->getFile(),
            $this->exception->getLine(),
            $this->exception->getTrace()
        );
    }
}
