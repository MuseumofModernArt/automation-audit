$('.rm').click(
    function(){
        var id = $(this).attr('id');
        $.ajax({
            url: "rm.php",
            type: "POST",
            data: {"delete": id},
            success: function(data){
            }
        });
        $(this).hide();
        $(this).parent().html("deleted on 1234 by Ben");
    });

$(document).mousemove(function(e) {
    $('.logo').offset({
        left: e.pageX,
        top: e.pageY + 20
    });
});