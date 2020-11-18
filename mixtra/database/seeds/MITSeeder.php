<?php

use Illuminate\Database\Seeder;

class MITSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Please wait updating the data...');

        $this->call('MITUsersSeeder');
        $this->call('MITModulesSeeder');
        $this->call('MITPrivilegesSeeder');
        $this->call('MITSettingsSeeder');

        $this->command->info('Updating the data completed !');
    }
}

class MITSettingsSeeder extends Seeder
{
    public function run()
    {

        $data = [

            //LOGIN REGISTER STYLE
            [
                'created_at' => date('Y-m-d H:i:s'),
                'name' => 'login_background_image',
                'label' => 'Login Background Image',
                'content' => null,
                'content_input_type' => 'upload_image',
                'group_setting' => trans('mixtra.login_register_style'),
                'dataenum' => null,
                'helper' => null,
            ],

            //APPLICATION SETTING
            [
                'created_at' => date('Y-m-d H:i:s'),
                'name' => 'appname',
                'label' => 'Application Name',
                'group_setting' => trans('mixtra.application_setting'),
                'content' => 'MIXTRA FRAMEWORK',
                'content_input_type' => 'text',
                'dataenum' => null,
                'helper' => null,
            ],
            [
                'created_at' => date('Y-m-d H:i:s'),
                'name' => 'logo_dark',
                'label' => 'Logo Dark',
                'content' => '',
                'content_input_type' => 'upload_image',
                'group_setting' => trans('mixtra.application_setting'),
                'dataenum' => null,
                'helper' => 'PNG File (Recomended Size: 250 x 132px, 72dpi)',
            ],
            [
                'created_at' => date('Y-m-d H:i:s'),
                'name' => 'logo_light',
                'label' => 'Logo Light',
                'content' => '',
                'content_input_type' => 'upload_image',
                'group_setting' => trans('mixtra.application_setting'),
                'dataenum' => null,
                'helper' => 'PNG File (Recomended Size: 40 x 40px, 72dpi)',
            ],
            [
                'created_at' => date('Y-m-d H:i:s'),
                'name' => 'logo_light_text',
                'label' => 'Logo Light Text',
                'content' => '',
                'content_input_type' => 'upload_image',
                'group_setting' => trans('mixtra.application_setting'),
                'dataenum' => null,
                'helper' => 'PNG File (Recomended Size: 108 x 21px, 72dpi)',
            ],
            [
                'created_at' => date('Y-m-d H:i:s'),
                'name' => 'favicon',
                'label' => 'Favicon',
                'content' => '',
                'content_input_type' => 'upload_image',
                'group_setting' => trans('mixtra.application_setting'),
                'dataenum' => null,
                'helper' => 'PNG File (Recomended Size: 32 x 32px, 72dpi)',
            ],
            [
                'created_at' => date('Y-m-d H:i:s'),
                'name' => 'api_debug_mode',
                'label' => 'API Debug Mode',
                'content' => 'true',
                'content_input_type' => 'select',
                'group_setting' => trans('mixtra.application_setting'),
                'dataenum' => 'true,false',
                'helper' => 'PNG File (Recomended Size: 32 x 32px, 72dpi)',
                'helper' => null,
            ],
            //EMAIL SETTING
            [
                'created_at' => date('Y-m-d H:i:s'),
                'name' => 'email_sender',
                'label' => 'Email Sender',
                'content' => 'support@mixtra.co.id',
                'content_input_type' => 'text',
                'group_setting' => trans('mixtra.email_setting'),
                'dataenum' => null,
                'helper' => null,
            ],
            [
                'created_at' => date('Y-m-d H:i:s'),
                'name' => 'smtp_driver',
                'label' => 'Mail Driver',
                'content' => 'mail',
                'content_input_type' => 'select',
                'group_setting' => trans('mixtra.email_setting'),
                'dataenum' => 'smtp,mail,sendmail',
                'helper' => null,
            ],
            [
                'created_at' => date('Y-m-d H:i:s'),
                'name' => 'smtp_host',
                'label' => 'SMTP Host',
                'content' => '',
                'content_input_type' => 'text',
                'group_setting' => trans('mixtra.email_setting'),
                'dataenum' => null,
                'helper' => null,
            ],
            [
                'created_at' => date('Y-m-d H:i:s'),
                'name' => 'smtp_port',
                'label' => 'SMTP Port',
                'content' => '25',
                'content_input_type' => 'text',
                'group_setting' => trans('mixtra.email_setting'),
                'dataenum' => null,
                'helper' => 'default 25',
            ],
            [
                'created_at' => date('Y-m-d H:i:s'),
                'name' => 'smtp_username',
                'label' => 'SMTP Username',
                'content' => '',
                'content_input_type' => 'text',
                'group_setting' => trans('mixtra.email_setting'),
                'dataenum' => null,
                'helper' => null,
            ],
            [
                'created_at' => date('Y-m-d H:i:s'),
                'name' => 'smtp_password',
                'label' => 'SMTP Password',
                'content' => '',
                'content_input_type' => 'text',
                'group_setting' => trans('mixtra.email_setting'),
                'dataenum' => null,
                'helper' => null,
            ],
        ];

        foreach ($data as $row) {
            $count = DB::table('mit_settings')->where('name', $row['name'])->count();
            if ($count) {
                if ($count > 1) {
                    $newsId = DB::table('mit_settings')->where('name', $row['name'])->orderby('id', 'asc')->take(1)->first();
                    DB::table('mit_settings')->where('name', $row['name'])->where('id', '!=', $newsId->id)->delete();
                }
                continue;
            }
            DB::table('mit_settings')->insert($row);
        }
    }
}


