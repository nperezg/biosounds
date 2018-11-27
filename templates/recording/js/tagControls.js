$(function() {

    $('.canvas')
        .on('mouseenter', '.tag-controls', function(e) {
            $(this).css("background-color","rgba(255,255,255, 0.15)");

            let controls = $(this).next();
            $(this).next()
                .css("top", e.pageY - $(this).parent().offset().top)
                .css("left", e.pageX - $(this).parent().offset().left);

            if (!$(this).next().is(':visible')) {
                $(this).next().fadeIn(400);
            }
        })
        .on('mouseleave', '.tag-controls', function() {
            $(".panel-tag").hide();
            $(this).css("background-color", '');
        })
        .on('mouseenter', '.panel-tag', function() {
            $(this).prev().css("background-color","rgba(255,255,255, 0.15)");

            $(this)
                .show()
                .not(this).hide();
        })
        .on('mouseleave', '.panel-tag', function() {
            $(this).prev().css("background-color", '');
            $(this).fadeOut("fast");
        })
        .on('click', '.zoom-tag', function(e) {
            let canvasPosition = $('#myCanvas')[0].getBoundingClientRect();
            let tagElement = $(this).parent().parent().parent().prev()[0].getBoundingClientRect();
            let left = tagElement.left - canvasPosition.left;
            let top = tagElement.top - canvasPosition.top;

            setSelectionData({
                x: left,
                x2: left + tagElement.width,
                y: top,
                y2: top + tagElement.height
            });

            $('#recordingForm').submit();
            e.preventDefault();
        })
        .on('click', '.tag', function(e) {
            $('.panel-tag').hide();

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
            e.preventDefault();
        })
        .on( 'click', '.estimate-distance', function(e) {
            let tagElement = $(this).parent().parent().parent().prev()[0].getBoundingClientRect();
            let left = tagElement.left - $('#myCanvas')[0].getBoundingClientRect().left;
            let width = left + tagElement.width;

            let startTime = (left / specWidth * selectionDuration + minTime);
            let endTime = (width/ specWidth * selectionDuration + startTime);
            endTime = (endTime - startTime) > 30 ? startTime + 30 : endTime;

            $('#x').val(startTime);
            $('#w').val(endTime);
            $('#y').val(1);
            $('#h').val(fileFreqMax);

            $("input[name=filter]").prop('checked', false);
            $("input[name=continuous_play]").prop('checked', false);
            $("input[name=estimateDistID]").val(this.id.substring(this.id.indexOf('_') + 1, this.id.length)); //Set Tag ID
            toggleLoading();
            $("#recordingForm").submit();
            e.preventDefault();
        });
});
