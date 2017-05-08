<?php

require_once('server/download.php');

$downloader = new Downloader($_GET['file']);

$downloader->sendFile();
