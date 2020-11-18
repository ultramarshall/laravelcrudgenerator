<?php

namespace mixtra;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use mixtra\commands\MITInstallationCommand;
use mixtra\commands\MITUpdateCommand;
use App;

class MixtraServiceProvider extends ServiceProvider
{
    public function boot()
    {
    	$this->loadViewsFrom(__DIR__.'/views', 'mitbooster');
        $this->publishes([__DIR__.'/config/mixtra.php' => config_path('mixtra.php')],'mit_config');            
        $this->publishes([__DIR__.'/lang' => resource_path('lang')], 'mit_localization');                 
        $this->publishes([__DIR__.'/database' => base_path('database')],'mit_migration');
        if(!file_exists(resource_path('views/mixtra/sidebar.blade.php'))) {
            $this->publishes([__DIR__.'/userfiles/views/mixtra/sidebar.blade.php' 
                => resource_path('views/mixtra/sidebar.blade.php')],'mit_sidebar');
        }
        if(!file_exists(app_path('Http/Controllers/UsersController.php'))) {
            $this->publishes([__DIR__.'/userfiles/controllers/UsersController.php' 
                => app_path('Http/Controllers/UsersController.php')],'mit_user_controller');
        }        


        $this->publishes([__DIR__.'/assets'=>public_path('assets')],'mit_asset');  

        require __DIR__.'/validations/validation.php';        
        require __DIR__.'/routes.php';
    }

    public function register()
    {                        
        require __DIR__.'/helpers/Helper.php';     
        $this->mergeConfigFrom(__DIR__.'/config/mixtra.php','mixtra');     

        $this->app->singleton('mit', function ()
        {
            return true;
        });
   
        $this->registerMITCommand();

        $this->commands('mitinstall');
        $this->commands('mitupdate');
        $this->commands(['\mixtra\commands\MITVersionCommand']);


        $this->app->register('Barryvdh\DomPDF\ServiceProvider');
        $this->app->register('Maatwebsite\Excel\ExcelServiceProvider');
        $this->app->register('Unisharp\Laravelfilemanager\LaravelFilemanagerServiceProvider');
        $this->app->register('Intervention\Image\ImageServiceProvider');
           
        $loader = AliasLoader::getInstance();
        $loader->alias('PDF', 'Barryvdh\DomPDF\Facade');
        $loader->alias('Excel', 'Maatwebsite\Excel\Facades\Excel');
        $loader->alias('Image', 'Intervention\Image\Facades\Image');
        $loader->alias('MITBooster', 'mixtra\helpers\MITBooster');
        $loader->alias('MIT', 'mixtra\helpers\MIT');
	}

    private function registerMITCommand()
    {
        $this->app->singleton('mitinstall',function() {
            return new MITInstallationCommand;
        });
        
        $this->app->singleton('mitupdate',function() {
            return new MITUpdateCommand;
        });        
    }    
}
