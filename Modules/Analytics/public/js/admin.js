$(function () {
	
	if ($('#visitors_3').length) {
		
		var so = new SWFObject(_analyticSWFLine, "amline_chart", "910", "500", "8");
		so.addParam("wmode", "transparent");
		so.addVariable("path", _keyFileUrl);
		so.addVariable("settings_file", escape(_xml.visitors_3));
		so.addVariable("data_file", escape(_csv.visitors_3));
		so.addVariable("preloader_color", "#666");
		so.write("visitors_3");
		
		var so = new SWFObject(_analyticSWFPie, "amline_chart", "448", "500", "8");
		so.addParam("wmode", "transparent");
		so.addVariable("path", _keyFileUrl);
		so.addVariable("settings_file", escape(_xml.country));
		so.addVariable("data_file", escape(_csv.country));
		so.addVariable("preloader_color", "#666");
		so.write("country");
		
		var so = new SWFObject(_analyticSWFPie, "amline_chart", "450", "500", "8");
		so.addParam("wmode", "transparent");
		so.addVariable("path", _keyFileUrl);
		so.addVariable("settings_file", escape(_xml.country));
		so.addVariable("data_file", escape(_csv.city));
		so.addVariable("preloader_color", "#666");
		so.write("city");
		
	}

})
