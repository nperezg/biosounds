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

    $(".toggleTag").click(function(e) {
        let tagControls = $('.tag-controls');

        if (tagControls.is(':visible')) {
            $("input[name=showTags]").val(0);
            tagControls.hide();
            $('.panel-tag').hide();
            $(this).html("<span title='Show tags' class='glyphicon glyphicon-eye-open'></span>");
        } else {
            $("input[name=showTags]").val(1);
            tagControls.show();
            $(this).html("<span title='Hide tags' class='glyphicon glyphicon-eye-close'></span>");
        }
        e.preventDefault();
    });

    $('.new-tag').click(function(e) {
        if ($('#zoom-submit').is(':disabled')) {
            alert("Please, select an area of the spectrogram.");
        } else {
            $.ajax({
                type: "POST",
                url: this.href,
                data: $('#recordingForm').serialize(),
                success: function(data)
                {
                    $('#modalWindows').html(data);
                    $('#modal-div').modal('show');
                },
                error: function(response){
                    showMessage(response.responseJSON.message, true);
                }
            });
        }
        e.preventDefault();
    });
});
