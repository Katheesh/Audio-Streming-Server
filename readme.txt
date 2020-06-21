This class is written to enable offline radio making with php. My definition for offline radio is a set of recorded files that is played continuously after one another.
It only supports mp3 files and works with modern browsers.

Installation:
unzip the downloaded file and make a folder in your webserver root directory. name it something like "radio". The index file is a simple html file that loads the player in an iframe and shows the name and duration of the file being played and the next one. 
You first need to add a folder to your radio folder and name it something like "files" and then execute generatelist.php in your browser to generate list.txt file to be used as the time table for your radio. 
The structure of list.txt is very simple. It is a comma seperated file. The first column is the path to the sound files and the second column is the time to broadcast it. I used new line (\r\n) to start a new row. 
The getPlayingItem() method in the class outputs which file needs to be played and the playList() method does the rest. It starts playing based on the time the viewers requested so every one who connects to your internet radio listens to the same thing.

License:
This program is free for non-commercial use.

Please send any comments and questions to my email address:
haghparast@gmail.com