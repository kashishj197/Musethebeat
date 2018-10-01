<?php
$songQuery = mysqli_query($con,"SELECT * FROM songs ORDER BY RAND() LIMIT 10");
$resultArray = array();

while($row = mysqli_fetch_array($songQuery)) {
    array_push($resultArray, $row['id']);
}

$jsonArray = json_encode($resultArray);
?>

<script>

$(document).ready(function() {
    var newPlaylist = <?php echo $jsonArray; ?>;
    audioElement = new Audio();
    setTrack(newPlaylist[0], newPlaylist, false);
    //to have full width in volume bar
    updateVolumeProgressBar(audioElement.audio);

    //to prevent progressBar from highlighting progressBar content
    $("#nowPlayingBarContainer").on("mousedown touchstart mousemove touchmove", function(e){
        e.preventDefault();
    });

    //change values in progressBar on mouse click
    $(".playbackBar .progressBar").mousedown(function(){
        mouseDown = true;
    });

    $(".playbackBar .progressBar").mousemove(function(e){
        if(mouseDown){
            //set time of song,depending on mouse position
            timeFromOffset(e, this);
        }
    });

    $(".playbackBar .progressBar").mouseup(function(e){
        timeFromOffset(e, this);
    });

    $(".volumeBar .progressBar").mousedown(function(){
        mouseDown = true;
    });

    $(".volumeBar .progressBar").mousemove(function(e){
        if(mouseDown){

            var percentage = e.offsetX / $(this).width();
            if(percentage >= 0 && percentage <= 1 ){
                audioElement.audio.volume = percentage;
            }
        }
    });

    $(".volumeBar .progressBar").mouseup(function(e){
        var percentage = e.offsetX / $(this).width();
        audioElement.audio.volume = percentage;
    });

    $(document).mouseup(function(){
        mouseDown = false;
    });

});

//increase progressbar on dragging mouse or clicking anywhere on it
function timeFromOffset(mouse, progressBar){
    var percentage = mouse.offsetX / $(progressBar).width() * 100;
    var seconds = audioElement.audio.duration * (percentage / 100);
    audioElement.setTime(seconds);
}

//play prev song or rewind on click if song already played more then 3 secongs 
function prevSong(){
    if(audioElement.audio.currentTime >= 3 || currentIndex == 0){
        audioElement.setTime(0);
    }
    else{
        currentIndex = currentIndex - 1;
        setTrack(currentPlaylist[currentIndex], currentPlaylist, true);
    }
}
//to play nextsong add 1 to the currentPlaylist array
function nextSong(){
    if(repeat){
        audioElement.setTime(0);
        playSong();
        return;
    }
    if(currentIndex == currentPlaylist.length - 1){
        currentIndex = 0;
    }
    else{
        currentIndex++;
    }

    var trackToPlay = shuffle ? shufflePlaylist[currentIndex] : currentPlaylist[currentIndex];
    setTrack(trackToPlay, currentPlaylist, true);
}

//repeat songs
function setRepeat(){
    repeat = !repeat;
    var imageName = repeat ? "repeat-active.png" : "repeat.png";
    $(".controlButton.repeat img").attr("src","assets/images/icons/" + imageName);
}

//shuffle songs
function setShuffle(){
    shuffle = !shuffle;
    var imageName = shuffle ? "shuffle-active.png" : "shuffle.png";
    $(".controlButton.shuffle img").attr("src","assets/images/icons/" + imageName);
    if(shuffle){
        shuffleArray(shufflePlaylist);
        currentIndex = shufflePlaylist.indexOf(audioElement.currentlyPlaying.id);
    }
    else{
        currentIndex = currentPlaylist.indexOf(audioElement.currentlyPlaying.id);
    }
}

//shuffle the song array
function shuffleArray(a){
    var j,x,i;
    for(i=a.length ; i; i--){
        j=Math.floor(Math.random() * i);
        x = a[i-1];
        a[i-1] = a[j];
        a[j] = x;
    }
}

//Mute the song on click of volume icon
function setMute(){
    audioElement.audio.muted = !audioElement.audio.muted;
    var imageName = audioElement.audio.muted ? "volume-mute.png" : "volume.png";
    $(".controlButton.volume img").attr("src","assets/images/icons/" + imageName);
}

