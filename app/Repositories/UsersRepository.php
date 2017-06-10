<?php

namespace App\Repositories;

use App\Models\Users;

class UsersRepository
{
    const MODEL = 'App\Models\Users';

    /**
     * Obtiene todos los usuarios
     *
     * @return mixed
     */
    public function get()
    {
        return Users::get();
    }

    /**
     * Obtiene un usuario por su id
     *
     * @param $id
     * @return mixed
     */
    public function getById($id)
    {
        return Users::find($id);
    }

    public function getByGoogleId($id)
    {
        return Users::where('google_id', $id)->first();
    }

    public function getByEmail($email)
    {
        return Users::where('email', $email)->first();
    }

    /**
     * Crea un usuario
     *
     * @param $data
     * @return mixed
     */
    public function create($data)
    {
        return Users::create([
            'email' => $data->email,
            'name' => $data->name,
            'google_id' => $data->sub
        ]);
    }

    /**
     * Edita un usuario
     *
     * @param $data
     * @return mixed
     */
    public function edit($data)
    {
        return Users::where('id', '=', $data->id)
            ->update(['email' => $data->email]);
    }

    /**
     * Elimina un usuario por su id
     *
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        $user = Users::find($id);
        return $user->delete();
    }
}
