<?php

$output = require 'console.php';
$export = (object)[
    'output' => $output,
    'error' => function ($message) use ($output) {
        $output->writeln("<error>{$message}</error>");
    }
];

$export->iciba = function ($data, $options = null) use ($output) {
    $output->writeln('');

    $output->writeln(
        ' ' . $data->key . '  ' . "<magenta>英[ {$data->ps[0] }] 美[ {$data->ps[1]} ]</magenta>" . '<gray>  ~  iciba.com</gray>'
    );
    $output->writeln('');
    $trans = [];
    foreach ($data->pos as $key => $item) {
        $trans[$key][0] = trim($item);
    }

    foreach ($data->acceptation as $key => $item) {
        $trans[$key][1] = trim($item);
    }
    foreach ($trans as $item) {
        $output->writeln(" - <info>{$item[0]} {$item[1]}</info>");
    }
    $output->writeln('');
    foreach ($data->sent as $key => $item) {
        $key++;
        $orig = trim(str_replace($data->key, "<comment>{$data->key}</comment>", $item->orig));
        $orig = trim(str_replace(ucfirst($data->key), "<comment>" . ucfirst($data->key) . "</comment>", $orig));
        $trans = trim($item->trans);
        $output->writeln(" {$key}. {$orig}");
        $output->writeln(str_pad(' ', 4) . "<question>{$trans}</question>");
    }

    $output->writeln('');
    $output->writeln('  --------');
};

$export->youdao = function ($data, $options = null) use ($output) {
    $output->writeln('');
    $output->writeln(
        ' ' . $data->query . '  ' . "<magenta>英[ {$data->basic->{'uk-phonetic'}}] 美[ {$data->basic->{'us-phonetic'}} ]</magenta>"
        . '<gray>  ~  fanyi.youdao.com</gray>'
    );
    $output->writeln('');
    foreach ($data->basic->explains as $item) {
        $output->writeln(" - <info>{$item}</info>");
    }
    $output->writeln('');
    foreach ($data->web as $index => $item) {
        $index++;
        $key = trim(str_replace($data->query, "<comment>{$data->query}</comment>", $item->key));
        $key = trim(str_replace(ucfirst($data->query), "<comment>" . ucfirst($data->query) . "</comment>", $key));
        $value = implode(', ', $item->value);
        $output->writeln(" {$index}. {$key}");
        $output->writeln(str_pad(' ', 4) . "<question>{$value}</question>");
    }
    $output->writeln('');
    $output->writeln('  --------');
};

$export->dictionaryapi = function ($data, $options = null) use ($output) {
    $output->writeln('');
    $word = $data->entry[0]->hw;
    $output->writeln(' ' . $word . '<gray>  ~   dictionaryapi.com</gray>');
    $output->writeln('');
    foreach ($data->entry as $item) {
        if (!isset($item->def) || !isset($item->def->dt)) continue;
        $strings = $item->def->dt;
        $outputString = function ($query, $string) use ($output){
            $string = trim(str_replace($query, "<comment>{$query}</comment>", $string));
            $string = trim(str_replace(ucfirst($query), "<comment>" . ucfirst($query) . "</comment>", $string));
            return $output->writeln(" - <info>{$string}</info>");
        };
        if (is_string($strings)) {
            if ($strings = trim($strings, ' :')) {
                call_user_func($outputString, $word, $strings);
            }
            continue;
        }
        $strings = array_filter($strings, function ($val) {
            return is_string($val) && strlen(trim($val, ' :')) > 0;
        });
        $strings = array_map(function ($val) {
            return trim($val, ' :');
        }, $strings);
        foreach ($strings as $string) {
            call_user_func($outputString, $word, $string);
        }
    }
    $output->writeln('');
    $output->writeln('  --------');
};

return $export;
