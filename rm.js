$( document ).ready(function() {
    var user = $('.user').text();
    alert(user);
});



$('.rm').click(
    function(){
        var id = $(this).attr('id');
        var user = $('.user').innerHTML;
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
        alert(user);
        $(this).parent().html("Deleted");

    });