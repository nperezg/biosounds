$(document).ready(function(){
	var tagID;
	var tagControl;
	
	$(".container").on( "submit", ".async_form", function(e){
		submitAsyncForm(this, e);
	});

	$(".open-modal").click(function(e){	
		openModal(this.href);
		e.preventDefault();		
	});	
	
	$(".open-form").click(function(e){
		$("#hiddenForm").toggle();	
		$("#add_user_btn").toggle();
		$("#upload_btn").toggle();
		e.preventDefault();		
	});	
	
	$(".toggleTag").click(function(e){	
		toggleTags($(this));
	});
	
	$(".readingMode").click(function(e){	
		var currentTimePlayer = player.currentTime;
		$("#x").val(currentTimePlayer + timeMin);
		$("#w").val(currentTimePlayer + timeMin + 60);
		$("#y").val(1);
		$("#h").val(fileFreqMax);
		$("input[name=filter]").prop("checked", false);
		$("input[name=continuous_play]").prop("checked", true);
		$("#soundForm").submit();
		e.preventDefault();	
	});
	
	/* Save users list */
	$(".save-user").on('change', 'input, select, textarea', function(){
		saveFormList($(this), "user");
	});
	
	/* Save sounds list */
	$(".save-sound").on('change', 'input, select, textarea', function(){
		saveFormList($(this), "SoundManager");
	});
	
	$("[data-hide]").on("click", function(){
		$(this).closest("." + $(this).attr("data-hide")).hide();
	});
	
	$(".container").on( "click", ".zoom-tag", function(e) {
		var leftCanvas = $("#myCanvas")[0].getBoundingClientRect().left;
		var topCanvas = $("#myCanvas")[0].getBoundingClientRect().top;
		
		var tagElement = $(this).parent().parent().parent().prev();
		var left = tagElement[0].getBoundingClientRect().left - leftCanvas;
		var width = left + tagElement[0].getBoundingClientRect().width;
		var top = tagElement[0].getBoundingClientRect().top - topCanvas;
		var height = tagElement[0].getBoundingClientRect().height + top;
				
		var coordinates = {x:left, x2:width, y:top, y2:height}; 
		showCoords(coordinates);
		
		$("#soundForm").submit();
		e.preventDefault();
	});
	
	$(".container").on( "click", ".estimate-distance", function(e) {
		var leftCanvas = $("#myCanvas")[0].getBoundingClientRect().left;
		var elemID = $(this).attr("id");
		var tagID = elemID.substring(elemID.indexOf("_")+1, elemID.length);
		
		var tagElement = $(this).parent().parent().parent().prev();
		var left = tagElement[0].getBoundingClientRect().left - leftCanvas;
		var width = left + tagElement[0].getBoundingClientRect().width;
				
		var tMin = (left / specWidth * selectionDuration + timeMin);
		var tMax = (width/ specWidth * selectionDuration + timeMin);	
		
		var timeLength = tMax - tMin;

		if(timeLength > 30)
			tMax = tMin + 30;

		$('#x').val(tMin);
		$('#w').val(tMax);
		$('#y').val(1);
		$('#h').val(fileFreqMax);
		
		$("input[name=filter]").prop("checked", false);
		$("input[name=continuous_play]").prop("checked", false);
		$("input[name=estimateDistID]").val(tagID);
		toggleLoading();
		$("#soundForm").submit();
		e.preventDefault();
	});
	
	$(".container").on( "click", ".tag", function(e) {
		$('.panel-tag').hide();
		if($('#zoom_submit').prop("disabled") && this.id == "add-tag-btn")
			alert("Please, select an area of the spectrogram.");
		else {	
			$.ajax({
				   type: "POST",
				   url: this.href,
				   data: $("#soundForm").serialize(),
				   success: function(data)
				   {		  
						if(data.status == 'error'){
							showMessage(data.message, true);
						}
						else {
							$("#"+tagControl).fadeOut("fast");
							$('#modalWindows').html(data);
							$("#modal-div").modal('show');
						}
				   },
				   error: function(data, error, message){
						showMessage(message, true);
				   }			   
			 });
		 }
		e.preventDefault();
	});	
	
	$( ".container").on("mouseenter", ".tag-controls", function(e) {
		$(this).css("background-color","rgba(255,255,255, 0.15)");
			
    	var controls = $(this).next();
		controls.css("top",e.pageY - $(this).parent().offset().top);
		controls.css("left",e.pageX - $(this).parent().offset().left);
		if(!controls.is(':visible')){
			controls.fadeIn(400);
		}
	  });
	  
	$( ".container").on("mouseleave", ".tag-controls",  function() {
		  $(".panel-tag").hide();
		  $(this).css("background-color","");
	});
	  
	$( ".container").on("mouseenter", ".panel-tag", function(){
		$(this).show();
		$(this).prev().css("background-color","rgba(255,255,255, 0.15)");
		$(".panel-tag").not(this).hide();	
	});	
	
	$( ".container").on("mouseleave", ".panel-tag", function() {
		$(this).prev().css("background-color","");
		$(".panel-tag").fadeOut("fast");
	});  
	
	$(".log").click(function(){
		$("#messageBox").hide();	
	});
	
	$(".user").click(function(){
		$("#messageBox").hide();	
	});
	
	$(".channel-left").click(function(e){
		$("input[name=channel]").val(1);
		$("#soundForm").submit();
		e.preventDefault();
	});
	
	$(".channel-right").click(function(e){
		$("input[name=channel]").val(2);
		$("#soundForm").submit();
		e.preventDefault();
	});
	
	$("#shift-left").click(function(e){
		var shiftRate = 0.95;
		
		var shiftLeftMin = Math.round(timeMin - (selectionDuration * shiftRate));
		if(shiftLeftMin < 0)
			shiftLeftMin = 0;	
		var shiftLeftMax = Math.round(timeMax - (selectionDuration * shiftRate));

		$("#x").val(shiftLeftMin);	
		$("#w").val(shiftLeftMax);
		$("#soundForm").submit();
		
		e.preventDefault();	
	});
	
	$("#shift-right").click(function(e){
		var shiftRate = 0.95;
					
		var shiftRightMin = Math.round(timeMin + (selectionDuration * shiftRate));
		var shiftRightMax = Math.round(timeMax + (selectionDuration * shiftRate));
		if(shiftRightMax > fileDuration)
			shiftRightMax = fileDuration;
		
		$("#x").val(shiftRightMin);	
		$("#w").val(shiftRightMax);
		$("#soundForm").submit();
			
		e.preventDefault();	
	});
	
	$(".viewport").click(function(e){
		$("#x").val(0);
		$("#w").val(fileDuration);
		$("#y").val(1);
		$("#h").val(fileFreqMax);
		$("input[name=filter]").prop("checked", false);
		$("input[name=continuous_play]").prop("checked", false);
		$("input[name=estimateDistID]").val("");
		$("#soundForm").submit();
		e.preventDefault();
	});
	
	$("#zoom_submit").click(function(e){
		$(this).prop("disabled", true);
		$("#soundForm").submit();
	});
	
	$(".container").on( "click", "#clean_tmp", function(e) {
		cleanTmp();
	});
	
	toggleLoading();
	 
	 $.fn.toggleDisabled = function(){
        return this.each(function(){
            this.disabled = !this.disabled;
        });
    };
});


