<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User\SubscribeToNews;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
{
   public function subscribe_news(Request $request){
       $validator = Validator::make($request->all(), [

           "email"=>"required|unique:subscribe_to_news|email|max:30|min:11",


       ]);

       if($validator->fails()){

           $response = [
               'status' => false,
               'errors'    => $validator->errors(),
               'message' => "Validation Fails",
           ];


           return response()->json($response, 404);

       }
       try {
           $subscribe=new SubscribeToNews();
           $subscribe->email=$request->email;
           $subscribe->save();

           Mail::raw('WelCome To Open Mat Subscription ' ,function ($message) use ($subscribe) {
               $message->from(env('MAIL_USERNAME'));
               $message->to($subscribe->email);
               $message->subject('Subscribe to news');
           });

           $response = [
               'status' => true,
               'data'    => $subscribe,
               'message' => "success",
           ];
           return response()->json($response, 200);
       }catch (\Exception $exception){
           $response = [
               'status' => false,
               'message' => $exception->getMessage(),
           ];
           return response()->json($response, 404);
       }


   }
}
