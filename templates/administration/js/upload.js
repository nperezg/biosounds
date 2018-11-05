$(function() {

    var uploadDir = Math.floor(Math.random() * (10000000 - 100000) + 100000);

    $("#file-uploader").pluploadQueue({
        runtimes : 'html5',
        url : base_url + '/scripts/uploaded.php?dir=' + uploadDir,
        max_file_size : '1000mb',
        chunk_size : '1mb',
        unique_names : false,
        filters : {
            max_file_size : '1000mb',
            mime_types: [
                {title : "Recording files", extensions : "flac,wav,ogg,mp3"}
            ]
        },
        init : {
            UploadComplete: function(up) {
                $("#save_button").toggleDisabled();
            },
            Error: function(up, args) {
                showMessage("Error uploading files.");
            },
            FilesAdded: function(up, files) {
                plupload.each(files, function(file) {
                    if (file.name.replace(/\.[^/.]+$/, "").length > 40) {
                        $(".plupload_start").addClass('plupload_disabled');
                        up.removeFile(file);
                        showMessage('File name too long. Max is 40 characters. Please rename the file '
                            + file.name + ' and upload it again.', true);
                    }
                });
            }
        }
    });

    $('#upload-form').submit(function(e){
        toggleLoading('Saving...');
        $('#save_button').toggleDisabled();
        var values = $(this).serialize();
        values["colID"] = $("#collection").val();

        $.ajax({
            type: 'POST',
            url: base_url + '/ajaxcallmanager.php?class=file&action=upload&id=' + uploadDir,
            data: values,
            success: function(data){
                showMessage(data.message, true);
                $("#hiddenForm").toggle();
                $("#upload_btn").toggle();
                toggleLoading();

            },
            error: function(response) {
                showMessage(response.responseJSON.message, true);
                toggleLoading();
            }
        });
        e.preventDefault();
    });

    $('#species-name').autocomplete({
        source:base_url + '/ajaxcallmanager.php?class=species&action=getList', minLength:3,
        change: function (event, ui) {
            if (!ui.item) {
                $('#species-name').val('');
                $('#species').val('');
            }
        },
        select: function (event, ui) {
            event.preventDefault();
            var label = ui.item.label.split('(')[0];
            $('#species-name').val(label);
            $('#species').val(ui.item.value);
        }});

    $("#reference").change(function(e) {
        var referenceFields = $('.reference-field');
        var requiredFields = $('.field-required');
        referenceFields.prop('disabled', !referenceFields.prop('disabled'));
        requiredFields.prop('required', !requiredFields.prop('required'));
    });


    $("#from-file").change(function(e) {
        var fileFields = $('.file-field');
        fileFields.prop('disabled', !fileFields.prop('disabled'));
        fileFields.prop('required', !fileFields.prop('required'));
    });
});