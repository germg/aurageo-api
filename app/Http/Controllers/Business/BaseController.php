<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    protected $currentUser;

    /**
     * Constructor de la clase
     *
     * BaseController constructor.
     */
    public function __construct(){
        $this->currentUser = getAuthenticatedUser();
    }

    /**
     * Verifica si el usuario puede realizar la acción
     *
     * @return bool
     */
    public function canPerformAction($user_id)
    {
        return isset($this->currentUser->id) && intval($user_id) === $this->currentUser->id;
    }
}