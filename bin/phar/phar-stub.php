#!/usr/bin/env php
<?php

/**
 * Koch Framework
 * Jens A. Koch © 2005 - onwards
 *
 * This file is part of https://github.com/KSST/KF
* SPDX-License-Identifier: MIT *
 *
 * *
 * *
 * */

Phar::mapPhar('koch-framework.phar');

require_once 'phar://koch-framework.phar/vendor/autoload.php';

// rely on composers autoloader

__HALT_COMPILER();
