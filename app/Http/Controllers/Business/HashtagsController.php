<?php

namespace App\Http\Controllers\Business;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Repositories\HashtagsRepository as HashtagsRepository;
use App\Repositories\PlacesRepository as PlacesRepository;

class HashtagsController extends BaseController
{
    /**
     * Repositorio hashtags.
     */
    private $hashtagsRepository;
    private $placesRepository;

    /**
     * Constructor de HashtagsController.
     *
     * @param $cardsRepository $repository
     */
    public function __construct(HashtagsRepository $hashtagsRepository, PlacesRepository $placesRepository)
    {
        parent::__construct();
        $this->hashtagsRepository = $hashtagsRepository;
        $this->placesRepository = $placesRepository;
    }

    /**
     * @param array $data
     * @return mixed
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'placeId' => 'required|numeric',
            'description' => 'required|max:45'
        ]);
    }

    /**
     * Obtiene un hashtag por su id
     *
     * @param $id
     * @return Response
     */
    public function getById($id)
    {
        try {
            return response($this->hashtagsRepository->getById($id), Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->message = \AurageoConstants::HASHTAG_GET_BY_ID_ERROR;
            Log::error($this->message . " Error: " . $e);
            return response($this->message, Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Obtiene un hashtag por su place_id
     *
     * @param $id
     * @return Response
     */
    public function getByPlaceId($place_id)
    {
        try {
            return response($this->hashtagsRepository->getByPlaceId($place_id), Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->message = \AurageoConstants::HASHTAG_GET_BY_PLACE_ID_ERROR;
            Log::error($this->message . " Error: " . $e);
            return response($this->message, Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Valida y crea un hashtag
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
            } else {
                // Transformo el array de datos a objeto (para hacer flechita)
                $data = (object)$data;
                $res = $this->hashtagsRepository->create($data);
                return response($res->id, Response::HTTP_OK);
            }
        } catch (\Exception $e) {
            $this->message = \AurageoConstants::HASHTAG_CREATE_ERROR;
            Log::error($this->message . " Error: " . $e);
            return response($this->message, Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Valida y edita un hashtag
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

            $place = $this->placesRepository->getById($data->placeId);

            if (!$this->canPerformAction($place->userId)) {
                Log::info(\AurageoConstants::CANNOT_PERFORM_ACTION_LOG . "EDIT Hashtag, USER_ID: $place->userId, CURRENT_USER_ID: " . isset($this->currentUser->id) ? $this->currentUser->id : 0 . ", PLACE_ID: $data->placeId");
                return response(\AurageoConstants::CANNOT_PERFORM_ACTION, Response::HTTP_FORBIDDEN);
            }

            $res = $this->hashtagsRepository->edit($data);
            return response(Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->message = \AurageoConstants::HASHTAG_EDIT_ERROR;
            Log::error($this->message . " Error: " . $e);
            return response($this->message, Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Elimina un hashtag
     *
     * @param $id
     * @return Response
     */
    public function delete($id)
    {
        try {

            $place = $this->placesRepository->getByHashtagId($id);

            if (!$this->canPerformAction($place->userId)) {
                Log::info(\AurageoConstants::CANNOT_PERFORM_ACTION_LOG . "DELETE Hashtag, USER_ID: $place->userId, CURRENT_USER_ID: " . isset($this->currentUser->id) ? $this->currentUser->id : 0 . ", PLACE_ID: $id");
                return response(\AurageoConstants::CANNOT_PERFORM_ACTION, Response::HTTP_FORBIDDEN);
            }

            $res = $this->hashtagsRepository->delete($id);
            return response(Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->message = \AurageoConstants::HASHTAG_DELETE_ERROR;
            Log::error($this->message . " Error: " . $e);
            return response($this->message, Response::HTTP_FORBIDDEN);
        }
    }
}
