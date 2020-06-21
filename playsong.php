<?php


require 'offlineradio.class.php';
$init = new stdClass();
$init->mp3Folder = dirname(__FILE__).DS.'files';
$init->playListName = 'list.txt';
$radio = new OfflineRadio($init);
$radio->playList();