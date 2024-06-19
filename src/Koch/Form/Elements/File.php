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

class File extends Input implements FormElementInterface
{
    /**
     * Flag variable for the uploadType.
     *
     * There are several different formelements available to upload files:
     *
     * 1) Ajaxupload    -> UploadAjax.php
     * 2) APC           -> UploadApc.php
     * 3) Uploadify     -> Uploadify.php
     * 4) Default HTML  -> this class
     *
     * @string
     */
    protected $uploadType;

    public function __construct()
    {
        $this->type = 'file';

        // Watch out, that the opening form tag needs the enctype="multipart/form-data"
        // else you'll get the filename only and not the content of the file.
        // Correct encoding is automatically set, when using $form->addElement() method.
    }

    /**
     * Flag variable for the uploadType.
     *
     * There are several different formelements available to upload files:
     *
     * @param $uploadType ajaxupload, apc, uploadify, html
     *
     * @return File \Koch\Form\Element\File
     */
    public function setUploadType($uploadType)
    {
        $this->uploadType = $uploadType;

        return $this;
    }

    public function render()
    {
        switch ($this->uploadType) {
            default:
            case 'ajaxupload':
                return new \Koch\Form\Elements\UploadAjax();
            case 'apc':
                return new \Koch\Form\Elements\UploadApc();
            case 'uploadify':
                return new \Koch\Form\Elements\Uploadify();
            case 'html':
                /*
                 * Fallback to normal <input type="file"> upload
                 * Currently not using the render method of the parent class
                 * return parent::render();
                 */
                return '<input type="file" name="file[]" multiple="true">';
        }
    }

    /**
     * Magic-Method for rendering the subclass formelements.
     *
     * The render method needs a bit magic to render formelement objects directly.
     * See the short returns calls like the following above:
     *
     *      return new Koch\Form\Element\UploadAjax();
     *
     * The long form is:
     *
     *      $formelement = new Koch\Form\Element\UploadAjax();
     *      $formelement->render();
     */
    public function __toString(): string
    {
        return $this->render();
    }
}
