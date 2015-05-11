$('.rm').click(
    function(){
        var id = $(this).attr('id');
        console.log(id);
        $.ajax({
            url: "rm.php",
            type: "POST",
            data: {"uuid": id},
            success: function(data){
                alert(data);
            }
        });
        $(this).hide();
        $(this).parent().html("deleted on 1234 by Ben");
    });