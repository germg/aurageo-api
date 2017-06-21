<?php

namespace App\Http\Controllers\Business;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Repositories\CardsRepository as CardsRepository;
use App\Repositories\PlacesRepository as PlacesRepository;

class CardsController extends BaseController
{
    /**
     * Repositorio usuarios.
     */
    private $cardsRepository;
    private $placesRepository;

    /**
     * Constructor de CardsController.
     *
     * @param $cardsRepository $repository
     */
    public function __construct(CardsRepository $cardsRepository, PlacesRepository $placesRepository)
    {
        parent::__construct();
        $this->cardsRepository = $cardsRepository;
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
            'deleted' => 'required|boolean'
        ]);
    }

    /**
     * Obtiene una tarjeta por su id
     *
     * @param $id
     * @return Response
     */
    public function getById($id)
    {
        try {
            return response($this->cardsRepository->getById($id), Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->message = \AurageoConstants::CARD_GET_BY_ID_ERROR;
            Log::error($this->message . " Error: " . $e);
            return response($this->message, Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Obtiene tarjetas por su place_id
     *
     * @param $id
     * @return Response
     */
    public function getByPlaceId($id)
    {
        try {
            return response($this->cardsRepository->getByPlaceId($id), Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->message = \AurageoConstants::CARD_GET_BY_PLACE_ID_ERROR;
            Log::error($this->message . " Error: " . $e);
            return response($this->message, Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Obtiene tarjetas por su place_id con limite y desplazamiento
     *
     * @param $id
     * @param $offset
     * @param $limit
     * @return Response
     */
    public function getByPlaceIdWithOffsetAndLimit($id, $offset, $limit)
    {
        try {
            return response($this->cardsRepository->getByPlaceIdWithOffsetAndLimit($id, $offset, $limit), Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->message = \AurageoConstants::CARD_GET_BY_PLACE_ID_OFFSET_LIMIT_ERROR;
            Log::error($this->message . " Error: " . $e);
            return response($this->message, Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Valida y crea una tarjeta
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
            $res = $this->cardsRepository->create($data);
            return response($res->id, Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->message = \AurageoConstants::CARD_CREATE_ERROR;
            Log::error($this->message . " Error: " . $e);
            return response($this->message, Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Valida y edita una tarjeta
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

            $place = $this->placesRepository->getById($data->place_id);

            if (!$this->canPerformAction($place->userId)) {
                Log::info(\AurageoConstants::CANNOT_PERFORM_ACTION_LOG . "EDIT Place, USER_ID: $place->userId, CURRENT_USER_ID: " . $this->getCurrentUserId() . ", PLACE_ID: $data->place_id");
                return response(\AurageoConstants::CANNOT_PERFORM_ACTION, Response::HTTP_FORBIDDEN);
            }

            $res = $this->cardsRepository->edit($data);
            return response(Response::HTTP_OK);

        } catch (\Exception $e) {
            $this->message = \AurageoConstants::CARD_EDIT_ERROR;
            Log::error($this->message . " Error: " . $e);
            return response($this->message, Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Elimina un usuario
     *
     * @param $id
     * @return Response
     */
    public function delete($id)
    {
        try {

            $place = $this->placesRepository->getByCardId($id);

            if (!$this->canPerformAction($place->userId)) {
                Log::info(\AurageoConstants::CANNOT_PERFORM_ACTION_LOG . "DELETE Place, USER_ID: $place->userId, CURRENT_USER_ID: " . $this->getCurrentUserId() . ", PLACE_ID: $id");
                return response(\AurageoConstants::CANNOT_PERFORM_ACTION, Response::HTTP_FORBIDDEN);
            }

            $res = $this->cardsRepository->delete($id);
            return response(Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->message = \AurageoConstants::CARD_DELETE_ERROR;
            Log::error($this->message . " Error: " . $e);
            return response($this->message, Response::HTTP_FORBIDDEN);
        }
    }
}
