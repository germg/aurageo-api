<?php

namespace App\Repositories;

use App\Models\Cards;

class CardsRepository
{
    const MODEL = 'App\Models\Cards';

    /**
     * Obtiene todas las tarjetas de un lugar
     *
     * @return mixed
     */
    public function getByPlaceId($place_id)
    {
        return Cards::where('place_id', '=', $place_id)
            ->get();
    }

    /**
     * Obtiene una tarjeta por su id
     *
     * @param $id
     * @return mixed
     */
    public function getById($id)
    {
        return Cards::find($id);
    }

    /**
     * Crea una tarjeta
     *
     * @param $data
     * @return mixed
     */
    public function create($data)
    {
        return Cards::create([
            'place_id' => $data->place_id,
            'image_url' => isset($data->image_url) ? $data->image_url : null,
            'description' => isset($data->description) ? $data->description : null
        ]);
    }

    /**
     * Edita una tarjeta
     *
     * @param $data
     * @return mixed
     */
    public function edit($data)
    {
        return Cards::where('id', '=', $data->id)
            ->update([
                'place_id' => $data->place_id,
                'image_url' => $data->image_url,
                'description' => $data->description,
            ]);
    }

    /**
     * Elimina una tarjeta por su id
     *
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        return Cards::where('id', '=', $id)
            ->update([
                'deleted' => 1
            ]);
    }
}
