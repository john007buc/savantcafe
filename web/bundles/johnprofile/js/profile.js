$(document).ready(function(){
    $("ul.profile_links li a").on('click',function(e){
        e.preventDefault();
        var profile_url=$(this).attr('href');
         alert(profile_url);

        $.ajax({
                url:profile_url,
                method:"POST",
                success:function(result){
                $("div.profile-content").html(result);
              }});

    });

    $(document).on('submit','form.profile_form',function(e) {

        var url = $(this).attr('action'); // the script where you handle the form input.


        var formData=new FormData(this);
        $.ajax({
            type: "POST",
            url: url,
            data:formData,
            processData:false,
            contentType:false,
             //$("form.profile_form").serialize(), // serializes the form's elements.
            success: function(data)
            {
                $("#profile-content").html(data); // show response from the php script.

            }
        });

        // return false; // avoid to execute the actual submit of the form.
        e.preventDefault();
    });




})
