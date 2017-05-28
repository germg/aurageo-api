<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Repositories\UsersRepository as UsersRepository;
use Mockery\CountValidator\Exception;

class UsersController extends Controller
{
    /**
     * Repositorio usuarios.
     */
    private $usersRepository;

    /**
     * Constructor de UsuariosController.
     *
     * @param $usuariosRepository $repository
     */
    public function __construct(UsersRepository $usersRepository)
    {
        $this->usersRepository = $usersRepository;
    }

    /**
     * @param array $data
     * @return mixed
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => 'required|email'
        ]);
    }

    /**
     * Valida y crea un usuario
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
                $res = $this->usersRepository->create($data);
                return response($res->id, Response::HTTP_OK);
            }
        } catch (Exception $e) {
            return response("Ocurrió un error al crear el usuario.", Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Elimina un usuario
     *
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request)
    {
        // Obtiene todos los parametros que vienen
        $data = (object)$request->all();

        try {
            if (isset($data->id)) {
                // Transformo el array de datos a objeto (para hacer flechita)
                $data = (object)$data;
                $res = $this->usersRepository->delete($data->id);
                return response(Response::HTTP_OK);
            } else {
                return response("Debe indicar un id para eliminar", Response::HTTP_FORBIDDEN);
            }
        } catch (Exception $e) {
            return response("Ocurrió un error al eliminar el usuario.", Response::HTTP_FORBIDDEN);
        }
    }
}
