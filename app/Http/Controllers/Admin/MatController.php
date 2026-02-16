<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ExportMats;
use App\Models\Admin\TrendingMats;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use GuzzleHttp\Client;
use App\Http\Controllers\Controller;
use App\Imports\MatsImport;
use App\Models\Admin\ApplicationRecord;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Admin\Mat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MatController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'region' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'website' => 'required|url|max:255',
            'physical_address' => 'required|string|max:255',
            "open_mat_time"=>"required|string|max:255",
            "open_mat_day"=>"required|string|max:255",
            "link_to_waiver"=>"nullable|string|max:255",
            "other_info"=>"nullable|string|max:255",
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5048',
            'event_date' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => false,
                'errors'    => $validator->errors(),
                'message' => "Validation Fails",
            ];
            return response()->json($response, 404);

        }








        $validatedData =  $validator->validated();
        $validatedData['user_id'] = $request->user()->id;
//        $validatedData['event_date'] = $request->date;
//        $base64Image=$request->image;
//        if ($request->image!=null){
//            if (Str::startsWith($base64Image, 'data:image/')) {
//                list($type, $base64Image) = explode(';', $base64Image);
//                list(, $base64Image) = explode(',', $base64Image);
//
//                $decoded = base64_decode($base64Image);
//
//                // Determine the file extension based on the content type
//                $extension = explode('/', $type)[1];
//            } else {
//                // If the base64 string doesn't have a data URI scheme, assume it's already decoded
//                $decoded = base64_decode($base64Image);
//                $extension = 'png'; // You may want to adjust this default extension
//            }
//
//            // Generate a unique name for the image file
//            $imageName = uniqid() . '.' . $extension;
//            $imagePath = 'images/' . $imageName;
//
//            Storage::disk('public')->put($imagePath, $decoded);
//            $validatedData['image'] = "https://openmat.dev-mn.xyz/storage/".$imagePath;
//        }else{
//            $validatedData['image'] = null;
//        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = 'images/' . $imageName;

            $image->storeAs('public', $imagePath);

            $validatedData['image'] = asset('storage/' . $imagePath);
//            $validatedData['image'] = "images/".$imageName;

        } else {
            $validatedData['image'] = null;
        }
        $apiKey = 'AIzaSyBiIvCLftcHwIONNtyTUsGHkS1vZuCPeuw';
        $client = new Client();
        $response = $client->request('GET', 'https://maps.googleapis.com/maps/api/geocode/json', [
            'query' => [
                'address' => urlencode($request->physical_address),
                'key' => $apiKey,
            ]
        ]);
//dd(1);
        $body = json_decode($response->getBody(), true);

        if ($body['status'] === 'OK') {
            $location = $body['results'][0]['geometry']['location'];
            $latitude = $location['lat'];
            $longitude = $location['lng'];
//dd($longitude);
            $validatedData['longitude']=$longitude;
            $validatedData['latitude']=$latitude;
            $mat = Mat::create($validatedData);
            $allmats=Mat::where('user_id',$request->user()->id)->get();

            $response = [
                'status' => true,
                'data'    => $allmats,
                'message' => "success",
            ];
            return response()->json($response, 200);
        } else {
            // Handle error, such as invalid address or API quota exceeded
            $response = [
                'status' => false,
                'errors'    => ['error'=>"Something went wrong with google's map fetching"],
                'message' => "Try Again",
            ];
            return response()->json($response, 404);
        }


    }

    public function all_mats(){
        $mats=Mat::where('user_id',request()->user()->id)->with('trendingMat')->get();
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
    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'region' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'website' => 'required|url|max:255',
            'physical_address' => 'required|string|max:255',
            "open_mat_time"=>"required|string|max:255",
            "open_mat_day"=>"required|string|max:255",
            "link_to_waiver"=>"nullable|string|max:255",
            "other_info"=>"nullable|string|max:255",
            "id"=>"required|numeric|exists:mats,id|between:1,999999999",
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5048',
            'event_date' => 'required|date_format:Y-m-d',
        ]);

        if($validator->fails()){

            $response = [
                'status' => false,
                'errors'    => $validator->errors(),
                'message' => "Validation Fails",
            ];


            return response()->json($response, 404);

        }
        $apiKey = 'AIzaSyBiIvCLftcHwIONNtyTUsGHkS1vZuCPeuw';
        $client = new Client();
        $response = $client->request('GET', 'https://maps.googleapis.com/maps/api/geocode/json', [
            'query' => [
                'address' => urlencode($request->physical_address),
                'key' => $apiKey,
            ]
        ]);
        $validatedData =  $validator->validated();
        $validatedData['user_id'] = $request->user()->id;
