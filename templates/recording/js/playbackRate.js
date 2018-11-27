$(function() {

    currentTime = 0;

    let playbackControl = document.querySelector('.playback-rate-control');
    let playbackValue = document.querySelector('.playback-rate-value');
    let context = new AudioContext();
    let offctx = new OfflineAudioContext(channelNum, 512, frequency);  // length (512) doesn't matter, just non-zero
    let source = null;
    let bufferPlay = null;
    let request = new XMLHttpRequest();

    let startTime = 0;
    let pauseTime = 0;
    let elapsedRateTime = 0;
    let pause = false;
    let seek = 0;
    let clock;

    request.open('GET', soundFilePath, true);
    request.responseType = 'arraybuffer';
    request.onload = function() {
        offctx.decodeAudioData(request.response).then(function(buffer) {
            console.log("Sample rate of buffer: " + buffer.sampleRate);
            bufferPlay = buffer;
            playButton.prop('disabled', false);

            if (isContinuous || isDirectStart) {
                playButton.trigger( 'click' );
                isDirectStart = false;
            }
        });
    }
    request.send();

    playButton.click(function() {
        if (this.dataset.playing === 'false') {
            createSource();
            source.start(0, currentTime);
            startTime = context.currentTime;
            clock = setInterval(function(){
                getCurrentTime();
            }, 30);

            if (!getCookie('playStartTime')) {
                document.cookie = "playStartTime=" + new Date().valueOf() / 1000;
            }

            this.dataset.playing = 'true';
            playButton.html('<span class="glyphicon glyphicon-pause"></span>');
            $('#playerCursor').draggable('disable');
        }
        else if (this.dataset.playing === 'true') {
            pause = true;
            seek = 0;
            clearSource();
        }
    });

    $("#stop").click(function() {
        if (!source) {
            stop();
            return;
        }
        clearSource();
    });

    playbackControl.oninput = function() {
        if (source !== null) {
            elapsedRateTime = currentTime - ((context.currentTime - startTime) * this.value);
            source.playbackRate.value = this.value;
            seek = 0;
        }
        console.log(this.value);
        playbackValue.innerHTML = this.value;
    };

    $('#playerCursor').draggable({
        axis: 'x',
        containment: 'parent',
        cursor: 'ew-resize',
        drag: function () {
            seek = parseFloat(this.style.left) / specWidth * selectionDuration;
            currentTime = seek;

            $("#time_sec_div").html(Math.round(minTime + seek));
            pauseTime = 0;
            elapsedRateTime = 0;
        }
    });

    function clearSource()
    {
        if (source) {
            source.stop();
            source = null;
        }
    }

    function stop()
    {
        clearInterval(clock);

        $('#playerCursor').draggable('enable');
        pauseTime = currentTime;
        elapsedRateTime = 0;

        if (!pause) {
            pauseTime = 0;
            startTime = 0;
            currentTime = 0;
            resetCursor();
            $("#time_sec_div").html(Math.round(minTime));
        }

        playButton.html('<span class="glyphicon glyphicon-play"></span>');
        playButton.attr('data-playing', false);
        pause = false;

        //Distance estimation popup after playing
        if (estimateDistID && estimateDistID > 0 && !isContinuous) {
            openModal(base_url + "/ajaxcallmanager.php?class=tag&action=showCallDistance&id="+estimateDistID);
        }
    }

    function createSource()
    {
        source = context.createBufferSource();
        source.buffer = bufferPlay;
        source.loop = false;
        source.onended = function() {
            savePlayLog();

            if (isContinuous && !pause) {
                continuousPlay();
            }

            stop();
        };
        source.connect(context.destination);
        source.playbackRate.value = playbackControl.value;
    }

    function getCurrentTime()
    {
        if (source) {
            currentTime = (context.currentTime - startTime) * source.playbackRate.value + elapsedRateTime + seek;
            currentTime += elapsedRateTime === 0 ? pauseTime : 0;

            moveCursor(currentTime);
            $("#time_sec_div").html(Math.round(minTime + currentTime)); //Add minTime to offset when zooming
        }
    }

    function resetCursor()
    {
        playerCursor.style.left = 0;
        seek = 0;
    }

    function moveCursor(time)
    {
        playerCursor.style.left = (time < 0 ? 0 : time / selectionDuration) * specWidth + 'px';
    }
});