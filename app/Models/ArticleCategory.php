<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleCategory extends Model
{
    use HasFactory;
    protected $table = 'articlecategories';
    public $guarded = ['id'];
    protected $hidden = ['created_at','updated_at'];
}
