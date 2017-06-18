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
            $this->message = "Ocurrió un error al obtener el hashtag por su id.";
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
            $this->message = "Ocurrió un error al obtener las tarjetas por place_id.";
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
            $this->message = "Ocurrió un error al crear el hashtag.";
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
                return response("Lo sentimos, no puede realizar esta acción.", Response::HTTP_FORBIDDEN);
            }

            $res = $this->hashtagsRepository->edit($data);
            return response(Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->message = "Ocurrió un error al editar el hastag.";
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
                return response("Lo sentimos, no puede realizar esta acción.", Response::HTTP_FORBIDDEN);
            }

            $res = $this->hashtagsRepository->delete($id);
            return response(Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->message = "Ocurrió un error al eliminar el hashtag.";
            Log::error($this->message . " Error: " . $e);
            return response($this->message, Response::HTTP_FORBIDDEN);
        }
    }
}
