<?php

namespace App\Repositories;

use App\Models\Bookmarks;

class BookmarksRepository
{
    const MODEL = 'App\Models\Bookmarks';

    /**
     * Obtiene todos los Bookmarks
     *
     * @return mixed
     */
    public function get()
    {
        return Bookmarks::get();
    }

    /**
     * Obtiene un Bookmarks por user_id
     *
     * @param $id
     * @return mixed
     */
    public function getByUserId($user_id)
    {
        return Bookmarks::where('user_id', '=', $user_id)
            ->get([
                'user_id as userId',
                'place_id as placeId'
            ]);
    }

    /**
     * Obtiene un Bookmarks por place_id
     *
     * @param $id
     * @return mixed
     */
    public function getByPlaceId($place_id)
    {
        return Bookmarks::where('place_id', '=', $place_id)
            ->get([
                'user_id as userId',
                'place_id as placeId'
            ]);
    }

    public function getByUserIdAndPlaceId($user_id, $place_id){
        return Bookmarks::where([
            ['user_id', '=', $user_id],
            ['place_id', '=', $place_id]
        ])->first();
    }

    /**
     * Crea un bookmark
     *
     * @param $user_id
     * @param $place_id
     * @return mixed
     */
    public function create($user_id, $place_id)
    {
        return Bookmarks::create([
            'user_id' => $user_id,
            'place_id' => $place_id,
        ]);
    }

    /**
     * Elimina un bookmark por su user_id y place_id
     *
     * @param $id
     * @return mixed
     */
    public function delete($user_id, $place_id)
    {
        $bookmark = Bookmarks::where([
            ['user_id', '=', $user_id],
            ['place_id', '=', $place_id]
        ]);
        return $bookmark->delete();
    }
}
