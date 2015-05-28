$( document ).ready(function() {


$('.rm').click(
    function(){
        var id = $(this).attr('id');
        var user = $('.user').text();
        var button = this;
        $.ajax({
            url: "rm.php",
            type: "POST",
            data: {"uuid": id, "user": user},
            success: function(data){
                console.log(data);
                $(button).hide();
                $(button).parent().html('<img class="exlposion" src="explosion-1.gif">').delay(1000).html(data);
            }
        });

});

});

