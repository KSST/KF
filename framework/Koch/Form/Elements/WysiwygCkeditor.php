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

namespace Koch\Form\Elements;

use Koch\Form\FormElementInterface;

/**
 * Formelement_WysiwygCkeditor.
 *
 * @see http://ckeditor.com/ Official Website of CKeditor
 * @see http://docs.cksource.com/ CKEditor Documentations
 * @see http://docs.cksource.com/CKEditor_3.x/Developers_Guide/Integration
 */
class WysiwygCkeditor extends Textarea implements FormElementInterface
{
    public function __construct()
    {
        self::checkDependencies();
    }

    /**
     * Ensure, that the library is available, before the client requests a non-existant file.
     */
    public static function checkDependencies()
    {
        if (!is_file(APPLICATION_PATH . 'themes/core/javascript/ckeditor/ckeditor.js')) {
            throw new \Koch\Exception\Exception('Ckeditor Javascript Library missing!');
        }
    }

    /**
     * This renders a textarea with the WYSWIWYG editor ckeditor attached.
     */
    public function render()
    {
        // a) loads the ckeditor javascript files
        $javascript = '<script type="text/javascript"';
        $javascript .= ' src="' . WWW_ROOT_THEMES_CORE . 'javascript/ckeditor/ckeditor.js"></script>';

        // b) plug it to an specific textarea by ID
        // This script block must be included at any point "after" the <textarea> tag in the page.
        $javascript .= '<script type="text/javascript">
                                CKEDITOR.replace("' . $this->getName() . '");
                        </script>';

        // Watch out! Serve html elements first, before javascript dom selections are applied on them!
        return $javascript;
    }
}
