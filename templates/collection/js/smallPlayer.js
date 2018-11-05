$(function() {
    var player = $('audio');
    var clock;
    var spectrogramWidth = 600;

    player.on('playing', function(event) {
        player.stop();
        clock = setInterval(function() {
            moveCursor(event.target);
        }, 30);
    });

    player.on('pause', function(event) {
        clearInterval(clock);
    });

    function moveCursor(element)
    {
        var time = element.currentTime / element.duration;
        $('#player-cursor-'+element.getAttribute('data-id')).css('margin-left', time * spectrogramWidth + 'px');
    }
});
