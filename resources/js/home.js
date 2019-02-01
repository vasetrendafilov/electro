$(function(){
  $('#totorijal').click(function(){
    const tour = new Shepherd.Tour({
        defaults: {
            classes: 'shepherd-theme-arrows'
        }
    });
    tour.addStep('example-step', {
        text: 'Во овој дел можете да видите дел од вашата статистика.',
        attachTo: '.progress-bar.bg-danger bottom',
        buttons: [
          {
            text: 'Следно',
            action: tour.next
          }
        ]
    });
    tour.addStep('example-step2', {
        text: 'Со клик на аватарот го отварате вашиот профил.',
        attachTo: '#myProf bottom',
        buttons: [
            {
              text: 'Назад',
              action: tour.back
            },
            {
              text: 'Следно',
              action: tour.next
            }
        ]
    });

    tour.addStep('example-step3', {
        text: "Тука можете да влезете во злосторствата,работите,<br>одите на разни локации,следите инфромација/статистика и слично.",
        attachTo: '.navigation bottom',
        buttons: [
            {
              text: 'Назад',
              action: tour.back
            },
            {
              text: 'Следно',
              action: tour.next
            }
        ]
    });

    tour.addStep('example-step4', {
        text: 'Топ 5 фамилии.',
        attachTo: '.clans bottom',
        buttons: [
            {
              text: 'Назад',
              action: tour.back
            },
            {
              text: 'Следно',
              action: tour.next
            }
        ]
    });

     tour.addStep('example-step5', {
        text: 'Пребарувај пријатели.',
        attachTo: '#search top',
        buttons: [
            {
              text: 'Назад',
              action: tour.back
            },
            {
              text: 'Следно',
              action: tour.next
            }
        ]
    });

    tour.addStep('example-step6', {
        text: 'Ова беше почетен туторијал, за секое посебно мени има посебен туторијал со клик на - туторијал.',
        attachTo: '#name bottom',
        buttons: [
            {
              text: 'Назад',
              action: tour.back
            },
            {
              text: 'Затвори',
              action: tour.next
            }
        ]
    });

    tour.start();
  });
  $('#logo').click(function(){if(isMobile){toggleClassMenu();}});
  $('#myProf').click(function(){
    $.get("/mafija2/public/profile", function(data){
      if(isMobile){toggleClassMenu();}
      $('#container').html(data);
    });
  });
  $('#statistika').click(function(){
    if(isMobile){toggleClassMenu();}
    $('#container').fadeOut(1);
    getData("/mafija2/public/statistika");
  });
  $('.sub-menu').children('li').click(function(){
    if(isMobile){toggleClassMenu();}
    $('#container').fadeOut(1);
    $('.navigation').children('.nav').children('li').children('.svg').rotate(0);
    $(this).parents('.sub-menu').removeClass('show').addClass('collapse');
    $('.navigation').children('.nav').children('li').addClass('collapsed');
    var val = $(this).children('input').val();
    val1 = val;
    if(val == 'crime'){
      val1 = 'rabota';
    }
    if(val == 'drugs'){
      val1 = 'drinks';
    }
    if(val == 'cars'|| val == 'trki'|| val == 'garaza'){
      $('#container').load("/mafija2/resources/views/templates/loading/cardCars.html",function(){
        $('#container').fadeIn("fast");});
    }
    if(val == 'crime' || val == 'rabota'){
      $('#container').load("/mafija2/resources/views/templates/loading/cardRabota.html",function(){
        $('#container').fadeIn("fast");});
    }
    if(val == 'drugs' || val == 'drinks'|| val == 'shop'){
      $('#container').load("/mafija2/resources/views/templates/loading/cardDrink.html",function(){
        $('#container').fadeIn("fast");});
    }
    getData("/mafija2/public/ajax/"+val1,val);
  });
  var temp = $('.chat .content').html();
  $('#search').on("keyup",function(){
       if($(this).val() != ''){
         $.get("/mafija2/public/ajax/search",{key: $(this).val()}, function(data){
           $('.chat .content').html(data);
         });
       }else{
        $('.chat .content').html(temp);
       }
     });
   $('[data-toggle="tooltip"]').tooltip();
});
function jail(time,jail){
  if(jail != time){
    var i = parseInt(time);
    setTimeout(function(){
    $("#loading").html(zatvor).fadeIn();
    countdown(new Date(new Date().getTime() + (jail*1000)));},(i+3)*1000);
  }
}
