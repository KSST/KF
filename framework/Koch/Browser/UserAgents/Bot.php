<?php
/**
 * -------------------------------------------------
 * BOT
 * -------------------------------------------------
 * 
 * Array Structure:
 * - search    = searchstring
 * - type      = browser|bot
 *
 */
return array(
    'Googlebot' =>
    array(
        'regexp' =>
        array(
            '/Googlebot\/([0-9a-z\+\-\.]+).*/si',
            '/Googlebot\-(Image\/[0-9a-z\+\-\.]+).*/si',
        ),
        'type' => 'bot',
    ),
    'MSN Bot' =>
    array(
        'regexp' =>
        array(
            '/msnbot(-media|)\/([0-9a-z\+\-\.]+).*/si',
            '/msnbot\/([0-9a-z\+\-\.]+).*/si',
        ),
        'type' => 'bot',
    ),
);
