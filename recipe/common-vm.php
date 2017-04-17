<?php

require_once __DIR__ . '/common.php';

set('writable_use_sudo', true);
set('default_branch', 'develop');

task('deploy:publish', function () {
    $dirs = get('shared_dirs');
    foreach ($dirs as $dir) {
        run("mkdir -p {{release_path}}/{$dir} && sudo chmod -R 777 {{release_path}}/{$dir}");
    }
    run("cd {{release_path}} && ln -sfn {{release_path}}/web /var/www/{{project}}");

    $stages = env('stages');
    foreach ($stages as $stage) {
        run("cd {{release_path}}/web && [[ -e index-{$stage}.php ]] && cp -f index-{$stage}.php index.php");
    }
})->desc('Publishing to www');

task('deploy:prerequisites', function () {
    $stages = env('stages');
    foreach ($stages as $stage) {
        run("cd {{release_path}} && touch {$stage}");
    }
});

task('deploy', [
    'deploy:prepare',
    'deploy:release',
    'deploy:update_code',
    'deploy:prerequisites',
    'deploy:shared',
    'deploy:vendors',
    'deploy:symlink',
    'deploy:publish',
    'cleanup',
])->desc('Deploy your project');

after('deploy', 'success');