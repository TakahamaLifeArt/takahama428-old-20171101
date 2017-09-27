$(function(){	
	
	/*
	$("#size_table tbody tr:even").each(function(){
		$(this).find("tr:even").addClass("even");
	});
	*/
	
	$("#size_detail table").each(function(tbl){
		tbl++;
		var curTable = $(this);
		var numofcol = $("tbody tr:eq(0)", curTable).children().size();
		var colspan = 0;
		curTable.find("tbody tr").each(function(){
			var colStart = 0;
			$(this).find("td").each(function(col){
				var self = $(this);
				col += (colStart+1);
				/*
				while(col>numofcol){
					col = col-numofcol;
				}
				if(col==1) colStart = 0;
				*/
				colspan = self.attr("colspan");
				if(colspan){
					for(var i=0; i<colspan; i++){
						self.addClass("item"+tbl+"_"+(col+i));
					}
					colStart = colspan-1;
				}else{
					self.addClass("item"+tbl+"_"+col);
				}
			});
		});
	});

	$("#size_detail tbody tr:even").addClass("even");
	
	$("#size_detail tbody td").each(function(){
		var classname;
		$(this).hover(function(){
			classname = $(this).attr("class");
			classname = "." + classname;
			$(classname).addClass('hov');
			$(this).addClass('hov');
			$(this).siblings().addClass('hov');
		},function(){
			$(classname).removeClass('hov');
			$(this).removeClass('hov');
			$(this).siblings().removeClass('hov');
		});
	});
	
});