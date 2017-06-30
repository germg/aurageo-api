<?php

namespace App\Repositories;

use App\Models\Cards;
use Illuminate\Support\Facades\DB;

class CardsRepository
{
    const MODEL = 'App\Models\Cards';
    const UPLOADS_FOLDER = '/uploads';

    /**
     * Obtiene todas las tarjetas de un lugar
     *
     * @return mixed
     */
    public function getByPlaceId($place_id)
    {
        return Cards::where('place_id', '=', $place_id)
            ->where('deleted', 0)
            ->orderBy('created_at', 'desc')
            ->get([
                'id',
                'place_id as placeId',
                DB::raw('concat("' . env('APP_URL') .'", image_url) as imageUrl'),
                'description',
                'created_at as createdAt'
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
            ->orderBy('created_at', 'desc')
            ->skip($offset)
            ->take($limit)
            ->get([
                'id',
                DB::raw('concat("' . env('APP_URL') .'", image_url) as imageUrl'),
                'description',
                'created_at as createdAt'
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
                'created_at as createdAt'
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
        $image_url = "";

        if(isset($data->imageUrl) && !empty($data->imageUrl)){
            $image_url = substr($data->imageUrl, strpos($data->imageUrl, self::UPLOADS_FOLDER));
        }

        return Cards::create([
            'place_id' => $data->placeId,
            'image_url' => !empty($image_url) ? $image_url : null,
            'description' => isset($data->description) ? $data->description : null,
            'deleted' => $data->deleted
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
        $image_url = "";

        if(isset($data->imageUrl) && !empty($data->imageUrl)){
            $image_url = substr($data->imageUrl, strpos($data->imageUrl, self::UPLOADS_FOLDER));
        }

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
