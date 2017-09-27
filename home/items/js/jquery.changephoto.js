$(document).ready(function(){
	$("#color_thumb li img, #style_thumb li img").click(function(){
		var imgname=$(this).attr("src").replace("_s.jpg", ".jpg");
		var imgcolor=$(this).attr("title");
		//var imgsize=$(this).attr("alt");
		
		$("#color_thumb li.nowimg").removeClass("nowimg");
		$("#style_thumb li.nowimg").removeClass("nowimg");
		$(this).parent().addClass("nowimg");
		
		$("#item_image_l").attr("src", imgname);
		$("#notes_color").html(imgcolor);
		//$("#size_span").html(imgsize);
	});	

	// popup
	if($("a[rel^='prettyPhoto']").length>0) $("a[rel^='prettyPhoto']").prettyPhoto();
	
});