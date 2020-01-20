<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (isset($_POST["Street"]) && isset($_POST["City"]) && isset($_POST["State"])){
		$street = $_POST["Street"];
		$city = $_POST["City"];
		$state = $_POST["State"];
		$address = urlencode($street.",".$city.",".$state);
		$geoURL = "https://maps.googleapis.com/maps/api/geocode/xml?address=$address&key=AIzaSyCc3pxReBk_P7LvpjI3wtQnS1I0Fdrj5Lc";
		$xml = file_get_contents($geoURL);
		$xmlObj = simplexml_load_string($xml);
		$lat = $xmlObj->result->geometry->location->lat;
		$lng = $xmlObj->result->geometry->location->lng;
		$forecastURL = "https://api.forecast.io/forecast/9f02d80eaa0a03ce18a8c57d6d4ee687/$lat,$lng?exclude=minutely,hourly,alerts,flags";
		$json = file_get_contents($forecastURL);
		exit($json);
	}
	if (isset($_POST["Lat"]) && isset($_POST["Lon"])){
		$lat = $_POST["Lat"];
		$lng = $_POST["Lon"];
		$forecastURL = "https://api.forecast.io/forecast/9f02d80eaa0a03ce18a8c57d6d4ee687/$lat,$lng?exclude=minutely,hourly,alerts,flags";
		$json = file_get_contents($forecastURL);
		exit($json);
	}

	if(isset($_POST["Time"]) && isset($_POST["LatGlob"]) && isset($_POST["LonGlob"])){
		$latGlobal = $_POST["LatGlob"];
		$lonGlobal = $_POST["LonGlob"];
		$time = $_POST["Time"];
		$dailyURL = "https://api.darksky.net/forecast/9f02d80eaa0a03ce18a8c57d6d4ee687/$latGlobal,$lonGlobal,$time?exclude=minutely";
		$json = file_get_contents($dailyURL);
		exit($json);
	}
}		
?>

