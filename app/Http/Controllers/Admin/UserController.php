<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Blog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
   public function index(){
       $users=User::all();
       $response = [
           'status' => true,
           'data'    => $users,
           'message' => "success",
       ];
       return response()->json($response, 200);
   }
   public function store(Request $request){

       $validator = Validator::make($request->all(), [
           "username"=>"required|max:50|min:3|unique:users,username",
           "email"=>"required|unique:users|email|max:30|min:11",
           "password" => "required|min:4|max:16",
           "phone"=>"required|max:20",
           "first_name"=>"required|max:20",
           "last_name"=>"required|max:20",
           "status" => ["required", "string", "in:active,inactive"],

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
//       $data['user_id']=$request->user()->id;
       $data['role_id']=$request->role_id;
       $data['password']=bcrypt($request->password);
       $data['phone_verified']=1;
       $data['email_verified']=1;
       $data['user_verified']=1;



       $user=User::create($data);
       $response = [
           'status' => true,
           'data'    => $user,
           'message' => "success",
       ];
       return response()->json($response, 200);


   }
   public function change_user_status(Request $request){

       $validator = Validator::make($request->all(), [
           "status" => ["required", "string", "in:active,inactive"],
           "user_id"=>"required|numeric|exists:users,id|between:1,99999999",

       ]);

       if($validator->fails()){

           $response = [
               'status' => false,
               'errors'    => $validator->errors(),
               'message' => "Validation Fails",
           ];


           return response()->json($response, 404);

       }


       $user=User::where('id',$request->user_id)->first();

       if ($user){
           $user->status=$request->status;
           $user->save();
           $response = [
               'status' => true,
               'message' => "success",
           ];
           return response()->json($response, 200);
       }
       $errors=array(
           'errors'=>["invalid id"],
       );
       $response = [
           'status' => false,
           'errors'    => $errors,
           'message' => "failed",
       ];
       return response()->json($response, 404);
   }
   public function update(Request $request){
       $validator = Validator::make($request->all(), [
           "username"=>"required|max:50|min:3|unique:users,username,{$request->id}",
           'email' => "required|max:30|min:11|email|unique:users,email,{$request->id}",
           "phone"=>"required|max:20",
           "first_name"=>"required|max:20",
           "last_name"=>"required|max:20",
           "status" => ["required", "string", "in:active,inactive"],

           "id"=>"required|numeric|exists:users,id|between:1,999999999"
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
       $data['user_id']=$request->user()->id;
       $user=User::where('id',$request->id)->first();
       $user->update($request->all());
       $response = [
           'status' => true,
           'data'    => $user,
           'message' => "success",
       ];
       return response()->json($response, 200);
   }

   public function del_user($id){
       $user=User::where('id',$id)->first();

       if ($user){
           $user->delete();
           $response = [
               'status' => true,
               'message' => "success",
           ];
           return response()->json($response, 200);
       }
       $errors=array(
           'errors'=>["invalid id"],
       );
       $response = [
           'status' => false,
           'errors'    => $errors,
           'message' => "failed",
       ];
       return response()->json($response, 404);
   }

   public function single_user($id){
       $user=User::where('id',$id)->first();


           $response = [
               'status' => true,
               'data'=>$user,
               'message' => "success",
           ];
           return response()->json($response, 200);


   }
}
