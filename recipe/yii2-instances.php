<?php

use Deployer\Database\Database;
use Deployer\Deployer;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

option('from', null, InputOption::VALUE_OPTIONAL, 'The source server for copying the instance');

task('copy:dump', function () {
    if (!input()->getOption('from')) {
        throw new Exception('The source server is required');
    }

    $to = Database::loadUsingStage(input()->getArgument('stage'));

    Database::loadUsingStage(input()->getOption('from'))->restore($to->dump());
});

task('copy:restore', function () {
});

task('copy', ['copy:dump', 'copy:restore']);