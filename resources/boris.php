#!/usr/bin/env php
<?php

// This debugging script is meant to be run from inside a development 1maa/php-cli Docker container.

// Alternatively you can put a boris.phar in the expected path of your system,
// or simply change the require_once to point somewhere else.

require_once 'phar:///usr/local/bin/boris.phar/lib/autoload.php';
require_once __DIR__.'/../vendor/autoload.php';

set_time_limit(0);

$boris = new Boris\Boris('psr7hmac>');
$boris->start();
