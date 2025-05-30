<?php
 
namespace App\Http\Controllers;
 
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class orderController extends Controller
{
    public function orderCount(string $date, int $type_id)
    {

        $selectedDate = Carbon::createFromFormat('Y-m-d', $date)->startOfDay();
        // Convert Carbon instances to strings in the correct format
        $startDateString = $selectedDate->toDateTimeString();

        $nextDate = $selectedDate->addDay();
        $endDateString = $nextDate->toDateTimeString();

        if($type_id == null || $type_id == 0){
            return $orders = DB::table('orders')
            ->whereBetween('order_time', [
                $startDateString,
                $endDateString,
            ])
            ->select(
                DB::raw("TO_CHAR(order_time, 'YYYY-MM-DD') AS order_date"),
                DB::raw("TO_CHAR(order_time, 'HH24') AS order_hour"),
                DB::raw("COUNT(*) AS order_count")
            )
            ->groupBy(
                DB::raw("TO_CHAR(order_time, 'YYYY-MM-DD')"),
                DB::raw("TO_CHAR(order_time, 'HH24')")
            )
            ->orderBy('order_date', 'asc')
            ->orderBy('order_hour', 'asc')
            ->get();
            }
            else{
                return $result = DB::table('orders as o')
                ->selectRaw("TO_CHAR(o.order_time, 'YYYY-MM-DD') AS order_date")
                ->selectRaw("TO_CHAR(o.order_time, 'HH24') AS order_hour")
                ->selectRaw("COUNT(*) AS order_count")
                ->where('o.order_time', '>=', $startDateString)
                ->where('o.order_time', '<', $endDateString)
                ->where('o.order_type', $type_id)
                ->groupByRaw("TO_CHAR(o.order_time, 'YYYY-MM-DD'), TO_CHAR(o.order_time, 'HH24')")
                ->orderByRaw("order_date, order_hour")
                ->get();
            }
    }
}