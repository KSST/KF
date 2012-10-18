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
 *
 */

namespace Koch\Feed\Generator;

/**
 * RSSCreator20 is a FeedCreator that implements RDF Site Summary (RSS) 2.0.
 *
 * @see http://cyber.law.harvard.edu/rss/rss.html
 */
class RSS20 extends RSS091
{
    public function __construct($identifier = '')
    {
        parent::__construct($identifier);
        parent::setRSSVersion('2.0" xmlns:atom="http://www.w3.org/2005/Atom');
    }
}
