<?php

/**
 * -------------------------------------------------
 * BROWSER
 * -------------------------------------------------
 *
 * Array Structure:
 * - regexp    = regular expression
 * - type       = browser|bot
 * - engine    = browserengine
 * - vparam   = version string name
 * - eparam   = engine string name
 */
return array(
    // Firefox
    'Firefox' =>
    array(
        'regexp' =>
        array(
            0 => '/mozilla.*rv:[0-9\.]+.*gecko\/[0-9]+.*firefox\/([0-9a-z\+\-\.]+).*/si',
        ),
        'type' => 'browser',
        'engine' => 'gecko',
        'vparam' => 'firefox/',
        'eparam' => 'rv:',
    ),
    // Safari
    'Safari' =>
    array(
        'regexp' =>
        array(
            0 => '/mozilla.*applewebkit.*safari\/([0-9a-z\+\-\.]+).*/si',
        ),
        'type' => 'browser',
        'engine' => 'webkit',
        'vparam' => 'version/',
        'eparam' => 'applewebkit/',
    ),
    // Google Chrome
    'Google Chrome' =>
    array(
        'regexp' =>
        array(
            0 => '/\schrome/si',
        ),
        'type' => 'browser',
        'engine' => 'webkit',
        'vparam' => 'chrome/',
        'eparam' => 'applewebkit/',
    ),
    // Opera
    'Opera' =>
    array(
        'regexp' =>
        array(
            0 => '/mozilla.*opera ([0-9a-z\+\-\.]+).*/si',
            1 => '/^opera\/([0-9a-z\+\-\.]+).*/si',
        ),
        'type' => 'browser',
        'engine' => 'presto',
        'vparam' => 'version/',
        'eparam' => 'presto/',
    ),
    // Internet Explorer
    'Internet Explorer' =>
    array(
        'regexp' =>
        array(
            0 => '/microsoft.*internet.*explorer/si',
            1 => '/mozilla.*MSIE ([0-9a-z\+\-\.]+).*/si',
        ),
        'type' => 'browser',
        'engine' => 'trident',
        'vparam' => 'msie ',
        'eparam' => 'trident/',
    ),
    // Konqueror
    'Konqueror' =>
    array(
        'regexp' =>
        array(
            0 => '/mozilla.*konqueror\/([0-9a-z\+\-\.]+).*/si',
        ),
        'type' => 'browser',
        'engine' => 'khtml',
        'vparam' => 'Konqueror/',
        'eparam' => 'KHTML/',
    ),
);
