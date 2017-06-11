<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class Users extends Model implements AuthenticatableContract
{
    use Authenticatable;

    // Atributo importante para que no intente guardar los campos: updated_at y created_at
    public $timestamps = false;
    protected $fillable = [
        'name', 'email', 'google_id'
    ];
    protected $hidden = ['google_id'];

    // Relacion NaN con Places
    public function bookmarks()
    {
        return $this->belongsToMany('App\Models\Places', 'bookmarks', 'user_id', 'place_id');
    }

    // Relacion 1aN con Places
    public function places()
    {
        return $this->hasMany('App\Models\Places', 'user_id');
    }
}
