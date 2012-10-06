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

namespace Koch\Debug;

/**
 * Koch Framework - Class for the Initialization of the phpDoctrine Profiler
 * Its an debugging utility.
 *
 * @category    Koch
 * @package     Core
 * @subpackage  Doctrine
 */
class DoctrineProfiler
{
    /**
     * Returns Doctrine's connection profiler
     *
     * @return Doctrine_Connection_Profiler
     */
    public static function getProfiler()
    {
        return Doctrine_Manager::connection()->getListener();
    }

    /**
     * Attached the Doctrine Profiler as Listener to the connection
     */
    public static function attachProfiler()
    {
        // instantiate Profiler and attach to doctrine connection
        Doctrine_Manager::connection()->setListener(new Doctrine_Connection_Profiler);

        register_shutdown_function('Koch_Doctrine_Profiler::shutdown');
    }

    /**
     * Displayes all Doctrine Querys with profiling Informations
     *
     * Because this is debug output, it's ok that direct output breaks the abstraction.
     * @return Direct HTML Output
     */
    public static function displayProfilingHTML()
    {
        /**
         * @var int total number of database queries performed
         */
        $query_counter = 0;

        /**
         * @var int time in seconds, counting the elapsed time for all queries
         */
        $time = 0;

        $html = '';
        $html .= '<!-- Disable Debug Mode to remove this!-->
              <style type="text/css">
              /*<![CDATA[*/
                table.doctrine-profiler {
                    background: none repeat scroll 0 0 #FFFFCC;
                    border-width: 1px;
                    border-style: outset;
                    border-color: #BF0000;
                    border-collapse: collapse;
                    font-size: 11px;
                    color: #222;
                 }
                table.doctrine-profiler th {
                    border:1px inset #BF0000;
                    padding: 3px;
                    padding-bottom: 3px;
                    font-weight: bold;
                    background: #E03937;
                }
                table.doctrine-profiler td {
                    border:1px inset grey;
                    padding: 2px;
                }
                table.doctrine-profiler tr:hover {
                    background: #ffff88;
                }
                fieldset.doctrine-profiler legend {
                    background:#fff;
                    border:1px solid #333;
                    font-weight:700;
                    padding:2px 15px;
                }
                /*]]>*/
                </style>';

        $html .= '<p>&nbsp;</p><fieldset class="doctrine-profiler"><legend>Debug Console for Doctrine Queries</legend>';
        $html .= '<table class="doctrine-profiler" width="95%">';
        $html .= '<tr>
                <th>Query Counter</th>
                <th>Command</th>
                <th>Time</th>
                <th>Query with placeholder (?) for parameters</th>
                <th>Parameters</th>
              </tr>';

        foreach (self::getProfiler() as $event) {
            /**
             * By activiating the following lines, only the "execute" queries are shown.
             * It's usefull for debugging a certain type of database statement.
             */
            /*
            if ($event->getName() != 'execute') {
                continue;
            }
            */

            // increase query counter
            $query_counter++;

            // increase time
            $time += $event->getElapsedSecs();

            $html .= '<tr>';
            $html .= '<td style="text-align: center;">' . $query_counter . '</td>';
            $html .= '<td style="text-align: center;">' . $event->getName() . '</td>';
            $html .= '<td>' . sprintf('%f', $event->getElapsedSecs()) . '</td>';
            $html .= '<td>' . $event->getQuery() . '</td>';

            $params = $event->getParams();
            if (empty($params) == false) {
                $html .= '<td>';
                $html .= wordwrap(join(', ', $params), 150, "\n", true);
                $html .= '</td>';
            } else {
                $html .= '<td>';
                $html .= '&nbsp;';
                $html .= '</td>';
            }

            $html .= '</tr>';
        }

        $html .= '</table>';
        $html .= '<p style="font-weight: bold;">&nbsp; &raquo; &nbsp; '.$query_counter.' statements in ' . sprintf('%2.5f', $time) . ' secs.</p>';
        $html .= '</fieldset>';

        echo $html;
    }

    /**
     * shutdown function for register_shutdown_function
     */
    public static function shutdown()
    {
        // append Doctrine's SQL-Profiling Report
        self::displayProfilingHTML();
    }
}
