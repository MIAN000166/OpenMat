<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Admin\Mat;
use App\Models\Admin\TrendingMats;
use Illuminate\Http\Request;

class MatController extends Controller
{
    public function all_mats(){
        $mats=Mat::all();
        $response = [
            'status' => true,
            'data'    => $mats,
            'message' => "success",
        ];
        return response()->json($response, 200);
    }
    public function single_mat($id){
        $mat=Mat::where('id',$id)->first();
        $relatedmats=Mat::where(['event_date'=>$mat->event_date,'location'=>$mat->location])->get();
        $mat['related_mats']=$relatedmats;


        $response = [
            'status' => true,
            'data'=>$mat,
            'message' => "success",
        ];
        return response()->json($response, 200);


    }
    public function get_tending_mats(Request $request){



        $mats = Mat::where('featured','true')->get();

        $response = array(
            'status' => true,
            'data'=>$mats,
            'message' => "Success"
        );
        return response()->json($response, 200);

//        $ack->borrower_signature_image_path = asset( $ack->borrower_signature_image_path);



    }

    public function tending_mat($id){
        $mat=Mat::where('id',$id)->first();
        $response = array(
            'status' => true,
            'data'=>$mat,
            'message' => "Success"
        );
        return response()->json($response, 200);

    }



}
