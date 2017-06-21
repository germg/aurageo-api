<?php

namespace App\Http\Controllers\Business;

use App\Models\Hashtags;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Repositories\PlacesRepository as PlacesRepository;
use App\Repositories\HashtagsRepository as HashtagsRepository;

class PlacesController extends BaseController
{
    /**
     * Repositorio lugares.
     */
    private $placesRepository;
    private $hashtagsRepository;

    /**
     * Constructor de PlacesController.
     *
     * @param $placesRepository $repository
     */
    public function __construct(PlacesRepository $placesRepository, HashtagsRepository $hashtagsRepository)
    {
        parent::__construct();
        $this->placesRepository = $placesRepository;
        $this->hashtagsRepository = $hashtagsRepository;
    }

    /**
     * @param array $data
     * @return mixed
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:45',
            'latitude' => 'required|max:20',
            'longitude' => 'required|max:20',
            'deleted' => 'required|boolean',
            'avatarUrl' => 'required|max:256',
            'userId' => 'required|numeric',
            'visible' => 'required|boolean',
            'address' => 'required|max:100'
        ]);
    }

    /**
     * Obtiene una lista de lugares marcado como favorito por user_id
     *
     * @param $user_id
     * @return Response
     */
    public function getBookmarkedByUserId($user_id)
    {
        try {
            return response($this->placesRepository->getBookmarkedByUserId($user_id), Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->message = \AurageoConstants::PLACE_GET_BOOKMARKED_BY_USER_ID_ERROR;
            Log::error($this->message . " Error: " . $e);
            return response($this->message, Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Obtiene los lugares cercanos a las coordenadas pasadas
     * @param $latitude
     * @param $longitude
     * @return Response
     */
    public function getPlacesNearToCoordinate($latitude, $longitude)
    {
        try {
            return response($this->placesRepository->getPlacesNearToCoordinate($latitude, $longitude), Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->message = \AurageoConstants::PLACE_GET_PLACES_NEAR_ERROR;
            Log::error($this->message . " Error: " . $e);
            return response($this->message, Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Obtiene una lista de lugares por su user_id
     * @param $user_id
     * @return Response
     */
    public function getByUserId($user_id)
    {
        try {
            return response($this->placesRepository->getByUserId($user_id), Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->message = \AurageoConstants::PLACE_GET_BY_USER_ID_ERROR;
            Log::error($this->message . " Error: " . $e);
            return response($this->message, Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Obtiene un lugar por su id
     *
     * @param $id
     * @return Response
     */
    public function getById($id)
    {
        try {
            return response($this->placesRepository->getById($id), Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->message = \AurageoConstants::PLACE_GET_BY_ID_ERROR;
            Log::error($this->message . " Error: " . $e);
            return response($this->message, Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Valida y crea un lugar
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request)
    {
        // Obtiene todos los parametros que vienen
        $data = $request->all();
        // Obtiene la respuesta del validador
        $validator = $this->validator($data);

        try {
            if ($validator->fails()) {
                return response($validator->messages(), Response::HTTP_FORBIDDEN);
            }

            // Transformo el array de datos a objeto (para hacer flechita)
            $data = (object)$data;
            $res = $this->placesRepository->create($data);

            if(isset($data->hashtags)){
                $tag = null;
                foreach($data->hashtags as $hashtag){
                    $tag = new Hashtags();
                    $tag->placeId = $res->id;
                    $tag->description = $hashtag["description"];
                    $this->hashtagsRepository->create($tag);
                }
                unset($tag);
            }

            return response($res->id, Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->message = \AurageoConstants::PLACE_CREATE_ERROR;
            Log::error($this->message . " Error: " . $e);
            return response($this->message, Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Valida y edita un lugar
     *
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request)
    {
        // Obtiene todos los parametros que vienen
        $data = $request->all();

        // Obtiene la respuesta del validador
        $validator = $this->validator($data);

        try {
            if ($validator->fails()) {
                return response($validator->messages(), Response::HTTP_FORBIDDEN);
            }

            // Transformo el array de datos a objeto (para hacer flechita)
            $data = (object)$data;

            if (!$this->canPerformAction($data->userId)) {
                Log::info(\AurageoConstants::CANNOT_PERFORM_ACTION_LOG . "EDIT Place , USER_ID: $data->userId, PLACE_ID: $data->id");
                return response(\AurageoConstants::CANNOT_PERFORM_ACTION, Response::HTTP_FORBIDDEN);
            }

            $res = $this->placesRepository->edit($data);

            if(isset($data->hashtags)){
                foreach($data->hashtags as $hashtag){
                    $hashtag = (object)$hashtag;
                    if(!$hashtag->id || ($hashtag->id && $hashtag->id === null)){
                        $this->hashtagsRepository->create($hashtag);
                    }else if(empty($hashtag->description)){
                        $this->hashtagsRepository->delete($hashtag->id);
                    }else{
                        $this->hashtagsRepository->edit($hashtag);
                    }
                }
            }

            return response(Response::HTTP_OK);

        } catch (\Exception $e) {
            $this->message = \AurageoConstants::PLACE_EDIT_ERROR;
            Log::error($this->message . " Error: " . $e);
            return response($this->message, Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Elimina un lugar
     *
     * @param $id
     * @return Response
     */
    public function delete($id)
    {
        try {

            $place = $this->placesRepository->getById($id);

            if (!$this->canPerformAction($place->userId)) {
                Log::info(\AurageoConstants::CANNOT_PERFORM_ACTION_LOG . "EDIT Place , USER_ID: $place->userId, PLACE_ID: $id");
                return response(\AurageoConstants::CANNOT_PERFORM_ACTION, Response::HTTP_FORBIDDEN);
            }

            $res = $this->placesRepository->delete($id);
            return response(Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->message = \AurageoConstants::PLACE_DELETE_ERROR;
            Log::error($this->message . " Error: " . $e);
            return response($this->message, Response::HTTP_FORBIDDEN);
        }
    }
}
