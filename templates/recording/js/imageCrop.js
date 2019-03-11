$(function() {

    let enableZoom = function() {
        let filterElement = $("label[for='filter']");
        let filterCheckbox = $("input[name='filter']");

        if (!filterCheckbox.prop("checked")) {
            filterElement.trigger("click");
        }
        $('#zoom-submit').prop("disabled", false);
    }

    let setSelectionData = function(coordinates) {
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
    }

    selectData = function(coordinates) {
        enableZoom();
        setSelectionData(coordinates);
    }

    $('#cropbox').Jcrop({
        onChange: setSelectionData,
        onSelect: selectData,
        addClass: 'custom',
        bgColor: 'black'
    });
});
