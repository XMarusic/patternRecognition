<?php 
    include 'inc/loadFiles.php'; 
    ini_set('memory_limit', '-1');
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1" />
        <meta http-equiv="X-UA-Compatible" content="IE=10" />
        <link rel="stylesheet" type="text/css" href="/css/style.css">
        <script src="js/jquery-1.11.1.min.js"></script>
        <script src="js/playSong.js"></script>
    </head>
    <body>
        <header>
            <div class="inline mid-align logoWrapper">
                <div class="inline mid-align logoMain"></div>
                <div class="inline mid-align">smartShuffle</div>
            </div>
            <div class="inline mid-align searchFromList">
                <input class="searchInput" type="text" placeholder ="Look for your song..."/>
            </div>
        </header>
        <div class="contentWrapper">
            <div class="audioControllers">
                <div class="inline mid-align audioControlImgWrapper">
                    <img class="audioControlIcons playPrev" src="images/playPrev.png">
                </div>
                <div class="inline mid-align audioControlImgWrapper">
                    <img class="audioControlIcons playNextMain" src="images/playNext.png">
                </div>
            </div>
            <div class="leftBarWrapper">
                <div class="leftBarInnerWrapper">
                    <div class="leftBarOptionsWrapper">
                        <div class="leftBarHeader">OPTIONS</div>
                        <ul class="leftBarUl">
                            <li class="leftBarUlLi">Recommendation</li>
                        </ul>
                    </div>
                    <div class="leftBarOptionsWrapper historyLeftBar">
                        <div class="leftBarHeader">HISTORY</div>
                        <ul class="contentSongList leftBarUl historyUL">
                            <?php getHistory(); ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="songListWrapper">
                 <div class="broadList">
                    <div class="broadHeader"></div>
                    <ul class="broadSongListUl resultSongListUl" name="resultSongUl"></ul>
                </div>

                <div class="topPickList">
                    <div class="songListHeader topPickHeader">TOP PICKS FOR YOU</div>
                    <ul class="contentSongList broadSongListUl topPicksUl" name="topSongUl">
                        <?php getTopPicks(); ?>
                    </ul>
                </div>
                <div class="entireSongList">
                    <div class="songListHeader entireListHeader">SONG LIST</div>
                    <div class="headerList">
                        <div class="inline mid-align serialNumber">#</div>
                        <div class="inline mid-align songNameHeader">SONG</div>
                        <div class="inline mid-align expectedTimeHeader">MEAN PT</div>
                    </div>
                    <ul class="contentSongList contextSongListUl entirePickUl" name="entireSongUl">
                        <?php getFiles(); ?>
                    </ul>
                </div>
            </div>
            <audio controls>
              <source src="" type="audio/mpeg">
            Your browser does not support the audio element.
            </audio>
        </div>
    </body>
</html>
