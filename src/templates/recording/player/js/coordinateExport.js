document.getElementById('exportCoordinates').addEventListener('click', function() {
    let message = 'Data copied to clipboard successfully.'

    const input = document.createElement('input');
    document.body.appendChild(input);
        input.value = $('#x').val() + '\t' + $('#w').val() + '\t' + $('#y').val() + '\t' + $('#h').val() + '\t';
    input.focus();
    input.select();

    const isSuccessful = document.execCommand('copy');

    if (!isSuccessful) {
        message = 'Data copy to clipboard failed.';
    }

    input.remove();

    showAlert(message);
});