function toggleTags(element){	
	if($('.tag-controls').is(':visible')){
		$("input[name=showTags]").val(0);
		$('.tag-controls').hide();
		$('.panel-tag').hide();
		element.html("<span title='Show Tags' class='glyphicon glyphicon-eye-open'></span>");
	}
	else {
		$("input[name=showTags]").val(1);
		$('.tag-controls').show();
		element.html("<span title='Hide Tags' class='glyphicon glyphicon-eye-close'></span>");
	}
}

function showMessage(message, warning){
	$("#message").html(message);
	$("#messageBox").show();	
}

function toggleLoading(message){
	$(".loading").toggle();
	if(message != undefined)
		$("#loading-text").html(message);
}

function openModal(href){
	$.ajax({
	   type: "POST",
	   url: href,
	   success: function(data)
	   {	
			if(data.status == 'error'){
				showMessage(data.message, true);
			}
			else {
				$('#modalWindows').html(data);
				$("#modal-div").modal('show');
			}
	   },
	   error: function(data, error, message){
			showMessage(message, true);
	   }	
	});
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) === ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) === 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function deleteCookie(name){
	document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}

function deleteTag(tagID){
	$.ajax({
		type: "POST",
		url: "ajaxcallmanager.php?class=SoundTag&action=delete&id="+tagID,
		success: function(data){
			$("#"+tagID ).remove();	
			showMessage("Tag succesfully removed!");
		},
		error: function(data, error, message){
			showMessage(message, true);
		}
	});	
}

/*
 * Function for checking if there is any file upload process running.
 */
function checkUploadProcesses() {
	$.ajax({
		type: "POST",
		url: "ajaxcallmanager.php?class=file&action=checkUploadProcess",
		success: function(running) {
			if(running){
				$("#upload_btn").html("<img src='assets/images/ajax-loader.gif'> Upload process running");
				$("#upload_btn").prop("disabled", "disabled");
			}
			else {
				$("#upload_btn").html("<span class='glyphicon glyphicon-upload'></span> Upload Sounds");
				$("#upload_btn").prop("disabled", "");
			}
		}
	});	
	setTimeout(checkUploadProcesses, 3000);
}

/*
 * Function for saving the fields of a list with formulars.
 */
function saveFormList(element, object){
	var row = element.closest("tr"); 
	var columns = row.find("input, select");
	var values = {};
	var url = "ajaxcallmanager.php?class=" + object + "&action=save";

	columns.each(function(i, item) {
		var value = item.value;
		if(item.type == "checkbox" && item.checked)
			value = true;
		else if(item.type == "checkbox" && !item.checked)
			value = false;
		
		values[item.name+"_"+item.type] = value;
	});

	$.ajax({
	   type: "POST",
	   data: values,
	   url: url,
	   success: function(data) {
		   console.log("Data has been saved. Rows: "+data);
	   },
	   error: function (xhr, ajaxOptions, thrownError) {
		   $("#message").html("Error: "+thrownError);
		   $("#messageBox").show();
	   }
	});
}

/*
 * Shows Call Distance Estimation Window
 */
function showEstimateDist(tagID){
	var url = "ajaxcallmanager.php?class=SoundTag&action=showCallDistance&id="+tagID;
	openModal(url);
}

function submitAsyncForm(form, e){
	e.preventDefault();
	$.ajax({
	   type: "POST",
	   url: form.action,
	   data: $(form).serialize(),
	   success: function(data){		  
			if(data.status == 'error')
				showMessage(data.message, true);
			else 
				showMessage("Changes saved.");
	   },
	   error: function(data, error, message){
			showMessage(message, true);
	   }			   
	 });	
}

function cleanTmp(){
	$.ajax({
		type: "POST",
		url: "ajaxcallmanager.php?class=admin&action=cleanTmp",
		success: function(data){
			showMessage("Temporary file have been deleted.");
		},
		error: function(data, error, message){
			showMessage(message, true);
		}
	});	
}
