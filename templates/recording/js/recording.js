$(function() {

    let shiftRate = 0.95;

    $("#shift-left").click(function(e) {
        let shiftLeftMin = Math.round(minTime - (selectionDuration * shiftRate));

        if (shiftLeftMin < 0) {
            shiftLeftMin = 0;
        }

        $("#x").val(shiftLeftMin);
        $("#w").val(Math.round(maxTime - (selectionDuration * shiftRate)));
        $("#recordingForm").submit();

        e.preventDefault();
    });

    $("#shift-right").click(function(e) {
        let shiftRightMax = Math.round(maxTime + (selectionDuration * shiftRate));

        if (shiftRightMax > fileDuration) {
            shiftRightMax = fileDuration;
        }

        $("#x").val(Math.round(minTime + (selectionDuration * shiftRate)));
        $("#w").val(shiftRightMax);
        $("#recordingForm").submit();

        e.preventDefault();
    });

    $(".viewport").click(function(e) {
        $("#x").val(0);
        $("#w").val(fileDuration);
        $("#y").val(1);
        $("#h").val(fileFreqMax);
        $("input[name=filter]").prop("checked", false);
        $("input[name=continuous_play]").prop("checked", false);
        $("input[name=estimateDistID]").val("");
        $("#recordingForm").submit();
        e.preventDefault();
    });

    $("#zoom-submit").click(function(e) {
        $(this).prop("disabled", true);
        $("#recordingForm").submit();
    });

    $(".readingMode").click(function(e) {
        $("#x").val(currentTime + minTime);
        $("#w").val(currentTime + minTime + 60);
        $("#y").val(1);
        $("#h").val(fileFreqMax);
        $("input[name=filter]").prop("checked", false);
        $("input[name=continuous_play]").prop("checked", true);
        $("#recordingForm").submit();
        e.preventDefault();
    });

    $(".channel-left").click(function(e){
        $("input[name=channel]").val(1);
        $("#recordingForm").submit();
        e.preventDefault();
    });

    $(".channel-right").click(function(e) {
        $("input[name=channel]").val(2);
        $("#recordingForm").submit();
        e.preventDefault();
    });

    $('.js-toggle-tags').click(function(e) {
        let show = this.dataset.show;
        this.dataset.show = show ? '' : 1;
        document.getElementsByName('showTags')[0].value = !show;

        $('.tag-controls').toggle();

        if (show) {
            $(this).find('i').removeClass('fa-eye-slash').addClass('fa-eye');
        } else {
            $(this).find('i').removeClass('fa-eye').addClass('fa-eye-slash');
        }


        // if (tagControls.is(':visible')) {
        //     $("input[name=showTags]").val(0);
        //     tagControls.hide();
        //     $('.js-panel-tag').hide();
        //     $(this).find('i').removeClass('fa-eye-slash').addClass('fas fa-eye');
        // } else {
        //     $("input[name=showTags]").val(1);
        //     tagControls.show();
        //     $(this).find('i').removeClass('fa-eye').addClass('fas fa-eye-slash');
        // }
        e.preventDefault();
    });

    $('.js-new-tag').click(function(e) {
        if ($('#zoom-submit').is(':disabled')) {
            showAlert('Please, select an area of the spectrogram.');
        } else {
            $.ajax({
                type: "POST",
                url: this.href,
                data: $('#recordingForm').serialize()
            })
                .done(function(response) {
                    $('#modalWindows').html(JSON.parse(response).data);
                    $('#modal-div').modal('show');
                })
                .fail(function(response){
                    showAlert(JSON.parse(response.responseText).message);
                });
        }
        e.preventDefault();
    });

     $('#recordingForm').on('submit', function(){
         toggleLoading();
     });
 });
