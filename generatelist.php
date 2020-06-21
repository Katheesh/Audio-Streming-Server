<?php
require 'offlineradio.class.php';
$init = new stdClass();
$init->mp3Folder = dirname(__FILE__).DS.'files';
$init->playListName = 'list.txt';
$init->startTime = '19:10:00';
$radio = new OfflineRadio($init);
$radio->generateList();