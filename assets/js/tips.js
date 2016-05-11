// document ready event
$(document).ready(function(){
	getTips($( "#sortSelector" ).val());
});

$( "#submitBtn" ).click(function() {
	sendTip();
});

function getTips(order){
	$.ajax({
		type: "GET",
		url: "tips/refresh.php?order="+order
	}).done(function( data )
	{
		var jsonData = JSON.parse(data);
		var jsonLength = jsonData.results.length;
		var html = "";
		for (var i = 0; i < jsonLength; i++) {
			var result = jsonData.results[i];
			html += '<div id="thead-'+ result.id +'" class="tipheader"><span>'+ result.title +'</span><span id="expand" style="float: right;font-size:180%;">+</span></div>'
			html += '<div id="tcon-'+ result.id +'" class="tipcontent">'
			html += '<p style="float:left; color:gray;"><small>Submitted by ' + result.author + '</small></p><p style="float:right; color:gray;"><small>' + result.submit_time + '</small>'
			html += '</p><hr style="height:1px; visibility:hidden;" /><p>'+ result.description +'</p>'
			html += '<div align="right"><button class="thanks" id="tbut-'+ result.id +'" type="button">Thanks!</button><span class="voteText">'+result.votes+'</span></div></div>'
		}
		$('#tipcontainer').html(html);
	});
}

function sendTip() {
	var form_data = new FormData();
	form_data.append('title', $("#title").val());
	form_data.append('description', $("#description").val());
	form_data.append('author', $("#author").val());
	$.ajax({
				url: 'tips/upload.php', // point to server-side PHP script 
				dataType: 'text',  // what to expect back from the PHP script, if anything
				cache: false,
				contentType: false,
				processData: false,
				data: form_data,                         
				type: 'post',
				success: function(php_script_response){
					if (php_script_response != "") {
						alert(php_script_response);
					}
				}
	 });
	// asynchronously reset inputs
	$("#title").prop("value", "");
	$("#description").prop("value", "");
	$("#author").prop("value", "");
	alert("Your tip was submitted for approval.");
}

$('.tipcontainer').on('click', '.tipheader', function (){
	var header = $(this);
    //getting the next element
    var content = header.next();
    //open up the content needed - toggle the slide- if visible, slide up, if not slidedown.
    content.slideToggle(500, function () {
        //execute this after slideToggle is done
        //change text of header based on visibility of content div
        header.find("#expand").text(function () {
            //change text based on condition
            return content.is(":visible") ? "-" : "+";
        });
    });
});

$('.tipcontainer').on('click', '.thanks', function (){
	var thanksBut = $(this);
	var form_data = new FormData();
	form_data.append('tip_id', thanksBut.attr('id').substr(5));
	$.ajax({
				url: 'tips/vote.php', // point to server-side PHP script 
				dataType: 'text',  // what to expect back from the PHP script, if anything
				cache: false,
				contentType: false,
				processData: false,
				data: form_data,                         
				type: 'post',
				success: function(newVotes){
					thanksBut.next().text(newVotes);
				}
	 });
});

$( "#sortSelector" ).change(function() {
	getTips($( "#sortSelector" ).val());
});
