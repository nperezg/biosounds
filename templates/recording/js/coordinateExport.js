$(function() {
    $('#export-coordinates').click(function(){
        let minTime = $('#x').val();
        let maxTime = $('#w').val();
        let duration = (maxTime - minTime).toFixed(2);

        const input = document.createElement('input');
        document.body.appendChild(input);
        input.value = minTime + ', ' + maxTime + ', ' + $('#y').val() + ', ' + $('#h').val() + ', ' + duration;
        input.focus();
        input.select();

        const isSuccessful = document.execCommand('copy');

        if (!isSuccessful) {
            console.error('Failed to copy text.');
        }

        input.remove();
    });
});
