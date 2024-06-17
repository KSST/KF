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

/**
 *
 */
class JqSelectImage extends Select implements FormElementInterface
{
    private $html = null;

    private $directory;

    /**
     * JQSelectImage uses a simple jquery selection to insert the img src into a preview div.
     */
    public function __construct()
    {
        $this->type = 'image';
    }

    public function getFiles()
    {
        $dir = new \Koch\Files\Directory();

        $files = $dir->getFiles($this->getDirectory());

        return $files;
    }

    /**
     * @return bool
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * Setter for directory.
     */
    public function setDirectory($directory)
    {
        $this->directory = $directory;

        return $this;
    }

    /**
     * Shortcut to $this->setDirectory.
     */
    public function fromDirectory($directory)
    {
        $this->setDirectory($directory);

        return $this;
    }

    public function render()
    {
        $files = $this->getFiles();

        // set "images" hardcoded to identify the select options and append the Name
        parent::setID('images_' . $this->getNameWithoutBrackets());

        if (empty($files)) {
            $this->html = 'There are no images in "' . $this->getDirectory() . '" to select. Please upload some.';
        } else {
            $this->setOptions($files);

            // @todo first image is not displayed... display it.
            // Watch out!
            // the div images/preview must be present in the dom, before you assign js function to it via $('#image')
            $javascript = '<script type="text/javascript">
                           $(document).ready(function () {
                              $("#images_' . $this->getNameWithoutBrackets() . '").change(function () {
                                    var src = $("option:selected", this).val();
                                    $("#imagePreview_' . $this->getNameWithoutBrackets() . '").html(
                                        src ? "<img src=\'" + src + "\'>" : ""
                                    );
                                });
                            });
                            </script>';

            $html = parent::render() . CR . '<div id="imagePreview_' . $this->getNameWithoutBrackets() . '"></div>';

            $this->html = $html . $javascript;
        }

        return $this->html;
    }
}
