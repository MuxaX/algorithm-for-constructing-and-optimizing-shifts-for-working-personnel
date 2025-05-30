<?php
 
namespace App\Http\Controllers;
 
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use MathParser\StdMathParser;
use MathParser\Interpreting\Evaluator;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;

class shiftController extends Controller
{
    public function createShifts(Request $request)
    {

        $validatedData = $request->validate([
            'profession_id' => 'required|integer',
            'count_day' => 'required|integer',
            'date_start' => 'required|string',
            'count_day_end' => 'required|integer'
        ]);
        //return $validatedData['date_start'];


        $type_id = DB::table('profession as p')
        ->join('order_type as ot', 'p.order_type_id', '=', 'ot.order_type_id')
        ->where('p.profession_id', $validatedData['profession_id'])
        ->value('ot.order_type_id');

        $formula = DB::table('loadformulas')
        ->where('profession_id', $validatedData['profession_id'])
        ->value('formula');

        $variables = DB::table('orders as o')
        ->where('o.order_type', $type_id)
        ->count();

        // Create a new math parser instance
        $parser = new StdMathParser();
        $AST = $parser->parse($formula);

        // Create an evaluator and set the variables
        $evaluator = new Evaluator();

        // Assuming the formula uses a variable 'x'
        // Create an array with the variable and its value
        $variablesArray = ['X' => $variables];

        // Set the variables in the evaluator
        $evaluator->setVariables($variablesArray);

        // Вычисленное выражение из формулы, введённое пользователем
        $result = $AST->accept($evaluator);
        $roundedResult = ceil($result);

            // Count employees (optional, but not needed here)
        //$count = DB::table('employee as e')
        //->join('profession as p', 'e.profession', '=', 'p.profession_id')
        //->join('order_type as ot', 'p.order_type_id', '=', 'ot.order_type_id')
        //->where('ot.order_type_id', $type_id)
        //->count();

        // Выборка сотрудников по данному типу профессии
        $employees = DB::table('employee as e')
        ->join('profession as p', 'e.profession', '=', 'p.profession_id')
        ->join('order_type as ot', 'p.order_type_id', '=', 'ot.order_type_id')
        ->where('ot.order_type_id', $type_id)
        ->get();

        // Rest of your code remains the same

        $today = new DateTime('today', new DateTimeZone('UTC'));
        $startDateString = $today->format('Y-m-d H:i:s');
        $thirtyDaysAgo = clone $today;
        $count_day_get = $validatedData['count_day'];
        $thirtyDaysAgo->modify("-$count_day_get days");
        $endDateString = $thirtyDaysAgo->format('Y-m-d H:i:s');

       
        // Construct the query for orders between 8:00:00 and 17:00:00
        $orderResultDay = DB::table('orders as o')
        ->selectRaw("TO_CHAR(o.order_time, 'YYYY-MM-DD') AS order_date")
        ->selectRaw("TO_CHAR(o.order_time, 'HH24') AS order_hour")
        ->selectRaw("COUNT(*) AS order_count")
        ->where('o.order_type', $type_id)
        ->where('o.order_time', '<', $startDateString)
        ->where('o.order_time', '>', $endDateString)
        ->whereRaw("EXTRACT(HOUR FROM o.order_time) BETWEEN 8 AND 16") // Filter hours between 8 and 16
        ->groupByRaw("TO_CHAR(o.order_time, 'YYYY-MM-DD'), TO_CHAR(o.order_time, 'HH24')")
        ->orderByRaw("order_date, order_hour")
        ->get();

        // Construct the query for orders between 17:00:00 and 24:00:00
        $orderResultNight = DB::table('orders as o')
        ->selectRaw("TO_CHAR(o.order_time, 'YYYY-MM-DD') AS order_date")
        ->selectRaw("TO_CHAR(o.order_time, 'HH24') AS order_hour")
        ->selectRaw("COUNT(*) AS order_count")
        ->where('o.order_type', $type_id)
        ->where('o.order_time', '<', $startDateString)
        ->where('o.order_time', '>', $endDateString)
        ->whereRaw("EXTRACT(HOUR FROM o.order_time) BETWEEN 17 AND 23") // Filter hours between 17 and 23
        ->groupByRaw("TO_CHAR(o.order_time, 'YYYY-MM-DD'), TO_CHAR(o.order_time, 'HH24')")
        ->orderByRaw("order_date, order_hour")
        ->get();

        // Найдём среднее количество заказов в каждой смене
        $averageDay = $orderResultDay->count('order_count');
        $averageNight = $orderResultNight->count('order_count');
        // Общее количество средних заказов
        $sumOrder = $averageDay+$averageNight;

        // Найдём пропорциональное соотношение каждой типа смен 
        // кол-во заказов во время смены/ общее кол-во заказов
        $proportionDayEmploye = $averageDay/$sumOrder;
        $proportionNightEmployee = $averageNight/$sumOrder;

        //Теперь находим нужное количество для каждого типа смен
        //Полученное количество смен * пропорциональная часть от общего кол-ва заказов у конкретного типа смен
        $shiftsDay = round($roundedResult*$proportionDayEmploye);
        $shiftNight = round($roundedResult*$proportionNightEmployee);

        // Если общее количество смен больше, чем было было изначально,
        // то нужно выполнить следующее условие:
        if ($shiftsDay + $shiftNight != $roundedResult) {
            if ($shiftsDay < $shiftNight) {
                $shiftsDay = $roundedResult - $shiftNight;
            } else {
                $shiftNight = $roundedResult - $shiftsDay;
            }
        }

        //Работа с датами

        $DateStartShift = Carbon::createFromFormat('Y-m-d', $validatedData['date_start'])->startOfDay();
        $DateStartShiftString = $DateStartShift->toDateTimeString();

        $count_day_end_get = $validatedData['count_day_end'];

        $DateStartCopy = Carbon::createFromFormat('Y-m-d', $validatedData['date_start'])->startOfDay();
        
        $DateEndShift = $DateStartShift->addDay($count_day_end_get);
        $DateEndShiftString = $DateEndShift->toDateTimeString();


        // Вносим смены в базу данных и распределяем между сотрудниками
        $availableEmployees = count($employees);
        $employeeIndex = 0;


        for ($i = 0; $i <= $count_day_end_get; $i++) {
            $currentDate = clone $DateStartCopy;
            //return $currentDateString;
            $currentDate->addDay($i);
            $currentDateString = $currentDate->toDateTimeString();


            // Сбрасываем индекс сотрудников за каждый день
            $employeeIndex = 0;

            // Day shifts
            for ($j = 0; $j < $shiftsDay; $j++) {
                if ($employeeIndex < $availableEmployees) {
                    // Назначить смену следующему доступному сотруднику
                    $employee = $employees[$employeeIndex];
                    $shift = [
                        'shift_type' => 1,
                        'date_start' => $currentDate->format('Y-m-d 08:00:00'),
                        'date_end' => $currentDate->format('Y-m-d 17:00:00'),
                    ];
                    DB::table('shift')->insert($shift);
                    $lastInsertedId = DB::getPdo()->lastInsertId(); 
                    $assignment = [
                        'employee' => $employee->employee_id,
                        'shift' => $lastInsertedId,
                        'assignment_date' => $shift['date_start'],
                    ];
                    DB::table('shift_assignment')->insert($assignment);
                    $employeeIndex++;
                } else {
                    // 
                    break;
                }
            }

            // Night shifts
            for ($j = 0; $j < $shiftNight; $j++) {
                if ($employeeIndex < $availableEmployees) {
                    // Assign the shift to the next available employee
                    $employee = $employees[$employeeIndex];
                    $shift = [
                        'shift_type' => 2,
                        'date_start' => $currentDate->format('Y-m-d 17:00:00'),
                        'date_end' => $currentDate->format('Y-m-d 23:59:59'),
                    ];
                    DB::table('shift')->insert($shift);
                    $lastInsertedId = DB::getPdo()->lastInsertId(); // Get the last inserted ID
                    $assignment = [
                        'employee' => $employee->employee_id,
                        'shift' => $lastInsertedId,
                        'assignment_date' => $shift['date_start'],
                    ];
                    DB::table('shift_assignment')->insert($assignment);
                    $employeeIndex++;
                } else {
                    // If there are not enough employees, break the loop
                    break;
                }
            }
        }

        return "Create!"; 

    }

    public function shiftList(string $date){
        $selectedDate = Carbon::createFromFormat('Y-m-d', $date)->startOfDay();
        // Convert Carbon instances to strings in the correct format
        $startDateString = $selectedDate->toDateTimeString();

        $nextDate = $selectedDate->addDay();
        $endDateString = $nextDate->toDateTimeString();

        return $results = DB::table('shift_assignment as sa')
        ->join('employee as e', 'sa.employee', '=', 'e.employee_id')
        ->join('shift as s', 'sa.shift', '=', 's.shift_id')
        ->join('profession as p', 'e.profession', '=', 'p.profession_id')
        ->whereBetween('s.date_start', [$startDateString, $endDateString])
        ->select('e.name', 's.date_start', 's.date_end', 'sa.assignment_date', 'p.profession_name')
        ->get();
    }

    public function getCountDay(int $profession){

        return $count_day = DB::table('loadformulas')
        ->where('profession_id', $profession)
        ->select('count_day')
        ->first();
    }

}