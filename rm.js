$('.rm').click(
    function(){
        var id = $(this).attr('id');
        console.log(id);
        $.ajax({
            url: "rm.php",
            type: "POST",
            data: {"uuid": id},
            success: function(data){
                console.log(data);
            }
        });
        $(this).hide();
        $(this).parent().html("Deleted");

    });