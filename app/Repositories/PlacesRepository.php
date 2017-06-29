<?php

namespace App\Repositories;

use App\Models\Places;
use Illuminate\Support\Facades\DB;

class PlacesRepository
{
    const MODEL = 'App\Models\Places';
    const UPLOADS_FOLDER = '/uploads';

    private $bookmarksRepository;
    private $hashtagsRepository;
    private $current_user_id;

    public function __construct(BookmarksRepository $bookmarksRepository, HashtagsRepository $hashtagsRepository)
    {
        $this->bookmarksRepository = $bookmarksRepository;
        $currentUser = getAuthenticatedUser();
        $this->current_user_id = isset($currentUser->id) ? $currentUser->id : 0;
        $this->hashtagsRepository = $hashtagsRepository;
    }

    /**
     * Sabe decir si el lugar esta marcado como favorito
     *
     * @param $id
     * @param $current_user_id
     * @return bool
     */
    private function isBookmarked($id)
    {
        return $this->bookmarksRepository->getByUserIdAndPlaceId($this->current_user_id, $id) !== null;
    }

    /**
     * Completa atributos calculados
     *
     * @param $data
     * @param $current_user_id
     */
    private function completeAttributes(&$data)
    {
        if ($data) {

            if (is_array($data)) {
                for ($i = 0; $i < sizeof($data); $i++) {
                    $data[$i]["bookmarked"] = $this->isBookmarked($data[$i]["id"]);
                    $data[$i]["owned"] = $data[$i]["userId"] === $this->current_user_id;
                    $data[$i]["hashtags"] = $this->hashtagsRepository->getByPlaceId($data[$i]["id"]);
                }
            } else {
                if (isset($data->id)) {
                    $data->bookmarked = $this->isBookmarked($data->id);
                    $data->owned = $data->userId === $this->current_user_id;
                    $data->hashtags = $this->hashtagsRepository->getByPlaceId($data->id);
                }
            }
        }
    }

    /**
     *  Obtiene una lista de lugares marcado como favorito por user_id
     *
     * @param $user_id
     * @return mixed
     */
    public function getBookmarkedByUserId($user_id)
    {
        $places = Places::join('bookmarks', 'bookmarks.place_id', 'id')
            ->where([
                ['bookmarks.user_id', '=', $user_id],
                ['deleted', '=', 0]
            ])->get(['id',
                'name',
                'description',
                'latitude',
                'longitude',
                'deleted',
                DB::raw('concat("' . env('APP_URL') . '", avatar_url) as avatarUrl'),
                'places.user_id as userId',
                'visible',
                'address']);

        $this->completeAttributes($places);

        return $places;
    }

    /**
     * Obtiene una lista de lugares por su user_id
     * @param $user_id
     * @return mixed
     */
    public function getByUserId($user_id)
    {
        $places = Places::where([
            ['user_id', '=', $user_id],
            ['deleted', '=', 0]
        ])->get(['id',
            'name',
            'description',
            'latitude',
            'longitude',
            'deleted',
            DB::raw('concat("' . env('APP_URL') . '", avatar_url) as avatarUrl'),
            'places.user_id as userId',
            'visible',
            'address'])->toArray();

        $this->completeAttributes($places);

        return $places;
    }

    /**
     * Obtiene una lista de lugares
     *
     * @param $id
     * @return mixed
     */
    public function get($id)
    {
        $place = Places::find(['id',
            'name',
            'description',
            'latitude',
            'longitude',
            'deleted',
            DB::raw('concat("' . env('APP_URL') . '", avatar_url) as avatarUrl'),
            'user_id as userId',
            'visible',
            'address']);

        $this->completeAttributes($place);

        return $place;
    }

    /**
     * Obtiene un lugar por su id
     *
     * @param $id
     * @return mixed
     */
    public function getById($id)
    {
        $place = Places::where('id', $id)
            ->get(['id',
                'name',
                'description',
                'latitude',
                'longitude',
                'deleted',
                DB::raw('concat("' . env('APP_URL') . '", avatar_url) as avatarUrl'),
                'user_id as userId',
                'visible',
                'address'
            ])
            ->first();

        $this->completeAttributes($place);

        return $place;
    }

    /**
     * Obtiene un lugar por el id de tarjeta
     *
     * @param $card_id
     * @return mixed
     */
    public function getByCardId($card_id){
        return Places::join('cards', 'cards.place_id', 'places.id')
            ->where('cards.id', $card_id)
            ->get(['places.id',
                'places.name',
                'places.description',
                'places.latitude',
                'places.longitude',
                'places.deleted',
                DB::raw('concat("' . env('APP_URL') . '", places.avatar_url) as avatarUrl'),
                'places.user_id as userId',
                'places.visible',
                'places.address'
            ])
            ->first();
    }

    /**
     * Obtiene un lugar po id de hashtag
     *
     * @param $hashtag_id
     * @return mixed
     */
    public function getByHashtagId($hashtag_id){
        return Places::join('hashtags', 'hashtag.place_id', 'places.id')
            ->where('hashtag.id', $hashtag_id)
            ->get(['places.id',
                'places.name',
                'places.description',
                'places.latitude',
                'places.longitude',
                'places.deleted',
                DB::raw('concat("' . env('APP_URL') . '", places.avatar_url) as avatarUrl'),
                'places.user_id as userId',
                'places.visible',
                'places.address'
            ])
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
        $avatar_url = substr($data->avatarUrl, strpos($data->avatarUrl, self::UPLOADS_FOLDER));

        return Places::create([
            'name' => $data->name,
            'description' => isset($data->description) ? $data->description : null,
            'latitude' => $data->latitude,
            'longitude' => $data->longitude,
            'deleted' => $data->deleted,
            'avatar_url' => $avatar_url,
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
        $avatar_url = substr($data->avatarUrl, strpos($data->avatarUrl, self::UPLOADS_FOLDER));

        return Places::where('id', '=', $data->id)
            ->update([
                'name' => $data->name,
                'description' => $data->description,
                'latitude' => $data->latitude,
                'longitude' => $data->longitude,
                'deleted' => $data->deleted,
                //'avatar_url' => $data->avatarUrl, // Por el momento en editar no se actualiza la url del avatar
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
        $places = Places::where([
            ['visible', '=', '1'],
            ['deleted', '=', '0'],
        ])->select(['id',
            'name',
            'description',
            'latitude',
            'longitude',
            'deleted',
            DB::raw('concat("' . env('APP_URL') . '", avatar_url) as avatarUrl'),
            'user_id as userId',
            'visible',
            'address',
            DB::raw('(6351 * acos( cos( radians(' . $latitude . ') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(' . $langitude . ') ) + sin( radians(' . $latitude . ') ) * sin(radians(latitude)) ) ) AS distance')
        ])
            ->orderBy('distance')
            ->havingRaw('distance < ' . env("PLACES_SEARCH_DISTANCE"))
            ->take(env("LIMIT_SEARCH_DISTANCE"))
            ->get()
            ->toArray();

        $this->completeAttributes($places);

        return $places;
    }

    /**
     * Actualiza la url de una imagen
     *
     * @param $id
     * @param $avatar_url
     * @return mixed
     */
    public function updateAvatarUrl($id, $avatar_url)
    {
        return Places::where('id', '=', $id)
            ->update([
                'avatar_url' => $avatar_url
            ]);
    }
}
