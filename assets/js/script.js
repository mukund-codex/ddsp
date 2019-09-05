$(document).ready(function(){
    $('body').bind('cut copy paste', function(e){
        e.preventDefault();
        return false;
    });
});

$(document).on("contextmenu", function (e) {        
    e.preventDefault();
});


$(document).keydown(function (e) {
    if (e.keyCode == 123) { // Prevent F12
        return false;
    } else if (e.ctrlKey && e.shiftKey && e.keyCode == 73) { // Prevent Ctrl+Shift+I        
        return false;
    }
});