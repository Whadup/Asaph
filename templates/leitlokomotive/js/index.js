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
  $('html,body').animate({scrollTop: 310}, 800);

  
});