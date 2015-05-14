$('.rm').click(
    function(){
        var id = $(this).attr('id');
        var user = $('.user').html;
        console.log(id);
        $.ajax({
            url: "rm.php",
            type: "POST",
            data: {"uuid": id, "user": user},
            success: function(data){
                console.log(data);
            }
        });
        $(this).hide();
        $(this).parent().html("Deleted");

    });