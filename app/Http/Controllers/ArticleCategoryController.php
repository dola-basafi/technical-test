<?php

namespace App\Http\Controllers;

use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArticleCategoryController extends Controller
{
    function index()
    {
        $data = ArticleCategory::all();
        return response()->json([
            'status' => true,
            'message' => $data
        ]);
    }
    function store(Request $request)
    {
        $validator =  Validator::make(
            $request->all(),
            [
                'name' => ['required']
            ],
            [
                'required' => ':attribute cannot empty'
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 400);
        }
        ArticleCategory::create([
            'name' => $request->input('name')
        ]);
        return response()->json([
            'status' => true,
            'message' => "success create data"
        ]);
    }
    function show($id)
    {
        $data = ArticleCategory::find($id);
        return response()->json([
            'status' => true,
            'message' => $data
        ]);
    }
}
