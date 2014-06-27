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

namespace Koch\Pagination;

interface AdapterInterface
{
    /**
     * @return integer
     */
    public function getTotalNumberOfResults();
    public function getSlice($offset, $length);
    public function getArray();
}
