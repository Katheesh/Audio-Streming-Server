<?php
require 'offlineradio.class.php';
$init = new stdClass();
$init->mp3Folder = dirname(__FILE__).DS.'files';
$init->playListName = 'list.txt';
$radio = new OfflineRadio($init);
$output = $radio->getPlayingItem();
echo json_encode($output);