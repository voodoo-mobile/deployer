<?php

require 'yii2-app-basic.php';

set('shared_dirs', [
    'runtime',
    'web/assets',
    'web/uploads',
]);

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

task('publish', function () {
    $dirs = get('shared_dirs');
    foreach ($dirs as $dir) {
        run("mkdir -p {{release_path}}/" . $dir . " && sudo chmod -R 777 {{release_path}}/" . $dir);
    }
    run("cd {{release_path}} && ln -sfn {{release_path}}/web /var/www/{{branch_path}}");
})->desc('Publishing to www');

task('deploy:prerequisites', function () {
    $stages = env('stages');
    foreach ($stages as $stage) {
        run("cd {{release_path}} && touch " . $stage);
    }
});

after('deploy:update_code', 'deploy:prerequisites');
before('cleanup', 'publish');