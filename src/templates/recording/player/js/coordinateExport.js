document.getElementById('exportCoordinates').addEventListener('click', function() {
    let minTime = $('#x').val();
    let maxTime = $('#w').val();
    let duration = (maxTime - minTime).toFixed(2);
    let message = 'Data copied to clipboard successfully.'

    const input = document.createElement('input');
    document.body.appendChild(input);
    input.value = minTime + ', ' + maxTime + ', ' + $('#y').val() + ', ' + $('#h').val() + ', ' + duration;
    input.focus();
    input.select();

    const isSuccessful = document.execCommand('copy');

    if (!isSuccessful) {
        message = 'Data copy to clipboard failed.';
    }

    input.remove();

    showAlert(message);
});
