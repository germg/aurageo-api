<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Repositories\BookmarksRepository as BookmarksRepository;
use Mockery\CountValidator\Exception;
use Illuminate\Support\Facades\Validator;

class BookmarksController extends Controller
{
    private $bookmarksRepository;

    /**
     * BookmarksController constructor.
     * @param BookmarksRepository $bookmarksRepository
     */
    public function __construct(BookmarksRepository $bookmarksRepository)
    {
        $this->bookmarksRepository = $bookmarksRepository;
    }

    /**
     * @param array $data
     * @return mixed
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'user_id' => 'required|numeric',
            'place_id' => 'required|numeric'
        ]);
    }

    /**
     * Crea un bookmark
     *
     * @param $user_id
     * @param $place_id
     * @return Response
     */
    public function create($user_id, $place_id)
    {
        try {
            $data = array('user_id' => $user_id, 'place_id' => $place_id);
            $validator = $this->validator($data);

            if ($validator->fails()) {
                return response($validator->messages(), Response::HTTP_FORBIDDEN);
            } else {

                $bookmark = $this->bookmarksRepository->getByUserIdAndPlaceId($user_id, $place_id);

                if ($bookmark) {
                    return response("Ya existe un bookmark para el usuario y lugar.", Response::HTTP_FORBIDDEN);
                }

                // Transformo el array de datos a objeto (para hacer flechita)
                $res = $this->bookmarksRepository->create($user_id, $place_id);
                return response($res->id, Response::HTTP_OK);
            }
        } catch (Exception $e) {
            return response("Ocurrió un error al crear el bookmark.", Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Elimina un bookmark por user_id y place_id
     *
     * @param $user_id
     * @param $place_id
     * @return Response
     */
    public function delete($user_id, $place_id)
    {
        try {
            $data = array('user_id' => $user_id, 'place_id' => $place_id);
            $validator = $this->validator($data);
            if ($validator->fails()) {
                return response($validator->messages(), Response::HTTP_FORBIDDEN);
            } else {
                $res = $this->bookmarksRepository->delete($user_id, $place_id);
                return response(Response::HTTP_OK);
            }
        } catch (Exception $e) {
            return response("Ocurrió un error al eliminar el bookmark.", Response::HTTP_FORBIDDEN);
        }
    }
}