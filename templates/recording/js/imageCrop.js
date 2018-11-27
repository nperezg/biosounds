$(function() {

    let enableZoom = function() {
        let filterElement = $("input[name=filter]");
        $('#zoom-submit').prop("disabled", false);
        filterElement.prop("checked", true);
        filterElement.prop("disabled", false);
    }

    setSelectionData = function(coordinates) {
        let xmin = (coordinates.x / specWidth * selectionDuration + minTime).toFixed(1);
        let xmax = (coordinates.x2 / specWidth * selectionDuration + minTime).toFixed(1);
        let ymax = Math.round((coordinates.y / specHeight) *- (maxFrequency - minFrequency) + maxFrequency);
        let ymin = Math.round((coordinates.y2 / specHeight) *- (maxFrequency - minFrequency) + maxFrequency);

        if (xmin === xmax || ymin === ymax) {
            xmin = minTime;
            xmax = maxTime;
            ymin = minFrequency;
            ymax = maxFrequency;
        }
        //Values for Boxes Filter
        $('#x').val(xmin);
        $('#w').val(xmax);
        $('#y').val(ymin);
        $('#h').val(ymax);

        enableZoom();
    }

    $('#cropbox').Jcrop({
        onChange: setSelectionData,
        onSelect: setSelectionData,
        addClass: 'custom',
        bgColor: 'black'
    });
});
