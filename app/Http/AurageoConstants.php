<?php

class AurageoConstants{

    /*---------------------------------*/
    /* Controllers */
    /*---------------------------------*/
    const BOOKMARK_EXISTENTE = "Ya existe un bookmark para el usuario y lugar.";
    const BOOKMARK_CREATE_ERROR = "Ocurrió un error al crear el bookmark.";
    const BOOKMARK_DELETE_ERROR = "Ocurrió un error al eliminar el bookmark.";

    const CARD_CREATE_ERROR = "Ocurrió un error al crear la tarjeta.";
    const CARD_EDIT_ERROR = "Ocurrió un error al editar la tarjeta.";
    const CARD_DELETE_ERROR = "Ocurrió un error al eliminar la tarjeta.";
    const CARD_GET_BY_ID_ERROR = "Ocurrió un error al obtener la tarjeta por su id.";
    const CARD_GET_BY_PLACE_ID_ERROR = "Ocurrió un error al obtener las tarjetas por place_id.";
    const CARD_GET_BY_PLACE_ID_OFFSET_LIMIT_ERROR = "Ocurrió un error al obtener las tarjetas por place_id con desplazamiento y limite.";

    const HASHTAG_CREATE_ERROR = "Ocurrió un error al crear el hashtag.";
    const HASHTAG_EDIT_ERROR = "Ocurrió un error al editar el hastag.";
    const HASHTAG_DELETE_ERROR = "Ocurrió un error al eliminar el hashtag.";
    const HASHTAG_GET_BY_ID_ERROR = "Ocurrió un error al obtener el hashtag por su id.";
    const HASHTAG_GET_BY_PLACE_ID_ERROR = "Ocurrió un error al obtener las tarjetas por place_id.";

    const MULTIMEDIA_WITHOUT_IMAGE = "No se ha encontrado la imagen para guardar.";
    const MULTIMEDIA_UPLOAD_ERROR = "Ocurrió un error al subir la imagen del lugar.";

    const PLACE_CREATE_ERROR = "Ocurrió un error al crear el lugar.";
    const PLACE_EDIT_ERROR = "Ocurrió un error al editar el lugar.";
    const PLACE_DELETE_ERROR = "Ocurrió un error al eliminar el lugar.";
    const PLACE_GET_BOOKMARKED_BY_USER_ID_ERROR = "Ocurrió un error al intentar obtener lugares marcados como favorito por user_id.";
    const PLACE_GET_PLACES_NEAR_ERROR = "Ocurrió un error al intentar obtener lugares cercanos a una coordenada.";
    const PLACE_GET_BY_USER_ID_ERROR = "Ocurrió un error al intentar obtener lugares por user_id.";
    consT PLACE_GET_BY_ID_ERROR = "Ocurrió un error al obtener el lugar por su id.";

    const USER_CREATE_ERROR = "Ocurrió un error al crear el usuario.";
    const USER_DELETE_ERROR = "Ocurrió un error al eliminar el usuario.";
    const USER_CANNOT_CREATE_TOKEN_ERROR = 'No se pudo crear el token.';
    const USER_CANNOT_VERIFY_GTOKEN_ERROR = "No se pudo verificar el token de Google.";
    const USER_AUTH_ERROR = "Ocurrió un error al autenticar el usuario.";
    const USER_LOGOUT_ERROR = "Ocurrió un error al cerrar la sesión.";

    const CANNOT_PERFORM_ACTION = "Lo sentimos, no puede realizar esta acción.";
}