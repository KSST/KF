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

namespace Koch\View\Helper;

/**
 * Interface for all Nodes (Leaf-Objects).
 *
 * Each node (leaf-object) has to provide a method...
 */
interface ViewNodeInterface
{
    /**
     * Get the contents of this component in string form.
     *
     * @return string
     */
    public function render();

    public function __toString();

    /**
     * Set the data.
     */
    public function setData(array $data); // array | assign placeholders 'data' = $data

    /**
     * Set the default content or to overwrite the leaf-content.
     */
    public function setContent($content); // string | set content / placeholders 'data'

    /**
     * Set the default content.
     */
    public function __construct($defaultContent);
}
