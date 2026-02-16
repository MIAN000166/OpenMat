<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ContactController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            "first_name"=>"required|min:3|max:50",

            "last_name"=>"required|max:50|min:3",
            "email"=>"required|unique:users|email|max:30|min:11",
            "phone"=>"required|min:3|max:30",
            "business_name"=>"required|min:4|max:50",
            "inquiry_reason"=>"required|min:3|max:50",
            "other_reason"=>  Rule::when($request->inquiry_reason=="other",'required|max:50|min:4'),

            "message"=>"required|min:3|max:200",


        ]);

        if($validator->fails()){

            $response = [
                'status' => false,
                'errors'    => $validator->errors(),
                'message' => "Validation Fails",
            ];


            return response()->json($response, 404);

        }
        $data=$request->all();
        $contactForm=ContactUs::create($validator->validated());
        $response = [
            'status' => true,
            'data'    => $contactForm,
            'message' => "success",
        ];
        return response()->json($response, 200);
    }
}