//set the track and play
function setTrack(trackId, newPlaylist, play) {
    if(newPlaylist != currentPlaylist){
        currentPlaylist = newPlaylist;
        shufflePlaylist = currentPlaylist.slice();
        shuffleArray(shufflePlaylist);
    }
    if(shuffle){
        currentIndex = shufflePlaylist.indexOf(trackId);
    }else{
        currentIndex = currentPlaylist.indexOf(trackId);
    }

    pauseSong();

    $.post("includes/handlers/ajax/getSongJson.php", {songId: trackId}, function(data){

        var track = JSON.parse(data);

        $(".trackName span").text(track.title);

        $.post("includes/handlers/ajax/getArtistJson.php", {artistId: track.artist}, function(data){

        var artist = JSON.parse(data);

        $(".artistName span").text(artist.name);
        $(".artistName span").attr("onclick","openPage('artist.php?id=" + artist.id + "')");
    });

        $.post("includes/handlers/ajax/getAlbumJson.php", {albumId: track.album}, function(data){

        var album = JSON.parse(data);

        $(".albumLink img").attr("src", album.artworkPath);
        $(".albumLink img").attr("onclick","openPage('album.php?id=" + album.id + "')");
        $(".trackName span").attr("onclick","openPage('album.php?id=" + album.id + "')");
    });

        audioElement.setTrack(track);

        if(play == true) {
            playSong();
        }
    });
}

//play the song on click
function playSong() {

    if(audioElement.audio.currentTime == 0) {
        $.post("includes/handlers/ajax/updatePlays.php", { songId: audioElement.currentlyPlaying.id });
    }
    $(".controlButton.play").hide();
    $(".controlButton.pause").show();
    audioElement.play();
}

//pause the song on click 
function pauseSong(){
    $(".controlButton.play").show();
    $(".controlButton.pause").hide();
    audioElement.pause();
}

</script>

<div id="nowPlayingBarContainer">
    <div id="nowPlayingBar">
        <div id="nowPlayingLeft">
            <span class="albumLink">
                <img src="" class="albumArtwork" role="link" tabindex="0">
            </span>
            
            <div class="trackInfo">
                <span class="trackName">
                    <span role="link" tabindex="0"></span>
                </span>
                <span class="artistName">
                    <span role="link" tabindex="0"></span>
                </span>
            </div>
        </div>
        
        <div id="nowPlayingCenter">
            <div class="content playerControls">
                <div class="buttons">
                    <button class="controlButton shuffle" title="Shuffle Button" onclick="setShuffle()">
                        <img src="assets/images/icons/shuffle.png" alt="shuffle">
                    </button>
                    
                    <button class="controlButton previous" title="Previous Button" onclick="prevSong()">
                        <img src="assets/images/icons/previous.png" alt="previous">
                    </button>
                    
                    <button class="controlButton play" title="Play Button" onclick="playSong()">
                        <img src="assets/images/icons/play.png" alt="play">
                    </button>
                    
                    <button class="controlButton pause" title="Pause Button" style="display: none;" onclick="pauseSong()">
                        <img src="assets/images/icons/pause.png" alt="pause">
                    </button>
                    
                    <button class="controlButton next" title="Next Button" onclick="nextSong()">
                        <img src="assets/images/icons/next.png" alt="next">
                    </button>
                    
                    <button class="controlButton repeat" title="Repeat Button" onclick="setRepeat()">
                        <img src="assets/images/icons/repeat.png" alt="repeat">
                    </button>
                    
                </div>
                
                    <div class="playbackBar">
                        <span class="progressTime current">0.00</span>
                        
                        <div class="progressBar">
                            <div class="progressBarBg">
                                <div class="progress"></div>
                            </div>
                        </div>
                        
                        <span class="progressTime remaining">0.00</span>
                    </div>
            </div>
        </div>
        
        <div id="nowPlayingRight">
            <div class="volumeBar">
                 <button class="controlButton volume" title="Volume Button" onclick="setMute();">
                        <img src="assets/images/icons/volume.png" alt="volume">
                 </button>
                 <div class="progressBar">
                    <div class="progressBarBg">
                        <div class="progress"></div>
                    </div>
                 </div>
            </div>
        </div>
    </div>
</div>