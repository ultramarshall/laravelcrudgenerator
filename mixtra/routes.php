<?php
$namespace = '\mixtra\controllers';
/* ROUTER FOR API GENERATOR */
Route::group(['middleware' => ['api', '\mixtra\middlewares\MITAuthAPI'], 'namespace' => 'App\Http\Controllers'], function () {
    //Router for custom api defeault

    $dir = scandir(base_path("app/Http/Controllers"));
    foreach ($dir as $v) {
        $v = str_replace('.php', '', $v);
        $names = array_filter(preg_split('/(?=[A-Z])/', str_replace('Controller', '', $v)));
        $names = strtolower(implode('_', $names));

        if (substr($names, 0, 4) == 'api_') {
            $names = str_replace('api_', '', $names);
            Route::any('api/'.$names, $v.'@execute_api');
        }
    }
});

/* ROUTER FOR UPLOADS */
Route::group(['middleware' => ['web'], 'namespace' => $namespace], function () {
    //Route::get('api-documentation', ['uses' => 'ApiCustomController@apiDocumentation', 'as' => 'apiDocumentation']);
    //Route::get('download-documentation-postman', ['uses' => 'ApiCustomController@getDownloadPostman', 'as' => 'downloadDocumentationPostman']);
    Route::get('uploads/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'FileController@getPreview', 'as' => 'fileControllerPreview']);
});

/* ROUTER FOR WEB */
Route::group([
	'middleware' => ['web'], 
	'prefix' => config('mixtra.ADMIN_PATH'), 
	'namespace' => $namespace,
], function () {
    
    Route::get('login', ['uses' => 'AdminController@getLogin', 'as' => 'getLogin']);
	Route::post('login', ['uses' => 'AdminController@postLogin', 'as' => 'postLogin']);
    Route::get('logout', ['uses' => 'AdminController@getLogout', 'as' => 'getLogout']);

    Route::get('profile', ['uses' => 'AdminController@getProfile', 'as' => 'getProfile']);
    //Route::get('forgot', ['uses' => 'AdminController@getForgot', 'as' => 'getForgot']);

    Route::get('lock-screen', ['uses' => 'AdminController@getLockscreen', 'as' => 'getLockScreen']);
    Route::post('unlock-screen', ['uses' => 'AdminController@postUnlockScreen', 'as' => 'postUnlockScreen']);



});

// ROUTER FOR OWN CONTROLLER FROM CB
Route::group([
    'middleware' => ['web', '\mixtra\middlewares\Backend'],
    'prefix' => config('mixtra.ADMIN_PATH'),
    'namespace' => 'App\Http\Controllers',
], function() use ($namespace) {

    if (Request::is(config('mixtra.ADMIN_PATH'))) {
        $menus = DB::table('mit_menus')
            ->where('is_dashboard', 1)
            ->first();
        if ($menus) {
            if ($menus->type == 'Statistic') {
                Route::get('/', '\mixtra\controllers\StatisticBuilderController@getDashboard');
            } elseif ($menus->type == 'Module') {
                $module = MITBooster::first('mit_modules', ['path' => $menus->path]);
                Route::get('/', $module->controller.'@getIndex');
            } elseif ($menus->type == 'Route') {
                $action = str_replace("Controller", "Controller@", $menus->path);
                $action = str_replace(['Get', 'Post'], ['get', 'post'], $action);
                Route::get('/', $action);
            } elseif ($menus->type == 'Controller & Method') {
                Route::get('/', $menus->path);
            } elseif ($menus->type == 'URL') {
                redirect($menus->path);
            }
        }
    }

    try {
        $modules = DB::table('mit_modules')->where('path', '!=', '')->where('controller', '!=', '')
            ->where('is_protected', 0)->get();
        foreach ($modules as $v) {
            MITBooster::routeController($v->path, $v->controller);
        }
    } catch (Exception $e) {

    }
});

/* ROUTER FOR BACKEND CRUDBOOSTER */
Route::group([
    'middleware' => ['web', '\mixtra\middlewares\Backend'],
    'prefix' => config('mixtra.ADMIN_PATH'),
    'namespace' => $namespace,
], function () {

    /* DO NOT EDIT THESE BELLOW LINES */
    if (Request::is(config('mixtra.ADMIN_PATH'))) {
        $menus = DB::table('mit_menus')->where('is_dashboard', 1)->first();
        if (! $menus) {
            MITBooster::routeController('/', 'AdminController', $namespace = '\mixtra\controllers');
        }
    }

    MITBooster::routeController('api_generator', 'ApiCustomController', $namespace = '\mixtra\controllers');

    try {

        $master_controller = glob(__DIR__.'/controllers/*.php');
        foreach ($master_controller as &$m) {
            $m = str_replace('.php', '', basename($m));
        }

        $moduls = DB::table('mit_modules')->whereIn('controller', $master_controller)->get();

        foreach ($moduls as $v) {
            if (@$v->path && @$v->controller) {
                MITBooster::routeController($v->path, $v->controller, $namespace = '\mixtra\controllers');
            }
        }
    } catch (Exception $e) {

    }
});

