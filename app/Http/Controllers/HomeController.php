<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $thietbi = DB::table('thietbi')->get();
        return view('index', ['thietbi' => $thietbi]);
    }
}