<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $language = 'en';
        $locale = 'en';

        $plans = [];
        $plans[2000] = ['app_users' => 1, 'apps' => 1];
        $plans[3000] = ['app_users' => 3, 'apps' => 2];
        $plans[4000] = ['app_users' => 6, 'apps' => 3];
        $plans[5000] = ['app_users' => 10, 'apps' => 4];
        $plans[6000] = ['app_users' => 16, 'apps' => 6];
        $plans[7000] = ['app_users' => 24, 'apps' => 8];
        $plans[8000] = ['app_users' => 40, 'apps' => 10];

        foreach ($plans as $price => $adminPlan) {
            $plan = new \Platform\Models\Plan;
            $plan->name = '$' . ($price / 100) . '/mo';
            $plan->role = 3;
            $plan->price = $price;
            $plan->billing_interval = 'month';
            $plan->limitations = [
                'apps' => $adminPlan['apps'],
                'app_users' => $adminPlan['app_users']
            ];
            $plan->created_by = 1;
            $plan->save();
        }

        $user = new \App\User;

        $user->role = 1;
        $user->name = 'Admin';
        $user->email = 'admin@example.com';
        $user->password = bcrypt('welcome123');
        $user->language = $language;
        $user->locale = $locale;
        $user->timezone = config('general.app_timezone');
        $user->app_name = config('app.name');
        $user->app_contact = config('general.mail_contact');
        $user->app_mail_name_from =  config('general.mail_name_from');
        $user->app_mail_address_from =  config('general.mail_address_from');
        $user->app_color = '#304FFE';
        $user->app_host = str_replace(['http://', 'https://'], '', config('general.app_url'));
        $user->save();

        $user = new \App\User;

        $user->role = 2;
        $user->name = 'User';
        $user->email = 'user@example.com';
        $user->password = bcrypt('welcome123');
        $user->language = $language;
        $user->locale = $locale;
        $user->timezone = config('general.app_timezone');
        $user->save();

        Eloquent::unguard();
 
        if (config('app.demo')) {
          $this->call('DemoContentSeeder');
        }

        Eloquent::reguard();
    }
}
