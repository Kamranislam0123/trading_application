<?php

namespace App\Http\Controllers;

use App\Model\ClientManagement;
use App\Model\ClientOperation;
use App\Model\ClientService;
use App\Model\ClientSource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;

class ClientManagementController extends Controller
{
    public function index(){
        $clients = ClientManagement::where('marketing_id', Auth::user()->employee->id??'')->get();
        return view('crm.index',compact('clients'));
    }

    public function employeeClientOrder(Request $request){
        $clients = ClientManagement::groupBy('marketing_id')->get();
        return view('crm.employee_client_order',compact('clients'));
    }

    public function employeeClientOrderPrint(Request $request){
        $clients = ClientManagement::groupBy('marketing_id')->get();
        return view('crm.employee_client_order_print',compact('clients'));
    }

    public function employeeWorkOrders(Request $request, $id){
        $clients = ClientManagement::where('marketing_id', $id)->get();
        return view('crm.employee_work_orders',compact('clients'));
    }

    public function add(){
        $sources = ClientSource::where('status',1)->get();
        $services = ClientService::where('status',1)->get();
        return view('crm.create', compact('sources','services'));
    }
    public function store(Request $request){


        $request->validate([
            // 'marketing' => 'required',
            'name' => 'required|string|max:255',
            'company' => 'required',
            'mobile' => 'required|numeric',
            'client_source_id' => 'required',
            'status' => 'required',
            'address' => 'required|max:255',
            'amount' => 'required|numeric',
            'contact_date' => 'required|date',
            'comments' => 'required|max:255',
            'client_service_id' => 'required',
            'next_contact_date' => 'required|date',
        ]);
        
        // dd(Auth::user()->employee->id);
        $client = new ClientManagement();
        $client->marketing_id = Auth::user()->employee->id??0;
        $client->client_name = $request->name;
        $client->company_name = $request->company;
        $client->mobile = $request->mobile;
        $client->client_source_id = $request->client_source_id;
        $client->address = $request->address;
        $client->status = $request->status;
        $client->propose_amount = $request->amount;
        $client->date = date('Y-m-d',strtotime($request->contact_date));
        $client->comments = $request->comments;
        $client->last_remark = $request->comments;
        $client->client_service_id = $request->client_service_id;
        $client->last_contact_date = $request->contact_date;
        $client->next_contact_date = $request->next_contact_date;
        $client->user_id = Auth::id();
        $client->save();

        $clientOperation = new ClientOperation();
        $clientOperation->client_management_id = $client->id;
        $clientOperation->client_name = $request->name;
        $clientOperation->company_name = $request->company;
        $clientOperation->status = $request->status;
        $clientOperation->mobile = $client->mobile;
        $clientOperation->date = date('Y-m-d',strtotime($request->contact_date));
        $clientOperation->last_contact_date = $request->contact_date;
        $clientOperation->client_service_id = $request->client_service_id;
        $clientOperation->remark = $request->comments;
        $clientOperation->save();

        return redirect()->route('marketing')->with('message', 'Client add successfully.');
    }


    public function update(Request $request){

        $rules =[
            'client_name' => 'required',
            'status' => 'required',
            'order_complete_date' => 'nullable|date',
            'propose_amount' => 'required|numeric|min:0',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }
        $client = ClientManagement::find($request->client_id);
        $client->propose_amount = $request->propose_amount;
        $client->order_complete_date = $request->order_complete_date;
        $client->status = $request->status;
        $client->client_name = $request->client_name;
        $client->save();

        return response()->json(['success' => true, 'message' => 'Update has been completed.']);

    }

    public function regularUpdate(Request $request){

        $rules =[
            'name' => 'required|max:255',
            'company_name' => 'required|max:255',
            'date' => 'required|date',
            'next_contact_date' => 'required|date',
            'status' => 'required',
            'remark' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }
        $client = ClientManagement::find($request->id);
        $clientOperation = new ClientOperation();
        $clientOperation->client_management_id = $client->id;
        $clientOperation->client_name = $request->name;
        $clientOperation->company_name = $request->company_name;
        $clientOperation->status = $request->status;
        $clientOperation->mobile = $client->mobile;
        $clientOperation->date = $request->date;
        $clientOperation->client_service_id = $client->client_service_id;
        $clientOperation->remark = $request->remark;
        $clientOperation->last_contact_date = $request->last_contact_date;
        $clientOperation->save();


        $client->status = $request->status;
        $client->client_name = $request->name;
        $client->company_name = $request->company_name;
        $client->last_contact_date = $request->date;
        $client->next_contact_date = $request->next_contact_date;
        $client->last_remark = $request->remark;
        $client->save();

        return response()->json(['success' => true, 'message' => 'Update has been completed.']);


    }
    public function clientOperationDetails($id){

        $clients = ClientOperation::where('client_management_id',$id)->orderBy('created_at','desc')->get();
        return view('crm.details',compact('clients'));
    }

    public function getClient(Request $request){
        $client = ClientManagement::where('id',$request->clientId)
            ->first();

        return response()->json($client);

    }
}
