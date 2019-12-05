<?php

/**
 * This file is part of project fanyi.
 *
 * this is a PHP port of project afc163/fanyi
 *
 * Author: Jake
 * Create: 2019-12-04 18:50:27
 */

use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use Symfony\Component\Process\Process;

require 'vendor/autoload.php';
require 'lib/functions.php';
$print = require 'lib/print.php';

$source = require_json('lib/source.json');

$word = $argv[1];

$process = new Process('say ' . $word);
$process->start();

$request = new Client();

$promises = [];

// iciba
$promises[] = $request->getAsync(str_replace('${word}', $word, $source->iciba))
    ->then(
        function (ResponseInterface $response) use ($print) {
            if ($response->getStatusCode() === 200) {
                return parseXml($response->getBody(), function ($err, $result) use ($print) {
                    if (!$err) {
                        return call_user_func($print->iciba, $result);
                    }
                });
            }
        }, $print->error
    );

// youdao
$promises[] = $request->getAsync(str_replace('${word}', $word, $source->youdao))
    ->then(
        function (ResponseInterface $response) use ($print) {
            if ($response->getStatusCode() === 200) {
                if (($result = json_decode($response->getBody()))) {
                    return call_user_func($print->youdao, $result);
                }
            }
        }, $print->error
    );

// dictionaryapi
$promises[] = $request->getAsync(str_replace('${word}', $word, $source->dictionaryapi), ['timeout' => 6])
    ->then(
        function (ResponseInterface $response) use ($print) {
            if ($response->getStatusCode() === 200) {
                return parseXml($response->getBody(), function ($err, $result) use ($print) {
                    if ($err) {
                        return null;
                    }

                    return call_user_func($print->dictionaryapi, $result);
                });
            }
        }, $print->error
    );

Promise\settle($promises)->wait();
$process->wait();
