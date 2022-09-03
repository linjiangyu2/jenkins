$(function(){
	$(".addressInfo").on('mouseover', '.address',function(){
		$(this).addClass("address-hover");	
	}).on('mouseout', '.address',function(){
		$(this).removeClass("address-hover");
	});
})

$(function(){
	$(".addressInfo").on('click', '.name', function(){
		 $(this).addClass("selected").parent().siblings().find('.name').removeClass("selected");
	});
	$(".payType li").click(function(){
		 $(this).addClass("selected").siblings().removeClass("selected");
	});
})
