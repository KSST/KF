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

use Koch\Form\Elements\File;
use Koch\Form\FormElementInterface;

class UploadAjax extends File implements FormElementInterface
{
    /**
     * This renders a ajax file upload form using jQuery ajaxupload.
     */
    public function render()
    {
        // a) loads the ajaxupload javascript file
        $javascript = '<script src="'.WWW_ROOT_THEMES_CORE . 'javascript/jquery/ajaxupload.js'. '" '
            . 'type="text/javascript"></script>';

        // b) handler for the ajaxupload
        $javascript .= "
        <script type=\"text/javascript\">// <![CDATA[
        $(function(){
                var btnUpload=$(\"#upload\");
                var status=$(\"#upload_status\");
                new AjaxUpload(btnUpload, {
                    action: 'upload-file.php',
                    // name of the file input box
                    name: 'uploadfile',
                    onSubmit: function(file, ext){
                        if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))) {
                            // check for valid file extension
                            status.text('Only JPG, PNG or GIF files are allowed');

                            return false;
                        }
                        status.text('Uploading...');
                    },
                    onComplete: function(file, response){

                        // on completion clear the status
                        status.text('');

                        // add uploaded file to list
                        if (response===\"success\") {
                            $('<li></li>').appendTo('#files')
                            .html('<img src=\"./uploads/'+file+'\" alt=\"\" /><br />'+file)
                            .addClass('success');
                        } else {
                            $('<li></li>').appendTo('#files').text(file).addClass('error');
                        }
                    }
                });
            });
            // ]]></script>";

        // c) css style
        $html = '<STYLE type="text/css">'.CR.'
                 #upload{
                     margin:30px 200px; padding:15px;
                     font-weight:bold; font-size:1.3em;
                     font-family:Arial, Helvetica, sans-serif;
                     text-align:center;
                     background:#f2f2f2;
                     color:#3366cc;
                     border:1px solid #ccc;
                     width:150px;
                     cursor:pointer !important;
                     -moz-border-radius:5px; -webkit-border-radius:5px;
                     }'.CR.
                '</STYLE>';

        // d) output div elements (Button, Status, Files)
        $html .= '<!-- Ajax Upload Button --><div id="upload">Upload File</div>
                  <!-- Ajax Upload Status --><span id="upload_status"></span>
                  <!-- List Files --><ul id="files"></ul>';

        return $javascript.$html;
    }
}
