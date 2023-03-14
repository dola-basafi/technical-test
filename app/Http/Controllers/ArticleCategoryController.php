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
        $start = $request->query('page', 0);
        if ($start < 1 || !is_numeric($start)) {
            $start = 0;
        }
        $start = $start*5 ;
        $data = DB::table('articlecategories')->offset($start)->limit(5)->get();
        return response()->json([
            'status' => true,
            'message' => $data
        ]);
    }
    function update(Request $request, $id)
    {
        if ($request->input('categoryname')) {
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
        $validator =  Validator::make(
            $request->all(),
            [
                'categoryname' => ['required']
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
        $delete = ArticleCategory::find($id);
        if ($delete) {
            $delete->delete();
            return response()->json([
                'status' => true,
                'message' => 'success delete data'
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'data with this id is not found'
        ]);
    }
}
