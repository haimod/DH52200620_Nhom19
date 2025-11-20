<?php
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index'); // trả về view resources/views/index.blade.php
});

Route::get('/',function(){
$thietBi = DB::table('thietbi')->get();// Lấy tất cả dữ liệu bảng ThietBi
return view('thietbi',['thietbi' => $thietBi]);// Truyền dữ liệu vào view


});