function test(){
alert("soy el numero 2");
	$(".bt_collapse").on("click",function(e){
		$("body").animate(
			{opacity: 0.25,}
			, 700, function() {
				// Animation complete.
					
				$("#theme_selected_css").attr("href","theme/amedita/amedita.css");
		});
		
		$("body").animate(
			{opacity: 1,}
			, 1000, function() {}
		);
		
	});
	
	
};

