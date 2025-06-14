<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $fillable = [
        'category_name',
        'name',
        'description',
        'image',
        'duration',
        'number_of_episodes',
        'genre',
    ];

    public function category(){
        return $this->belongsTo(Category::class, 'category_name', 'name');
    }
}
