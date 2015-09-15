<?php

require 'yii2-app-basic.php';

env('sources_path', '{{release_path}}/sources/web');

$dirs = [
    '{{sources_path}}/runtime',
    '{{sources_path}}/web/assets',
    '{{sources_path}}/web/uploads',
    '{{sources_path}}/rbac'
];

set('shared_dirs', $dirs);
set('writable_dirs', $dirs);

set('writable_use_sudo', true);
set('default_branch', 'develop');

env('branch', function () {
    if (input()->hasOption('branch')) {
        return input()->getOption('branch');
    } else {
        return get('default_branch');
    }
});

env('branch_path', function () {
    $branch = env('branch');

    if (!empty($branch) && $branch != get('default_branch')) {
        return '{{project}}-' . strtolower(str_replace('/', '-', $branch));
    }

    return '{{project}}';
});

option('branch', null, \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL, 'Branch to deploy.');

task('deploy:vendors', function () {
    run("cd {{sources_path}} && curl -sS https://getcomposer.org/installer | php");
    run("cd {{sources_path}} && php composer.phar install --prefer-dist");
})->desc('Installing vendors');

task('publish', function () {
    run("cd {{sources_path}} && ln -sfn {{sources_path}}/web /var/www/{{branch_path}}");
})->desc('Publishing to www');

task('deploy:run_migrations', function () {
    run('php {{sources_path}}/yii migrate up --interactive=0');
})->desc('Run migrations');

task('deploy:prerequisites', function () {
    $stages = env('stages');
    foreach ($stages as $stage) {
        run("cd {{sources_path}} && touch " . $stage);
    }
});

after('deploy:update_code', 'deploy:prerequisites');
before('cleanup', 'publish');