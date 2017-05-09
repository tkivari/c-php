<?php

namespace Cara\Server;

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
        header('Content-type: application/octet-stream');
        header("Accept-Ranges: bytes");

        $fp = @fopen($this->file, 'rb');
        $filesize   = filesize($this->file); 
        $length = $filesize;           // Initial content length (for whole file if no range is specified)
        $start  = 0;                   // Initial start byte if no range is specified
        $end    = $filesize - 1;       // Initial end byte if no range is specifed (get the entire file)


        if (isset($_SERVER['HTTP_RANGE'])) {
            $calculated_start = $start;
            $calculated_end   = $end;
            list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);

            // if more than one range is specified, 
            if (strpos($range, ',') !== false) {
                http_response_code(416); // return HTTP respons 416 - requested range not satisfiable
                header("Content-Range: bytes $start-$end/$filesize");
                exit;
            }
            if ($range == '-') {
                $calculated_start = $filesize - substr($range, 1);
            }else{
                $range  = explode('-', $range);
                $calculated_start = $range[0];
                $calculated_end   = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $filesize;
            }
            
            // if the requested end byte is larger than the file size, just return up to the end of the file.
            $calculated_end = ($calculated_end > $end) ? $end : $calculated_end;

            // if the requested start byte is greater than the requested end byte, or the start or end bytes are greater than the file size,
            // then return a 416 code (range not satisfiable)
            if ($calculated_start > $calculated_end || $calculated_start > $filesize - 1 || $calculated_end >= $filesize) {
                http_response_code(416); // return HTTP response 416 - requested range not satisfiable
                header("Content-Range: bytes $start-$end/$filesize");
                exit;
            }
            $start  = $calculated_start;
            $end    = $calculated_end;
            $length = $end - $start + 1;
            fseek($fp, $start);

            http_response_code(206); // return HTTP response 206 - Partial content
        }

        header("Content-Range: bytes $start-$end/$filesize");
        header("Content-Length: ".$length);
        $buffer = 1024 * 8; // set the file buffer size to 8192 (8KB chunk size)
        while(!feof($fp) && ($p = ftell($fp)) <= $end) {
            // if getting the next buffer size from file would be more data than the file size, trim buffer size to the end of the file.
            if ($p + $buffer > $end) {
                $buffer = $end - $p + 1;
            }
            set_time_limit(0);
            echo fread($fp, $buffer);
            flush();
        }
        fclose($fp);
        exit();

    }

    private function returnError($data, $code)
    {
        header('Content-Type: application/json');
        http_response_code($code); // return HTTP response $code

        echo json_encode($data);

        exit(0);
    }
}