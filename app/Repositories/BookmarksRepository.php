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
        return Hashtags::get();
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
            ->get();
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
            ->get();
    }

    /**
     * Crea un hashtag
     *
     * @param $data
     * @return mixed
     */
    public function create($data)
    {
        return Bookmarks::create([
            'user_id' => $data->user_id,
            'place_id' => $data->place_id,
        ]);
    }

    /**
     * Elimina un bookmark por su id
     *
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        $user = Bookmarks::find($id);
        return $user->delete();
    }
}
