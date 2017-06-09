<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
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
            $res = $this->usersRepository->delete($id);
            return response(Response::HTTP_OK);
        } catch (Exception $e) {
            return response("Ocurrió un error al eliminar el usuario.", Response::HTTP_FORBIDDEN);
        }
    }

// Google Login
    public function googleLogin(Request $request)
    {
        $google_redirect_url = route('glogin');
        $gClient = new \Google_Client();
        $gClient->setApplicationName(env('SERVICES_GOOGLE_APP_NAME'));
        $gClient->setClientId(env('SERVICES_GOOGLE_CLIENT_ID'));
        $gClient->setClientSecret(env('SERVICES_GOOGLE_CLIENT_SECRET'));
        $gClient->setRedirectUri($google_redirect_url);
        $gClient->setDeveloperKey(env('SERVICES_GOOGLE_API_KEY'));
        $gClient->setScopes(array(
            'https://www.googleapis.com/auth/plus.me',
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile',
        ));
        $google_oauthV2 = new \Google_Service_Oauth2($gClient);
        if ($request->get('code')) {
            $gClient->authenticate($request->get('code'));
            $request->session()->put('token', $gClient->getAccessToken());
        }
        if ($request->session()->get('token')) {
            $gClient->setAccessToken($request->session()->get('token'));
        }
        if ($gClient->getAccessToken()) {
            //For logged in user, get details from google using access token
            $guser = (object)$google_oauthV2->userinfo->get();

            $request->session()->put('name', $guser->name);
            $e = $guser->email;
            $user = $this->usersRepository->getByEmail($e);

            if (!$user) {
                //logged your user via auth login

                $this->usersRepository->create($guser);
                $user = $this->usersRepository->getByEmail($e);
            }

            try {
                $token = JWTAuth::fromUser($user, array(date("Y/m/d h:i:s")));
            } catch (JWTException $e) {
                return response('No se pudo crear el token.', Response::HTTP_FORBIDDEN);
            }

            $user->auth = compact('token');

            return response($user, Response::HTTP_OK);
            //return redirect()->route('user.glist');
        } else {
            //For Guest user, get google login url
            $authUrl = $gClient->createAuthUrl();
            return redirect()->to($authUrl);
        }
    }

    public function logout(Request $request)
    {
        $data = (object)$request->all();
        $t = $token = JWTAuth::getToken($data->token);
        if (isset($t) && !empty($t->value)) {
            JWTAuth::setToken($t)->invalidate();
        }
        return response(Response::HTTP_OK);
    }

    // public function listGoogleUser(Request $request){
    //   $users = User::orderBy('id','DESC')->paginate(5);
    //  return view('users.list',compact('users'))->with('i', ($request->input('page', 1) - 1) * 5);;
    //  return "";
    // }

    public function getAuthenticatedUser()
    {
        try {

            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        // the token is valid and we have found the user via the sub claim
        return response()->json(compact('user'));
    }
}
