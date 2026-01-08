$(".menu-sistema div").click(function() {
      $("ul").slideToggle();
      $("ul ul").css("display", "none");
});

$(".menu-sistema > ul > li").click(function() {
      var litrue = $(this).attr('val');
      $('.menu-sistema ul li').removeAttr('val'); 
      $('.menu-sistema ul li').removeClass('active'); 
      
      if(litrue==undefined){
            $(this).addClass('active');
            $(this).attr('val','true'); 
            $(".menu-sistema ul ul").slideUp();
            $(this).find('ul').slideToggle(); 
      }else{
            $(this).removeClass('active');
            $(".menu-sistema ul ul").slideUp();
            $(this).removeAttr('val');
      }      
});

$(window).resize(function() {
      if($(window).width() > 768) {
            $("ul").removeAttr('style');
      }
});