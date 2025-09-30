<?php

namespace App\Http\Controllers;

use App\Model\Bank;
use App\Model\BankAccount;
use App\Model\Employee;
use App\Model\EmployeeAttendance;
use App\Model\Holiday;
use App\Model\Leave;
use App\Model\Salary;
use App\Model\SalaryChangeLog;
use App\Model\SalaryProcess;
use App\Model\TransactionLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Validator;

class PayrollController extends Controller
{
    public function salaryUpdateIndex() {
        return view('payroll.salary_update.all');
    }

    public function salaryUpdatePost(Request $request) {
        $rules = [
            'tax' => 'required|numeric|min:0',
            'others_deduct' => 'nullable|numeric',
            'gross_salary' => 'required|numeric|min:0',
            'date' => 'required|date',
            'type' => 'nullable',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }
        $others_deduct=0;
        if ($request->others_deduct) {
            $others_deduct= $request->others_deduct;
        }

        $employee = Employee::find($request->id);
        $employee->medical = round($request->gross_salary * .04);
        $employee->travel = round($request->gross_salary * .12);
        $employee->house_rent = round($request->gross_salary * .24);
        $employee->basic_salary = round($request->gross_salary * .60);
        $employee->tax = $request->tax;
        $employee->others_deduct =$others_deduct;
        $employee->gross_salary =$request->gross_salary;
        $employee->save();

        if ($request->type) {

            $salaryChangeLog = new SalaryChangeLog();
            $salaryChangeLog->employee_id = $employee->id;
            $salaryChangeLog->date = $request->date;
            $salaryChangeLog->type = $request->type;
            $salaryChangeLog->basic_salary = round($request->gross_salary * .60);
            $salaryChangeLog->house_rent = round($request->gross_salary * .24);
            $salaryChangeLog->travel = round($request->gross_salary * .12);
            $salaryChangeLog->medical = round($request->gross_salary * .04);
            $salaryChangeLog->tax = $request->tax;
            $salaryChangeLog->others_deduct = $request->others_deduct;
            $salaryChangeLog->gross_salary = $request->gross_salary;
            $salaryChangeLog->save();
        }



        return response()->json(['success' => true, 'message' => 'Updates has been completed.']);
    }

    public function salaryProcessIndex() {
        $banks = Bank::where('status', 1)->orderBy('name')->get();

        return view('payroll.salary_process.index', compact('banks'));
    }

    public function salaryProcessPost(Request $request) {

        $totalSalary=0;
        $employees = Employee::get();
        foreach ($employees as $employee){

            $absent_count=EmployeeAttendance::where('employee_id',$employee->id)
                ->where('present_or_absent',0)
                ->whereYear('date', '=', date('Y'))
                ->whereMonth('date', '=', $request->month)
                ->count();
            $late_count=EmployeeAttendance::where('employee_id',$employee->id)
                ->where('present_or_absent',1)
                ->where('late',1)
                ->whereYear('date',date('Y'))
                ->whereMonth('date',$request->month)
                ->count();
            $working_days=cal_days_in_month(CAL_GREGORIAN,$request->month,date('Y'));


            $late=(int)($late_count/3);


            $per_day_salary=$employee->gross_salary/$working_days;

            $deduct_absent_salary=$absent_count+$late*$per_day_salary;

            $totalSalary+=$employee->gross_salary-$deduct_absent_salary-$employee->others_deduct;


        }

       // $totalSalary = Employee::sum('gross_salary');

        $bankAccount = BankAccount::find($request->account);

        if ($totalSalary > $bankAccount->balance) {
            return redirect()->route('payroll.salary_process.index')->with('error', 'Insufficient Balance.');
        }

        $salaryProcess = new SalaryProcess();
        $salaryProcess->date = $request->date;
        $salaryProcess->month = $request->month;
        $salaryProcess->year = $request->year;
        $salaryProcess->bank_id = $request->bank;
        $salaryProcess->branch_id = $request->branch;
        $salaryProcess->bank_account_id = $request->account;
        $salaryProcess->total = $totalSalary;
        $salaryProcess->save();

        $employees = Employee::get();

        foreach ($employees as $employee) {

            $absent_count=EmployeeAttendance::where('employee_id',$employee->id)
                ->where('present_or_absent',0)
                ->whereYear('date',date('Y'))
                 ->whereMonth('date',$request->month)
                 ->count();
            $late_count=EmployeeAttendance::where('employee_id',$employee->id)
                ->where('present_or_absent',1)
                ->where('late',1)
                ->whereYear('date',date('Y'))
                ->whereMonth('date',$request->month)
                ->count();
            $working_days=cal_days_in_month(CAL_GREGORIAN,$request->month,date('Y'));

            $per_day_salary=$employee->gross_salary/$working_days;

            $late=(int)($late_count/3);

            $deduct_absent_salary=$absent_count+$late*$per_day_salary;

            $salary = new Salary();
            $salary->salary_process_id = $salaryProcess->id;
            $salary->employee_id = $employee->id;
            $salary->date = $request->date;
            $salary->month = $request->month;
            $salary->year = $request->year;
            $salary->basic_salary = $employee->basic_salary;
            $salary->house_rent = $employee->house_rent;
            $salary->travel = $employee->travel;
            $salary->medical = $employee->medical;
            $salary->tax = $employee->tax;
            $salary->others_deduct = $employee->others_deduct;
            $salary->absent_deduct = $deduct_absent_salary;
            $salary->gross_salary = $employee->gross_salary;
            $salary->save();
        }

        BankAccount::find($request->account)->decrement('balance', $totalSalary);

        $log = new TransactionLog();
        $log->date = $request->date;
        $log->particular = 'Salary';
        $log->transaction_type = 2;
        $log->transaction_method = 2;
        $log->account_head_type_id = 5;
        $log->account_head_sub_type_id = 5;
        $log->bank_id = $request->bank;
        $log->branch_id = $request->branch;
        $log->bank_account_id = $request->account;
        $log->amount = $totalSalary;
        $log->salary_process_id = $salaryProcess->id;
        $log->save();

        return redirect()->route('payroll.salary_process.index')->with('message', 'Salary process successful.');
    }

