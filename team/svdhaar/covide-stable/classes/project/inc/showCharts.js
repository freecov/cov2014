function showCharts() {
	$("#displayChart").css("display","none");
	if ($("#charts").is(":hidden")) {
		$('#project_overflow_users').css("overflow", "hidden");
		$("#charts").slideDown("slow", function () {
			$('#project_overflow_users').css("overflow", "auto");
      });
		$("#arrowShow").attr("src", "themes/default/icons/arrowHide.png");
      } else {
		$('#project_overflow_users').css("overflow", "hidden");
        $("#charts").slideUp("slow");
		$("#arrowShow").attr("src", "themes/default/icons/arrowShow.png");
      }
}

function getChartUser(user, id, name) {
	var charts = Array();
	var showBarChart = $('#gBarChartUser').css("display");
	if (showBarChart == 'none' || showBarChart == undefined) {
		showPieChart = 'block';
		showBarChart = 'none';
	} else {
		showPieChart = 'none';
	}
	$("#chartUser").empty();
	$("#chartUser").append('<img src="themes/default/icons/loader.gif" />');
	jQuery.post("index.php?mod=project&action=showhours", {id: id, user: user, name: name, ajCall: true, showad: 1},
	   function(html) {
			$("#chartUser").empty();
			charts = html.split(' ');
			var chartsimg = "<img src='"+ charts[0] +"' id='gPieChartUser' style='display:" + showPieChart + ";'/>";
			chartsimg += "<img src='"+ charts[1] +"' id='gBarChartUser' style='display:" + showBarChart + ";'/>";
			$("#chartUser").append(chartsimg);
		}, 'html'
	)
}

function showBarChart() {
	$('#gPieChartUserinProject').css("display", "none");
	$('#gPieChartCosts').css("display", "none");
	$('#gbarChartCosts').css("display", "block");
}

function showPieAllUserCost() {
	$('#gPieChartCosts').css("display", "none");
	$('#gbarChartCosts').css("display", "none");
	$('#gPieChartUserinProject').css("display", "block");
}

function showPie() {
	$('#gPieChartUserinProject').css("display", "none");
	$('#gbarChartCosts').css("display", "none");
	$('#gPieChartCosts').css("display", "block");
}

function showPieChartUser() {
	$('#gBarChartUser').css("display", "none");
	$('#gPieChartUser').css("display", "block");
}

function showBarChartUser() {
	$('#gPieChartUser').css("display", "none");
	$('#gBarChartUser').css("display", "block");
}

