// document ready event
$(document).ready(function(){
	getTips($( "#sortSelector" ).val());
});

// submit the tip
$( "#submitBtn" ).click(function() {
	sendTip();
});

// get tips from the database and populate the container
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
			html += '<div id="thead-'+ result.id +'" class="tipheader"><span>'+ result.title +'</span><span id="expand" style="float: right;">+</span></div>'
			html += '<div id="tcon-'+ result.id +'" class="tipcontent">'
			html += '<span style="float:left; color:gray;"><small>Submitted by ' + result.author + '</small></span><span style="float:right; color:gray;"><small>' + result.submit_time + '</small></span>'
			html += '<hr style="height:1px; visibility:hidden;" /><p>'+ result.description +'</p>'
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
			url: 'tips/upload.php',
			dataType: 'text',
			cache: false,
			contentType: false,
			processData: false,
			data: form_data,
			type: 'post',
			success: function(response){
				if (response != "") {
					alert(response);
				}
			}
	 });
	// asynchronously reset inputs
	$("#title").prop("value", "");
	$("#description").prop("value", "");
	$("#author").prop("value", "");
	alert("Your tip was submitted for approval.");
}

// expand the tip on click
$('.tipcontainer').on('click', '.tipheader', function (){
	// get the header that was clicked
	var header = $(this);
    // get the element following the header (the actual tip info)
    var content = header.next();
    // toggle slide animation
    content.slideToggle(500, function () {
        header.find("#expand").text(function () {
            // change the +/- if the tip is open or not
            return content.is(":visible") ? "-" : "+";
        });
    });
});

// submit the user's vote
$('.tipcontainer').on('click', '.thanks', function (){
	var thanksBut = $(this);
	var form_data = new FormData();
	form_data.append('tip_id', thanksBut.attr('id').substr(5));
	$.ajax({
				url: 'tips/vote.php',
				dataType: 'text',
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
	// Get new tips on sort change
	getTips($( "#sortSelector" ).val());
});