<!DOCTYPE html>
<html>
<head>
	<!-- forecast: 9f02d80eaa0a03ce18a8c57d6d4ee687  -->
	<!--  key=API_KEY  AIzaSyCc3pxReBk_P7LvpjI3wtQnS1I0Fdrj5Lc -->
	<meta charset="utf-8">
	<title>Weather Search</title>
	<style type="text/css">
		body{
			font-family:'Times New Roman',sans-serif;
		}
		body a{
			outline: none;
		}

		#div1{
			color: white;
			width: 800px;
			height: 250px;
			margin: 0 auto;
			position: relative;
			top: 30px;
			background-color: #32ab39;
			border-radius: 14px;
			text-align: center;
		}
		#div1 h{
			font-size: 46px;
			font-style: italic;
			position: relative;
			top: 6px;
		}
		#div1 b{
			font-size: 21px;
		}
		.div1_left{
			position: relative;
			top: 10px;
			left: -250px;
		}
		.div1_left input{
			width: 120px;
		}
		#div1_select{
			position: relative;
			top: 12px;
			left: -207px;
		}
		#div1_select select{
			border: none;
			width: 218px;
			height: 16px;
			border-radius: 3px;
		}
		#div1_bottom{
			position: relative;
			top: 76px;
			left: -44px;
		}
		#div1_bottom input{
			background-color: white;
			height: 20px;
			border: 0;
			border-radius: 5px;
		}
		#div1_right{
			width: 300px;
			position: relative;
			top: -89px;
			left: 502px;
		}
		#verticalLine{
			background-color: white;
			width: 5px;
			height: 128px;
			position: relative;
			top: -114px;
			left: 442px;
			border-radius: 10px;
		}		
	</style>
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<script type="text/javascript">
		var latGlobal;
		var lonGlobal;
		/*Disable input text when checking Current Location.*/
		function checkboxOnClick(checkbox){
			if (checkbox.checked == true){
				document.getElementById("street").value = "";
				document.getElementById("city").value = "";
				document.getElementById("state").value = "State";
				document.getElementById("street").disabled = true;
				document.getElementById("city").disabled = true;
				document.getElementById("state").disabled = true;
			}
			else{
				document.getElementById("street").disabled = false;
				document.getElementById("city").disabled = false;
				document.getElementById("state").disabled = false;
			}
		}
		/*Clear the input and enable them.*/
		function clearForm(){
			document.getElementById("searchForm").reset();
			document.getElementById("street").disabled = false;
			document.getElementById("city").disabled = false;
			document.getElementById("state").disabled = false;
			document.getElementById("div2").innerHTML = "";
		}
		/*If input is invalid, return a error div.*/	
		function validate(){
			error_text = '<div id="errorDiv">';
			error_text += '<p>Please check the input address.</p>';
			error_text += '</div>';
		}
		function submitForm(){
			if (document.getElementById("currentLocation").checked == true){
				currentLocation();
			}
			else{
				inputAddress();
			}
		}
		/*If choose Current Location.*/
		var currLocCity;
		function currentLocation(){
			var url = "http://ip-api.com/json";
			var xhr = new XMLHttpRequest();			
			xhr.open("POST",url,false);
			xhr.send();
			if(xhr.readyState == 4 && xhr.status == 200) {
    			var jsonObj = JSON.parse(xhr.responseText);
  			}
  			else{
				alert("Error: No response.");
				return;
			}
		  	if (jsonObj == null){
	  			alert("Error: Json File is Null.");
	  			return;
	  		}
	  		var lat = jsonObj.lat;
	  		var lon = jsonObj.lon;
	  		currLocCity = jsonObj.city;

	  		var url = document.getElementById("searchForm").action;
			var location = "Lat="+lat+"&Lon="+lon;
	  		function loadJSON(url,location){		
				xhr.open("POST",url,false);
				xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded;charset=utf-8");
				xhr.send(location);
				if(xhr.readyState == 4 && xhr.status == 200) {
	    			jsonObj = JSON.parse(xhr.responseText);
	    			return jsonObj;
	  			}
	  			else{
					alert("Error: No response.");
					return;
				}
	  		}
	  		jsonObj = loadJSON(url,location);

	  		if (jsonObj == null){
	  			alert("Error: Json File is Null.");
	  			return;
	  		}
			jsonObj.onload = generateHTML(jsonObj);
			document.getElementById("div2").innerHTML = html_text;
			latGlobal = jsonObj.latitude;
			lonGlobal = jsonObj.longitude;
		}
		/*
			If not choose Current Location. 
			Check if input text is missing and send address information to php.
		*/
		function inputAddress(){	
			var Street = document.getElementById("street").value;
			var City = document.getElementById("city").value;
			var State = document.getElementById("state").value;

			if (Street=="" || City=="" || State=="State"){
				validate();
				document.getElementById("div2").innerHTML = error_text;
			}
			else{
				var url = document.getElementById("searchForm").action;
				var address = "Street="+Street+"&City="+City+"&State="+State;
				function loadJSON(url,address){
					var xhr = new XMLHttpRequest();			
					xhr.open("POST",url,false);
					xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded;charset=utf-8");
					xhr.send(address);
					if(xhr.readyState == 4 && xhr.status == 200) {
		    			var jsonObj = JSON.parse(xhr.responseText);
		    			return jsonObj;
		  			}
		  			else{
						alert("Error: No response.");
						return;
					}
		  		}
		  		try{
		  			jsonObj = loadJSON(url,address);
		  		}
		  		catch(err){
		  			validate();
		  			document.getElementById("div2").innerHTML = error_text;
		  			return;
		  		}
		  		if (jsonObj == null){
		  			validate();
		  			document.getElementById("div2").innerHTML = error_text;
		  			return;
		  		}

				jsonObj.onload = generateHTML(jsonObj);
				document.getElementById("div2").innerHTML = html_text;
				latGlobal = jsonObj.latitude;
				lonGlobal = jsonObj.longitude;
			}
		}
		function dailyDetail(id){
			var url = document.getElementById("searchForm").action;
			var locAndTime = "LatGlob="+latGlobal+"&LonGlob="+lonGlobal+"&Time="+id;
			function loadJSON(url,locAndTime){
				var xhr = new XMLHttpRequest();	
				xhr.open("POST",url,false);
				xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded;charset=utf-8");
				xhr.send(locAndTime);
				if(xhr.readyState == 4 && xhr.status == 200) {
	    			jsonObj = JSON.parse(xhr.responseText);
	    			return jsonObj;
	  			}
	  			else{
					alert("Error: No response.");
					return;
				}
	  		}
	  		jsonObj = loadJSON(url,locAndTime);

	  		if (jsonObj == null){
	  			alert("Error: Json File is Null.");
	  			return;
	  		}
	  		jsonObj.onload = generateDaily(jsonObj);
	  		document.getElementById("div2").innerHTML = daily_text;
		}
		function generateHTML(jsonObj){
			//generate daily temp.
			html_text = '<div id="current">';
			var cityName;
			if (document.getElementById("city").value != ""){
				cityName = document.getElementById("city").value;
			}
			else{
				cityName = currLocCity;
			}
			html_text += '<p id="cityFromInput" class="fitContent">'+cityName+'</p>';
			html_text += '<p id="timezone" class="fitContent">'+jsonObj.timezone+'</p>';

			html_text += '<div id="temperature">';
			html_text += '<p id="temp" class="fitContent">'+jsonObj.currently.temperature+'</p>';
			html_text += '<img src = "https://cdn3.iconfinder.com/data/icons/virtual-notebook/16/button_shape_oval-512.png" id="degree" class="fitContent">';
			html_text += '<p id="F" class="fitContent">F</p>';
			html_text += '</div>';

			html_text += '<p id="summary" class="fitContent">'+jsonObj.currently.summary+'</p>';

			html_text += '<div id="table">';	
			html_text += '<div class="cell">';
			html_text += '<img src = "https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-16-512.png" title="Humidity" id="humid_img" class="img1">';
			html_text += '<p id="humidity" class="cellText">'+jsonObj.currently.humidity+'</p>';
			html_text += '</div>';

			html_text += '<div class="cell">';
			html_text += '<img src = "https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-25-512.png" title="Pressure" id="press_img" class="img1">';
			html_text += '<p id="pressure" class="cellText">'+jsonObj.currently.pressure+'</p>';
			html_text += '</div>';

			html_text += '<div class="cell">';
			html_text += '<img src = "https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-27-512.png" title="WindSpeed" id="wind_img" class="img1">';
			html_text += '<p id="wind" class="cellText">'+jsonObj.currently.windSpeed+'</p>';
			html_text += '</div>';

			html_text += '<div class="cell">';
			html_text += '<img src = "https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-30-512.png" title="Visibility" id="visi_img" class="img1">';
			html_text += '<p id="visibility" class="cellText">'+jsonObj.currently.visibility+'</p>';
			html_text += '</div>';

			html_text += '<div class="cell">';
			html_text += '<img src = "https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-28-512.png" title="CloudCover" id="cloud_img" class="img1">';
			html_text += '<p id="cloud" class="cellText">'+jsonObj.currently.cloudCover+'</p>';
			html_text += '</div>';

			html_text += '<div class="cell">';
			html_text += '<img src = "https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-24-512.png" title="Ozone" id="ozone_img" class="img1">';
			html_text += '<p id="ozone" class="cellText">'+jsonObj.currently.ozone+'</p>';
			html_text += '</div>';
			html_text += '</div>';
			html_text += '</div>';

			//generate weekly temp.
			html_text += '<table id="weekTemp">';
			html_text += '<tr>';
			html_text += '<th><b>Date</b></th>';
			html_text += '<th><b>Status</b></th>';
			html_text += '<th><b>Summary</b></th>';
			html_text += '<th><b>&nbsp;TemperatureHigh&nbsp;</b></th>';
			html_text += '<th><b>&nbsp;TemperatureLow&nbsp;</b></th>';
			html_text += '<th><b>&nbsp;Wind Speed&nbsp;</b></th>';
			html_text += '</tr>';
			var weekData = jsonObj.daily.data;
			for (i=0; i<weekData.length; i++){
				html_text += '<tr>';
				var weekDate = new Date(weekData[i].time*1000);
				var year = weekDate.getFullYear();
				var month = (weekDate.getMonth()+1 < 10) ?('0'+(weekDate.getMonth()+1)):weekDate.getMonth()+1;
				var day = (weekDate.getDate()< 10) ?('0'+(weekDate.getDate())):weekDate.getDate();
				html_text += '<td><b>'+year+'-'+month+'-'+day+'</b></td>';
				html_text += '<td>'
				var icon = weekData[i].icon;
				switch (icon){
			  		case "clear-day":
			  		iconURL="https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-12-512.png";
			    	break;
			    	case "clear-night":
			  		iconURL="https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-12-512.png";
			    	break;
			 		case "rain":
			  		iconURL="https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-04-512.png";
			    	break;
			    	case "snow":
			  		iconURL="https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-19-512.png";
			    	break;
			    	case "sleet":
			  		iconURL="https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-07-512.png";
			    	break;
			    	case "wind":
			  		iconURL="https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-27-512.png";
			    	break;
			    	case "fog":
			  		iconURL="https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-28-512.png";
			    	break;
			    	case "cloudy":
			  		iconURL="https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-01-512.png";
			    	break;
			    	case "partly-cloudy-day":
			  		iconURL="https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-02-512.png";
			    	break;
			    	case "partly-cloudy-night":
			  		iconURL="https://cdn2.iconfinder.com/data/icons/weather-74/24/weather-02-512.png";
			    	break;
			    	default:
			    	iconURL="";
			 	}
			 	html_text += '<img src = "'+iconURL+'" id="iconImg">';
				html_text += '</td>';
				html_text += "<td><a href='#' class='detailAnchor' id='"+weekData[i].time+"' onclick='dailyDetail(this.id)'>"+"&nbsp;&nbsp;&nbsp;&nbsp;"+weekData[i].summary+"&nbsp;&nbsp;&nbsp;&nbsp;"+"</a></td>";
				html_text += '<td><b>'+weekData[i].temperatureHigh+'</b></td>';
				html_text += '<td><b>'+weekData[i].temperatureLow+'</b></td>';
				html_text += '<td><b>'+weekData[i].windSpeed+'</b></td>';
				html_text += '</tr>';
			}

			html_text += '</table>';
			html_text += '<div id="blankDiv">';
			html_text += '</div>';
		}
		function generateDaily(jsonObj){
			daily_text = '<h id="detailHeader">Daily Weather Detail</h>'
			daily_text += '<div id="daily">';

			daily_text += '<div id="dailySum">';
			daily_text += '<p>'+jsonObj.currently.summary+'</p>';
			daily_text += '</div>';

			daily_text += '<div id="dailyDiv1">';
			daily_text += '<p id="temp" class="fitContent">'+Math.round(jsonObj.currently.temperature)+'</p>';
			daily_text += '<img src = "https://cdn3.iconfinder.com/data/icons/virtual-notebook/16/button_shape_oval-512.png" id="degree" class="fitContent">';
			daily_text += '<p id="F" class="fitContent">F</p>';
			daily_text += '</div>';

			var icon = jsonObj.currently.icon;
			switch (icon){
		  		case "clear-day":
		  		iconURL="https://cdn3.iconfinder.com/data/icons/weather-344/142/sun-512.png";
		    	break;
		    	case "clear-night":
		  		iconURL="https://cdn3.iconfinder.com/data/icons/weather-344/142/sun-512.png";
		    	break;
		 		case "rain":
		  		iconURL="https://cdn3.iconfinder.com/data/icons/weather-344/142/rain-512.png";
		    	break;
		    	case "snow":
		  		iconURL="https://cdn3.iconfinder.com/data/icons/weather-344/142/snow-512.png";
		    	break;
		    	case "sleet":
		  		iconURL="https://cdn3.iconfinder.com/data/icons/weather-344/142/lightning-512.png";
		    	break;
		    	case "wind":
		  		iconURL="https://cdn4.iconfinder.com/data/icons/the-weather-is-nice-today/64/weather_10-512.png";
		    	break;
		    	case "fog":
		  		iconURL="https://cdn3.iconfinder.com/data/icons/weather-344/142/cloudy-512.png";
		    	break;
		    	case "cloudy":
		  		iconURL="https://cdn3.iconfinder.com/data/icons/weather-344/142/cloud-512.png";
		    	break;
		    	case "partly-cloudy-day":
		  		iconURL="https://cdn3.iconfinder.com/data/icons/weather-344/142/sunny-512.png";
		    	break;
		    	case "partly-cloudy-night":
		  		iconURL="https://cdn3.iconfinder.com/data/icons/weather-344/142/sunny-512.png";
		    	break;
		    	default:
			    iconURL="";
		 	}
			daily_text += '<img src = "'+iconURL+'" id="dailyIcon">';

			var precipIntensity = jsonObj.currently.precipIntensity;
			var display = "";
			if (precipIntensity <= 0.001){
				display = "None";
			}
			else if (precipIntensity <= 0.015){
				display = "Very Light";
			}
			else if (precipIntensity <= 0.05){
				display = "Light";
			}
			else if (precipIntensity <= 0.015){
				display = "Very Light";
			}
			else if (precipIntensity <= 0.1){
				display = "Moderate";
			}
			else if (precipIntensity > 0.1){
				display = "heavy";
			}

			daily_text += '<div id="dailyDiv2">';
			daily_text += '<p>Precipitation:</p>';
			daily_text += '<p>Chance of Rain:</p>';
			daily_text += '<p>Wind Speed:</p>';
			daily_text += '<p>Humidity:</p>';
			daily_text += '<p>Visibility:</p>';
			daily_text += '<p>Sunrise / Sunset:</p>';
			daily_text += '</div>';

			daily_text += '<div id="dailyDiv3">';
			daily_text += '<p>'+display+'</p>';
			daily_text += '<p>'+Math.round(jsonObj.currently.precipProbability*100)+'<span> %</span></p>';
			daily_text += '<p>'+jsonObj.currently.windSpeed+'<span>mph</span></p>';
			daily_text += '<p>'+Math.round(jsonObj.currently.humidity*100)+'<span> %</span></p>';
			daily_text += '<p>'+jsonObj.currently.visibility+'<span>mi</span></p>';

			var offset = jsonObj.offset;
			var sunRise = new Date(jsonObj.daily.data[0].sunriseTime*1000);
			var sunriseTime = sunRise.getUTCHours()+offset;
			var sunSet = new Date(jsonObj.daily.data[0].sunsetTime*1000);
			var sunsetTime_ = sunSet.getUTCHours()+offset;
			var sunsetTime = (sunsetTime_ > 0) ? sunsetTime_-12 : sunsetTime_+12;
			daily_text += '<p>'+sunriseTime+'<span> AM/ </span>'+sunsetTime+'<span> PM</span></p>';
			daily_text += '</div>';
			daily_text += '</div>'; //daily div end.

			daily_text += "<h id='detailHeader2'>Day's Hourly Weather</h>";
			/*Day's Hourly Div*/
			daily_text += '<div id="hourlyWeather">';			
			daily_text +="<a href='#' onclick='loadHourlyWeather(jsonObj)'><img src='https://cdn4.iconfinder.com/data/icons/geosm-e-commerce/18/point-down-512.png' id='arrows'></a>";

			daily_text += '<div id="chart" style="display: none;">';
			daily_text += '</div>';

			daily_text += '</div>';
		}
		google.charts.load('current', {packages: ['corechart', 'line']});
      	// google.charts.setOnLoadCallback(drawChart);
		function loadHourlyWeather(jsonObj){
			
			var chartDiv = document.getElementById("chart");
			if (chartDiv.style.display == "none"){
				chart.style.display = "block";
				document.getElementById("arrows").src = 'https://cdn0.iconfinder.com/data/icons/navigation-set-arrows-part-one/32/ExpandLess-512.png';
				drawChart(jsonObj);
			}else{
				chart.style.display = "none";
				document.getElementById("arrows").src = 'https://cdn4.iconfinder.com/data/icons/geosm-e-commerce/18/point-down-512.png';
			}
		}
		function drawChart(jsonObj){
			var data = new google.visualization.DataTable();
     	 	data.addColumn('number', 'Time');
      		data.addColumn('number', 'T');
      		for (i = 0; i < 24; i++){
      			var temp = jsonObj.hourly.data[i].temperature;
      			data.addRow([i,temp]);
      		}
      		var options = {
      			width: 900,
      			height: 215,
      			legend:{
      				textStyle: {
			        	fontName: 'Times New Roman'
			    	}
			    },
			    tooltip:{
      				textStyle: {
			        	fontName: 'Times New Roman'
			    	}
			    },
      			hAxis: {
      				textStyle: {
      					fontName: 'Times New Roman',
      				},
      				title: 'Time',
      				titleTextStyle: {
			        	fontName: 'Times New Roman',
			        	italic: true
			    	}
      			},
      			vAxis: {
      				textPosition: 'none',
      				title: 'Temperature',
      				titleTextStyle: {
			       	 	fontName: 'Times New Roman',
			        	italic: true
		    		}
      			},
      			series:{
      				0: {
      					color: "#a7d0d9"
      				}
      			},


      		};
		    var chart = new google.visualization.LineChart(document.getElementById('chart'));

		    chart.draw(data, options);
		}
	
	</script>

	<style>
		#chart{
			width: 900px;
			margin: 0 auto;
			position: relative;
			top: 115px;
		}
		#hourlyWeather{
			margin: 0 auto;
		}
		#hourlyWeather a{
			position: relative;
			top: 135px;
		}
		#arrows{
			width: 64px;
			height: auto;
			margin: 0;
		}
		#detailHeader{
			font-size: 42px;
			font-weight: bold;
			position: relative;
			top: 62px;
		}
		#detailHeader2{
			font-size: 42px;
			font-weight: bold;
			position: relative;
			top: 122px;
		}
		#daily{
			color: white;
			width: 640px;
			height: 535px;
			margin: 0 auto;
			position: relative;
			top: 84px;
			background-color: #a7d0d9;
			border-radius: 13px;
		}
		#dailySum{
			width: 300px;
			height: 96px;
			margin: 0;
			text-align: left;
			position: relative;
			left: 26px;
			top: 53px;
			display: table;
		}
		#dailySum p{
			max-width: 300px;
			font-size: 41px;
			font-weight: bold;
			display: table-cell;
			vertical-align: middle;
		}
		#dailyDiv1 #temp{
			font-size: 138px;
			font-weight: bold;
			position: relative;
		}
		#dailyDiv1 #degree{
			width: 17px;
			height: 17px;
			position: relative;
			top: 21px;
		}
		#dailyDiv1 #F{
			font-size: 109px;
			font-weight: bold;
			position: relative;
			top: 25px;
		}
		#dailyDiv1{
			display: flex;
    		justify-content: flex-start;
    		position: relative;
    		top: 47px;
    		left: 26px;
		}
		#dailyIcon{
			margin: 0;
			width: 300px;
			height: auto;
			position: relative;
			top: -270px;
			left: 141px;
		}
		#dailyDiv2{
			text-align: right;
			margin-top: -264px;
			position: relative;
    		left: -240px;
		}
		#dailyDiv2 p{
			font-weight: bold;
			font-size: 24px;
			margin: auto 0;
			padding: 4px 0;
		}
		#dailyDiv3{
			text-align: left;
			margin-top: -219px;
    		position: relative;
    		left: 404px;
		}		
		#dailyDiv3 p{
			font-weight: bold;
			font-size: 38px;
			margin-top: -8px;
			margin-bottom: 0;
		}
		#dailyDiv3 span{
			font-size: 21px;
		}

	</style>

	<style>
		#div2{
			text-align: center;
		}
		#blankDiv{
			height: 130px;
		}
		#errorDiv{
			color: black;
			width: 424px;
			height: 27px;
			margin: 0 auto;
			position: relative;
			top: 60px;
			background-color: #f0f0f0;
			border-style: solid;
			border-color: #aaaaaa;
		}
		#errorDiv p{
			font-size: 21px;
			margin: 0 auto;
		}
		#current{
			color: white;
			width: 532px;
			height: 352px;
			margin: 0 auto;
			position: relative;
			top: 68px;
			background-color: #5cc3f3;
			border-radius: 13px;
		}
		.fitContent{
			margin: 0;
			width: fit-content;
			width: -webkit-fit-content;
			width: -moz-fit-content;
		}
		#cityFromInput{
			font-size: 38px;
			font-weight: bold;
			position: relative;
			top: 20px;
			left: 22px;
		}
		#timezone{
			font-size: 17px;
			position: relative;
			top: 20px;
			left: 22px;
		}
		#temperature{
			display: flex;
    		justify-content: flex-start;
    		position:relative;
    		top:20px;
    		left:22px;
		}
		#temp{
			font-size: 110px;
			font-weight: bold;
			position: relative;
		}
		#degree{
			width: 17px;
			height: 17px;
			position: relative;
			top: 12px;
		}
		#F{
			font-size: 54px;
			font-weight: bold;
			position: relative;
			top: 50px;
		}
		#summary{
			font-size: 43px;
			font-weight: bold;
			position: relative;
			top: 20px;
			left: 22px;
		}
		#table{
			margin: 0 auto;
			width: 97%;
			position: relative;
			top: 27px;
			display: table;
		}
		.cell{			
			display: table-cell;
			width: 16.67%;
		}
		.img1{
			margin: 0;
			width: 30px;
			height: auto;
		}
		.cellText{
			font-size: 28px;
			font-weight: bold;
			margin: 0;
		}
		#weekTemp{
			color: white;
			margin: 0 auto;
			position: relative;
			top:100px;
			background-color: #9ec8ed;
			border-collapse: collapse;
			padding: 10px;
		}
		#weekTemp, th, td{
			border: 2px solid #4e9dc6;
		}
		#weekTemp th{
			font-size: 19px;
			padding: 0 4px;			
		}
		#weekTemp td{
			font-size: 20px;
			padding: 0 4px;				
		}
		#iconImg{
			width: 47px;
			height: auto;
		}
		.detailAnchor{
			color: white;
			font-weight: bold;
			text-decoration: none;
		}

	</style>
