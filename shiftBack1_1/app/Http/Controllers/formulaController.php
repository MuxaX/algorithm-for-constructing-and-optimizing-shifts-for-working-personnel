<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class formulaController extends Controller
{
    public function allFormul(){
        return $data = DB::table('loadformulas as l')
        ->join('profession as p', 'l.profession_id', '=', 'p.profession_id')
        ->select('p.profession_name', 'l.formula')
        ->get();
    }

    public function dataType(){
        return $type_order = DB::table('order_type')
        ->select('order_type_id', 'order_type_name')
        ->get();
    }

    public function averTime(int $id_type, int $count_day)
    {
        // Calculate the average time difference
        $averageTimeResult = DB::table('orders as o')
            ->select(DB::raw('AVG(o.order_close - o.order_time) as difference'))
            ->where('o.order_type', $id_type)
            ->where('o.order_time', '>=', now()->subDays($count_day))
            ->first();

        // Count the number of orders in the last 10 days
        $orderCount = DB::table('orders as o')
            ->where('o.order_type', $id_type)
            ->where('o.order_time', '>=', now()->subDays($count_day))
            ->count();

        // Check if the result is not null
        if ($averageTimeResult) {
            return [
                'average_time' => $averageTimeResult->difference,
                'order_count' => $orderCount,
            ];
        } else {
            return [
                'average_time' => null,
                'order_count' => $orderCount,
            ];
        }
    }

    public function dataProf(){
        return $profession = DB::table('profession')
        ->select('profession_id', 'profession_name')
        ->get();
    }

    public function selectProfession(string $name){
        return DB::table('profession')
        ->where('profession_name', $name)
        ->value('profession_id');
    }

    public function createFormule(Request $request){

        $validatedData = $request->validate([
            'profession_id' => 'required|integer',
            'formula' => 'required|string',
        ]);
            DB::table('loadformulas')
                ->insert([
                    'profession_id' => $validatedData['profession_id'],
                    'formula' => $validatedData['formula'],
                ]);

                return response()->json(['message' => 'Formula created successfully'], 201);
    }
}