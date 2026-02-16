<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Admin\Mat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MatFilterController extends Controller
{
    public function filter(Request $request)
    {



        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'event_day' => 'nullable|min:1',
            'event_title' => 'nullable|min:1',
            'event_location' => 'nullable|min:1',
            'event_state' => 'nullable|min:1',
            'event_date' => 'nullable|min:1',
            'region' => 'nullable|min:1',

        ]);
        $validator->sometimes(['event_day', 'event_title', 'event_location', 'event_state','event_date','region'], 'required', function ($input) {
            return empty($input->event_day) && empty($input->event_title) && empty($input->event_location) && empty($input->event_state) && empty($input->region) && empty($input->event_date);
        });
        if ($validator->fails()) {

            $response = [
                'status' => false,
                'errors' => $validator->errors(),
                'message' => "Validation Fails",
            ];


            return response()->json($response, 404);
        }
        $matQuery = Mat::query();
        if ($request->event_day==null){
//            $matQuery->WhereNotNull('open_mat_day');
        }else{
            $matQuery->where('open_mat_day', request('event_day'));
        }
        if ($request->event_title==null){
//            $matQuery->WhereNotNull('name');
        }else{
            $matQuery->where('name', request('event_title'));
        }
        if ($request->event_location==null){
//            $matQuery->WhereNotNull('location');
        }else{
            $matQuery->where('location', request('event_location'));
        }
        if ($request->event_state==null){
//            $matQuery->WhereNotNull('state');
        }else{
            $matQuery->where('state', request('event_state'));
        }
        if ($request->event_date==null){
//            $matQuery->WhereNotNull('event_date');
        }else{
            $matQuery->where('event_date', request('event_date'));
        }
        if ($request->region==null){
//            $matQuery->WhereNotNull('region');
        }else{
            $matQuery->where('region', request('region'));
        }

//       dd($request->all());

//        if(request()->has('region')&& request()->event_date==null && request()->event_title==null && request()->event_location==null && request()->event_state==null) {
//
//            $matQuery->where('region', request('region'));
//        }
//        if(request()->has('event_date') && request()->event_title==null && request()->event_location==null && request()->event_state==null) {
//
//            $matQuery->where('event_date', request('event_date'));
//        }
//        if(request()->event_date!=null && request()->event_title!=null && request()->event_location==null && request()->event_state==null) {
//
//            $matQuery->where('event_date', request('event_date'));
//        }
//        if(request()->event_date!=null && request()->event_title==null && request()->event_location!=null && request()->event_state==null) {
//            $matQuery->where('event_date', request('event_date'));
//        }
//        if(request()->event_date!=null && request()->event_title==null && request()->event_location==null && request()->event_state!=null) {
//            $matQuery->where('event_date', request('event_date'));
//        }
//
//
//        if(request()->has('event_day') && request()->event_title==null && request()->event_location==null && request()->event_state==null) {
//
//            $matQuery->where('open_mat_day', request('event_day'));
//        }
////        if(request()->event_day!=null && request()->event_title==null && request()->event_location==null && request()->event_state==null) {
////
////            $matQuery->where('open_mat_day', request('event_day'));
////        }
//
//        if(request()->has('event_title') && request()->event_day==null && request()->event_location==null && request()->event_state==null) {
//            $matQuery->where('name', 'like', '%' . request('event_title') . '%');
//        }
//
//        if(request()->has('event_location') && request()->event_day==null && request()->event_title==null && request()->event_state==null) {
//            $matQuery->where('location', 'like', '%' . request('event_location') . '%');
//        }
//
//        if(request()->has('event_state')  && request()->event_day==null && request()->event_title==null && request()->event_location==null ) {
//            $matQuery->where('state', 'like', '%' . request('event_state') . '%');
//        }
//        if (request()->has('event_day') && request()->has('event_title') && request()->event_location==null && request()->event_state==null){
//            $matQuery->where('open_mat_day', 'like', '%' . request('event_day') . '%');
//            $matQuery->where('name', 'like', '%' . request('event_title') . '%');
//
//
//        }
//        if (request()->event_day==null && request()->event_title==null && request()->region!=null && request()->event_state!=null){
//            $matQuery->where('state', 'like', '%' . request('event_state') . '%');
//            $matQuery->where('region', 'like', '%' . request('region') . '%');
//
//
//        }
//        if (request()->event_day==null && request()->event_title==null && request()->event_location!=null && request()->region!=null){
//            $matQuery->where('region', 'like', '%' . request('region') . '%');
//            $matQuery->where('location', 'like', '%' . request('event_location') . '%');
//
//
//        }
//        if (request()->event_day==null && request()->event_title==null && request()->event_location!=null && request()->event_state!=null){
//            $matQuery->where('state', 'like', '%' . request('event_state') . '%');
//            $matQuery->where('location', 'like', '%' . request('event_location') . '%');
//
//
//        }
//        if (request()->event_day!=null && request()->event_title==null && request()->event_location==null && request()->event_state!=null){
//            $matQuery->where('open_mat_day', 'like', '%' . request('event_day') . '%');
//            $matQuery->where('state', 'like', '%' . request('event_state') . '%');
//
//
//        }
//        if (request()->event_day==null && request()->event_title!=null && request()->event_location!=null && request()->event_state==null){
//            $matQuery->where('name', 'like', '%' . request('event_title') . '%');
//            $matQuery->where('location', 'like', '%' . request('event_location') . '%');
//
//
//        }
//if (request()->has('event_day') && request()->has('event_title') && request()->has('event_location') && request()->has('event_state') &&  request()->has('event_date') && request()->has('region')){
//    $matQuery->where('open_mat_day', 'like', '%' . request('event_day') . '%');
//    $matQuery->where('name', 'like', '%' . request('event_title') . '%');
//    $matQuery->where('location', 'like', '%' . request('event_location') . '%');
//    $matQuery->where('state', 'like', '%' . request('event_state') . '%');
//    $matQuery->where('event_date', 'like', '%' . request('event_date') . '%');
//    $matQuery->where('region', 'like', '%' . request('region') . '%');
//
//}
//dd(request()->event_day .'title:'. request()->event_title .'location:'. request()->event_location .'state:'. request()->event_state .'date:'.  request()->event_date .'region:'. request()->region);
        $results = $matQuery->get();


        $response = [
            'status' => true,
            'data' => $results,
            'message' => "success",
        ];
        return response()->json($response, 200);
    }

    public function get_regions(){
        $regions = Mat::distinct()->pluck('region');
        $uniqueRegions = $regions->toArray();
        $response = [
            'status' => true,
            'data' => $regions,
            'message' => "success",
        ];
        return response()->json($response, 200);

    }
}
