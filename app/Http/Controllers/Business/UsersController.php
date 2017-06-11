<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Repositories\UsersRepository as UsersRepository;
use Mockery\CountValidator\Exception;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

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
     * @param $id
     * @return Response
     */
    public function delete($id)
    {
        try {
            $this->usersRepository->delete($id);
            return response(Response::HTTP_OK);
        } catch (Exception $e) {
            return response("Ocurrió un error al eliminar el usuario.", Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Realiza el login con Google y genera un token
     *
     * @param Request $request
     * @return Response
     */
    public function login(Request $request)
    {
        try {
            $data = (object)$request->all();
            $client = new \Google_Client();
            $payload = (object)$client->verifyIdToken($data->token);

            if ($payload) {
                $user = $this->usersRepository->getByGoogleId($payload->sub);

                if (!$user) {
                    $user = $this->usersRepository->create($payload);
                }

                try {
                    $token = JWTAuth::fromUser($user, array(date("Y/m/d h:i:s")));
                } catch (JWTException $e) {
                    return response('No se pudo crear el token.', Response::HTTP_FORBIDDEN);
                }

                $auth = compact('token');
                $user->token = $auth["token"];

                return response($user, Response::HTTP_OK);
            } else {
                return response("No se pudo verificar el token de Google.", Response::HTTP_FORBIDDEN);
            }
        } catch (Exception $e) {
            return response("Ocurrió un error al autenticar el usuario.", Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Finaliza la sesión e invalida el token
     *
     * @param Request $request
     * @return Response
     */
    public function logout(Request $request)
    {
        $data = (object)$request->all();
        $t = $token = JWTAuth::getToken($data->token);
        if (isset($t) && !empty($t->value)) {
            JWTAuth::setToken($t)->invalidate();
        }
        return response(Response::HTTP_OK);
    }

    public function test(){

        $user = $this->usersRepository->getById(5);

        try {
            $token = JWTAuth::fromUser($user, array(date("Y/m/d h:i:s")));
        } catch (JWTException $e) {
            return response('No se pudo crear el token.', Response::HTTP_FORBIDDEN);
        }

        $auth = compact('token');
        $user->token = $auth["token"];

        return response($user, Response::HTTP_OK);
    }
}
