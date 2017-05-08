<?php

namespace \Cara\Server;

class Downloader {

    private $file_path = "/files/"; // Note the trailing slash.
    private $file;

    public function __construct($file)
    {
        $this->file = $this->file_path . $file;

        if (!is_file($this->file)) {
            $code = 404;
            $data = [
                'success' => false,
                'message' => "File Not Found"
            ];

            $this->returnError($data, $code);
        }
    }

    public function sendFile()
    {
        
    }

    private function returnError($data, $code)
    {
        header('Content-Type: application/json');
        http_response_code($code); // return HTTP response $code

        echo json_encode($data);

        exit(0);
    }
}