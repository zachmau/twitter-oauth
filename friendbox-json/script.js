/* Execute upon DOM LOAD */
$(document).ready(function(){
	/* LOAD widget data */
	$.getJSON("load.php",function(data){
		if(data.error)
		{
			/* If there is an error, output and exit */
			$(".content").html(data.error);
			return false;
		}
		$(".content .fans").html('');
		/* Remove the rotating GIF */
		$.each(data.members,function(i,val){
			/* Loop through all the shown members and add them to the .content DIV */
			$(".content .fans").append('<a href="http://twitter.com/'+i+'" target="_blank"><img src="'+val+'" width="48" height="48" title="'+i+'" alt="'+i+'" /></a>');

		});
		$('#counter').html(data.membersCount);
		/* Set the member counter */
		$('.fanPageLink').attr('href',data.fanPage+'/members').attr('target','_blank');
		/* Set the .fanPageLink-s to point to the profile page */
	});

	$('.joinFP').click(function(e){
		
		/* IF the green button has been clicked.. */
		
		if($('.content').html().indexOf('id="mask"')!=-1)
		{
			/* ..and the form is already shown exit */
			e.preventDefault();
			return false;
		}

		/* ..in the other case, start a fade out effect */
		$(".content .fans").fadeOut("slow",function(){
			$('.content').append('<div id="mask">\
			To join our fan page, you just have to fill in your name\
			<label>Twitter username:</label>\
			<input id="twitterName" name="twitter" type="text" size="20" />\
			<a href="" class="greyButton" onclick="sendData();return false;">Join!</a> or <a href="#" onclick="cancel();return false;">cancel</a>\
			<div id="response"></div>\
			</div>');
			
		});
		
		/* Prevent the link from redirecting the page */
		e.preventDefault();
	});
});
function sendData()
{
	/* This function sends the form via AJAX */
	$('#response').html('<img src="img/loader.gif" />');
	var twitter = $('#twitterName').val();
	if(!twitter.length)
	{
		$('#response').html('<span style="color:red">Please fill in your twitter username.</span>');
		return false;
	}
	$.ajax({
		type: "POST",
		url: "add.php",
		data: "twitter="+encodeURIComponent(twitter),
		/* Sending the filled in twitter name */
		success: function(msg){
			/* PHP returns 1 on success, and 0 on error */
			var status = parseInt(msg);
			if(status)
			{
				$('#response').html('Thank you for being a fan! You will be added in a few minutes. <a href="#" onclick="cancel();return false">Hide this form</a>.');
				$('#twitterName').val('');
			}
			else
			$('#response').html('<span style="color:red">There is no such twitter user.</span>');
			
		}
	});
}
function cancel()
{
	/* Hides the "Join" form */
	$('#mask').remove();
	$('.content .fans').fadeIn('slow');
}