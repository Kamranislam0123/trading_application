<?php

namespace App\Http\Controllers;

use App\Model\TermsCondition;
use Illuminate\Http\Request;

class TermsConditionController extends Controller
{
    public function termsCondition(Request $request)
    {
        $terms_condition = TermsCondition::first();
        return view('terms_condition.add', compact('terms_condition'));
    }

    public function store(Request $request)
    {
        $terms_condition = TermsCondition::first();
        $data = $request->all();
        if ($terms_condition) {
            $terms_condition->update($data);
        } else {
            TermsCondition::create($data);
        }
        return redirect()->route('terms_condition')->with('message', 'Terms & conditions updated seccufully.');
    }
}
