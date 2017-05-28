<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cards extends Model
{
    protected $fillable = ['place_id','image_url','description', 'deleted'];
}