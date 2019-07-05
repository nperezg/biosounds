document.addEventListener("DOMContentLoaded", function() {

	if (error) {
		showAlert(error);
	}

	$('#loginForm').on('show.bs.collapse', function () {
		let alertBox = document.getElementById('alertBox');
		if (!alertBox) {
			return;
		}
		alertBox.classList.remove('show');
	});

	document.addEventListener('submit', (event) => {
		if (event.target.matches('.js-async-form')) {
			event.preventDefault();
			submitAsyncForm(event.target);
		}
	});

	$(".js-open-modal").click(function(e) {
		let data = [];
		if (this.dataset.id) {
			data = {'id': this.dataset.id};
		}
		openModal(this.href, data);
		e.preventDefault();		
	});	
	
	$("[data-hide]").on("click", function(){
		$(this).closest("." + $(this).attr("data-hide")).hide();
	});

	$(".log").click(function(){
		$("#alertBox").removeClass('show');
	});
	
	$(".user").click(function(){
		$("#alertBox").removeClass('show');
	});

	toggleLoading();
	 
	 $.fn.toggleDisabled = function(){
        return this.each(function(){
            this.disabled = !this.disabled;
        });
    };

	$('.js-species-autocomplete').autocomplete({
		source: function( request, response ) {
			$.post( baseUrl + '/species/getList', { term: request.term } )
				.done(function(data) {
					response(JSON.parse(data));
				})
				.fail(function(response) {
					showAlert(JSON.parse(response.responseText).message);
					response(null);
				});
		},
		minLength:3,
		change: function (event, ui) {
			if (!ui.item) {
				$('#speciesName').val('');
				$('#speciesId').val('');
			}
		},
		select: function (e, ui) {
			$('#speciesName').val(ui.item.label.split('(')[0]);
			$('#speciesId').val(ui.item.value);
			e.preventDefault();
		}
	});
});

function showAlert(message) {
	//TODO: Move hide login form to another place
	$('#loginForm').collapse('hide');

	let alertDiv = document.getElementById('alertBox');

	if (alertDiv) {
		alertDiv.getElementsByTagName('p')[0].textContent = message;
		return;
	}

	alertDiv = document.createElement('div');
	alertDiv.id = 'alertBox';
	alertDiv.classList.add('alert', 'alert-dismissible', 'alert-secondary', 'fade', 'show');
	alertDiv.setAttribute('role', 'alert');

	let paragraph = document.createElement('p');
	paragraph.textContent = message;

	let button = document.createElement('button');
	button.type = 'button';
	button.className = 'close';
	button.setAttribute('data-dismiss', 'alert');
	button.setAttribute('aria-label', 'close');

	let span = document.createElement('span');
	span.innerHTML = '&times;';
	button.appendChild(span);

	alertDiv.appendChild(paragraph);
	alertDiv.appendChild(button);

	let header = document.getElementsByTagName('header')[0];
	document.body.insertBefore(alertDiv, header);
}

function toggleLoading() {
	$('.loading').toggle();
}

function openModal(href, data = []) {
	$.ajax({
	    type: 'POST',
	    url: href,
		data: data,
	})
	.done(function(response){
		$('#modalWindows').html(JSON.parse(response).data);
		$("#modal-div").modal('show');
	})
	.fail(function(response){
	    showAlert(JSON.parse(response.responseText).message);
	});
}

function getCookie(cname) {
    let name = cname + "=";
    let ca = document.cookie.split(';');

    for(var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) === ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) === 0) {
            return c.substring(name.length, c.length);
        }
    }
    return '';
}

function deleteCookie(name) {
	document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}

/*
 * Function for saving the fields of a list with forms.
 */
function saveFormList(element, url)
{
	let row = element.closest("tr");
	let columns = row.find("input, select");
	let values = {};
	let value = '';

	columns.each(function(i, item) {
		value = item.value;
		if (item.type === "checkbox" && item.checked) {
			value = 1;
		} else if(item.type === "checkbox" && !item.checked) {
			value = 0;
		}
		
		values[item.name+"_"+item.type] = value;
	});

	$.ajax({
	   type: 'POST',
	   data: values,
	   url: baseUrl + '/' + url,
	})
		.fail(function(response){
			showAlert(JSON.parse(response.responseText).message);
		});
}

function submitAsyncForm(form)
{
	$.ajax({
		type: "POST",
		url: form.action,
		data: $(form).serialize(),
	})
		.done(function(response){
			showAlert(JSON.parse(response).message);
		})
		.fail(function(response){
			showAlert(JSON.parse(response.responseText).message);
		});
}
