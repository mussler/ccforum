$(document).ready(function(e) {
    $("#loginform").submit(function() {
    var url = "logincheck.php"; 
    $.ajax({
           type: "POST",
           url: url,
           data: $("#loginform").serialize(), // serializes the form's elements.
		   beforeSend: function() {
			   $("#loginform > input[type=submit]").attr('disabled', 'disabled');
		   },
           success: function(data)
		   {			
		   	   if(data == 'true') {
			 location.reload();
			   } else {
				   $(".red").remove();
				   $("<p class='red'>Incorrect login or password</p>").insertAfter("#loginform");
				   $("#loginform > input[type=submit]").removeAttr('disabled');
			   }
           }
         });

    return false; // avoid to execute the actual submit of the form.
});
    $("#logoutform").submit(function() {
    var url = "logout.php"; 
    $.ajax({
           type: "POST",
           url: url,
           success: function(data)
		   {
			   		  location.reload(true);
			   
           }
         });

    return false; // avoid to execute the actual submit of the form.
});
	


});


