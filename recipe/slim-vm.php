<?php

require_once __DIR__ . '/common-vm.php';

task('deploy:publish', function () {
    $dirs = get('shared_dirs');
    foreach ($dirs as $dir) {
        run("mkdir -p {{release_path}}/{$dir} && sudo chmod -R 777 {{release_path}}/{$dir}");
    }
    run("cd {{release_path}} && ln -sfn {{release_path}}/public /var/www/{{project}}");
})->desc('Publishing to www');