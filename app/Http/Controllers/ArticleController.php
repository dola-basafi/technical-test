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
        //make validation for request input
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
        //if category id exist on database
        if (!ArticleCategory::find($request->input('category_id'))) {
            //if category id not exist
            return response()->json([
                'status' => false,
                'messages' => 'category with this id is not found'
            ]);
        }
        //save file to files folder
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
        //catch query string page
        $start = $request->query('page', 0) - 1;
        if ($start < 1 || !is_numeric($start)) {
            $start = 0;
        }
        $start = $start * 5;
        //search each article with their category
        $data = DB::table('articles')
            ->join('articlecategories', 'articles.category_id', '=',  'articlecategories.id')
            ->select('articles.*', 'articlecategories.categoryname')
            ->offset($start)->limit(5)->get(); //make filter for each page, for 5 list per page
        //response json with all data
        return response()->json([
            'status' => true,
            'message' => $data
        ]);
    }

    function show(Request $request, $id)
    {
        //find article by id
        if (!Article::find($id)) {
            //if not found
            return response()->json([
                'status' => false,
                'messages' => 'data with this id not found'
            ]);
        }
        //search article with their category
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
        //find article by id
        $data = Article::find($id);
        if (!$data) {
            //if not found
            return response()->json([
                'status' => false,
                'messages' => 'data with this id is not found'
            ]);
        }
        //check request title exist or not
        if ($request->input('title')) {
            $data->title = $request->input('title');
        }
        //check request description exist or not
        if ($request->input('description')) {
            $data->description = $request->input('description');
        }
        //check request category_id exist or not
        if ($request->input('category_id')) {
            //if exist
            //find category_id to table articlecategory
            $cekCategory = ArticleCategory::find($request->input('category_id'));

            if (!$cekCategory) {
                return response()->json([
                    'status' => false,
                    'messages' => 'category id is not found'
                ]);
            }
            //if exist
            $data->category_id = $request->input('category_id');
        }
        //check request have file ?
        if ($request->hasFile('media')) {
            //replace url with local directory
            $banner = str_replace($request->getSchemeAndHttpHost() . "/storage/", "", $data->media);
            //delete old files
            Storage::disk('public')->delete($banner);
            //save new file to files folder 
            $path = $request->getSchemeAndHttpHost() . '/storage/' . $request->file('media')->store('files', 'public');
            $data->media = $path;
        }
        //update article
        $data->update();
        return response()->json([
            'status' => true,
            'messages' => 'success update data'
        ]);
    }
    function destroy(Request $request, $id)
    {
        //find article with id
        $dataDelete = Article::find($id);
        if (!$dataDelete) {
            //if not found
            return response()->json([
                'status' => false,
                'messages' => "data with this id not found"
            ]);
        }
        //replace url with local directory
        $banner = str_replace($request->getSchemeAndHttpHost() . "/storage/", "", $dataDelete->media);
        Storage::disk('public')->delete($banner);
        //delete article
        $dataDelete->delete();
        return response()->json([
            'status' => true,
            'messages' => "success delete data"
        ]);
    }
}
