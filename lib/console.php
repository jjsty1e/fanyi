<?php

use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\ConsoleOutput;

static $output = null;

// for singleton usage.
if ($output) return $output;

$output = new ConsoleOutput();

$output->getFormatter()->setStyle('magenta', new OutputFormatterStyle('magenta', 'default'));
$output->getFormatter()->setStyle('error', new OutputFormatterStyle('red', 'default'));
$output->getFormatter()->setStyle('question', new OutputFormatterStyle('cyan', 'default'));
$output->getFormatter()->setStyle('gray', new OutputFormatterStyle('cyan', 'default'));

return $output;