//        $validatedData['event_date'] = $request->date;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = 'images/' . $imageName;

            $image->storeAs('public', $imagePath);

            $validatedData['image'] = asset('storage/' . $imagePath);

        }
        $body = json_decode($response->getBody(), true);

        if ($body['status'] === 'OK') {
            $location = $body['results'][0]['geometry']['location'];
            $latitude = $location['lat'];
            $longitude = $location['lng'];
//dd($longitude);
            $validatedData['longitude'] = $longitude;
            $validatedData['latitude'] = $latitude;
        }

        $mat=Mat::where('id',$request->id)->first();
        $mat->update($validatedData);
        $response = [
            'status' => true,
            'data'    => $mat,
            'message' => "success",
        ];
        return response()->json($response, 200);
    }
    public function del_mat($id){
        $mat=Mat::where('id',$id)->first();

        if ($mat){
            $mat->delete();
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


//    public function import(Request $request)
//    {
//        $validator = Validator::make($request->all(), [
//            'file' => 'required|file|mimes:doc,docx',
//        ]);
//
//        if($validator->fails()) {
//
//            $response = [
//                'status' => false,
//                'errors' => $validator->errors(),
//                'message' => "Validation Fails",
//            ];
//
//
//            return response()->json($response, 404);
//
//        }
//        $requiredColumns = ['Name', 'Region', 'State', 'Location', 'Phone Number', 'Website', 'Physical Address', 'Open Mat Time', 'Open Mat Day', 'Link to waiver', 'Other Info'];
//
//        $file = $request->file('file');
//        $fileContents = file($file->getPathname());
//        $lineNumber = 0;
//
//// Validate header row
//        $headerRow = str_getcsv($fileContents[0]);
//        $trimmedHeaderRow = array_map('trim', $headerRow);
//
//        if (count($trimmedHeaderRow) !== count($requiredColumns)) {
//            $response = [
//                'status' => false,
//                'message' => 'The  file must have exactly these columns: ' . implode(', ', $requiredColumns),
//                'actual_columns' => $trimmedHeaderRow,
//            ];
//            return response()->json($response, 400);
//        }
//
//// Remove invisible characters (BOM) from column names
//        $trimmedHeaderRow = array_map(function ($column) {
//            return preg_replace('/[[:^print:]]/', '', $column);
//        }, $trimmedHeaderRow);
//
//        if (array_diff($requiredColumns, $trimmedHeaderRow)) {
//            $response = [
//                'status' => false,
//                'message' => 'The  file must have exactly these columns: ' . implode(', ', $requiredColumns),
//                'actual_columns' => $trimmedHeaderRow,
//            ];
//            return response()->json($response, 400);
//        }
//
//
//
//        try {
//            $file = $request->file('file');
//            $fileContents = file($file->getPathname());
//            $lineNumber = 0;
//
//            foreach ($fileContents as $line) {
//                $lineNumber++;
//
//                if ($lineNumber === 1) {
//                    continue;
//                }
//                $data = str_getcsv($line);
//                $existingRecord = Mat::where([
//                    'name' => $data[0],
//                    'region' => $data[1],
//                    'state' => $data[2],
//                    'location' => $data[3],
//                    'phone_number' => $data[4],
//                    'website' => $data[5],
//                    'physical_address' => $data[6],
//                    'open_mat_time' => $data[7],
//                    'open_mat_day' => $data[8],
//                    'link_to_waiver' => $data[9],
//                    'other_info' => $data[10],
//                    // Add conditions for other columns as needed
//                ])->first();
//                if (!$existingRecord) {
//                    $record = Mat::create([
//                        'name' => $data[0],
//                        'region' => $data[1],
//                        'state' => $data[2],
//                        'location' => $data[3],
//                        'phone_number' => $data[4],
//                        'website' => $data[5],
//                        'physical_address' => $data[6],
//                        'open_mat_time' => $data[7],
//                        'open_mat_day' => $data[8],
//                        'link_to_waiver' => $data[9],
//                        'other_info' => $data[10],
//                        'user_id'=>request()->user()->id,
//                    ]);
//                }
//            }
//
//
//            $response = [
//                'status' => true,
//                'message' => "success",
//            ];
//            return response()->json($response, 200);
//        } catch (\Exception $exception){
//            $response = [
//                'status' => false,
//                'errors'    => $exception->getMessage(),
//                'message' => "Validation Fails",
//            ];
//            return response()->json($response, 404);
//        }
//
//    }

//    public function extractDataWithImages(Request $request)
//    {
//        // Validate the uploaded file
//        $request->validate([
//            'file' => 'required|mimes:xlsx,xls',
//        ]);
////        $imageName= $request->file->getClientOriginalName();
////        $imagePath = 'images/' . $imageName;
////        $filePath=  $request->file->storeAs('public', $imagePath);
//////        dd($filePath);
//        // Load the Excel file
//        $filePath = public_path('storage/images/data.xlsx');
//        $spreadsheet = IOFactory::load($filePath);
//
//        // Get the active sheet
//        $sheet = $spreadsheet->getActiveSheet();
//
//        // Validate required headings
//        $requiredHeadings = [
//            'Name', 'Region', 'State', 'Location', 'Phone Number',
//            'Website', 'Physical Address', 'Open Mat Time', 'Open Mat Day',
//            'Link to Waiver', 'Other Info', 'Image'
//        ];
//
//        $actualHeadings = [];
//
//        foreach ($sheet->getRowIterator()->current()->getCellIterator() as $cell) {
//            $actualHeadings[] = $cell->getValue();
//        }
//
//        $missingHeadings = array_diff($requiredHeadings, $actualHeadings);
//
//        if (!empty($missingHeadings)) {
//            return response()->json(['error' => 'The uploaded file is missing required headings: ' . implode(', ', $missingHeadings)], 400);
//        }
//
//        // Get the highest row and column indices
//        $highestRow = $sheet->getHighestRow();
//
//        $extractedData = [];
//
//        // Iterate through each row
//        for ($row = 2; $row <= $highestRow; ++$row) { // Start from row 2 assuming row 1 is headers
//            // Get cell values
//            $rowData = [];
//
//            foreach ($sheet->getRowIterator($row)->current()->getCellIterator() as $cell) {
//                $rowData[] = $cell->getValue();
//            }
//
//            list($name, $region, $state, $location, $phone_number, $website, $physical_address, $open_mat_time, $open_mat_day, $link_to_waiver, $other_info, $image) = $rowData;
//
//            // Get image path
//            $drawing = $sheet->getDrawingCollection()[$row - 1] ?? null;
//            $imagePath = null;
//
//            if ($drawing instanceof Drawing) {
//                // Save image to a file
//                $imagePath = storage_path('app/images/image_' . $row . '.' . $drawing->getExtension());
//                file_put_contents($imagePath, $drawing->getContents());
//            }
//
//            // Store data in the database
//            $extractedData[] = compact('name', 'region', 'state', 'location', 'phone_number', 'website', 'physical_address', 'open_mat_time', 'open_mat_day', 'link_to_waiver', 'other_info', 'imagePath');
//
//            Mat::create(compact('name', 'region', 'state', 'location', 'phone_number', 'website', 'physical_address', 'open_mat_time', 'open_mat_day',
//                'link_to_waiver', 'other_info', 'image'));
//        }
//
//        return response()->json(['data' => $extractedData, 'message' => 'Data extracted and saved successfully.']);
//    }

    public function import(Request $request){

        if (empty($request->file('file'))){
            $response=[
                "status"=>false,
                "data"=>"No file selected",
                "message"=>"Something Went Wrong",
            ];
            return response()->json($response,400);
        }

        $validator = Validator::make(
            [
                'file'      => $request->file,
                'extension' => strtolower($request->file->getClientOriginalExtension()),
            ],
            [
                'file'          => 'required',
                'extension'      => 'required|in:xlsx,xls',
            ]
        );
        if ($validator->fails()){
            $response=[
                "status"=>false,
                "data"=>$validator->errors(),
                "message"=>"Something Went Wrong",
            ];
            return response()->json($response,400);
        }
        try {
//            $response=[
//                "status"=>true,
//                "message"=>"Wait i am on break",
//            ];
//            return response()->json($response,400);

            $import = new MatsImport();
            Excel::import($import, $request->file('file'));

            // Import successful
            return response()->json(['message' => 'Import successful']);
        }catch (\Maatwebsite\Excel\Validators\ValidationException $e){
            return response()->json(['error' => $e->errors()], 422);


        }




    }

    public function export()
    {
        return Excel::download(new ExportMats(), 'data.xlsx');
    }

    public function store_tending_mats(Request $request){

        $validator=Validator::make($request->all(),[

            "id"=>"required|numeric|exists:mats,id",
            'status' => ['required', 'in:true,false'],


        ]);
        if ($validator->fails()){
            $response = [
                'status' => false,
                'errors'    => $validator->errors(),
                'message' => "Validation Fails",
            ];


            return response()->json($response, 404);
        }

//        foreach ($request->id as $appId) {

            $ack = Mat::where(['id'=>$request->id])->first();
        $ack->featured=$request->status;
        $ack->save();
//            if ($ack) {
//                $trendingMat=new TrendingMats();
//                $trendingMat->name=$ack->name;
//                $trendingMat->region=$ack->region;
//                $trendingMat->state=$ack->state;
//                $trendingMat->location=$ack->location;
//                $trendingMat->phone_number=$ack->phone_number;
//                $trendingMat->website=$ack->website;
//                $trendingMat->physical_address=$ack->physical_address;
//                $trendingMat->open_mat_time=$ack->open_mat_time;
//                $trendingMat->open_mat_day=$ack->open_mat_day;
//                $trendingMat->link_to_waiver=$ack->link_to_waiver;
//                $trendingMat->other_info=$ack->other_info;
//                $trendingMat->event_date=$ack->event_date;
//                $trendingMat->image=$ack->image;
//                $trendingMat->user_id =$ack->user_id ;
//                $trendingMat->mat_id=$ack->id;
//                $trendingMat->save();
//
//
//            } else {
//                $response = array(
//                    'status' => false,
//                    'message' => "Invalid Id"
//                );
//                return response()->json($response, 404);
//            }
//        }
//        $all_trending_mats=TrendingMats::all();
        $response = array(
            'status' => true,
            'message' => "Success"
        );
        return response()->json($response, 200);

//        $ack->borrower_signature_image_path = asset( $ack->borrower_signature_image_path);



    }
    public function tending_mat($id){
        $mat=TrendingMats::where('id',$id)->first();
        $response = array(
            'status' => true,
            'data'=>$mat,
            'message' => "Success"
        );
        return response()->json($response, 200);

    }
    public function get_tending_mats(Request $request){



            $mats = TrendingMats::all();

        $response = array(
            'status' => true,
            'data'=>$mats,
            'message' => "Success"
        );
        return response()->json($response, 200);

//        $ack->borrower_signature_image_path = asset( $ack->borrower_signature_image_path);



    }
    public function del_tending_mat($id){

        $mat=TrendingMats::where('id',$id)->first();

        if ($mat){
            $mat->delete();
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

    public function mat_count(){
        $mats=Mat::all();
        if (!empty($mats)){
            $mat_count=count($mats);
        }else{
            $mat_count=0;
        }

        $featured_mats=Mat::where('featured','true')->get();
        if (!empty($featured_mats)){
            $count_featured_mat=count($featured_mats);
        }else{
            $count_featured_mat=0;
        }



        $data=array();
        $data['mat_count']=$mat_count;
        $data['featured_mats']=$count_featured_mat;

        $response = [
            'status' => true,
            'data'=>$data,
            'message' => "success",
        ];
        return response()->json($response, 200);

    }

    public function get_region(){
        $locations = Mat::distinct()->pluck('region');
        $response = [
            'status' => true,
            'data'=>$locations,
            'message' => "success",
        ];
        return response()->json($response, 200);

    }
//abc

}
