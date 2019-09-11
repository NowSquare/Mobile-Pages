<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete cache and migrate and seed database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        # https://stackoverflow.com/questions/49746440/laravel-artisan-use-of-undefined-constant-stdin-assumed-stdin-infinite-loop#comment96889989_49798203
        //if(! defined('STDIN')) define('STDIN', fopen("php://stdin","r"));

        // Clear media dir
        echo PHP_EOL;
        echo 'Empty media folder...';
        $contents = \Storage::disk('media')->listContents('');
        foreach ($contents as $content) {
          if ($content['type'] == 'dir') {
            \Storage::disk('media')->deleteDir($content['path']);
          }
        }
        echo ' done!' . PHP_EOL;

        echo 'Running config:cache...';
        \Artisan::call('config:cache');
        echo ' done!' . PHP_EOL;
      
        echo 'Running config:clear...';
        \Artisan::call('config:clear');
        echo ' done!' . PHP_EOL;

        echo 'Running cache:clear...';
        \Artisan::call('cache:clear');
        echo ' done!' . PHP_EOL;

        echo 'Running view:clear...';
        \Artisan::call('view:clear');
        echo ' done!' . PHP_EOL;

        echo 'Generating app key...';
        \Artisan::call('key:generate', ['--force' => true]);
        echo ' done!' . PHP_EOL;

        echo 'Generating JWT key...';
        \Artisan::call('jwt:secret', ['--force' => true]);
        echo ' done!' . PHP_EOL;

        echo 'Migrating database...';
        \Artisan::call('migrate:refresh', ['--force' => true]);
        echo ' done!' . PHP_EOL;

        echo 'Running config:cache...';
        \Artisan::call('config:cache');
        echo ' done!' . PHP_EOL;
      
        echo 'Running config:clear...';
        \Artisan::call('config:clear');
        echo ' done!' . PHP_EOL;

        echo 'Clearing cache:clear...';
        \Artisan::call('cache:clear');
        echo ' done!' . PHP_EOL;

        echo 'Seeding database...';
        \Artisan::call('db:seed', ['--force' => true]);
        echo ' done!' . PHP_EOL . PHP_EOL;

        echo 'All done!' . PHP_EOL;
    }
}
