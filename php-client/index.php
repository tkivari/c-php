<?php

require_once('client/client.php');

if (isset($_POST['file']) ) {
    $config = [
        'remote_file' => filter_var($_POST['file'], FILTER_SANITIZE_STRING),
        'server_url' => "http://file_server/index.php"
    ];

    $client = new \Cara\Client($config);
    $client->download();

    list($download_complete, $errors) = $client->getDownloadStatus();

    if ($download_complete) {
        $data = [
            'success' => true,
            'message' => "The file " . $_POST['file'] . " has been successfully downloaded.  Please check the project/downloads folder."
        ];

        $client->sendOutput($data);
    } else {
        $data = [
            'success' => false,
            'errors' => join(",", $errors)
        ];

        \Cara\Client::sendOutput($data, 400);
            
    }

    exit(0);
}


$data = [
    'success' => false,
    'message' => 'You must specify a file name.'
];

\Cara\Client::sendOutput($data, 400);