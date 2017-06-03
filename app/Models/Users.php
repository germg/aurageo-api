<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    // Atributo importante para que no intente guardar los campos: updated_at y created_at
    public $timestamps = false;
    protected $fillable = [
    'name', 'email', 'password',
    ];

    protected $hidden = [
    'password', 'remember_token',
    ];

    // Relacion NaN con Places
    public function places()
    {
        return $this->belongsToMany('App\Models\Places', 'bookmarks', 'user_id', 'place_id');
    }

    // Relacion 1aN con Places
    public function placesN()
    {
        return $this->hasMany('App\Models\Places', 'user_id');
    }
}
