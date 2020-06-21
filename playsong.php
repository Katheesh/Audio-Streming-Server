<?php
/**
 * Created by PhpStorm.
 * User: Rahman
 * Date: 12/3/2015
 * Time: 1:21 PM
 */
require 'offlineradio.class.php';
$init = new stdClass();
$init->mp3Folder = dirname(__FILE__).DS.'files';
$init->playListName = 'list.txt';
$radio = new OfflineRadio($init);
$radio->playList();