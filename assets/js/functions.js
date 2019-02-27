$(function() {
	var tagID;
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
	
	$("[data-hide]").on("click", function(){
		$(this).closest("." + $(this).attr("data-hide")).hide();
	});

	$(".log").click(function(){
		$("#messageBox").hide();	
	});
	
	$(".user").click(function(){
		$("#messageBox").hide();	
	});
	
	toggleLoading();
	 
	 $.fn.toggleDisabled = function(){
        return this.each(function(){
            this.disabled = !this.disabled;
        });
    };
});

function showMessage(message, warning) {
	$("#message").html(message);
	$("#messageBox").show();	
}

function toggleLoading(message) {
	$(".loading").toggle();
	if(message !== undefined)
		$("#loading-text").html(message);
}

function openModal(href) {
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

function deleteCookie(name) {
	document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}

/*
 * Function for saving the fields of a list with formulars.
 */
function saveFormList(element, object, base_url)
{
	var row = element.closest("tr"); 
	var columns = row.find("input, select");
	var values = {};
	var url = base_url + "/ajaxcallmanager.php?class=" + object + "&action=save";

	columns.each(function(i, item) {
		var value = item.value;
		if(item.type === "checkbox" && item.checked)
			value = 1;
		else if(item.type === "checkbox" && !item.checked)
			value = 0;
		
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
