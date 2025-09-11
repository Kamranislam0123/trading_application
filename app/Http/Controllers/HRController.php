<?php

namespace App\Http\Controllers;

use App\Model\AcitivityLog;
use App\User;
use App\Model\Department;
use App\Model\DesignationLog;
use App\Model\Employee;
use App\Model\EmployeeAttendance;
use App\Model\EmployeeTarget;
use App\Model\Leave;
use App\Model\SalaryChangeLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use DataTables;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class HRController extends Controller
{
    public function employeeIndex() {
        $departments = Department::where('status', 1)
            ->orderBy('name')->get();

        return view('hr.employee.all', compact('departments'));
    }

    public function employeeAdd() {
        $departments = Department::where('status', 1)
            ->orderBy('name')->get();

        $count = Employee::count();
        $employeeId = str_pad($count+1, 4, '0', STR_PAD_LEFT);

        return view('hr.employee.add', compact('departments', 'employeeId'));
    }

    public function employeeAddPost(Request $request) {


        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255',
            'password' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'joining_date' => 'nullable|date',
            'confirmation_date' => 'nullable|date',
            'department' => 'required',
            'designation' => 'required',
            'education_qualification' => 'nullable|max:255',
            'employee_type' => 'nullable',
            'reporting_to' => 'nullable|string|max:255',
            'gender' => 'nullable',
            'marital_status' => 'nullable',
            'mobile_no' => 'nullable|string|max:15',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'emergency_contact' => 'required|string|max:255',
            'signature' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
            'photo' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
            'present_address' => 'nullable|string|max:255',
            'permanent_address' => 'nullable|string|max:255',
            'religion' => 'nullable',
            'cv' => 'nullable|file|mimes:doc,pdf,docx|max:5120',
            'gross_salary' => 'nullable|numeric',
            'bank_name' => 'nullable|max:255',
            'bank_branch' => 'nullable|max:255',
            'bank_account' => 'nullable',
            'previous_salary' => 'nullable|numeric',
        ]);

        // Create User 
        $user = new User();
        $user->name = $request->name;
        
        // Handle email - check if provided email is unique, otherwise generate one
        if ($request->email) {
            $existingUser = User::where('email', $request->email)->first();
            if ($existingUser) {
                $user->email = 'employee_' . $request->employee_id . '@company.com';
            } else {
                $user->email = $request->email;
            }
        } else {
            $user->email = 'employee_' . $request->employee_id . '@company.com';
        }
        
        $user->password = bcrypt($request->password ?: 'password123');
        $user->save();
        $user->syncPermissions($request->permission);

        // Upload Signature
        $signature=null;
        if ($request->signature) {

            $file = $request->file('signature');
            $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'public/uploads/employee/signature';
            $file->move($destinationPath, $filename);

            $signature = 'uploads/employee/signature/'.$filename;

        }

        // Upload Photo
        $photo=null;
        if ($request->photo) {

            $file = $request->file('photo');
            $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'public/uploads/employee/photo';
            $file->move($destinationPath, $filename);

            $photo = 'uploads/employee/photo/'.$filename;
        }


        // Upload CV
        $cv = null;
        if ($request->cv) {
            $file = $request->file('cv');
            $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'public/uploads/employee/cv';
            $file->move($destinationPath, $filename);

            $cv = 'uploads/employee/cv/'.$filename;
        }

        $employee = new Employee();
        $employee->user_id = $user->id;
        $employee->name = $request->name;
        $employee->employee_id = $request->employee_id;
        $employee->email = $request->email;
        $employee->dob = $request->date_of_birth ?: null;
        $employee->joining_date = $request->joining_date ?: null;
        $employee->confirmation_date = $request->confirmation_date ?: null;
        $employee->department_id = $request->department;
        $employee->designation_id = $request->designation;
        $employee->education_qualification = $request->education_qualification ?: 'Not Specified';
        $employee->employee_type = $request->employee_type ?: 'Permanent';
        $employee->reporting_to = $request->reporting_to ?: 'Not Specified';
        $employee->gender = $request->gender ?: 'Not Specified';
        $employee->marital_status = $request->marital_status ?: 'Not Specified';
        $employee->mobile_no = $request->mobile_no ?: 'Not Provided';
        $employee->father_name = $request->father_name ?: 'Not Specified';
        $employee->mother_name = $request->mother_name ?: 'Not Specified';
        $employee->emergency_contact = $request->emergency_contact;
        $employee->signature = $signature;
        $employee->photo = $photo;
        $employee->present_address = $request->present_address ?: 'Not Provided';
        $employee->permanent_address = $request->permanent_address ?: 'Not Provided';
        $employee->religion = $request->religion ?: 'Not Specified';
        $employee->cv = $cv;


        $employee->previous_salary = $request->previous_salary ? $request->previous_salary : 0;
        $employee->gross_salary = $request->gross_salary ? $request->gross_salary : 0;
        $employee->bank_name = $request->bank_name ?: 'Not Provided';
        $employee->bank_branch = $request->bank_branch ?: 'Not Provided';
        $employee->bank_account = $request->bank_account ?: 'Not Provided';

        $grossSalary = $request->gross_salary ? $request->gross_salary : 0;
        $employee->medical = round($grossSalary * .04);
        $employee->travel = round($grossSalary * .12);
        $employee->house_rent = round($grossSalary * .24);
        $employee->basic_salary = round($grossSalary * .60);
        $employee->tax = 0;
        $employee->others_deduct =0;

        $employee->save();

        $designationLog = new DesignationLog();
        $designationLog->employee_id = $employee->id;
        $designationLog->department_id = $request->department;
        $designationLog->designation_id = $request->designation;
        $designationLog->date = date('Y-m-d');
        $designationLog->save();


            $salaryChangeLog = new SalaryChangeLog();
            $salaryChangeLog->employee_id = $employee->id;
            $salaryChangeLog->date = date('Y-m-d');
            $salaryChangeLog->basic_salary = round($grossSalary * .60);
            $salaryChangeLog->house_rent = round($grossSalary * .24);
            $salaryChangeLog->travel = round($grossSalary * .12);
            $salaryChangeLog->medical = round($grossSalary * .04);
            $salaryChangeLog->tax = 0;
            $salaryChangeLog->others_deduct = 0;
            $salaryChangeLog->gross_salary = round($grossSalary);
            $salaryChangeLog->type = 5;
            $salaryChangeLog->save();



        return redirect()->route('employee.all')->with('message', 'Employee add successfully.');
    }

    public function employeeEdit(Employee $employee) {
        $departments = Department::where('status', 1)
            ->orderBy('name')->get();

        return view('hr.employee.edit', compact('departments', 'employee'));
    }

    public function employeeEditPost(Employee $employee, Request $request) {

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:employees,email,'. $employee->id,
            'date_of_birth' => 'nullable|date',
            'joining_date' => 'nullable|date',
            'confirmation_date' => 'nullable|date',
            'education_qualification' => 'nullable|max:255',
            /*'department' => 'required',
            'designation' => 'required',*/
            'employee_type' => 'required',
            'reporting_to' => 'nullable|string|max:255',
            'gender' => 'required',
            'marital_status' => 'required',
            'mobile_no' => 'nullable|digits:11',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'emergency_contact' => 'required|string|max:255',
            'signature' => 'nullable|image',
            'photo' => 'nullable|image',
            'present_address' => 'required|string|max:255',
            'permanent_address' => 'required|string|max:255',
            'religion' => 'required',
            'cv' => 'nullable|mimes:doc,pdf,docx',
            'bank_name' => 'nullable|max:255',
            'bank_branch' => 'nullable|max:255',
            'bank_account' => 'nullable|max:255',
            Rule::unique('employees')->ignore($employee->id),

        ]);

        // Update User Info 
        $user = User::where('id', $employee->user_id)->first();
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->password !='') {
            $user->password = bcrypt($request->password);
        }
        $user->save();

        $signature = $employee->signature;
        if ($request->signature) {
            // Previous Photo
            $previousPhoto = public_path($employee->signature);
            unlink($previousPhoto);

            $file = $request->file('signature');
            $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'public/uploads/employee/signature';
            $file->move($destinationPath, $filename);

            $signature = 'uploads/employee/signature/'.$filename;
        }

        $photo = $employee->photo;
        if ($request->photo) {
            // Previous Photo
            $previousPhoto = public_path($employee->photo);
            unlink($previousPhoto);

            $file = $request->file('photo');
            $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'public/uploads/employee/photo';
            $file->move($destinationPath, $filename);

            $photo = 'uploads/employee/photo/'.$filename;
        }

        // Upload CV
        $cv = $employee->cv;
        if ($request->cv) {
            // Previous CV
            if ($employee->cv) {
                $previousCV = public_path($employee->cv);
                unlink($previousCV);
            }

            $file = $request->file('cv');
            $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'public/uploads/employee/cv';
            $file->move($destinationPath, $filename);

            $cv = 'uploads/employee/cv/'.$filename;
        }

        $employee->name = $request->name;
        $employee->dob = $request->date_of_birth;
        $employee->joining_date = $request->joining_date;
        $employee->confirmation_date = $request->confirmation_date;
        $employee->education_qualification = $request->education_qualification;
        /*$employee->department_id = $request->department;
        $employee->designation_id = $request->designation;*/
        $employee->employee_type = $request->employee_type;
        $employee->reporting_to = $request->reporting_to;
        $employee->gender = $request->gender;
        $employee->marital_status = $request->marital_status;
        $employee->mobile_no = $request->mobile_no ?: 'Not Provided';
        $employee->father_name = $request->father_name ?: 'Not Specified';
        $employee->mother_name = $request->mother_name ?: 'Not Specified';
        $employee->emergency_contact = $request->emergency_contact;
        $employee->signature = $signature;
        $employee->photo = $photo;
        $employee->present_address = $request->present_address ?: 'Not Provided';
        $employee->permanent_address = $request->permanent_address ?: 'Not Provided';
        $employee->email = $request->email;
        $employee->religion = $request->religion ?: 'Not Specified';
        $employee->cv = $cv;
        $employee->bank_name =$request->bank_name;
        $employee->bank_branch =$request->bank_branch;
        $employee->bank_account =$request->bank_account;
        $employee->save();

        return redirect()->route('employee.all')->with('message', 'Employee edit successfully.');
    }

    public function employeeDetails(Employee $employee) {
        $leaves = Leave::where('employee_id', $employee->id)
            ->where('year', date('Y'))
            ->orderBy('created_at', 'desc')
            ->get();

        return view('hr.employee.details', compact('employee', 'leaves'));
    }

    public function getLeave(Request $request) {
        $leaves = Leave::where('employee_id', $request->employeeId)
            ->where('year', $request->year)
            ->orderBy('created_at', 'desc')
            ->get();

        $html = view('partials.leave_table', compact('leaves'))->render();

        return response()->json(['html' => $html]);
    }

    public function employeeDesignationUpdate(Request $request) {
        $rules = [
            'department' => 'required',
            'designation' => 'required',
            'date' => 'required|date',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $employee = Employee::find($request->id);
        $employee->department_id = $request->department;
        $employee->designation_id = $request->designation;
        $employee->save();

        $log = new DesignationLog();
        $log->employee_id = $employee->id;
        $log->department_id = $request->department;
        $log->designation_id = $request->designation;
        $log->date = $request->date;
        $log->save();

        return response()->json(['success' => true, 'message' => 'Update has been completed.']);
    }

    public function employeeDatatable() {
        $query = Employee::with('department', 'designation');

        return DataTables::eloquent($query)
            ->addColumn('department', function(Employee $employee) {
                return $employee->department->name;
            })
            ->addColumn('designation', function(Employee $employee) {
                return $employee->designation->name;
            })
            ->addColumn('action', function(Employee $employee) {
                $btn = '<a class="btn btn-primary btn-sm btn-change-designation" role="button" data-id="' . $employee->id . '">Change Designation</a> ';
                $btn .= '<a class="btn btn-primary btn-sm" href="' . route('employee.details', ['employee' => $employee->id]) . '">Details</a> ';
                $btn .= '<a class="btn btn-info btn-sm" href="'.route('employee.edit', ['employee' => $employee->id]).'">Edit</a> ';
                $btn .= '<a class="btn btn-primary btn-sm btn-employee-target" role="button" data-id="' . $employee->id . '"> Target </a> ';
                return $btn;
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


            ->rawColumns(['action', 'photo', 'employee_type'])
            ->toJson();
    }

    public function employeeAttendance(Request $request)
    {

        $employees=Employee::with('department','designation')
            ->get();


        return view('hr.attendance.add',compact('employees'));
    }



    public function employeeAttendancePost(Request $request)
    {
        $this->validate($request,[
            'attend_date'=>'required'
        ]);

        $attendance_date=date('d-m-y',strtotime($request->attend_date));
        $error=$attendance_date.' Attendance Already Done';


        $check_attendance=EmployeeAttendance::
        whereDate('date',$request->attend_date)->count();

        if ($check_attendance > 0) {

            return redirect()->route('employee.attendance')->with('error',$error);
        }

        $employees = Employee::get();

        foreach ($employees as $employee) {
            // Present
            $presentId = 'present_'.$employee->id;
            $noteId = 'note_'.$employee->id;
            $lateId = 'late_'.$employee->id;
            $lateTimeId = 'late_time_'.$employee->id;
            $presentTimeId = 'present_time_'.$employee->id;

            $attendance = new EmployeeAttendance();
            $attendance->employee_id = $employee->id;
            $attendance->date = $request->attend_date;

            if ($request->$presentId) {
                // present
                $attendance->present_or_absent = 1;
                $attendance->note = $request->$noteId;
                $attendance->present_time =  date('H:i:s',strtotime($request->$presentTimeId ));

                if ($request->$lateId) {
                    $attendance->late = 1;
                    $attendance->late_time = date('H:i:s',strtotime($request->$lateTimeId ));
                    $attendance->note = $request->$noteId;
                } else {
                    $attendance->late = 0;
                }
            } else {
                // absent
                $attendance->present_or_absent = 0;
                $attendance->late = 0;
                $attendance->late_time = null;
                $attendance->note = $request->$noteId;
            }

            $attendance->save();


        }

        return redirect()->route('employee.attendance')->with('message','Today Employee Attendance Completed.');
    }

    public function employeeTargetUpdate(Request $request)
    {
        $employee_target = EmployeeTarget::where([
            'employee_id'=> $request->employee_id,
            'month'=> $request->month,
            'year'=> $request->year,
            ])->first();
        if($employee_target){
            $employee_target->amount = $request->amount;
            $employee_target->save();
        }else{
            $data = $request->all();
            EmployeeTarget::create($data);
        } 
        return "Employee target amount updated successfully done.";
    }


}
