$(document).ready(function(){
	var tagID;
	var tagControl;
	var container = $(".container");
	
	container.on( "submit", ".async_form", function(e){
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
		$("#x").val(currentTimePlayer + minTime);
		$("#w").val(currentTimePlayer + minTime + 60);
		$("#y").val(1);
		$("#h").val(fileFreqMax);
		$("input[name=filter]").prop("checked", false);
		$("input[name=continuous_play]").prop("checked", true);
		$("#recordingForm").submit();
		e.preventDefault();	
	});
	

	$("[data-hide]").on("click", function(){
		$(this).closest("." + $(this).attr("data-hide")).hide();
	});

    container.on( "click", ".zoom-tag", function(e) {
		var leftCanvas = $("#myCanvas")[0].getBoundingClientRect().left;
		var topCanvas = $("#myCanvas")[0].getBoundingClientRect().top;
		
		var tagElement = $(this).parent().parent().parent().prev();
		var left = tagElement[0].getBoundingClientRect().left - leftCanvas;
		var width = left + tagElement[0].getBoundingClientRect().width;
		var top = tagElement[0].getBoundingClientRect().top - topCanvas;
		var height = tagElement[0].getBoundingClientRect().height + top;
				
		var coordinates = {x:left, x2:width, y:top, y2:height}; 
		showCoordinates(coordinates);
		
		$("#recordingForm").submit();
		e.preventDefault();
	});

    container.on( "click", ".tag", function(e) {
		$('.panel-tag').hide();
		if($('#zoom_submit').prop("disabled") && this.id == "add-tag-btn")
			alert("Please, select an area of the spectrogram.");
		else {	
			$.ajax({
				   type: "POST",
				   url: this.href,
				   data: $("#recordingForm").serialize(),
				   success: function(data)
				   {		  
						$("#"+tagControl).fadeOut("fast");
						$('#modalWindows').html(data);
						$("#modal-div").modal('show');
				   },
				   error: function(response){
                       	showMessage(response.responseJSON.message, true);
				   }
			 });
		 }
		e.preventDefault();
	});

    container.on("mouseenter", ".tag-controls", function(e) {
		$(this).css("background-color","rgba(255,255,255, 0.15)");
			
    	var controls = $(this).next();
		controls.css("top",e.pageY - $(this).parent().offset().top);
		controls.css("left",e.pageX - $(this).parent().offset().left);
		if(!controls.is(':visible')){
			controls.fadeIn(400);
		}
	  });

    container.on("mouseleave", ".tag-controls",  function() {
		  $(".panel-tag").hide();
		  $(this).css("background-color","");
	});

    container.on("mouseenter", ".panel-tag", function(){
		$(this).show();
		$(this).prev().css("background-color","rgba(255,255,255, 0.15)");
		$(".panel-tag").not(this).hide();	
	});

    container.on("mouseleave", ".panel-tag", function() {
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
		$("#recordingForm").submit();
		e.preventDefault();
	});
	
	$(".channel-right").click(function(e){
		$("input[name=channel]").val(2);
		$("#recordingForm").submit();
		e.preventDefault();
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
	if(message !== undefined)
		$("#loading-text").html(message);
}

function openModal(href){
	$.ajax({
	   type: "POST",
	   url: href,
	   success: function(data)
	   {	
            $('#modalWindows').html(data);
            $("#modal-div").modal('show');
	   },
	   error: function(response){
           showMessage(response.responseJSON.message, true);
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

/*
 * Function for saving the fields of a list with formulars.
 */
function saveFormList(element, object, base_url){
	var row = element.closest("tr"); 
	var columns = row.find("input, select");
	var values = {};
	var url = base_url + "/ajaxcallmanager.php?class=" + object + "&action=save";

	columns.each(function(i, item) {
		var value = item.value;
		if(item.type === "checkbox" && item.checked)
			value = true;
		else if(item.type === "checkbox" && !item.checked)
			value = false;
		
		values[item.name+"_"+item.type] = value;
	});
	console.log(values);

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

function submitAsyncForm(form, e)
{
	e.preventDefault();
	$.ajax({
	   type: "POST",
	   url: form.action,
	   data: $(form).serialize(),
	   success: function(data){		  
			showMessage("Changes saved.");
	   },
	   error: function(response){
           showMessage(response.responseJSON.message, true);
	   }			   
	 });	
}