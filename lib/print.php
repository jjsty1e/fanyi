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
    $ukPs = $usPs = $ps = '';

    if (!empty($data->ps)) {
        if (is_array($data->ps)) {
            if (!empty($data->ps[0])) $ukPs = "英[ {$data->ps[0]} ] ";
            if (!empty($data->ps[1])) $usPs = "美[ {$data->ps[1]} ] ";
        } else {
            $usPs = "美[ {$data->ps} ] ";
        }
    }

    if ($ukPs || $usPs) {
        $ps = '  ' . "<magenta>{$ukPs}{$usPs}</magenta>";
    }
    $output->write(' <bold>' . $data->key . '</bold>');
    $output->writeln($ps . '<gray>  ~  iciba.com</gray>');
    $output->writeln('');

    if (!empty($data->pos)) {
        $trans = [];
        if (is_array($data->pos)) {
            foreach ($data->pos as $key => $item) {
                $trans[$key][0] = trim($item);
            }

            foreach ($data->acceptation as $key => $item) {
                $trans[$key][1] = trim($item);
            }
        }
        if (is_string($data->pos)) {
            $trans[] = [$data->pos, (string)$data->acceptation];
        }
        if (!empty($trans)) {
            foreach ($trans as $item) {
                $item = array_map('trim', $item);
                $output->writeln(" - <info>{$item[0]} {$item[1]}</info>");
            }
            $output->writeln('');
        }
    }

    if (!empty($data->sent)) {
        foreach ($data->sent as $key => $item) {
            $key++;
            $orig = trim(str_replace($data->key, "<comment>{$data->key}</comment>", $item->orig));
            $orig = trim(str_replace(ucfirst($data->key), "<comment>" . ucfirst($data->key) . "</comment>", $orig));
            $trans = trim($item->trans);
            $output->writeln(" {$key}. {$orig}");
            $output->writeln(str_pad(' ', 4) . "<question>{$trans}</question>");
        }
    }

    $output->writeln('');
    $output->writeln('  --------');
};

$export->youdao = function ($data, $options = null) use ($output) {
    $output->writeln('');
    $cnPtic = $hkPtic = $usPtic = $ptic = '';
    if (!empty($data->basic->{'uk-phonetic'})) $hkPtic = "英[ {$data->basic->{'uk-phonetic'}} ] ";
    if (!empty($data->basic->{'us-phonetic'})) $usPtic = "美[ {$data->basic->{'us-phonetic'}} ] ";
    if (empty($data->basic->{'uk-phonetic'}) &&
        empty($data->basic->{'us-phonetic'}) &&
        !empty($data->basic->phonetic)
    ) {
        $cnPtic = "[ {$data->basic->phonetic} ] ";
    }
    if ($hkPtic || $usPtic) {
        $ptic = '  ' . "<magenta>$cnPtic{$hkPtic}{$usPtic}</magenta>";
    }
    $output->write(' <bold>' . $data->query . '</bold>');
    $output->writeln($ptic . '<gray>  ~  fanyi.youdao.com</gray>');
    $output->writeln('');

    if (!empty($data->basic->explains)) {
        foreach ($data->basic->explains as $item) {
            $output->writeln(" - <info>{$item}</info>");
        }
        $output->writeln('');
    }

    if (!empty($data->web)) {
        foreach ($data->web as $index => $item) {
            $index++;
            $key = trim(str_replace($data->query, "<comment>{$data->query}</comment>", $item->key));
            $key = trim(str_replace(ucfirst($data->query), "<comment>" . ucfirst($data->query) . "</comment>", $key));
            $value = implode(', ', $item->value);
            $output->writeln(" {$index}. {$key}");
            $output->writeln(str_pad(' ', 4) . "<question>{$value}</question>");
        }
    }

    if (!empty($data->translation)) {
        $data->translation = (array)$data->translation;
        foreach ($data->translation as $trans) {
            $output->writeln(" - <info>{$trans}</info>");
        }
    }

    $output->writeln('');
    $output->writeln('  --------');
};

$export->dictionaryapi = function ($data, $options = null) use ($output) {
    $output->writeln('');
    if (empty($data->entry)) return null;
    if (!is_array($data->entry)) {
        $data->entry = [$data->entry];
    }
    $word = $data->entry[0]->hw;
    $output->writeln(' ' . $word . '<gray>  ~   dictionaryapi.com</gray>');
    $output->writeln('');
    $allStrings = [];
    foreach ($data->entry as $key =>  $item) {
        if (!isset($item->def) || !isset($item->def->dt)) continue;
        $strings = $item->def->dt;
        if (is_string($strings)) {
            $strings = [$strings];
        }
        $strings = array_filter($strings, function ($val) {
            return is_string($val) && strlen(trim($val, ' :')) > 0;
        });
        $strings = array_map(function ($val) {
            return trim($val, ' :');
        }, $strings);
        $allStrings = array_merge($allStrings, $strings);
    }

    foreach ($allStrings as $key => $string) {
        if ($key <= 9) {
            $string = trim(str_replace($word, "<comment>{$word}</comment>", $string));
            $string = trim(str_replace(ucfirst($word), "<comment>" . ucfirst($word) . "</comment>", $string));
            $output->writeln(" - <info>{$string}</info>");
        }
    }

    $output->writeln('');
    $output->writeln('  --------');
};

return $export;
