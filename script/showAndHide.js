$(document).ready(function() {
    $(".pokazukryj").click(function() {
        $(".pokazukryj").fadeOut(function() {
            $(".pokazukryj").text(($(".pokazukryj").text() == 'Ukryj') ? 'Pokaż' : 'Ukryj').fadeIn();
        })
    })
});