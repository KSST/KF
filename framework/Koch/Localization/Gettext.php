<?php

/**
 * Koch Framework
 * Jens-André Koch © 2005 - onwards
 *
 * This file is part of "Koch Framework".
 *
 * License: GNU/GPL v2 or any later version, see LICENSE file.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Koch\Localization;

use Koch\Localization\Adapter\Gettext\Extractor;

/**
 * Koch Framework - Class for handling Gettext Extraction.
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
     * @return Gettext
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
