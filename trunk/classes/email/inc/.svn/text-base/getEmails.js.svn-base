//get related emails by url
function getEmails($url, $id) {
	jQuery.post( "index.php?mod=email&action=getEmails", {url: $url, id: $id},
			   function(html){
					$("#pieChart").css("display","none");
					$("#tblEmail").remove();
					$(".div_right_chart").append(html);
				}, 'html'
	)
}

//show chart count hyperlinks(%)
function getChart() {
	//clear table content and display pieChart
	$("#tblEmail").remove();
	$("#pieChartRead").css("display","none");
	$("#pieChart").css("display","inline");
	$("#pieChartLink").css("display","inline");
}

//show chart count read en unread
function getChartRead() {
	//clear table content and display pieChart
	$("#tblEmail").remove();
	$("#pieChartLink").css("display","none");
	$("#pieChart").css("display","inline");
	$("#pieChartRead").css("display","inline");
}

function save_class() {
			//empty <span> loading and display loading...
			$("#loading").empty();
			$("#loading").append('Loading...');
			$("#loading").css("display","inline");
			//get values op post form
			var mod = 'email';
			var action = 'save_bcard';
			var bcard = $("#bcardclassification").val();
			var value = "";
			var idbox = new Array();
			var i = 0;
			//check wich checkbox are cheched and get id
			$("input:checked").each( function(i) {
				   value = this.value;
				   idbox[i] = value;
				   i++;
			});
			var dataString = 'mod=' + mod + '&action=' + action + '&classification=' + bcard + '&idbox=' + idbox;
			//do ajax call
			jQuery.ajax({
				type: "POST",
				url: "index.php",
				data: dataString,
				success: function() {
					$("#loading").empty();
					$("#loading").append('classifications is saved!');
				},
				error: function(x,e) {
					if (x.status==0) {
						alert('You are offline!!\n Please Check Your Network.');
					} else if (x.status==404) {
						alert('Requested URL not found.');
					} else if (x.status==500) {
						alert('Internel Server Error.');
					} else if (e=='timeout') {
						alert('Request Time out.');
					} else {
						alert('Unknow Error.\n'+x.responseText);
					}
				}
			});
}
