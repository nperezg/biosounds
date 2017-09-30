<script>
	var horizMove = 0;
	var fileDuration = <?php echo $this->duration;?>;
	var player = document.getElementById('mp3player'); 
	var playContinuous = $("input[name=continuous_play]").prop("checked");
	var estimateDistID = $("input[name=estimateDistID]").val();
	var time = null;
	var seek = 0;
	var timeMin = <?php echo $this->timeMin;?>;
	var timeMax = <?php echo $this->timeMax;?>;
	
	var freqMin = <?php echo $this->freqMin;?>;
	var freqMax = <?php echo $this->freqMax;?>;
	var fileFreqMax = <?php echo $this->fileFreqMax;?>;

	var specWidth = <?php echo $this->specWidth;?>;	
	var specHeight = <?php echo $this->specHeight;?>;	
	
	var soundID = <?php echo $this->soundID;?>;
	var userID = <?php echo $this->userID;?>;
	
	var selectionDuration = timeMax - timeMin;	
	
	var xmin = 0;
	var xmax = timeMax;
	
	var audio_clock;
	
	$(document).ready(function(){
		/*player.ontimeupdate = function() {
			timeMonitor();
		};*/
		
		//When the section playing finishes, load the rest of the sound and continue playing.
		player.onended = function() {
			stop();			
			
			if(timeMax >= fileDuration){
				saveListeningTime();
			}
			if(playContinuous)
				continuousPlay();
		};
		
		$('#playPause').on('click',function(){
			if($(this).html().indexOf('play') !=-1){
				play(xmin);
			    $(this).html('<span class="glyphicon glyphicon-pause"></span>');
			}
			else if($(this).html().indexOf('pause') !=-1){
				pause();
				$(this).html('<span class="glyphicon glyphicon-play"></span>');
			}
		});
		
		$('#stop').on('click',function(){
			stop();
		});
		
		if(playContinuous || estimateDistID)
			$('#playPause').trigger( "click" );
		
		$('#cropbox').Jcrop({
			onChange: showCoords,
			onSelect: showCoords,
			addClass: 'custom',
			bgColor: 'black'
		});
		
		$('#playerSpeed').on('change',function(){
			player.playbackRate = $(this).val();
		});
	});
	
	function getUrlVars() {
		var vars = {};
		var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
			vars[key] = value;
		});
		return vars;
	}

	function continuousPlay(){
		var maxTimeShift = timeMax + selectionDuration;
		if(fileDuration > timeMax){	
			toggleLoading("Loading Sound...");
			$("#x").val(timeMax);
			$("#w").val(maxTimeShift);
			$("#soundForm").submit();
		} 											
	}
	
	/**
	 * Function to move player cursor
	 */
	function moveObjRight(draw) 
	{    
		if (draw < 0)
			draw = 0;

		Hmove2 = draw;
	   	myLine.style.marginLeft = Hmove2 + "px";
	   	myLine.style.visibility = "visible";
	}
	
	function timeMonitor() {
		var time = player.currentTime; //Get the current position
		var time_current = time;
		time = time + timeMin; //Add time_min to offset when zooming in a sound

		$("#time_sec_div").html(Math.round(time));
		
		draw_time1 = (time_current - 0.2) / selectionDuration;
		draw_time2 = (draw_time1 * specWidth);
		moveObjRight(draw_time2);
	}
	
	function saveListeningTime()
	{
		var unixDate = new Date().valueOf()/1000;
		var playStartTime = getCookie('playStartTime');
		deleteCookie('playStartTime');
		
		$.post("ajaxcallmanager.php?class=soundlistenlog&action=save", {SoundID: soundID, UserID: userID, StartTime: playStartTime, StopTime: unixDate })
			.fail(function(xhr, textStatus, errorThrown) {
				console.log('Error while calling saving sound listening log: '+xhr.responseText);
			})
			.done(function(data) {
				if(data.error)
					console.log('Error while saving sound listening log: '+data.error);
		});	
	}

	function pause(){
		player.pause();
		clearInterval(audio_clock);
		saveListeningTime();
	}

	function play(xstart){
		seek = xstart - timeMin;
		if(!getCookie('playStartTime'))
			document.cookie = "playStartTime=" + new Date().valueOf() / 1000;
		player.play();
		if (seek > 0)
			player.currentTime = seek;
        xmin = 0;
		audio_clock = setInterval(function(){
			timeMonitor();
		}, 30);
	 }

	function stop(){
		player.currentTime = 0;
		$('#playPause').html('<span class="glyphicon glyphicon-play"></span>');
		if(!player.paused){
			player.pause();	
			saveListeningTime();				
		} 
		timeMonitor();		
		playContinuous = $("input[name=continuous_play]").prop("checked");	
		clearInterval(audio_clock);	

		if(estimateDistID && !playContinuous)
			showEstimateDist(estimateDistID);		
	}
	
	function showCoords(c){
		xmin = (c.x / specWidth * selectionDuration + timeMin).toFixed(1);
		xmax = (c.x2 / specWidth * selectionDuration + timeMin).toFixed(1);
		ymax = Math.round((c.y / specHeight) *- (freqMax - freqMin) + freqMax);
		ymin = Math.round((c.y2 / specHeight) *- (freqMax - freqMin) + freqMax);

		if (xmin == xmax || ymin == ymax){
			xmin = timeMin;
			xmax = timeMax;
			ymin = freqMin;
			ymax = freqMax;
		}
		//Values for Boxes Filter
		$('#x').val(xmin);
		$('#w').val(xmax);
		$('#y').val(ymin);
		$('#h').val(ymax);
		$('#zoom_submit').prop("disabled", false);
		$("input[name=filter]").prop("checked", true);
		$("input[name=filter]").prop("disabled", false);
	}
	
</script>
