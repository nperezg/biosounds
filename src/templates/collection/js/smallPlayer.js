$(function() {
    let player = $('audio');
    let clock;
    let spectrogramWidth = 600;

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
        let time = element.currentTime / element.duration;
        let cursor = document.querySelector("[data-cursor-id='" + element.getAttribute('data-id') + "']");
        cursor.style.setProperty('margin-left', time * spectrogramWidth + 'px');
    }
});