class MITPrivilegesSeeder extends Seeder
{
    public function run()
    {

        if (DB::table('mit_privileges')->where('name', 'Super Administrator')->count() == 0) {
            DB::table('mit_privileges')->insert([
                'created_at' => date('Y-m-d H:i:s'),
                'name' => 'Super Administrator',
                'is_superadmin' => 1,
                'theme_color' => 'skin-blue',
            ]);
        }
    }
}

class MITModulesSeeder extends Seeder
{
    public function run()
    {
        $data = [

            [
                'created_at' => date('Y-m-d H:i:s'),
                'name' => trans('mixtra.settings'),
                'icon' => 'fa fa-cog',
                'path' => 'settings',
                'table_name' => 'mit_settings',
                'controller' => 'SettingsController',
                'is_protected' => 1,
                'is_active' => 1,
            ],
            [
                'created_at' => date('Y-m-d H:i:s'),
                'name' => trans('mixtra.Modules'),
                'icon' => 'fa fa-database',
                'path' => 'module',
                'table_name' => 'mit_modules',
                'controller' => 'ModulesController',
                'is_protected' => 1,
                'is_active' => 1,
            ],
            [
                'created_at' => date('Y-m-d H:i:s'),
                'name' => trans('mixtra.Log_User_Access'),
                'icon' => 'fa fa-flag',
                'path' => 'logs',
                'table_name' => 'mit_logs',
                'controller' => 'LogsController',
                'is_protected' => 1,
                'is_active' => 1,
            ],
            [
                'created_at' => date('Y-m-d H:i:s'),
                'name' => trans('mixtra.Users_Management'),
                'icon' => 'fa fa-users',
                'path' => 'users',
                'table_name' => 'mit_users',
                'controller' => 'UsersController',
                'is_protected' => 0,
                'is_active' => 1,
            ],
            [
                'created_at' => date('Y-m-d H:i:s'),
                'name' => trans('mixtra.Privileges'),
                'icon' => 'fa fa-cog',
                'path' => 'privileges',
                'table_name' => 'mit_privileges',
                'controller' => 'PrivilegesController',
                'is_protected' => 1,
                'is_active' => 1,
            ],
            [
                'created_at' => date('Y-m-d H:i:s'),
                'name' => trans('mixtra.Menu_Management'),
                'icon' => 'fa fa-bars',
                'path' => 'menus',
                'table_name' => 'mit_menus',
                'controller' => 'MenusController',
                'is_protected' => 1,
                'is_active' => 1,
            ],
            [
                'created_at' => date('Y-m-d H:i:s'),
                'name' => trans('mixtra.Notifications'),
                'icon' => 'fa fa-cog',
                'path' => 'notifications',
                'table_name' => 'mit_notifications',
                'controller' => 'NotificationsController',
                'is_protected' => 1,
                'is_active' => 1,
            ],
            [
                'created_at' => date('Y-m-d H:i:s'),
                'name' => trans('mixtra.Email_Templates'),
                'icon' => 'fa fa-envelope-o',
                'path' => 'email_template',
                'table_name' => 'mit_email_templates',
                'controller' => 'EmailTemplatesController',
                'is_protected' => 1,
                'is_active' => 1,
            ],
            // [
            //     'created_at' => date('Y-m-d H:i:s'),
            //     'name' => trans('mixtra.Statistic_Builder'),
            //     'icon' => 'fa fa-dashboard',
            //     'path' => 'statistic_builder',
            //     'table_name' => 'cms_statistics',
            //     'controller' => 'StatisticBuilderController',
            //     'is_protected' => 1,
            //     'is_active' => 1,
            // ],
            [

                'created_at' => date('Y-m-d H:i:s'),
                'name' => trans('mixtra.API_Generator'),
                'icon' => 'fa fa-cloud-download',
                'path' => 'api_generator',
                'table_name' => '',
                'controller' => 'ApiCustomController',
                'is_protected' => 1,
                'is_active' => 1,
            ],

        ];

        foreach ($data as $k => $d) {
            if (DB::table('mit_modules')->where('name', $d['name'])->count()) {
                unset($data[$k]);
            }
        }

        DB::table('mit_modules')->insert($data);
    }
}

class MITUsersSeeder extends Seeder
{
    public function run()
    {

        if (DB::table('mit_users')->count() == 0) {
            $password = \Hash::make('123456');
            $mit_users = DB::table('mit_users')->insert([
                'created_at' => date('Y-m-d H:i:s'),
                'name' => 'Super Admin',
                'username' => 'administrator',
                'password' => $password,
                'mit_privileges_id' => 1,
            ]);
        }
    }
}

