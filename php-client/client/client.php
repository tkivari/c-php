<?php

namespace Cara;

use \Exception;

class Client
{
    private $remote_file;
    private $local_file;
    private $download_path = "/downloads/"; // Note: trailing slash
    private $download_complete = false;
    private $server_url;
    private $errors = [];

    /**
        Constructor for download client class 
        @param $config[] - Array of configuration options for this client: remote_file, local_file
        @return void
    */
    public function __construct($config = [])
    {
        $default_config = [
            'remote_file' => 'test.txt',
            'server_url' => "http://file_server/index.php"
        ];

        $config = array_merge($default_config, $config);

        $this->remote_file = $config['remote_file'];
        $this->server_url = $config['server_url'];
        $this->local_file = array_key_exists("local_file", $config) ? $config['local_file'] : $this->remote_file;
    }

    /**
        Download the file using curl, from the byte position calculated via filesize()
        @return Boolean
    */
    public function download()
    {
        try {
            $url = $this->server_url . "?file=" . $this->remote_file;
            $file = $this->download_path . $this->local_file;
        

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);

            if (file_exists($file)) {
                $from = filesize($file);
                curl_setopt($ch, CURLOPT_RANGE, $from . "-");
            }

            $fp = fopen($file, "a+");
            if (!$fp) {
                $this->download_success = false;
                $this->errors[] = "Unable to open file $file";
            }
            curl_setopt($ch, CURLOPT_FILE, $fp);
            $result = curl_exec($ch);
            
            if($errno = curl_errno($ch)) {
                $this->errors[] = curl_strerror($errno);
                $this->download_complete = false;
            } else {
                $this->download_complete = true;
            }

            curl_close($ch);

            fclose($fp);           

        } catch(Exception $e) {
            $this->download_success = false;
            $this->errors[] = $e->getMessage();
        }
    }

    /**
        Return the status of the download along with any errors encountered.
    */
    public function getDownloadStatus()
    {
        return [$this->download_complete, $this->errors];
    }


    /**
        echo the JSON output.
    */
    public static function sendOutput($data, $code = 200) {
        header('Content-Type: application/json');
        http_response_code($code); // return HTTP response $code

        echo json_encode($data);
    }
}

