<?php

namespace App\Http\Controllers;

use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ArticleCategoryController extends Controller
{
    function index(Request $request)
    {
        //catch query string page
        $start = $request->query('page', 0) - 1;
        if ($start < 1 || !is_numeric($start)) {
            $start = 0;
        }
        $start = $start*5 ;
        //make filter for each page, for 5 list per page
        $data = DB::table('articlecategories')->offset($start)->limit(5)->get();
        return response()->json([
            'status' => true,
            'message' => $data
        ]);
    }
    function update(Request $request, $id)
    {
        //check input categroyname is exist or not
        if ($request->input('categoryname')) {
            //if exist replace old data wtih new one
            ArticleCategory::find($id)->update([
                'categoryname' => $request->input('categoryname')
            ]);
        }
        return response()->json([
            'status' => true,
            'message' => "success update data"
        ]);
    }
    function store(Request $request)
    {
        //make validator for request input
        $validator =  Validator::make(
            $request->all(),
            [
                'categoryname' => ['required']
            ],
            [
                'required' => ':attribute cannot empty'
            ]
        );
        //if validator fail
        if ($validator->fails()) {
            //if fail
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 400);
        }
        //if not fail create data
        ArticleCategory::create([
            'categoryname' => $request->input('categoryname')
        ]);
        return response()->json([
            'status' => true,
            'message' => "success create data"
        ]);
    }
    function show($id)
    {
        $data = ArticleCategory::find($id);
         //if category with id exist
        if ($data) {
            return response()->json([
                'status' => true,
                'message' => $data
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'data with this id is not found'
        ]);
    }
    function destroy($id)
    {
        //find category with id
        $delete = ArticleCategory::find($id);
        //if category with id exist
        if ($delete) {
            $delete->delete();
            return response()->json([
                'status' => true,
                'message' => 'success delete data'
            ]);
        }
        //if category  not exist
        return response()->json([
            'status' => false,
            'message' => 'data with this id is not found'
        ]);
    }
}
