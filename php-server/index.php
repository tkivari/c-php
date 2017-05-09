<?php

require_once('server/download.php');

$downloader = new \Cara\Server\Downloader($_GET['file']);

$downloader->sendFile();