</head>
<body>
	<div id="div1">
		<h>Weather Search</h>
		<form id="searchForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
			<div class="div1_left">
				<b>Street &nbsp;</b><input type="text" name="street" id="street">
			</div>	
			<div class="div1_left">
				<b>City &nbsp;&nbsp;&nbsp;&nbsp;</b><input type="text" name="city" id="city">
			</div>
			<div id="div1_select">
				<b>State &nbsp</b><select name="state" id="state">
					<option value="State">&nbsp;&nbsp;State</option>
					<optgroup label="&nbsp&nbsp----------------------------------------------------&nbsp&nbsp&nbsp">
						<option value="AL">Alabama</option>
						<option value="AK">Alaska</option>
						<option value="AZ">Arizona</option>
						<option value="AR">Arkansas</option>
						<option value="CA">California</option>
						<option value="CO">Colorado</option>
						<option value="CT">Connecticut</option>
						<option value="DE">Delaware</option>
						<option value="DC">District Of Columbia</option>
						<option value="FL">Florida</option>
						<option value="GA">Georgia</option>
						<option value="HI">Hawaii</option>
						<option value="ID">Idaho</option>
						<option value="IL">Illinois</option>
						<option value="IN">Indiana</option>
						<option value="IA">Iowa</option>
						<option value="KS">Kansas</option>
						<option value="KY">Kentucky</option>
						<option value="LA">Louisiana</option>
						<option value="ME">Maine</option>
						<option value="MD">Maryland</option>
						<option value="MA">Massachusetts</option>
						<option value="MI">Michigan</option>
						<option value="MN">Minnesota</option>
						<option value="MS">Mississippi</option>
						<option value="MO">Missouri</option>
						<option value="MT">Montana</option>
						<option value="NE">Nebraska</option>
						<option value="NV">Nevada</option>
						<option value="NH">New Hampshire</option>
						<option value="NJ">New Jersey</option>
						<option value="NM">New Mexico</option>
						<option value="NY">New York</option>
						<option value="NC">North Carolina</option>
						<option value="ND">North Dakota</option>
						<option value="OH">Ohio</option>
						<option value="OK">Oklahoma</option>
						<option value="OR">Oregon</option>
						<option value="PA">Pennsylvania</option>
						<option value="RI">Rhode Island</option>
						<option value="SC">South Carolina</option>
						<option value="SD">South Dakota</option>
						<option value="TN">Tennessee</option>
						<option value="TX">Texas</option>
						<option value="UT">Utah</option>
						<option value="VT">Vermont</option>
						<option value="VA">Virginia</option>
						<option value="WA">Washington</option>
						<option value="WV">West Virginia</option>
						<option value="WI">Wisconsin</option>
						<option value="WY">Wyoming</option>
					</optgroup>
				</select>
			</div>
			<div id="div1_bottom">
				<input style="font-size: 14px; width: 57px;" type="button" value="search" onclick="submitForm()">
				<input style="font-size: 14px; width: 48px;" type="button" value="clear" onclick="clearForm()">
			</div>
			<div id="div1_right">
				<input type="checkbox" value="currentLocation" id="currentLocation" autocomplete="off" onclick="checkboxOnClick(this)"><b>Current Location</b>
			</div>
			<div id="verticalLine"></div>
		</form>
	</div>

	<div id="div2"></div>

</body>
</html>