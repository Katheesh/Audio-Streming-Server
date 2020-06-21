<?php
set_time_limit(0);
require 'offlineradio.class.php';
$init = new stdClass();
$init->mp3Folder = dirname(__FILE__).DS.'files';
$init->playListName24 = 'list24.txt';
$init->startTime = '13:00:00';
$radio = new OfflineRadio($init);
$radio->generateList24();