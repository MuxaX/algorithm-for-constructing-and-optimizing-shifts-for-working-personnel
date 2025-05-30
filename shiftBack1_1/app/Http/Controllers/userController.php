<?php
 
namespace App\Http\Controllers;
 
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class userController extends Controller
{
    public function selus(){

        return DB::table('employee')->get();
    }
}