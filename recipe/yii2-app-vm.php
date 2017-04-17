<?php

require_once __DIR__ . '/common-vm.php';

set('shared_dirs', [
    'runtime',
    'web/assets',
    'web/uploads',
]);

/**
 * Run migrations
 */
task('deploy:run_migrations', function () {
    run('php {{release_path}}/yii migrate up --interactive=0');
})->desc('Run migrations');

after('deploy:vendors', 'deploy:run_migrations');
