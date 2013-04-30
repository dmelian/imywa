function test(id){
	//alert("soy el numero 3");
	$("#" + id).find(".bt_collapse").on("click",function(e){
		$("body").animate(
			{opacity: 0.25,}
			, 700, function() {
				// Animation complete.
				$("#theme_selected_css").attr("href","theme/le-frog/le-frog.css");			
		});
		
		$("body").animate(
			{opacity: 1,}
			, 1000, function() {}
		);
		
	});
	
	
};

