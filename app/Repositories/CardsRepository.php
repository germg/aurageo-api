<?php

namespace App\Repositories;

use App\Models\Cards;
use Illuminate\Support\Facades\DB;

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
            ->where('deleted', 0)
            ->get([
                'id',
                'place_id as placeId',
                DB::raw('concat("' . env('APP_URL') .'", image_url) as imageUrl'),
                'description'
            ]);
    }

    /**
     * Obtiene todas las tarjetas de un lugar con limite y desplazamiento
     *
     * @return mixed
     */
    public function getByPlaceIdWithOffsetAndLimit($place_id, $offset, $limit)
    {
        return Cards::where('place_id', '=', $place_id)
            ->where('deleted', 0)
            ->skip($offset)
            ->take($limit)
            ->get([
                'id',
                DB::raw('concat("' . env('APP_URL') .'", image_url) as imageUrl'),
                'description'
            ]);
    }

    /**
     * Obtiene una tarjeta por su id
     *
     * @param $id
     * @return mixed
     */
    public function getById($id)
    {
        return Cards::where('id', '=', $id)
            ->get([
                'id',
                DB::raw('concat("' . env('APP_URL') .'", image_url) as imageUrl'),
                'description',
                'created_at as createdAt',
                'updated_at as updatedAt',
                'deleted'
            ])
            ->first();
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
            'place_id' => $data->placeId,
            'image_url' => isset($data->imageUrl) ? $data->imageUrl : null,
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
                'place_id' => $data->placeId,
                'image_url' => $data->imageUrl,
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

    /**
     * Actualiza la url de la imagen
     *
     * @param $id
     * @param $image_url
     * @return mixed
     */
    public function updateImageUrl($id, $image_url){
        return Cards::where('id', '=', $id)
            ->update([
                'image_url' => $image_url
            ]);
    }
}
