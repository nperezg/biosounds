$(function() {

    var player = document.getElementById('player');
    var playContinuous = $("input[name=continuous_play]").prop("checked");
    var estimateDistID = $("input[name=estimateDistID]").val();
    var time = null;
    var seek = 0;
    selectionDuration = maxTime - minTime;

    var xmin = 0;
    var xmax = maxTime;

    var clock;

    var playPauseElement = $('#playPause');

    //When the section playing finishes, load the rest of the sound and continue playing.
    player.onended = function() {
        stop();

        if(maxTime >= fileDuration){
            savePlayLog();
        }
        if(playContinuous === true) {
            continuousPlay();
        }
    };

    playPauseElement.on('click',function() {
        if ($(this).html().indexOf('play') !== -1) {
            play(xmin);
            $(this).html('<span class="glyphicon glyphicon-pause"></span>');
        }
        else if ($(this).html().indexOf('pause') !== -1) {
            pause();
            $(this).html('<span class="glyphicon glyphicon-play"></span>');
        }
    });

    $('#stop').on('click',function() {
        stop();
    });

    if (playContinuous === true || (estimateDistID && estimateDistID > 0)) {
        playPauseElement.trigger( 'click' );
    }

    $('#cropbox').Jcrop({
        onChange: showCoordinates,
        onSelect: showCoordinates,
        addClass: 'custom',
        bgColor: 'black'
    });

    $('#playerSpeed').on('change',function(){
        player.playbackRate = $(this).val();
    });

    function continuousPlay()
    {
        if (fileDuration > maxTime) {
            toggleLoading("Loading Recording...");
            $('#x').val(maxTime);
            $('#w').val(maxTime + selectionDuration);
            $('#recordingForm').submit();
        }
    }

    /**
     * Function to move player cursor
     */
    function moveObjRight(draw)
    {
        if (draw < 0) {
            draw = 0;
        }

        myLine.style.marginLeft = draw + "px";
        myLine.style.visibility = "visible";
    }

    function timeMonitor()
    {
        var time = player.currentTime; //Get the current position
        var time_current = time;
        time = time + minTime; //Add time_min to offset when zooming in a sound
        var draw_time1 = (time_current) / selectionDuration;

        $("#time_sec_div").html(Math.round(time));
        moveObjRight(draw_time1 * specWidth);
    }

    function savePlayLog()
    {
        var unixDate = new Date().valueOf()/1000;
        var playStartTime = getCookie('playStartTime');
        deleteCookie('playStartTime');

        $.post(base_url + "/ajaxcallmanager.php?class=PlayLog&action=save", {recordingId: recordingId, userId: userId, startTime: playStartTime, stopTime: unixDate })
            .fail(function(xhr, textStatus, errorThrown) {
                console.log('Error while saving play log: ' + xhr.responseText);
            })
            .done(function(data) {
                if(data.error)
                    console.log('Error while saving play log: ' + data.error);
            });
    }

    function pause()
    {
        player.pause();
        clearInterval(clock);
        savePlayLog();
    }

    function play(xstart)
    {
        seek = xstart - minTime;
        if(!getCookie('playStartTime'))
            document.cookie = "playStartTime=" + new Date().valueOf() / 1000;
        player.play();
        if (seek > 0)
            player.currentTime = seek;
        xmin = 0;
        clock = setInterval(function(){
            timeMonitor();
        }, 30);
    }

    function stop()
    {
        player.currentTime = 0;
        $('#playPause').html('<span class="glyphicon glyphicon-play"></span>');

        if (!player.paused) {
            player.pause();
            savePlayLog();
        }
        timeMonitor();
        playContinuous = $("input[name=continuous_play]").prop("checked");
        clearInterval(clock);

        if (estimateDistID && estimateDistID > 0 && !playContinuous) {
            var url = base_url + "/ajaxcallmanager.php?class=tag&action=showCallDistance&id="+estimateDistID;
            openModal(url);
        }
    }

    function showCoordinates(c)
    {
        var filterElement = $("input[name=filter]");
        xmin = (c.x / specWidth * selectionDuration + minTime).toFixed(1);
        xmax = (c.x2 / specWidth * selectionDuration + minTime).toFixed(1);
        ymax = Math.round((c.y / specHeight) *- (maxFrequency - minFrequency) + maxFrequency);
        ymin = Math.round((c.y2 / specHeight) *- (maxFrequency - minFrequency) + maxFrequency);

        if (xmin === xmax || ymin === ymax) {
            xmin = minTime;
            xmax = maxTime;
            ymin = minFrequency;
            ymax = maxFrequency;
        }
        //Values for Boxes Filter
        $('#x').val(xmin);
        $('#w').val(xmax);
        $('#y').val(ymin);
        $('#h').val(ymax);
        $('#zoom_submit').prop("disabled", false);

        filterElement.prop("checked", true);
        filterElement.prop("disabled", false);
    }
});
