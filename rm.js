$('.rm').click(
    function(){
        var id = $(this).attr('id');
        var user = $('.user').text();
        $.ajax({
            url: "rm.php",
            type: "POST",
            data: {"uuid": id, "user": user},
            success: function(data){
                console.log(data);
                $(this).hide();
                $(this).parent().html(data);
            }
        });
        

    });