<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Places extends Model
{
    // Atributo importante para que no intente guardar los campos: updated_at y created_at
    public $timestamps = false;
    protected $fillable = ['name','description','latitude','longitude','deleted','avatar_url','user_id','visible','address'];

    // Relacion NaN con Users
    public function bookmarks()
    {
        return $this->belongsToMany('App\Models\Users', 'bookmarks', 'user_id', 'place_id');
    }

    // Relacion 1aN con users
    public function user()
    {
        return $this->belongsTo('App\Models\Users', 'user_id');
    }
}