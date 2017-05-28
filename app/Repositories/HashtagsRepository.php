<?php

namespace App\Repositories;

use App\Models\Hashtags;

class HashtagsRepository
{
    const MODEL = 'App\Models\Hashtags';

    /**
     * Obtiene todos los hashtags
     *
     * @return mixed
     */
    public function get()
    {
        return Hashtags::get();
    }

    /**
     * Obtiene un hashtag por su id
     *
     * @param $id
     * @return mixed
     */
    public function getById($id)
    {
        return Hashtags::find($id);
    }

    /**
     * Obtiene un hashtag por su place_id
     *
     * @param $id
     * @return mixed
     */
    public function getByPlaceId($place_id)
    {
        return Hashtags::where('place_id', '=', $place_id)
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
        return Hashtags::create([
            'place_id' => $data->place_id,
            'description' => $data->description
        ]);
    }

    /**
     * Edita un hashtag
     *
     * @param $data
     * @return mixed
     */
    public function edit($data)
    {
        return Hashtags::where('id', '=', $data->id)
            ->update([
                'place_id' => $data->place_id,
                'description' => $data->description
            ]);
    }

    /**
     * Elimina un hashtag por su id
     *
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        $user = Hashtags::find($id);
        return $user->delete();
    }
}
