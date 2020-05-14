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
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

foreach ([__DIR__ . '/../../autoload.php', __DIR__ . '/vendor/autoload.php'] as $autoload) {
    if (file_exists($autoload)) {
        require_once $autoload;
    }
}

require 'lib/functions.php';
$print = require 'lib/print.php';

const FANYI_VERSION = 'v0.2.8';

$arguments = $argv;
$source = require_json(__DIR__ . '/lib/source.json');
$app = array_shift($arguments);
$word = urlencode(implode(' ', $arguments));

$inputDef = new InputDefinition();
$inputDef->addArgument(new InputArgument('word', InputArgument::OPTIONAL, 'the word to translate'));
$inputDef->addOption(new InputOption('help', 'h', InputOption::VALUE_OPTIONAL, 'show this message.', false));
$inputDef->addOption(new InputOption('version', 'v', InputOption::VALUE_OPTIONAL, 'show version number', false));

$input = new ArgvInput([$app, $word], $inputDef);
$word = $input->getArgument('word');

if (strlen($word) === 0) {
    if ($input->getOption('version') !== false) {
        return $print->output->writeln(FANYI_VERSION);
    }

    // same as $input->getOption('help') !== false
    $print->output->writeln('');
    $print->output->writeln('<gray>Examples:</gray>');
    $print->output->writeln('<cyan>  $ </cyan>fanyi word');
    $print->output->writeln('<cyan>  $ </cyan>fanyi world peace');
    $print->output->writeln('<cyan>  $ </cyan>fanyi 你好');
    return $print->output->writeln('');
}

try {
    $process = new Process('say ' . urldecode($word));
    $process->start();
}
catch (\Exception $e) {}

$request = new Client();

$promises = [];

// iciba
$promises[] = $request->getAsync(str_replace('${word}', $word, $source->iciba))
    ->then(
        function (ResponseInterface $response) use ($print) {
            if ($response->getStatusCode() === 200) {
                return parse_xml($response->getBody(), function ($err, $result) use ($print) {
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
$promises[] = $request->getAsync(str_replace('${word}', $word, $source->dictionaryapi), ['timeout' => 12])
    ->then(
        function (ResponseInterface $response) use ($print) {
            if ($response->getStatusCode() === 200) {
                return parse_xml($response->getBody(), function ($err, $result) use ($print) {
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
