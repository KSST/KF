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
 * Filter for updating the visitor statistics.
 *
 * This updates the statistics with the data of the current visitor.
 */
class Statistics implements FilterInterface
{
    private $config;
    private $user;
    private $curTimestamp;
    private $curDate;
    private $statsWhoDeleteTime;
    private $statsWhoTimeout;

    public function __construct(\Koch\Config $config, \Koch\User\User $user)
    {
        $this->config       = $config;
        $this->curTimestamp = time();
        $this->curDate      = date('d.m.Y', $this->curTimestamp);
        $this->user         = $user;

        // Load Models
        Doctrine::loadModels(APPLICATION_MODULES_PATH . 'statistics/model/records/generated/');
        Doctrine::loadModels(APPLICATION_MODULES_PATH . 'statistics/model/records/');

        $cfg                      = $config->readModuleConfig('statistics');
        $this->statsWhoDeleteTime = $cfg['statistics']['deleteTimeWho'];
        $this->statsWhoTimeout    = $cfg['statistics']['timoutWho'];
    }

    public function executeFilter(HttpRequestInterface $request, HttpResponseInterface $response)
    {
        // take the initiative or pass through (do nothing)
        if (isset($this->config['statistics']['enabled']) and $this->config['statistics']['enabled'] === 1) {
            return;
        }

        // @todo aquire pieces of informtion from current visitor
        // Determine the client's browser and system information based on
        // $_SERVER['HTTP_USER_AGENT']

        /*
            * The Who logics, must be processed in a seperate filter
            */
        Doctrine::getTable('CsStatistic')->deleteWhoEntriesOlderThen($this->statsWhoDeleteTime);
        $this->updateStatistics($request->getRemoteAddress());
        $this->updateWhoTables($request->getRemoteAddress(), $request->getRequestURI());
    }

    /**
     * update and/or create/insert a entry to the WhoIs and WhoWasOnline Tables.
     *
     * @param string visitorIp
     * @param string targetSite
     * @param string $visitorIp
     */
    private function updateWhoTables($visitorIp, $targetSite)
    {
        #Original if statement CHECK if user is an admin
        if ($this->user->isUserAuthed()) {
            $this->updateWhoIs($visitorIp, $targetSite, $this->user->getUserIdFromSession());
        } else {
            $this->updateWhoIs($visitorIp, $targetSite);
        }
    }

    /**
     * updateWhoIs.
     *
     * @param string $ip
     * @param $targetSite
     * @param $userID
     */
    private function updateWhoIs($ip, $targetSite, $userID = null)
    {
        $curTimestamp = $this->curTimestamp;

        $result = \Doctrine::getTable('CsStatistic')->updateWhoIsOnline($ip, $targetSite, $curTimestamp, $userID);

        if ($result === 0) {
            \Doctrine::getTable('CsStatistic')->insertWhoIsOnline($ip, $targetSite, $curTimestamp, $userID);
        }
    }

    /**
     * updateStatistics.
     *
     * @param string $visitorIp
     */
    private function updateStatistics($visitorIp)
    {
        // if there is no entry for this ip, increment hits
        if (false === \Doctrine::getTable('CsStatistic')->existsIpEntryWithIp($visitorIp)) {
            \Doctrine::getTable('CsStatistic')->incrementHitsByOne();
            $this->updateStatisticStats();
        }

        $userOnline = \Doctrine::getTable('CsStatistic')->countVisitorsOnline($this->statsWhoTimeout);

        \Doctrine::getTable('CsStatistic')->updateStatisticMaxUsers($userOnline);
    }

    /**
     * updateStatisticStats.
     */
    private function updateStatisticStats()
    {
        if (Doctrine::getTable('CsStatistic')->existsStatsEntryWithDate($this->curDate)) {
            Doctrine::getTable('CsStatistic')->incrementStatsWithDateByOne($this->curDate);
        } else {
            Doctrine::getTable('CsStatistic')->insertStats();
        }
    }
}
