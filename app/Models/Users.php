<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    // Atributo importante para que no intente guardar los campos: updated_at y created_at
    public $timestamps = false;
    protected $fillable = ['email'];
}