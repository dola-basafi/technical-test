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
        $path = $request->getSchemeAndHttpHost() . '/storage/' . $request->file('media')->store('files', 'public');
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

    function index(Request $request)
    {
        $start = $request->query('page', 0) - 1;
        if ($start < 1 || !is_numeric($start)) {
            $start = 0;
        }
        $start = $start * 5;
        $data = DB::table('articles')
            ->join('articlecategories', 'articles.category_id', '=',  'articlecategories.id')
            ->select('articles.*', 'articlecategories.categoryname')
            ->offset($start)->limit(5)->get();
       
        return response()->json([
            'status' => true,
            'message' => $data
        ]);
    }

    function show(Request $request, $id)
    {
        if (!Article::find($id)) {
            return response()->json([
                'status' => false,
                'messages' => 'data with this id not found'
            ]);
        }
        $data = DB::table('articles')
            ->join('articlecategories', 'articles.category_id', '=',  'articlecategories.id')
            ->where('articles.id', $id)
            ->select('articles.*', 'articlecategories.categoryname')->get();
       
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
        if ($request->hasFile('media')) {
            $banner = str_replace($request->getSchemeAndHttpHost() . "/storage/", "", $data->media);
            Storage::disk('public')->delete($banner);
            $path = $request->getSchemeAndHttpHost() . '/storage/' . $request->file('media')->store('files', 'public');
            $data->media = $path;
        }
        $data->update();
        return response()->json([
            'status' => true,
            'messages' => 'success update data'
        ]);
    }
    function destroy(Request $request, $id)
    {
        $dataDelete = Article::find($id);
        if (!$dataDelete) {
            return response()->json([
                'status' => false,
                'messages' => "data with this id not found"
            ]);
        }
        $banner = str_replace($request->getSchemeAndHttpHost() . "/storage/", "", $dataDelete->media);
        Storage::disk('public')->delete($banner);
        $dataDelete->delete();
        return response()->json([
            'status' => true,
            'messages' => "success delete data"
        ]);
    }
}
