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

require 'vendor/autoload.php';
require 'lib/functions.php';
$print = require 'lib/print.php';

$arguments = $argv;
$source = require_json('lib/source.json');
$app = array_shift($arguments);
$word = urlencode(implode(' ', $arguments));

$inputDef = new InputDefinition();
$inputDef->addArgument(new InputArgument('word', InputArgument::OPTIONAL, 'the word to translate'));
$inputDef->addOption(new InputOption('help', 'h', InputOption::VALUE_OPTIONAL, 'show this message.', false));
$inputDef->addOption(new InputOption('version', 'v', InputOption::VALUE_OPTIONAL, 'show version number', false));

$input = new ArgvInput([$app, $word], $inputDef);
$word = $input->getArgument('word');

if (strlen($word) === 0 || $input->getOption('help') !== false) {
    $print->output->writeln('');
    $print->output->writeln('<gray>Examples:</gray>');
    $print->output->writeln('<cyan>  $ </cyan>fanyi word');
    $print->output->writeln('<cyan>  $ </cyan>fanyi world peace');
    $print->output->writeln('<cyan>  $ </cyan>fanyi 你好');
    return $print->output->writeln('');
}

if ($input->getOption('version') !== false) {
    return $print->output->writeln(require_json('composer.json')->version);
}

$process = new Process('say ' . urldecode($word));
$process->start();

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
$promises[] = $request->getAsync(str_replace('${word}', $word, $source->dictionaryapi), ['timeout' => 6])
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
