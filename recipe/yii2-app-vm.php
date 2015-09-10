<?php

require 'yii2-app-basic.php';

// Set up a new path. It is necessary for apps where the source path is not located in the root folder
env('sources_path', '{{release_path}}/sources/web');

// Set up writable paths
set('writable_dirs', ['{{sources_path}}/runtime', '{{sources_path}}/web/assets']);

// Do not use sdo users. It is a bad thing actually.
set('writable_use_sudo', false);

// Overriding vendors task. Composer.json is not located in the root folder
task('deploy:vendors', function () {
    run("cd {{sources_path}} && curl -sS https://getcomposer.org/installer | php");
    run("cd {{sources_path}} && php composer.phar install");
})->desc('Installing vendors');

// Same for migrations
task('deploy:run_migrations', function () {
    run('php {{sources_path}}/yii migrate up --interactive=0');
})->desc('Run migrations');

// Creates a link to /var/www/project
task('publish', function () {
    run("cd {{sources_path}} && ln -sfn {{sources_path}}/web /var/www/{{project}}");
})->desc('Publishing to www');

// Include publishing to the chain of tasks
after('cleanup', 'publish');
