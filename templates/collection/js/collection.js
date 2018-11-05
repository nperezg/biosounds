$(function() {
    $('.view').click(function () {
       $(this).addClass('active');
    });

    $('#species-name').autocomplete({
        source: baseUrl + '/ajaxcallmanager.php?class=species&action=getList', minLength:3,
        change: function (event, ui) {
            if (!ui.item) {
                $('#species-name').val('');
                $('#species').val('');
            }
        },
        select: function (event, ui) {
            event.preventDefault();
            var label = ui.item.label.split('(')[0];
            $('#species-name').val(label);
            $('#species').val(ui.item.value);
        }
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