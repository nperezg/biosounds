$(function() {
    const viewFreqRange = maxFrequency - minFrequency;
    const viewTotalTime = maxTime - minTime;

    let closeModalTagForm = false;
    let reviewForm = $('#reviewForm');
    let readyToClose = true;

    let freq_min = $('#min_freq').val();
    let freq_max = $('#max_freq').val();
    let time_min = $('#min_time').val();
    let time_max = $('#max_time').val();

    let left = ((time_min - minTime) / viewTotalTime) * specWidth;
    let top = (((viewFreqRange + minFrequency) - freq_max) / viewFreqRange) * specHeight;
    let height = ((freq_max-freq_min) / viewFreqRange) * specHeight;
    let width = ((time_max-time_min) / viewTotalTime) * specWidth;

    if (reviewForm.length) {
        $(this).find('.modal-content').addClass('modal-tag');
        $('#tag-panel')
            .removeClass('col-md-12')
            .addClass('col-md-7')
            .addClass('line-right');
        $('#review-panel').addClass('col-md-5');
    }

    $('#modal-div').on('shown.bs.modal', function() {
        $('#species_desc').focus();
    });

    $("#call_distance").prop("readonly", true);

    $('#googleImages').click(function(){
        $(this).attr('href', 'http://www.google.com/images?q=' + $('#species_desc').val() + '');
    });

    $('#xenoImages').click(function(){
        $(this).attr('href', 'http://www.xeno-canto.org/browse.php?query=' + $('#species_desc').val() + '');
    });

    $('#tagForm').submit(function(e) {

        if ($("#tagForm :input").prop('disabled') === true) {
            return;
        }

        readyToClose = true;

        if (this.checkValidity() === false) {
            e.stopPropagation();
            readyToClose = false;
        } else {
            let tagId = $("input[name='tag_id']").val();

            $.ajax({
                type: 'POST',
                url: baseUrl + '/tag/save',
                data: $(this).serialize()
            })
                .done(function(response) {
                    let jsonResponse = JSON.parse(response);

                    if (jsonResponse.tagId && jsonResponse.tagId > 1) {
                        tagId = jsonResponse.tagId;
                        createTag(tagId);
                    }
                    updateTag(tagId);
                    showAlert(jsonResponse.message);

                    if (closeModalTagForm === true) {
                        $('#modal-div').modal('hide');
                    }
                })
                .fail(function(response) {
                       showAlert(JSON.parse(response.responseText).message);
                });
        }

        this.classList.add('was-validated');
        e.preventDefault();
    });

    $('#deleteButton').click(function(){
        if (confirm('Are you sure you want to delete this tag?')) {
            let modal =  $('#modal-div');
            modal.find('button').prop('disabled', true);
            modal.modal('hide');

            let tagId = $(this).data('tag-id');

            $.ajax({
                type: "POST",
                url: baseUrl + '/tag/delete/' + tagId,
                success: function(){
                    $('#' + tagId ).remove();
                    showAlert('Tag successfully removed');
                },
                error: function(response){
                    showAlert(JSON.parse(response.responseText).message);
                }
            });
        } else {
            return false;
        }
    });

    $('#bsave').click(function() {
        closeModalTagForm = !reviewForm.length;

        $('#tagForm').submit();

        if (reviewForm.length) {
            reviewForm.submit();
        }
    });

    $('#distance_not_estimable').click( function() {
        if ($(this).is(':checked')) {
            $('#call_distance').val(null);
        }
    });

    $("#review-accept-btn").click(function(e){
        $('#reviewSpeciesName').prop('disabled', true);
        $('#reviewSpeciesId').val('');
        $('#review_status').val(1);
        $('#state').html('Accepted');
        e.preventDefault();
    });

    $("#review-correct-btn").click(function(e){
        $('#reviewSpeciesName')
            .prop('disabled', function(i, v) { return !v; })
            .prop('required', function(i, v) { return !v; });
        $('#review_status').val(2);
        $('#state').html('Corrected');
        e.preventDefault();
    });

    $("#review-delete-btn").click(function(e){
        $('#reviewSpeciesName').prop('disabled', true);
        $('#reviewSpeciesId').val('');
        $('#review_status').val(3);
        $('#state').html('Deleted');
        e.preventDefault();
    });

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
                $(this).val('');
                $('#reviewSpeciesId').val('');
            }
        },
        select: function (e, ui) {
            $(this).val(ui.item.label.split('(')[0]);
            $('#reviewSpeciesId').val(ui.item.value);
            e.preventDefault();
        }
    });

    reviewForm.submit(function(e) {
        let reviewStatus = $('#review_status');

        if (this.checkValidity() === false
            || (parseInt(reviewStatus.val()) === 2 && !$('#reviewSpeciesId').val())
        ){
            e.stopPropagation();
        } else {
            if (reviewStatus.val()) {
                $.ajax({
                    type: 'post',
                    url: baseUrl + '/tagReview/save',
                    data: $(this).serialize()
                })
                    .done(function(response) {
                        showAlert(JSON.parse(response).message);
                    })
                    .fail(function(response) {
                        showAlert(JSON.parse(response.responseText).message, true);
                    });
            }

            if (!closeModalTagForm && readyToClose) {
                $('#modal-div').modal('hide');
            }
        }

        this.classList.add('was-validated');
        e.preventDefault();
    });

    let createTag = function(tagId) {
        let speciesName = $('#reviewSpeciesName').val();

        let newTag = "<div class='tag-controls tag-dashed' id='" + tagId + "' style='z-index:800; border-color: white; left: ";
        newTag += left+"px; top: "+top+"px; height: "+height+"px; width: "+width+"px;'></div>";
        newTag += "<div class='card js-panel-tag'><div class='card-header'><small>" + speciesName +"</small></div>";
        newTag += "<div class='card-body mx-auto'><div class='btn-group' role='group'>";
        newTag += "<a href='" + baseUrl + "/tag/edit/" + tagId + "' class='btn btn-outline-primary btn-sm js-tag' title='Edit tag'>";
        newTag += "<i class='fas fa-edit' aria-hidden='true'></i></a>";
        newTag += "<a href='#' onclick='return false;' class='btn btn-outline-primary btn-sm zoom-tag' title='Zoom tag'><i class='fas fa-search' aria-hidden='true'></i></a>";
        newTag += "<a href='#' onclick='return false;' id='est_" + tagId + "' type='button' class='btn btn-outline-primary btn-sm estimate-distance' title='Estimate call distance'><i class='fas fa-bullhorn' aria-hidden='true'></i></a>";
        newTag += "</div></div></div>";

        $('#myCanvas').append(newTag);
    }

    let updateTag = function(tagId)
    {
        let tagElement = $('#' + tagId);
        console.log(tagElement);

        const callDistance = $('#call_distance').val();
        const distanceNotEstimable = $('#distance_not_estimable').is(':checked');

        tagElement.removeClass('tag-orange');
        if (!callDistance && !distanceNotEstimable) {
            tagElement.addClass('tag-orange');
        }

        //tagElement.addClass('tag-orange');
        tagElement
            .css('width', width + 'px')
            .css('height', height + 'px')
            .css('left', left + 'px')
            .css('top', top + 'px');
    }
});


