<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Repositories\CardsRepository as CardsRepository;
use Mockery\CountValidator\Exception;

class CardsController extends Controller
{
    /**
     * Repositorio usuarios.
     */
    private $cardsRepository;

    /**
     * Constructor de CardsController.
     *
     * @param $cardsRepository $repository
     */
    public function __construct(CardsRepository $cardsRepository)
    {
        $this->cardsRepository = $cardsRepository;
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
        } catch (Exception $e) {
            return response("Ocurrió un error al obtener la tarjeta por su id.", Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Obtiene una tarjeta por su place_id
     *
     * @param $id
     * @return Response
     */
    public function getByPlaceId($id)
    {
        try {
            return response($this->cardsRepository->getByPlaceId($id), Response::HTTP_OK);
        } catch (Exception $e) {
            return response("Ocurrió un error al obtener las tarjetas por place_id.", Response::HTTP_FORBIDDEN);
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
            } else {
                // Transformo el array de datos a objeto (para hacer flechita)
                $data = (object)$data;
                $res = $this->cardsRepository->create($data);
                return response($res->id, Response::HTTP_OK);
            }
        } catch (Exception $e) {
            return response("Ocurrió un error al crear la tarjeta.", Response::HTTP_FORBIDDEN);
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
            } else {
                // Transformo el array de datos a objeto (para hacer flechita)
                $data = (object)$data;
                $res = $this->cardsRepository->edit($data);
                return response(Response::HTTP_OK);
            }
        } catch (Exception $e) {
            return response("Ocurrió un error al editar la tarjeta.", Response::HTTP_FORBIDDEN);
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
            $res = $this->cardsRepository->delete($id);
            return response(Response::HTTP_OK);
        } catch (Exception $e) {
            return response("Ocurrió un error al eliminar la tarjeta.", Response::HTTP_FORBIDDEN);
        }
    }
}
