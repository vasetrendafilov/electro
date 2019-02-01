$(function(){
  $('.navigation').children('.nav').children('.collapsed').click(function(){
    if($(this).hasClass( "collapsed")){
      $(this).children('.svg').rotate(180);
    }else{
      $(this).children('.svg').rotate(0);
    }
  });
    $('.search').children('input').focus(function(){
      $('.expand').css('left','265px');
      $('.simplebar-scroll-content').scrollTop(2000);
    });
    $('.content').on('click','li',function(){
      var username = $(this).children('span').first().html();
      $('.expand').children('.messenger').prop('disabled', true);
      $('.expand').children('button:nth-child(2)').addClass('profil');
      if($(this).parents('ul').hasClass("friends")){
        $('.expand').children('button').first().html("ИЗБРИШИ <input type='hidden' value='"+username+"'>").removeClass().addClass('btn btn-danger friends');
        $('.expand').children('button:nth-child(2)').html("ПРОФИЛ <input type='hidden' value='"+username+"'>");
        $('.expand').children('.messenger').html("ЧАТ<input type='hidden' value='"+username+"'>");
        $('.expand').children('.messenger').removeAttr('disabled');
      }else if ($(this).parents('ul').hasClass("people")){
        $('.expand').children('button').first().html("ДОДАДИ <input type='hidden' value='"+username+"'>").removeClass().addClass('btn btn-danger add');
        $('.expand').children('button:nth-child(2)').html("ПРОФИЛ <input type='hidden' value='"+username+"'>");
      }else if ($(this).parents('ul').hasClass("confirm")){
        $('.expand').children('button').first().html("ОДОБРИ <input type='hidden' value='"+username+"'>").removeClass().addClass('btn btn-danger confirm');
        $('.expand').children('button:nth-child(2)').html("ПРОФИЛ <input type='hidden' value='"+username+"'>");
      }else if ($(this).parents('ul').hasClass("clans")){
        $('.expand').children('button').first().html("ВЛЕЗИ <input type='hidden' value='"+username+"'>").removeClass().addClass('btn btn-danger join');
        $('.expand').children('button:nth-child(2)').html("ПРОФИЛ <input type='hidden' value='"+username+"'>").removeClass('profil');
      }else{
        $('.expand').children('button').first().html("ОДОБРИ <input type='hidden' value='"+username+"'>").removeClass().addClass('btn btn-danger clanConfirm');
        $('.expand').children('button:nth-child(2)').html("ПРОФИЛ <input type='hidden' value='"+username+"'>");
      }
      $('.expand').css('top', $(this).position().top + 4).css('left','265px');
      $(".expand").animate({left: '50px'});
    });
    $(".expand").children('button').first().click(function() {
      if(isMobile){toggleClassChatMenu();}
      if($(this).hasClass("add")){
      var val = $(this).children('input').val();
      $.get("/mafija2/public/add/friend",{username:val},function(data){
        $('#container').children('.container').html(data);
      });
      }else if($(this).hasClass("confirm")){
        var val = $(this).children('input').val();
        $.get("/mafija2/public/confirm/friend",{username:val},function(data){
          $('#container').children('.container').html(data);
        });
      }else if ($(this).hasClass("friends")){
        var val = $(this).children('input').val();
        $.get("/mafija2/public/delete/friend",{username:val},function(data){
          $('#container').children('.container').html(data);
        });
      }else if ($(this).hasClass("join")){
        var clan = $(this).children('input').val();
        $.get("/mafija2/public/join/clan",{name:clan},function(data){
          $('#container').children('.container').html(data);
        });
      }
      else if ($(this).hasClass("clanConfirm")){
        var clan = $(this).children('input').val();
        $.get("/mafija2/public/confirm/clan",{name:clan},function(data){
          $('#container').children('.container').html(data);
        });
      }
      refreshChatTiles();
      $('.expand').css('left','265px');
    });
    $('.expand').children('button:nth-child(2)').click(function(){
      var username = $(this).children('input').val();
      if(isMobile){toggleClassChatMenu();}
      $('.expand').css('left','265px');
      if($(this).hasClass("profil")){
        getData("/mafija2/public/profile/people",username);
      }else{
        getData("/mafija2/public/profile/clan",username);
      }
    });
    chatNum = 0;
    chatEngine = '';
    $(".messenger").click(function(){
      $('.expand').css('left','265px');
      var username = $(this).children('input').val();
      $('#chat-loading').fadeIn();
      $.get("/mafija2/public/chat/room",{username:username},function(data){
        if(data != ""){
            refreshChatTiles();
            $('#chat-room').remove();
            $('#container').append("<input type='hidden' id='chat-room' value='"+data+"'>");
          if(chatNum == 0){
            $('#messenger-container').load("/mafija2/resources/views/chat.html", function() {
              $('#messenger-container').fadeIn(1);
              $('.chat #friend-name').text(username);
            });
            chatNum = 1;
          }else{
            $('#messenger-container').fadeIn(1);
            $('.chat #friend-name').text(username);
            chatEngine.on('$.ready', function(data) {
              app.ready(data);
              app.bindMessages();
            });
            app.init();
            msgCount = 0;
          }
        }
      });
    });
});
if(isMobile){
  gate = true;
  function toggleClassMenu() {
    if(gate){
      myMenu.classList.add("menu--animatable");
      if(!myMenu.classList.contains("menu--visible")) {
        myMenu.classList.add("menu--visible");
        $('.sidebar .navigation').css('overflow-y','scroll');
      } else {
        myMenu.classList.remove('menu--visible');
      }
    }
  }
  function OnTransitionEnd() {
    myMenu.classList.remove("menu--animatable");
  }
  function toggleClassChatMenu() {
    if(gate){
      chatMenu.classList.add("chatMenu--animatable");
      if(!chatMenu.classList.contains("chatMenu--visible")) {
        chatMenu.classList.add("chatMenu--visible");
      } else {
        chatMenu.classList.remove('chatMenu--visible');
      }
    }
  }
  function OnTransitionEndChat() {
    chatMenu.classList.remove("chatMenu--animatable");
  }

  var myMenu = document.querySelector(".menu");
  var chatMenu = document.querySelector(".chatMenu");
  myMenu.addEventListener("transitionend", OnTransitionEnd, false);
  chatMenu.addEventListener("transitionend", OnTransitionEndChat, false);
}
function getData(val,type){
  $.get(val,{type:type},function(data){
    $('#container').html(data);
    $('#container').fadeIn("fast");
    $(window).scrollTop(0);
    $('[data-toggle="popover"]').popover();
    $('[data-toggle="tooltip"]').tooltip();
    var nav = $('.navbar').position().top +$('.navbar').outerHeight(true);
    $('.card-navigation').css('top',(nav )+'px');
    $('.card-navigation').children('ul').children('li').first().css("border-bottom","3px solid #E61924");
        $('.card-navigation').children('ul').children('li').click(function(){
          $('.card-navigation').children('ul').children('li').css("border-bottom","none");
          $(this).css("border-bottom","3px solid #E61924");
            $('.card-columns').css('display','none');
            switch ($(this).attr('id')){
              case '1':
                $("#first").fadeIn("slow");
                break;
              case '2':
                $("#second").fadeIn("slow");
                break;
              case '3':
                $("#last").fadeIn("slow");
                break;
            }
          $(window).scrollTop(0);
        });
    var isMoving;
    $(".slider").on("input", function(){
      gate = false;
      $(this).siblings('input').val(this.value);
      price = parseInt($(this).parents('.row').children('input').val());
      pari = parseInt($('#pari').text());
        min = parseInt($(this).attr('min'));
        if($(this).hasClass('reverse')){
          $(this).parents('.row').children('.col').children('.outPari').css('color','darkgreen');
        }else{
          if(pari < ((this.value - min)* price)){
            $(this).parents('.row').children('.col').children('.outPari').css('color','#ec1a23');
          }else{
            $(this).parents('.row').children('.col').children('.outPari').css('color','darkgreen');
          }
        }
        $(this).parents('.row').children('.col').children('.outPari').children('output').text((this.value - min)* price );
        $(this).parents('.card-body').children('.btn').children('span').text(((this.value - min)* price)+'$');
      window.clearTimeout( isMoving );
      isMoving = setTimeout(function() {
      gate = true;
    }, 500);
    });
    $(".result").on("input", function(){
      $(this).siblings('.slider').val(this.value);
      min = parseInt($(this).siblings('.slider').attr('min'));
      max = parseInt($(this).siblings('.slider').attr('max'));
      if( this.value >= min && this.value <= max){
        price = parseInt($(this).parents('.row').children('input').val());
        pari = parseInt($('#pari').text());
        if($(this).siblings('.slider').hasClass('reverse')){
          $(this).parents('.row').children('.col').children('.outPari').css('color','darkgreen');
        }else{
          if(pari < ((this.value - min)* price)){
            $(this).parents('.row').children('.col').children('.outPari').css('color','#ec1a23');
          }else {
            $(this).parents('.row').children('.col').children('.outPari').css('color','darkgreen');
          }
          $(this).parents('.row').children('.col').children('.outPari').children('output').text((this.value - min)* price );
        }
      }
    });
  });
}
