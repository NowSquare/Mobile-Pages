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
        $contents = \Storage::disk('media')->listContents('');
        foreach ($contents as $content) {
          if ($content['type'] == 'dir') {
            \Storage::disk('media')->deleteDir($content['path']);
          }
        }
        echo 'media cleared - done!' . PHP_EOL;

        \Artisan::call('config:cache');
        echo 'config:cache - done!' . PHP_EOL;
      
        \Artisan::call('config:clear');
        echo 'config:clear - done!' . PHP_EOL;

        \Artisan::call('cache:clear');
        echo 'cache:clear - done!' . PHP_EOL;

        \Artisan::call('view:clear');
        echo 'view:clear - done!' . PHP_EOL;

        \Artisan::call('key:generate', ['--force' => true]);
        echo 'key:generate - done!' . PHP_EOL;

        \Artisan::call('jwt:secret', ['--force' => true]);
        echo 'jwt:secret - done!' . PHP_EOL;

        \Artisan::call('migrate:refresh', ['--force' => true]);
        echo 'migrate:refresh - done!' . PHP_EOL;

        \Artisan::call('config:cache');
        echo 'config:cache - done!' . PHP_EOL;
      
        \Artisan::call('config:clear');
        echo 'config:clear - done!' . PHP_EOL;

        \Artisan::call('cache:clear');
        echo 'cache:clear - done!' . PHP_EOL;

        \Artisan::call('db:seed', ['--force' => true]);
        echo 'db:seed - done!' . PHP_EOL . PHP_EOL;

        echo 'All done!' . PHP_EOL . PHP_EOL;
    }
}
