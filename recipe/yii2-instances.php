<?php

use Deployer\Database\Database;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

option('from', null, InputOption::VALUE_OPTIONAL, 'The source server for copying the instance');

task('copy:database', function () {
    if (!input()->getOption('from')) {
        throw new Exception('The source server is required');
    }

    $from = input()->getArgument('stage');
    if ($from == 'production') {
        throw new \Exception('That is strictly prohibited to restore the dump to the production server');
    }

    /** @var string $dump */
    $dump = Database::loadUsingStage($from)->dump();
    Database::loadUsingStage(input()->getOption('from'))->restore($dump);

    run("rm -f " . $dump);
});

task('copy', ['copy:database']);