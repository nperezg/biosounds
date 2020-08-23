$(function() {

    $('.view').click(function () {
       $(this).addClass('active');
    });

    $('#clear-filter').click(function(){
        $('.filter').val('');
    });

    $('.page-selector').click(function(e){
        var form = $('#search-form');
        form.prop('action', $(this).prop('href'));
        form.submit();
        e.preventDefault();
    });
});