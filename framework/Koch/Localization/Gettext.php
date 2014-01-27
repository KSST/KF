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

namespace Koch\Localization;

use Koch\Localization\Adapter\Gettext\Extractor;

/**
 * Class for handling Gettext Extraction.
 *
 * 1. Gettext extraction is normally performed by the "xgettext" tool.
 *    http://www.gnu.org/software/hello/manual/gettext/xgettext-Invocation.html
 *
 * 2. PHP as a platform is still missing essential features of the gettext toolchain.
 *    You wont' find a PECL extension for extraction NOR native PO/MO writing.
 *    Basically everything is missing - except the reading of compiled gettext files (--with-gettext).
 *
 * 3. The missing parts are implemented in PHP:
 *    a) gettext extractor basedon preg_matching.
 *       The extractor matches certain translation functions, like translate('term') or t('term') or _('term')
 *       and their counterparts in templates, often {t('term')} or {_('term')}.
 *    b) POT/PO/MO File Handling = reading and writing.
 */
class Gettext extends Extractor
{
    /**
     * Setup mandatory extractors
     */
    public function __construct()
    {
        // clean up
        $this->removeAllExtractors();

        // set basic extractors for php and smarty template files
        $this->setExtractor('php', 'PHP')
             ->setExtractor('tpl', 'Template');

        // register the tags/functions to extract
        $this->getExtractor('PHP')->addTags(array('translate', 't', '_'));

        // register the tags/placeholders to extract
        $this->getExtractor('Template')->addTags(array('_', 't'));
    }

    /**
     * Scans given files or directories and extracts gettext keys from the content
     *
     * @param string|array $resource
     *
     * @return Koch_Gettext_Extractor
     */
    public function multiScan($resource)
    {
        $this->inputFiles = array();

        if (false === is_array($resource)) {
            $resource = array($resource);
        }

        foreach ($resource as $item) {
            $this->log('Scanning ' . $item);
            $this->scan($item);
        }

        $this->extract($this->inputFiles);

        return $this;
    }
}
