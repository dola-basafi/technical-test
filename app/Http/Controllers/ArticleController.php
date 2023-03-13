<?php

namespace App\Http\Controllers;

use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{
    function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'title' => ['required'],
                'description' => ['required'],
                'category_id' => ['required','numeric'],
                'media' => ['required']
            ],
            [
                'required' => ':attribute cannot empty',
                'numeric' => ':attribute must be number'
            ]
            );
        if($validator->fails()){
            return response()->json([
                'status' =>false,
                'message' =>$validator->errors()
            ]);
        }
        if (!ArticleCategory::find($request->input('category_id'))) {
            return response()->json([
                'status' =>false,
                'message' =>'category with this id is not found'
            ]);
        }
        



    }
}
