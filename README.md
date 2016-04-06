# Music-Pattern-Recognition-And-Recommendation-Generation
Implementing smartShuffle technology and recommendations on the basis of similarity approach

Do this -

Main directory me, 

Make "data" folder
Make this directory 

data 
  -files
      -songs
        -copy paste songs 
  -json
     -history.json
     -duration.json
     -index_map.json
     -sim_matrix.json
     -songList.json
     -songnames.json

Run this in terminal while adding any new songs in the data folder - 

chmod -R 755 /var/www/html/data/files

Change in url.php between temporary and actual files. 
Also comment in playSong.js for switching between temporary and actual files


reset.php

If total_reset = true, complete new
total_rest = false only for adding new files.
