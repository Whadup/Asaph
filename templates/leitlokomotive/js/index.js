$(document).scroll(function(){
	if($(window).scrollTop()<310)
	{	bla = $(window).scrollTop();
		$("#header").css('top',(-bla)+'px');
	}
	else
	{
		$("#header").css('top',(-310)+'px');
	}
});
//scroll header out of view on load
$(document).ready(function (){
	view = $(window).height();   // returns height of browser viewport
	doc = $("#bottom").position().top+20;
	// alert(view + " " + doc);
	if(doc+310>view)
	{
		offset = 310+(view-doc) > 50 ? 310+(view-doc) : 50;
		$("#main").css('margin-bottom',offset+'px');
	}
	else
	{
		$("#main").css('margin-bottom','50px');
	}
  $('html,body').animate({scrollTop: 310}, 1000);

  
});