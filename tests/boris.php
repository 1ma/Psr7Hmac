#!/usr/bin/env php
<?php

require_once 'phar:///usr/local/bin/boris.phar/lib/autoload.php';
require_once __DIR__.'/../vendor/autoload.php';

set_time_limit(0);


$boris = new Boris\Boris('psr7hmac>');
$boris->start();

