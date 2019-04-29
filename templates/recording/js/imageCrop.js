$(function() {

    let enableZoom = function() {
        let filterElement = $("label[for='filter']");
        let filterCheckbox = $("input[name='filter']");

        if (!filterCheckbox.prop("checked")) {
            filterElement.trigger("click");
        }
        $('#zoom-submit').prop("disabled", false);
    }

    let calculateDecimals = function (value) {
        if (Math.floor(value) === 0) {
            return 5 - (value * 1000).toString().split('.')[0].length;
        }

        console.log(Math.floor(value) < 10);

        if (0 < Math.floor(value) && Math.floor(value) < 10) {
            return 1;
        }

        return 0;
    }

    let setSelectionData = function(coordinates) {
        let xmin = (coordinates.x / specWidth * selectionDuration + minTime);
        let xmax = (coordinates.x2 / specWidth * selectionDuration + minTime);
        let ymax = Math.round((coordinates.y / specHeight) *- (maxFrequency - minFrequency) + maxFrequency);
        let ymin = Math.round((coordinates.y2 / specHeight) *- (maxFrequency - minFrequency) + maxFrequency);

        if (xmin === xmax || ymin === ymax) {
            xmin = minTime;
            xmax = maxTime;
            ymin = minFrequency;
            ymax = maxFrequency;
        }

        let decimals = calculateDecimals(xmax - xmin);
        console.log(decimals);

        //Values for Boxes Filter
        $('#x').val(xmin.toFixed(decimals));
        $('#w').val(xmax.toFixed(decimals));
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
