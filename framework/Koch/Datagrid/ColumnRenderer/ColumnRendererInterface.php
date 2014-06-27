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

namespace Koch\Datagrid\ColumnRenderer;

/**
 * Interface for a Datagrid Column Renderer
 */
interface ColumnRendererInterface
{
    /**
     * Render the given cell of the column
     * @return string|null
     */
    public function renderCell($_Value);
}
