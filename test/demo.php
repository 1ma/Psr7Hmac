<?php

require_once __DIR__.'/../vendor/autoload.php';

// Client code
$request = new GuzzleHttp\Psr7\Request('GET', 'http://example.com');
$signedRequest = OneMA\HMACAuth::sign($request, '$ecr3t');

// Signed request is sent to server...

// Server code
var_dump(OneMA\HMACAuth::verify($signedRequest, '$ecr3t'));


// Again, with Diactoros...
$zendRequest = new \Zend\Diactoros\Request('http://example.com', 'GET');
$zendSignedRequest = OneMA\HMACAuth::sign($request, '$ecr3t');
var_dump(OneMA\HMACAuth::verify($zendSignedRequest, '$ecr3t'));
