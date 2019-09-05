<?php

use Illuminate\Database\Seeder;
use Platform\Controllers\Core;

use Faker\Factory as Faker;
use Illuminate\Support\Arr;
use Kalnoy\Nestedset\NestedSet;

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

      $site_count = 6;
      $site_min_pages_count = 4;
      $site_max_pages_count = 8;

      $background_colors = ['#EEEEEE', '#EEEEEE', '#EEEEEE', '#e1f5fe'];
      $primary_colors = ['#9b0000', '#eeeeee', '#D7D7D7', '#FBC02D'];
      $primary_text_colors = ['#eeeeee', '#222222', '#0026ca', '#333333'];
      $secondary_colors = ['#d50000', '#ff1744', '#304ffe', '#E64A19'];
      $secondary_text_colors = ['#ffffff', '#eeeeee'];
      $title_colors = ['#617d8a', '#708a95', '#8197a1'];
      $title_text_colors = ['#ffffff', '#eeeeee'];

      foreach (range(1, $site_count) as $site_id) {
        $site = new Platform\Models\Site;

        $site->account_id = $account_id;
        $site->name = $faker->catchPhrase;
        $site->design = [
          'bgColor' => $background_colors[mt_rand(0, count($background_colors) - 1)],
          'textColor' => $primary_text_colors[mt_rand(0, count($primary_text_colors) - 1)],
          'bgImg' => '',
          'headerBgColor' => $secondary_colors[mt_rand(0, count($secondary_colors) - 1)],
          'headerTextColor' => $secondary_text_colors[mt_rand(0, count($secondary_text_colors) - 1)],
          'titleBarBgColor' => $title_colors[mt_rand(0, count($title_colors) - 1)],
          'titleBarTextColor' => $title_text_colors[mt_rand(0, count($title_text_colors) - 1)]/*,
          'drawerBgColor' => $background_colors[mt_rand(0, count($background_colors) - 1)],
          'drawerTextColor' => $background_colors[mt_rand(0, count($background_colors) - 1)],*/
        ];
        $site->created_by = $created_by;
        $site->saveAsRoot();

        $site_page_count = mt_rand($site_min_pages_count, $site_max_pages_count);

        foreach (range(1, $site_page_count) as $site_page_id) {
          $sitePage = new Platform\Models\Site;

          $sitePage->account_id = $account_id;
          $sitePage->name = str_replace('.', '', $faker->sentence($nbWords = 3, $variableNbWords = true));
          $sitePage->content = [
          ];
          $sitePage->created_by = $created_by;

          $sitePage->appendToNode($site)->save();
        }
      }

      Eloquent::reguard();
    }
}
