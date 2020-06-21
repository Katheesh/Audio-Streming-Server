<?php
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
require_once 'class.mp3.php';
class OfflineRadio extends mp3
{
    public $playListName = '';
    public $playListFiles = [];
    public $mp3Folder = '';
    public $startTime = '00:00:00';
    public $timeZone = 'Asia/Tehran';
    public function __construct()
    {
        date_default_timezone_set($this->timeZone);
        $arguments = func_get_args();
        foreach($arguments as $argument){
            if (is_object($argument)){
                foreach($argument as $key=>$value){
                    $this->$key = $value;
                }
            }
        }
        if (!empty($this->playListName)){
            $tmp = file_get_contents(dirname(__FILE__).DS.$this->playListName);
            $this->playListFiles = explode("\r\n", $tmp);
        }
    }

    public function generateList()
    {
        $files = scandir($this->mp3Folder);
        $fp = fopen($this->playListName,'w');
        $durations = 0;
        foreach($files as $file){
            if (in_array($file,['.','..']))
                continue;
            $info = $this->get_mp3($this->mp3Folder.DS.$file, true, true);
            $duration = floor($info['data']['length']);
            $time = date('H:i:s',strtotime($this->startTime.'+'.$durations.'seconds'));
            $data = $this->mp3Folder.DS.$file.','.$time."\r\n";
            $durations += $duration;
            fwrite($fp, $data);
        }
        fclose($fp);
    }

    public function generateList24()
    {
        $files = scandir($this->mp3Folder);
        $fp = fopen($this->playListName24,'w');
        $durations = 0;
        $startTime = $this->startTime;
        $stopTime = strtotime($startTime);
        while($stopTime <= strtotime($startTime.'+1 day')) {
            foreach($files as $file){
                if (in_array($file,['.','..']))
                    continue;
                $info = $this->get_mp3($this->mp3Folder.DS.$file, true, true);
                $duration = floor($info['data']['length']);
                $time = date('H:i:s',strtotime($startTime.'+'.$durations.'seconds'));
                $stopTime = strtotime($startTime.'+'.$durations.'seconds');
                if ($stopTime >= strtotime($startTime.'+1 day')) {
                    exit;
                }
                $data = $this->mp3Folder.DS.$file.','.$time."\r\n";
                $durations += $duration;
                fwrite($fp, $data);
            }
        }
        fclose($fp);
    }

    public function getPlayingItem()
    {
        $playData = [];
        $startTimes = [];
        for($i=0; $i < count($this->playListFiles); $i++){
            $item = $this->playListFiles[$i];
            if(empty($item)){
                unset($this->playListFiles[$i]);
                continue;
            }
            $tmp = explode(',', $item);
            $playData[$i]['startTime'] = array_pop($tmp);
            $startTimes[$i] = $playData[$i]['startTime'];
            $playData[$i]['name'] = implode(',',$tmp);
        }
        array_multisort($startTimes, SORT_ASC, $playData);
        for($i=0; $i < count($playData); $i++){
            if (strtotime($playData[$i]['startTime']) >= time()){
                break;
            }
        }
        $info = $this->get_mp3($playData[$i-1]['name'], true, false);
        $tmp = explode(DS,$playData[$i-1]['name']);
        $name = array_pop($tmp);
        $playing = ['number'=>($i-1),'path'=>$playData[$i-1]['name'],'name'=>$name,'duration'=>$info['data']['time'],'startTime'=>$playData[$i-1]['startTime']];
        $info = $this->get_mp3($playData[$i]['name'], true, false);
        $tmp = explode(DS,$playData[$i]['name']);
        $name = array_pop($tmp);
        $next = ['number'=>$i,'path'=>$playData[$i]['name'],'name'=>$name,'duration'=>$info['data']['time'],'startTime'=>$playData[$i]['startTime']];
        $output = ['playing'=>$playing, 'next'=>$next];
        return $output;
    }

    public function play($file, $start = 0)
    {
        if(file_exists($file)){
            $info = $this->get_mp3($file, true, true);
            $end = floor($info['data']['length']);
            $startIndex = ceil($start * (1 / 0.026));
            $endIndex = $end > 0 ? ceil($end * (1 / 0.026)) : -1;
            $startPos = $info['frames'][$startIndex][0];
            $endPos = $info['frames'][$endIndex][0] + $info['frames'][$endIndex][2];
            $headerBytes = $info['frames'][0][0];
            header("Content-type: audio/mpeg");
//            header("Content-length: " . filesize($file));
            header("Cache-Control: no-cache");
            header("Content-Transfer-Encoding: binary");
            $fp = fopen($file, 'r');
            $header = fread($fp, $headerBytes);
            fseek ($fp , $startPos);
            $data = fread($fp, $endPos - $startPos);
            echo $header.$data;
        }else{
            header("HTTP/1.0 404 Not Found");
        }
    }

    public function playList()
    {
        $startingData = $this->getPlayingItem();
        $startingItem = $startingData['playing']['number'];
        for($i = $startingItem; $i < count($this->playListFiles); $i++){
            $tmp = explode(',',$this->playListFiles[$i]);
            $startTime = array_pop($tmp);
            $file = implode(',',$tmp);
            $start = time() - strtotime($startTime);
            if(file_exists($file)){
                $this->play($file, $start);
            }else{
                echo 'nothing to play!';
                header("HTTP/1.0 404 Not Found");
            }
        }
    }

}