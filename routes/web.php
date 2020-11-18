<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//MITBooster::routeController('/','SurfaceController');

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('test', function () {
	phpinfo();
});

Route::get('fix', function () {
    $table = db::table('our_assemblies')
        ->select('our_bookings_detail_id')
        ->distinct()
        ->get();
	foreach($table as $row) {
        $data = db::table('our_assemblies')
            ->select('id')
            ->where('our_bookings_detail_id',$row->our_bookings_detail_id)
            ->first();
    
        \Helpers\OURHelper::setPerfomance($data->id);
    }
    return "sukses";
});
