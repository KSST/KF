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

namespace Koch\Cron\Jobs;

use Koch\Cron\CronjobInterface;

class Dump implements CronjobInterface
{
    public function execute()
    {
        echo "It's just me, a dumb example of a cronjump. <br/> My name is 'dump.cronjob.php'.";
    }
}