    public function leaveIndex() {
        $employees = Employee::orderBy('employee_id')->get();

        return view('payroll.leave.index', compact('employees'));
    }

    public function leavePost(Request $request) {
        $request->validate([
            'employee' => 'required',
            'from' => 'required|date',
            'to' => 'required|date',
            'note' => 'nullable|max:255',
            'type' => 'required'
        ]);

        $fromObj = new Carbon($request->from);
        $toObj = new Carbon($request->to);
        $totalDays = $fromObj->diffInDays($toObj) + 1;

        $leave = new Leave();
        $leave->employee_id = $request->employee;
        $leave->type = $request->type;
        $leave->year = $toObj->format('Y');
        $leave->from = $request->from;
        $leave->to = $request->to;
        $leave->total_days = $totalDays;
        $leave->note = $request->note;
        $leave->save();

        return redirect()->route('payroll.leave.index')->with('message', 'Leave add successful.');
    }


    public function holidayIndex() {

        return view('payroll.holiday.index');
    }

    public function holidayAdd()
    {
        return view('payroll.holiday.add');
    }

    public function holidayPost(Request $request) {
        $request->validate([
            'name' => 'required',
            'from' => 'required|date',
            'to' => 'required|date',
        ]);

        $fromObj = new Carbon($request->from);
        $toObj = new Carbon($request->to);
        $totalDays = $fromObj->diffInDays($toObj) + 1;

        $holiday = new Holiday();
        $holiday->name = $request->name;
        $holiday->year = $toObj->format('Y');
        $holiday->from = $request->from;
        $holiday->to = $request->to;
        $holiday->total_days = $totalDays;
        $holiday->save();

        return redirect()->route('payroll.holiday.index')->with('message', 'Holiday add successful.');
    }

    public function holidayEdit(Holiday $holiday)
    {
        return view('payroll.holiday.edit',compact('holiday'));
    }

    public function holidayEditPost(Holiday $holiday,Request $request)
    {
        $request->validate([
            'name' => 'required',
            'from' => 'required|date',
            'to' => 'required|date',
        ]);

        $fromObj = new Carbon($request->from);
        $toObj = new Carbon($request->to);
        $totalDays = $fromObj->diffInDays($toObj) + 1;

        $holiday->name = $request->name;
        $holiday->year = $toObj->format('Y');
        $holiday->from = $request->from;
        $holiday->to = $request->to;
        $holiday->total_days = $totalDays;
        $holiday->save();

        return redirect()->route('payroll.holiday.index')->with('message', 'Holiday update successful.');
    }

    public function holidayDatatable()
    {
        $query=Holiday::query();
        return DataTables::eloquent($query)
            ->editColumn('from', function(Holiday $holiday) {
                return $holiday->from->format('j F, Y');
            })
            ->editColumn('to', function(Holiday $holiday) {
                return $holiday->to->format('j F, Y');
            })

            ->addColumn('action', function(Holiday $holiday) {
                return '<a href="'.route('payroll.holiday_edit', ['holiday' => $holiday->id]).'" class="btn btn-primary btn-sm">Edit</a>';
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function salaryUpdateDatatable() {
        $query = Employee::with('department', 'designation');

        return DataTables::eloquent($query)
            ->addColumn('department', function(Employee $employee) {
                return $employee->department ? $employee->department->name : 'Not Assigned';
            })
            ->addColumn('designation', function(Employee $employee) {
                return $employee->designation ? $employee->designation->name : 'Not Assigned';
            })
            ->addColumn('action', function(Employee $employee) {
                return '<a class="btn btn-info btn-sm btn-update" role="button" data-id="'.$employee->id.'">Update</a>';
            })
            ->editColumn('photo', function(Employee $employee) {
                return '<img src="'.asset($employee->photo).'" height="50px">';
            })
            ->editColumn('employee_type', function(Employee $employee) {
                if ($employee->employee_type == 1)
                    return '<span class="label label-success">Permanent</span>';
                else
                    return '<span class="label label-warning">Temporary</span>';
            })

            ->editColumn('gross_salary', function(Employee $employee) {
                return '৳ '.number_format($employee->gross_salary, 2);
            })
            ->rawColumns(['action', 'photo', 'employee_type'])
            ->toJson();
    }
}
