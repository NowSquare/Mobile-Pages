<?php

use Illuminate\Database\Seeder;
use Platform\Controllers\Core;

use Faker\Factory as Faker;
use Illuminate\Support\Arr;

class DemoContentSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
      Eloquent::unguard();

      $faker = Faker::create();

      $account_id = 1;
      $created_by = 2;

      $site_count = 12;

      $background_colors = ['#EEEEEE', '#EEEEEE', '#EEEEEE', '#e1f5fe'];
      $primary_colors = ['#9b0000', '#eeeeee', '#D7D7D7', '#FBC02D'];
      $primary_text_colors = ['#eeeeee', '#222222', '#0026ca', '#333333'];
      $secondary_colors = ['#d50000', '#ff1744', '#304ffe', '#E64A19'];

      foreach (range(1, $site_count) as $site_id) {
        $site = new Platform\Models\Site;

        $site->account_id = $account_id;
        $site->name = $faker->catchPhrase;
        $site->created_by = $created_by;
        $site->save();

        $site_page_count = mt_rand(4,9);

        foreach (range(1, $site_page_count) as $site_page_id) {
          $sitePage = new Platform\Models\SitePage;

          $sitePage->site_id = $site_id;
          $sitePage->order = $site_page_id - 1;
          $sitePage->title = $faker->catchPhrase;
          $sitePage->content = [
          ];
          $sitePage->created_by = $created_by;
          $sitePage->save();
        }
      }

      Eloquent::reguard();
    }
}
