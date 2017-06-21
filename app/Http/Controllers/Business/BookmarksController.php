<?php

namespace App\Http\Controllers\Business;

use Illuminate\Http\Response;
use App\Repositories\BookmarksRepository as BookmarksRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BookmarksController extends BaseController
{
    private $bookmarksRepository;

    /**
     * BookmarksController constructor.
     * @param BookmarksRepository $bookmarksRepository
     */
    public function __construct(BookmarksRepository $bookmarksRepository)
    {
        parent::__construct();
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
                    return response(\AurageoConstants::BOOKMARK_EXISTENTE, Response::HTTP_FORBIDDEN);
                }

                // Transformo el array de datos a objeto (para hacer flechita)
                $res = $this->bookmarksRepository->create($user_id, $place_id);
                return response($res->id, Response::HTTP_OK);
            }
        } catch (\Exception $e) {
            $this->message = \AurageoConstants::BOOKMARK_CREATE_ERROR;
            Log::error($this->message . " Error: " . $e);
            return response($this->message, Response::HTTP_FORBIDDEN);
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
            }

            if(!$this->canPerformAction($user_id)){
                return response(\AurageoConstants::CANNOT_PERFORM_ACTION, Response::HTTP_FORBIDDEN);
            }

            $res = $this->bookmarksRepository->delete($user_id, $place_id);
            return response(Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->message = \AurageoConstants::BOOKMARK_DELETE_ERROR;
            Log::error($this->message . " Error: " . $e);
            return response($this->message, Response::HTTP_FORBIDDEN);
        }
    }
}