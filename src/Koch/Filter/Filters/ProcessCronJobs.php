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

namespace Koch\Filter\Filters;

use Koch\Filter\FilterInterface;
use Koch\Http\HttpRequestInterface;
use Koch\Http\HttpResponseInterface;

/**
 * Filter for triggering the processing of cronjobs.
 *
 * Purpose: processes regular jobs (cron-daemon like).
 */
class ProcessCronJobs implements FilterInterface
{
    private $config   = null;
    private $cronjobs = null;

    public function __construct(Koch\Config $config, Koch\Cronjobs $cronjobs)
    {
        $this->config   = $config;
        $this->cronjobs = $cronjobs;
    }

    public function executeFilter(HttpRequestInterface $request, HttpResponseInterface $response)
    {
        // take the initiative, if cronjob processing is enabled in configuration
        if ($this->config['cronjobs']['enabled'] === 1) {
            $this->cronjobs->execute();
        }
    }
}
