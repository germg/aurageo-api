<?php

namespace App\Repositories;

use App\Models\Places;
use Illuminate\Support\Facades\DB;

class PlacesRepository
{
    const MODEL = 'App\Models\Places';

    /**
     *  Obtiene una lista de lugares marcado como favorito por user_id
     *
     * @param $user_id
     * @return mixed
     */
    public function getBookmarkedByUserId($user_id)
    {
        return Places::join('bookmarks', 'bookmarks.place_id', 'id')
            ->where('bookmarks.user_id', '=', $user_id)
            ->get(['id',
                'name',
                'description',
                'latitude',
                'longitude',
                'deleted',
                'avatar_url as avatarUrl',
                'places.user_id as userId',
                'visible',
                'address']);
    }

    /**
     * Obtiene una lista de lugares por su user_id
     * @param $user_id
     * @return mixed
     */
    public function getByUserId($user_id)
    {
        return Places::where('user_id', '=', $user_id)
            ->get(['id',
                'name',
                'description',
                'latitude',
                'longitude',
                'deleted',
                'avatar_url as avatarUrl',
                'places.user_id as userId',
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
        return Places::where('id', '=', $id)
            ->get(['id',
                'name',
                'description',
                'latitude',
                'longitude',
                'deleted',
                'avatar_url as avatarUrl',
                'user_id as userId',
                'visible',
                'address'])
            ->first();
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

    /**
     * Obtiene una lista de lugares cercanos al las coordenadas pasadas
     *
     * @param $latitude
     * @param $langitude
     * @return mixed
     */
    public function getPlacesNearToCoordinate($latitude, $langitude)
    {

        return Places::where([
    ['visible', '=', '1'],
    ['deleted', '=', '0'],
])
        ->select(['id',
            'name',
            'description',
            'latitude',
            'longitude',
            'deleted',
            'avatar_url as avatarUrl',
            'user_id as userId',
            'visible',
            'address',
            DB::raw('(6351 * acos( cos( radians('.$latitude.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$langitude.') ) + sin( radians('.$latitude.') ) * sin(radians(latitude)) ) ) AS distance')
        ])
            ->orderBy('distance')
            ->havingRaw('distance < ' . env("PLACES_SEARCH_DISTANCE"))
            ->get()->take(env("LIMIT_SEARCH_DISTANCE"));
    }
}
