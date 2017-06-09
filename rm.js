$( document ).ready(function() {
    $('.exlposion').hide();

    $(document).mousemove(function(e) {
        $('.exlposion').offset({
            left: e.pageX -60,
            top: e.pageY - 140
        });
    });

$('.rm').click(
    function(){
        var id = $(this).attr('id');
        var user = $('.user').text();
        var database = $('#database').text();
        var button = this;
        $.ajax({
            url: "rm.php",
            type: "POST",
            data: {"uuid": id, "user": user, "database": database},
            success: function(data){
                console.log(data);
                $(button).hide();
                $(button).parent().html(data);
                $('.exlposion').show().delay(1000).fadeOut(10);
                var buttonPosition = $(button).offset();
                console.log(buttonPosition);
            }
        });

});

});
