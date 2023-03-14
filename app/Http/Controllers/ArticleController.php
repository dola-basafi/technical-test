<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

use function GuzzleHttp\Promise\all;

class ArticleController extends Controller
{
    function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'title' => ['required'],
                'description' => ['required'],
                'category_id' => ['required', 'numeric'],
                'media' => ['required', 'max:2048']
            ],
            [
                'required' => ':attribute cannot empty',
                'numeric' => ':attribute must be number',
                'max' => ':attribute maximum 2 MB'
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'messages' => $validator->errors()
            ]);
        }
        if (!ArticleCategory::find($request->input('category_id'))) {
            return response()->json([
                'status' => false,
                'messages' => 'category with this id is not found'
            ]);
        }
        $path = Storage::putFile('public/files', $request->file('media'));        
        

        Article::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'category_id' => $request->input('category_id'),
            'media' => $path
        ]);
        return response()->json([
            'status' => true,
            'messages' => 'success create data'
        ]);
    }

    function index()
    {       
        $data = DB::table('articles')
                ->join('articlecategories', 'articles.category_id', '=',  'articlecategories.id')
                ->select('articles.*','articlecategories.categoryname') ->get();
        foreach ($data as $key => $value) {            
            $data[$key]->media = "http://127.0.0.1:8000/" . $data[$key]->media;
            $data[$key]->media = str_replace("/public/","/storage/",$data[$key]->media);
        }
        return response()->json([
            'status' => true,
            'message' => $data
        ]);
    }

    function show($id)
    {
        $data = DB::table('articles')
        ->join('articlecategories', 'articles.category_id', '=',  'articlecategories.id')
        ->where('articles.id',$id)
        ->select('articles.*','articlecategories.categoryname') ->get();            
        $data[0]->media = "http://127.0.0.1:8000/" . $data[0]->media;
        $data[0]->media = str_replace("/public/","/storage/",$data[0]->media);
        return response()->json([
            'status' => false,
            'message' => $data
        ]);
    }

    function update(Request $request, $id)
    {
        $data = Article::find($id);
        if (!$data) {
            return response()->json([
                'status' => false,
                'messages' => 'data with this id is not found'
            ]);
        }
        if ($request->input('title')) {
            $data->title = $request->input('title');
        }
        if ($request->input('descripton')) {
           $data->description = $request->input('description');
        }
        if ($request->input('category_id')) {
            $cekCategory = ArticleCategory::find($request->input('category_id'));
            if (!$cekCategory) {
                return response()->json([
                    'status' => false,
                    'messages' => 'category id is not found'
                ]);
            }
            $data->category_id = $request->input('category_id');            
        }
        if ($request->hasFile('media')){
            Storage::delete($data->media);
            $path = Storage::putFile('public/files',$request->file('media'));            
        }
        $data->update();
        return response()->json([
            'status' => true,
            'messages' => 'success update data'
        ]);
    }
    function destroy($id){
        $dataDelete = Article::find($id);
        if (!$dataDelete) {
                return response()->json([
                    'status' => false,
                    'messages' => "data with this id not found"
                ]);
        }
        Storage::delete($dataDelete->media);
        $dataDelete->delete();
        return response()->json([
            'status' => true,
            'messages' => "success delete data"
        ]);
    }
}
