<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\ApplicationRecord;
use App\Models\Admin\Blog;
use App\Models\Admin\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{
    public function store(Request $request){
        $validator=Validator::make($request->all(),[
           "name"=>"required|max:100|min:1",
           "category"=> "required|max:100|min:1",
            "thumbnail"=>"required|image|mimes:jpeg,png,jpg,gif|max:2048",
            "description"=>"required|string|min:1|max:5000",
        ]);
        if($validator->fails()){

            $response = [
                'status' => false,
                'errors'    => $validator->errors(),
                'message' => "Validation Fails",
            ];


            return response()->json($response, 404);

        }


            $imagePath = $request->file('thumbnail');
            $fileName = time() . '_thumbnail'.rand(4000,6000);
            $imagePath->move(public_path('uploads/blogs/thumbnails'), $fileName);





        $data=$request->all();
        $data['user_id']=$request->user()->id;
        $data['thumbnail']='uploads/blogs/thumbnails/' . $fileName;
        $blog=Blog::create($data);
        $response = [
            'status' => true,
            'data'    => $blog,
            'message' => "success",
        ];
        return response()->json($response, 200);
    }

    public function index(){
        $blog=Blog::all();
        $response = [
            'status' => true,
            'data'    => $blog,
            'message' => "success",
        ];
        return response()->json($response, 200);
    }
    public function get_by_id($id){
        $blog=Blog::where('id',$id)->first();
        $response = [
            'status' => true,
            'data'    => $blog,
            'message' => "success",
        ];
        return response()->json($response, 200);

    }
    public function update_by_id(Request $request){
        $validator=Validator::make($request->all(),[
            "name"=>"required|max:100|min:1",
            "category"=>"required|max:100|min:1",
            "thumbnail"=>"nullable|image|mimes:jpeg,png,jpg,gif|max:2048",
            "description"=>"required|string|min:1|max:5000",
            "id"=>"required|numeric|between:1,999999999|exists:blogs,id"
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
        if ($request->hasFile('thumbnail')) {
            $imagePath = $request->file('thumbnail');
            $fileName = time() . '_thumbnail'.rand(4000,6000);
            $imagePath->move(public_path('uploads/blogs/thumbnails'), $fileName);
            $data['thumbnail']='uploads/blogs/thumbnails/' . $fileName;

        }

        $blog=Blog::where('id',$request->id)->first();
        $blog->update($data);

        $response = [
            'status' => true,
            'data'    => $blog,
            'message' => "success",
        ];
        return response()->json($response, 200);
    }

    public function del_by_id($id){
        $blog=Blog::where('id',$id)->first();

        if ($blog){
            $blog->delete();
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
}
