<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Admin\ApplicationRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SearchFilterController extends Controller
{
//    public function filter(Request $request)
//    {
//
//
//
//        // Validate the incoming request data
//        $validator = Validator::make($request->all(), [
//            'years' => 'nullable|array',
//
//            'quarters' => 'nullable|array',
//            'program_streams' => 'nullable|array',
//            'provinces' => 'nullable|array',
//            'cities' => 'nullable|array',
//            'employers' => 'nullable|array',
//            'statuses' => 'nullable|array',
//            'nocs' => 'nullable|array',
//            'search_field' => 'nullable|string',
//        ]);
//        if ($validator->fails()) {
//
//            $response = [
//                'status' => false,
//                'errors' => $validator->errors(),
//                'message' => "Validation Fails",
//            ];
//
//
//            return response()->json($response, 404);
//        }
//
//            // Extract filter values from the request
//        if (!empty($request->years)){
//            $years = $request->input('years'); // Default to the last three years
//        }else{
//            $years = $request->input('years', [date('Y') - 2, date('Y') - 1, date('Y')]); // Default to the last three years
//        }
//
//        if (!empty($request->quarters)){
//            $quarters = $request->input('quarters'); // Default to Q1 through Q4
//        }else{
//            $quarters = $request->input('quarters', ['Q1', 'Q2', 'Q3', 'Q4']); // Default to Q1 through Q4
//        }
//
//        if (!empty($request->program_streams)){
//            $programStreams = $request->input('program_streams'); // Default to all program streams
//        }else{
//            $programStreams = $request->input('program_streams', ApplicationRecord::pluck('program_stream')->unique()->all()); // Default to all program streams
//        }
//        if (!empty($request->provinces)){
//            $provinces = $request->input('provinces'); // Default to all provinces
//        }else{
//            $provinces = $request->input('provinces', ApplicationRecord::pluck('province')->unique()->all()); // Default to all provinces
//        }
//        if (!empty($request->cities)){
//            $cities = $request->input('cities'); // Default to all cities
//        }else{
//            $cities = $request->input('cities', ApplicationRecord::pluck('city')->unique()->all()); // Default to all cities
//        }
//        if (!empty($request->employers)){
//            $employers = $request->input('employers');
//        }else{
//            $employers = $request->input('employers', ApplicationRecord::pluck('employer')->unique()->all());
//        }
//        if (!empty($request->statuses)){
//            $statuses = $request->input('statuses'); // Default to APPROVED
//        }else{
//            $statuses = $request->input('statuses', ['APPROVED']); // Default to APPROVED
//        }
//        if (!empty($request->statuses)){
//            $nocs = $request->input('nocs'); // Default to all NOCs
//        }else{
//            $nocs = $request->input('nocs', ApplicationRecord::pluck('noc_2016')->unique()->all()); // Default to all NOCs
//        }
//
//
//
//
//
//
//
//
//        $searchField = $request->input('search_field');
////        $responseData = [
////            'years' => $years,
////            'quarters' => $quarters,
////            'programStreams' => $programStreams,
////            'provinces' => $provinces,
////            'cities' => $cities,
////            'employers' => $employers,
////            'statuses' => $statuses,
////            'nocs' => $nocs,
////            'searchField' => $searchField,
////        ];
////
////        return response()->json(['data' => $responseData, 'message' => 'success'], 200);
//
//            // Build the query to filter records
//            $query = ApplicationRecord::query()
//                ->whereIn('year', $years)
//                ->whereIn('quarter', $quarters)
//                ->whereIn('program_stream', $programStreams)
//                ->whereIn('province', $provinces)
//                ->whereIn('city', $cities)
//                ->whereIn('employer', $employers)
//                ->whereIn('status', $statuses)
//                ->whereIn('noc_2016', $nocs);
//
//
//            // Apply search if provided
//            if ($searchField && empty($request->years)  && empty($request->quarters)  && empty($request->program_streams)  && empty($request->provinces)  && empty($request->cities)
//                && empty($request->employers)  && empty($request->statuses)  && empty($request->nocs)) {
//                $query = ApplicationRecord::query()
//                ->where(function ($query) use ($searchField) {
//                    $query->where('status', 'LIKE', "%$searchField%")
//                        ->orWhere('province', 'LIKE', "%$searchField%")
//                        ->orWhere('program_stream', 'LIKE', "%$searchField%")
//                        ->orWhere('employer', 'LIKE', "%$searchField%")
//                        ->orWhere('address', 'LIKE', "%$searchField%")
//                        ->orWhere('occupation', 'LIKE', "%$searchField%")
//                        ->orWhere('incorporate_status', 'LIKE', "%$searchField%")
//                        ->orWhere('limaS', 'LIKE', "%$searchField%")
//                        ->orWhere('positions', 'LIKE', "%$searchField%")
//                        ->orWhere('year', 'LIKE', "%$searchField%")
//                        ->orWhere('quarter', 'LIKE', "%$searchField%")
//                        ->orWhere('city', 'LIKE', "%$searchField%")
//                        ->orWhere('postal_code', 'LIKE', "%$searchField%")
//                        ->orWhere('noc_2016', 'LIKE', "%$searchField%")
//                        ->orWhere('description', 'LIKE', "%$searchField%");
//
//                });
//            }
//
//            // Retrieve filtered records
//            $filteredRecords = $query->get();
//
//            // Pass the filtered records to a view or return them as needed
//            $response = [
//                'status' => true,
//                'data' => $filteredRecords,
//                'message' => "success",
//            ];
//            return response()->json($response, 200);
//        }
//
//        public function get_app_records(){
//            $years =ApplicationRecord::distinct()->pluck('year');
//            $quarters = ApplicationRecord::distinct()->pluck('quarter');
//            $programStreams = ApplicationRecord::distinct()->pluck('program_stream');
//            $provinces = ApplicationRecord::distinct()->pluck('province');
//            $cities = ApplicationRecord::distinct()->pluck('city');
//            $employers = ApplicationRecord::distinct()->pluck('employer');
//            $statuses = ApplicationRecord::distinct()->pluck('status');
//            $nocs =ApplicationRecord::distinct()->pluck('noc_2016');
//            $data=array();
//            $data['year']=$years;
//            $data['quarter']=$quarters;
//            $data['program_stream']=$programStreams;
//            $data['province']=$provinces;
//            $data['city']=$cities;
//            $data['employer']=$employers;
//            $data['status']=$statuses;
//            $data['noc_2016']=$nocs;
//
//            $response = [
//                'status' => true,
//                'data' => $data,
//                'message' => "success",
//            ];
//            return response()->json($response, 200);
//        }
    }

