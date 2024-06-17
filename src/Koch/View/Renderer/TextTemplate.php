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

namespace Koch\View\Renderer;

use Koch\View\AbstractRenderer;

/**
 * View Renderer for Text Templates.
 *
 * This is a simple Text Template Engine.
 */
class TextTemplate extends AbstractRenderer
{
    /**
     * Sets the template file.
     *
     * @param string $file
     *
     * @throws InvalidArgumentException
     */
    public function setTemplate($file)
    {
        if (file_exists($file)) {
            $this->template = file_get_contents($file);
        } else {
            throw new \InvalidArgumentException(
                sprintf('Template file "%s" not found.', $file)
            );
        }
    }

    /**
     * Renders the template and returns it.
     *
     * @param string Template File.
     * @param array Viewdata
     *
     * @return string
     */
    public function render($template = null, $viewdata = null)
    {
        if ($template !== null) {
            $this->setTemplate($template);
        }
        if ($viewdata !== null) {
            $this->assign($viewdata);
        }

        $keys = [];

        // transform viewdata keys into placeholders
        foreach ($this->viewdata as $key => $value) {
            $keys[] = '{' . $key . '}';
        }

        // replace placeholders with values
        return str_replace($keys, $this->viewdata, $this->template);
    }

    /**
     * Renders template content to file.
     *
     * @param string $file Output file.
     *
     * @return bool
     */
    public function renderToFile($file)
    {
        return (bool) file_put_contents($file, $this->render());
    }

    public function fetch($template, $viewdata = null)
    {
        // @todo
    }

    /**
     * Assigns a value to a template parameter.
     *
     * @param string $tpl_parameter The template parameter name
     * @param mixed  $value         The value to assign
     */
    public function assign($tpl_parameter, $value = null)
    {
        // @todo
    }

    public function display($template, $viewdata = null)
    {
        echo $this->render($template, $viewdata);
    }

    public function initializeEngine($template = null)
    {
        // @todo
    }

    public function configureEngine()
    {
        // @todo
    }
}
