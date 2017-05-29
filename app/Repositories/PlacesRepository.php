<?php

namespace App\Repositories;

use App\Models\Places;

class PlacesRepository
{
    const MODEL = 'App\Models\Places';

    // TODO: Solo para test
    public function get_all()
    {
        return Places::get(['id',
            'name',
            'description',
            'latitude',
            'longitude',
            'deleted',
            'avatar_url as avatarUrl',
            'user_id as userId',
            'visible',
            'address']);
    }

    /**
     * Obtiene una lista de lugares
     *
     * @param $id
     * @return mixed
     */
    public function get($id)
    {
        return Places::find(['id',
            'name',
            'description',
            'latitude',
            'longitude',
            'deleted',
            'avatar_url as avatarUrl',
            'user_id as userId',
            'visible',
            'address']);
    }

    /**
     * Obtiene un lugar por su id
     *
     * @param $id
     * @return mixed
     */
    public function getById($id)
    {
        return Places::where('id', '=', $id)->get(['id',
            'name',
            'description',
            'latitude',
            'longitude',
            'deleted',
            'avatar_url as avatarUrl',
            'user_id as userId',
            'visible',
            'address']);
    }

    /**
     * Crea un lugar
     *
     * @param $data
     * @return mixed
     */
    public function create($data)
    {
        return Places::create([
            'name' => $data->name,
            'description' => isset($data->description) ? $data->description : null,
            'latitude' => $data->latitude,
            'longitude' => $data->longitude,
            'deleted' => $data->deleted,
            'avatar_url' => $data->avatarUrl,
            'user_id' => $data->userId,
            'visible' => $data->visible,
            'address' => $data->address
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
        return Places::where('id', '=', $data->id)
            ->update([
                'name' => $data->name,
                'description' => $data->description,
                'latitude' => $data->latitude,
                'longitude' => $data->longitude,
                'deleted' => $data->deleted,
                'avatar_url' => $data->avatarUrl,
                'user_id' => $data->userId,
                'visible' => $data->visible,
                'address' => $data->address
            ]);
    }

    /**
     * Elimina un lugar por su id
     *
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        return Places::where('id', '=', $id)
            ->update([
                'deleted' => 1
            ]);
    }
}
