<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RegistrationController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            "username"=>"required|max:50|min:3|unique:users,username",
            "email"=>"required|unique:users|email|max:30|min:11",
            "password" => "required|min:4|max:16|confirmed",
            "password_confirmation" => "required",

        ]);

        if($validator->fails()){

            $response = [
                'status' => false,
                'errors'    => $validator->errors(),
                'message' => "Validation Fails",
            ];


            return response()->json($response, 404);

        }
        $otp=mt_rand(1000,9999);

        try {
            $user=User::create(['username'=>request()->username,'first_name'=>request()->first_name,'last_name'=>request()->last_name,  'email' => request()->email,'role_id'=>2,
              'password' => bcrypt(request()->password),'role_id'=>2,'email_otp'=>$otp,'status'=>'active']);





            if (!empty($user)){


                Mail::raw('Your verification code is'.$otp ,function ($message) use ($user) {
                    $message->from(env('MAIL_USERNAME'));
                    $message->to($user->email);
                    $message->subject('Verification Code');
                });

                $response = [
                    'status' => true,
                    'data'    => $user,
                    'message' => "Verification code has been sent to your registered email",
                ];
                return response()->json($response, 200);
            }




        }catch (\Exception $e){
            $response = [
                'status' => false,
                'errors'    => $e->getMessage(),
                'message' => "failed",
            ];
            return response()->json($response, 404);
        }




    }

    public function verify(Request $request){
        $validator = Validator::make($request->all(), [

            "user_id"=>"required|numeric|exists:users,id|digits_between:1,111111111",
            "email_otp"=>"required|digits_between:1,9999|exists:users,email_otp",
        ]);

        if($validator->fails()){

            $response = [
                'status' => false,
                'errors'    => $validator->errors(),
                'message' => "Validation Fails",
            ];


            return response()->json($response, 404);

        }
        $user=User::where(['email_otp'=>request()->email_otp,'id'=>request()->user_id])->first();
        if ($user){
            $user->user_verified=1;
            $user->email_verified=1;
            $user->email_otp=null;

            $user->save();

            $response = [
                'status' => true,
                'data'    => $user,
                'message' => "User has been verified",
            ];


            return response()->json($response, 200);
        }else{


            $errors=array(
                'errors'=>["Verification failed"],
            );
            $response = [
                'status' => false,
                'errors'    => $errors,
                'message' => "Try again",
            ];
            return response()->json($response, 404);
        }
    }
}
