<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Repositories\PlacesRepository as PlacesRepository;
use App\Repositories\CardsRepository as CardsRepository;
use Mockery\CountValidator\Exception;
use DateTime;

class MultimediaController extends Controller
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
            if (!empty($_FILES)) {

                $uploadFolder = '/uploads/place' . $id . '/';
                $fullPath = $this->basePath . $uploadFolder;

                // Si no existe la carpeta del lugar se crea
                if (!file_exists($fullPath)) {
                    mkdir($fullPath, 0775, true);
                }

                $place = $this->placesRepository->getById($id);

                // Se elimina fisicamente la imagen anterior si tiene y existe
                if (isset($place->avatarUrl) && !empty($place->avatarUrl)) {
                    $path = $this->basePath . $place->avatarUrl;
                    if (file_exists($path)) {
                        unlink($path);
                    }
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

                // Se actualiza en la url en BD
                $this->placesRepository->updateAvatarUrl($id, $newName);

                return response(env('APP_URL') . $newName, Response::HTTP_OK);
            }

            return response("No se ha encontrado la imagen para guardar.", Response::HTTP_FORBIDDEN);
        } catch (Exception $e) {
            return response("Ocurrió un error al subir la imagen del lugar.", Response::HTTP_FORBIDDEN);
        }
    }

    public function uploadCardImage($place_id, $card_id)
    {
        try {
            if (!empty($_FILES)) {
                $uploadFolder = '/uploads/place' . $place_id . '/card' . $card_id . '/';
                $fullPath = $this->basePath . $uploadFolder;

                // Si no existe la carpeta se crea
                if (!file_exists($fullPath)) {
                    mkdir($fullPath, 0775, true);
                }

                $card = $this->cardsRepository->getById($card_id);

                // Se elimina fisicamente la imagen anterior si tiene y existe
                if (isset($card->imageUrl) && !empty($card->imageUrl)) {
                    $path = $this->basePath . $card->imageUrl;
                    if (file_exists($path)) {
                        unlink($path);
                    }
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

                // Se guarda en BD
                $this->cardsRepository->updateImageUrl($card_id, $newName);

                return response(env('APP_URL') . $newName, Response::HTTP_OK);
            }
            return response("No se ha encontrado la imagen para guardar.", Response::HTTP_FORBIDDEN);
        } catch (Exception $e) {
            return response("Ocurrió un error al subir la imagen de la propiedad.", Response::HTTP_FORBIDDEN);
        }
    }
}