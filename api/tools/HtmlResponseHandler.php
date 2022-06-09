<?php


class HtmlResponseHandler{

    /**
     * Formats and posts Json response, kills process
     * @param int $statusCode Html status code
     * @param array $headers additional headers list, the content type is set by default
     * @param array $body body of the response, cf index.php comments for formats
     */
    public static function formatedResponse(int $statusCode, array $headers, array $body){
        header("Content-Type: application/json");

        foreach ($headers as $headerName => $headerValue) {
            header("$headerName: $headerValue");
        }

        http_response_code($statusCode);

        echo json_encode($body);
        die();
    }
}