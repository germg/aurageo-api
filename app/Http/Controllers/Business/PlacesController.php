<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Services\PhpGeoService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Repositories\PlacesRepository as PlacesRepository;
use Mockery\CountValidator\Exception;

class PlacesController extends Controller
{
    /**
     * Repositorio lugares.
     */
    private $placesRepository;

    /**
     * Constructor de PlacesController.
     *
     * @param $placesRepository $repository
     */
    public function __construct(PlacesRepository $placesRepository)
    {
        $this->placesRepository = $placesRepository;
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
        } catch (Exception $e) {
            return response("Ocurrió un error al intentar obtener lugares marcados como favorito por user_id.", Response::HTTP_FORBIDDEN);
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
        } catch (Exception $e) {
            return response("Ocurrió un error al intentar obtener lugares cercanos a una coordenada.", Response::HTTP_FORBIDDEN);
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
        } catch (Exception $e) {
            return response("Ocurrió un error al intentar obtener lugares por user_id.", Response::HTTP_FORBIDDEN);
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
        } catch (Exception $e) {
            return response("Ocurrió un error al obtener el lugar por su id.", Response::HTTP_FORBIDDEN);
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
            } else {
                // Transformo el array de datos a objeto (para hacer flechita)
                $data = (object)$data;
                $res = $this->placesRepository->create($data);
                return response($res->id, Response::HTTP_OK);
            }
        } catch (Exception $e) {
            return response("Ocurrió un error al crear el lugar.", Response::HTTP_FORBIDDEN);
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
            } else {
                // Transformo el array de datos a objeto (para hacer flechita)
                $data = (object)$data;
                $res = $this->placesRepository->edit($data);
                return response(Response::HTTP_OK);
            }
        } catch (Exception $e) {
            return response("Ocurrió un error al editar el lugar.", Response::HTTP_FORBIDDEN);
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
            $res = $this->placesRepository->delete($id);
            return response(Response::HTTP_OK);
        } catch (Exception $e) {
            return response("Ocurrió un error al eliminar el lugar.", Response::HTTP_FORBIDDEN);
        }
    }
}
