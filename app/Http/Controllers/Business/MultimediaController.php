<?php

namespace App\Http\Controllers\Business;

use Illuminate\Http\Response;
use App\Repositories\PlacesRepository as PlacesRepository;
use App\Repositories\CardsRepository as CardsRepository;
use Illuminate\Support\Facades\Log;
use DateTime;

class MultimediaController extends BaseController
{
    private $basePath;
    private $placesRepository;
    private $cardsRepository;

    /**
     * MultimediaController constructor.
     * @param PlacesRepository $placesRepository
     * @param CardsRepository $cardsRepository
     */
    public function __construct(PlacesRepository $placesRepository, CardsRepository $cardsRepository)
    {
        parent::__construct();
        $this->basePath = env('URL_UPLOADS');
        $this->placesRepository = $placesRepository;
        $this->cardsRepository = $cardsRepository;
    }

    /**
     * Guarda una imagen para el avatar de un lugar
     *
     * @param $id
     * @return Response
     */
    public function uploadPlaceAvatar($id)
    {
        try {
            if (isset($id) && intval($id) !== 0) {
                $place = $this->placesRepository->getById($id);

                if (!$this->canPerformAction($place->userId)) {
                    Log::info(\AurageoConstants::CANNOT_PERFORM_ACTION_LOG . "DELETE Multimedia Avatar, USER_ID: $place->userId, CURRENT_USER_ID: " . $this->getCurrentUserId() . ", PLACE_ID: $id");
                    return response(\AurageoConstants::CANNOT_PERFORM_ACTION, Response::HTTP_FORBIDDEN);
                }

                // Se elimina fisicamente la imagen anterior si tiene y existe
                if (isset($place->avatarUrl) && !empty($place->avatarUrl)) {
                    $path = $this->basePath . $place->avatarUrl;
                    if (file_exists($path)) {
                        unlink($path);
                    }
                }
            }

            if (!empty($_FILES)) {

                $uploadFolder = '/uploads/places/';
                $fullPath = $this->basePath . $uploadFolder;

                // Si no existe la carpeta del lugar se crea
                if (!file_exists($fullPath)) {
                    mkdir($fullPath, 0775, true);
                }

                $fileName = array_values($_FILES)[0]['name'];
                $extension = substr($fileName, strripos($fileName, "."), strlen($fileName));
                $fecha = new DateTime();

                // Se crea el nuevo nombre con timestamp y un numero random. Se mantiene la extension.
                $newName = $uploadFolder . $fecha->getTimestamp() . "" . rand(0, 100000) . $extension;
                $tempPath = array_values($_FILES)[0]['tmp_name'];
                //$uploadPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $_FILES['file']['name'];

                // Direccion física donde se guardará el archivo
                $uploadPath = $this->basePath . $newName;

                // Se guarda el archivo
                move_uploaded_file($tempPath, $uploadPath);

                if (isset($id) && $id !== 0) {
                    // Se actualiza en la url en BD
                    $this->placesRepository->updateAvatarUrl($id, $newName);
                }

                return response(env('APP_URL') . $newName, Response::HTTP_OK);
            }

            return response(\AurageoConstants::MULTIMEDIA_WITHOUT_IMAGE, Response::HTTP_FORBIDDEN);
        } catch (\Exception $e) {
            $this->message = \AurageoConstants::MULTIMEDIA_UPLOAD_ERROR;
            Log::error($this->message . " Error: " . $e);
            return response($this->message, Response::HTTP_FORBIDDEN);
        }
    }

    public function uploadCardImage($id)
    {
        try {

            if (isset($id) && intval($id) !== 0) {
                $place = $this->placesRepository->getByCardId($id);

                if (!$this->canPerformAction($place->userId)) {
                    Log::info(\AurageoConstants::CANNOT_PERFORM_ACTION_LOG . "DELETE Multimedia Card, USER_ID: $place->userId, CURRENT_USER_ID: " . $this->getCurrentUserId() . ", CARD_ID: $id");
                    return response(\AurageoConstants::CANNOT_PERFORM_ACTION, Response::HTTP_FORBIDDEN);
                }

                $card = $this->cardsRepository->getById($id);

                // Se elimina fisicamente la imagen anterior si tiene y existe
                if (isset($card->imageUrl) && !empty($card->imageUrl)) {
                    $path = $this->basePath . $card->imageUrl;
                    if (file_exists($path)) {
                        unlink($path);
                    }
                }
            }

            if (!empty($_FILES)) {
                $uploadFolder = '/uploads/cards/';
                $fullPath = $this->basePath . $uploadFolder;

                // Si no existe la carpeta se crea
                if (!file_exists($fullPath)) {
                    mkdir($fullPath, 0775, true);
                }

                $fileName = array_values($_FILES)[0]['name'];
                $extension = substr($fileName, strripos($fileName, "."), strlen($fileName));
                $fecha = new DateTime();

                // Se crea el nuevo nombre con timestamp y un numero random. Se mantiene la extension.
                $newName = $uploadFolder . $fecha->getTimestamp() . "" . rand(0, 100000) . $extension;
                $tempPath = array_values($_FILES)[0]['tmp_name'];

                // Direccion física donde se guardará el archivo
                $uploadPath = $this->basePath . $newName;

                // Se guarda el archivo
                move_uploaded_file($tempPath, $uploadPath);

                if (isset($id) && $id !== 0) {
                    // Se guarda en BD
                    $this->cardsRepository->updateImageUrl($id, $newName);
                }

                return response(env('APP_URL') . $newName, Response::HTTP_OK);
            }
            return response(\AurageoConstants::MULTIMEDIA_WITHOUT_IMAGE, Response::HTTP_FORBIDDEN);
        } catch (\Exception $e) {
            $this->message = \AurageoConstants::MULTIMEDIA_UPLOAD_ERROR;
            Log::error($this->message . " Error: " . $e);
            return response($this->message, Response::HTTP_FORBIDDEN);
        }
    }
}