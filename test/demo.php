<?php

require_once __DIR__.'/../vendor/autoload.php';

// Client code
$request = new GuzzleHttp\Psr7\Request('GET', 'http://example.com');
$signedRequest = OneMA\HMACAuth::sign($request, '$ecr3t');

// Signed request is sent to server...

// Server code
var_dump(OneMA\HMACAuth::verify($signedRequest, '$ecr3t'));
