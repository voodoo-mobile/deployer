<?php

require 'yii2-app-basic.php';

env('sources_path', '{{release_path}}/sources/web');

set('writable_dirs', ['{{sources_path}}/runtime', '{{sources_path}}/web/assets']);
set('writable_use_sudo', true);

env('branch', function () {
    if (input()->hasOption('branch')) {
        return input()->getOption('branch');
    }
});

env('branch_path', function () {
    $branch = env('branch');
    if (!empty($branch)) {
        $branch = strtolower(str_replace('/', '-', $branch));
    }

    return $branch;
});

option('branch', null, \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL, 'Branch to deploy.');

task('deploy:vendors', function () {
    run("cd {{sources_path}} && curl -sS https://getcomposer.org/installer | php");
    run("cd {{sources_path}} && php composer.phar install");
})->desc('Installing vendors');

task('publish', function () {
    run("mkdir -p -m 777 {{sources_path}}/runtime");
    run("mkdir -p -m 777 {{sources_path}}/web/assets");

    run("cd {{sources_path}} && ln -sfn {{sources_path}}/web /var/www/{{project}}-{{branch_path}}");
})->desc('Publishing to www');

task('deploy:run_migrations', function () {
    run('php {{sources_path}}/yii migrate up --interactive=0');
})->desc('Run migrations');

before('cleanup', 'publish');