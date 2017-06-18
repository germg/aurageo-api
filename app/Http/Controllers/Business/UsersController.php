<?php

namespace App\Http\Controllers\Business;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Repositories\UsersRepository as UsersRepository;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UsersController extends BaseController
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
        parent::__construct();
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
        } catch (\Exception $e) {
            $this->message = "Ocurrió un error al crear el usuario.";
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
            $this->usersRepository->delete($id);
            return response(Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->message = "Ocurrió un error al eliminar el usuario.";
            Log::error($this->message . " Error: " . $e);
            return response($this->message, Response::HTTP_FORBIDDEN);
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

            if (isset($payload->sub)) {
                $user = $this->usersRepository->getByGoogleId($payload->sub);

                // Si no esta en la bd se crea
                if (!$user) {
                    $user = $this->usersRepository->create($payload);
                }

                $user->gtoken = $data->token;

                try {
                    $token = JWTAuth::fromUser($user, array(date("Y/m/d h:i:s")));
                } catch (JWTException $e) {
                    $msg = 'No se pudo crear el token.';
                    Log::error($msg . " Error: " . $e);
                    return response($msg, Response::HTTP_FORBIDDEN);
                }

                $auth = compact('token');
                $user->token = $auth["token"];

                return response($user, Response::HTTP_OK);
            } else {
                return response("No se pudo verificar el token de Google.", Response::HTTP_FORBIDDEN);
            }
        } catch (\Exception $e) {
            $this->message = "Ocurrió un error al autenticar el usuario.";
            Log::error($this->message . " Error: " . $e);
            return response($this->message, Response::HTTP_FORBIDDEN);
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
        try {
            JWTAuth::parseToken()->invalidate();
            return response(Response::HTTP_OK);
        }
        catch (\Exception $e) {
            $this->message = "Ocurrió un error al cerrar la sesión.";
            Log::error($this->message . " Error: " . $e);
            return response($this->message, Response::HTTP_FORBIDDEN);
        }
    }

    public function test()
    {
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
