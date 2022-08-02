$(function () {

    $('.view').click(function () {
        $(this).addClass('active');
    });

    $('#clear-filter').click(function () {
        $('.filter').val('');
    });

    $('.page-selector').click(function (e) {
        var form = $('#search-form');
        form.prop('action', $(this).prop('href'));
        form.submit();
        e.preventDefault();
    });

    $("#btn_map").click(function () {
        $("#map").toggle()
        if ($("#btn_map").text() == 'Hide Map') {
            $("#btn_map").text('Show Map')
        } else {
            $("#btn_map").text('Hide Map')
        }
    });
});