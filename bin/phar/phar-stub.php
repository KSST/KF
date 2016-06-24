#!/usr/bin/env php
<?php

/**
 * Koch Framework
 * SPDX-FileCopyrightText: 2005-2024 Jens A. Koch
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
