<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\CompanyBranch;

class CompanyBranchController extends Controller
{
    public function index() {
         $company_branch = CompanyBranch::all();

        return view('administrator.company_branch.all', compact('company_branch'));
    }

    public function add() {
        return view('administrator.company_branch.add');
    }
    
    public function addPost(Request $request) {

        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'status' => 'required'
        ]);

        $company_branch = new CompanyBranch();
        $company_branch->name = $request->name;
        $company_branch->address = $request->address;
        $company_branch->status = $request->status;
        $company_branch->save();

        return redirect()->route('company-branch')->with('message', 'Company Branch added successfully.');
    }

    public function edit(CompanyBranch $company_branch) {
        return view('administrator.company_branch.edit', compact('company_branch'));
    }

    public function editPost(CompanyBranch $company_branch, Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required'
        ]);

        $company_branch->name = $request->name;
        $company_branch->status = $request->status;
        $company_branch->save();

        return redirect()->route('company-branch')->with('message', 'Company Branch edited successfully.');
    }
}
