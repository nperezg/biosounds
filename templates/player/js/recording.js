$(function() {

    $('.estimate-distance').on( "click", function(e) {
        var leftCanvas = $("#myCanvas")[0].getBoundingClientRect().left;
        var elemID = $(this).attr("id");
        var tagID = elemID.substring(elemID.indexOf("_")+1, elemID.length);

        var tagElement = $(this).parent().parent().parent().prev();
        var left = tagElement[0].getBoundingClientRect().left - leftCanvas;
        var width = left + tagElement[0].getBoundingClientRect().width;

        var tMin = (left / specWidth * selectionDuration + minTime);
        var tMax = (width/ specWidth * selectionDuration + minTime);

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
        $("#recordingForm").submit();
        e.preventDefault();
    });

    $("#shift-left").click(function(e){
        var shiftRate = 0.95;

        var shiftLeftMin = Math.round(minTime - (selectionDuration * shiftRate));
        if(shiftLeftMin < 0)
            shiftLeftMin = 0;
        var shiftLeftMax = Math.round(maxTime - (selectionDuration * shiftRate));

        $("#x").val(shiftLeftMin);
        $("#w").val(shiftLeftMax);
        $("#recordingForm").submit();

        e.preventDefault();
    });

    $("#shift-right").click(function(e){
        var shiftRate = 0.95;

        var shiftRightMin = Math.round(minTime + (selectionDuration * shiftRate));
        var shiftRightMax = Math.round(maxTime + (selectionDuration * shiftRate));
        if(shiftRightMax > fileDuration)
            shiftRightMax = fileDuration;

        $("#x").val(shiftRightMin);
        $("#w").val(shiftRightMax);
        $("#recordingForm").submit();

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
        $("#recordingForm").submit();
        e.preventDefault();
    });

    $("#zoom_submit").click(function(e){
        $(this).prop("disabled", true);
        $("#recordingForm").submit();
    });
});