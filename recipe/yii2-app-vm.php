<?php

use Symfony\Component\Console\Input\InputOption;

require_once 'yii2-app-basic.php';

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

option('branch', null, InputOption::VALUE_OPTIONAL, 'Branch to deploy.');

task('deploy:publish', function () {
    $dirs = get('shared_dirs');
    foreach ($dirs as $dir) {
        run("mkdir -p {{release_path}}/" . $dir . " && sudo chmod -R 777 {{release_path}}/" . $dir);
    }
    run("cd {{release_path}} && ln -sfn {{release_path}}/web /var/www/{{project}}");
})->desc('Publishing to www');

task('deploy:prerequisites', function () {
    $stages = env('stages');
    foreach ($stages as $stage) {
        run("cd {{release_path}} && touch " . $stage);
    }
});

task('deploy:compile', function () {
    run("cd {{release_path}} && npm install && npm run-script production");
});

after('deploy:update_code', 'deploy:prerequisites');
after('deploy:update_code', 'deploy:compile');
before('cleanup', 'deploy:publish');