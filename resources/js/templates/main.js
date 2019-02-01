$(window).on('load', function(){
    $(".wrap").delay(1000).fadeOut("slow");
    $('#temp1').load("/../mafija2/resources/views/templates/loading/travel.html",function(){
      travel = $('#temp1').html();
      $('#temp1').remove();
    });
    $('#temp2').load("/../mafija2/resources/views/templates/loading/rabota.html",function(){
      rabota = $('#temp2').html();
      $('#temp2').remove();
    });
    $('#temp3').load("/../mafija2/resources/views/templates/loading/hand.html",function(){
      crime = $('#temp3').html();
      $('#temp3').remove();
    });
    $('#temp4').load("/../mafija2/resources/views/templates/loading/car.html",function(){
      car = $('#temp4').html();
      $('#temp4').remove();
    });
    $('#temp5').load("/../mafija2/resources/views/templates/loading/zatvor.html",function(){
      zatvor = $('#temp5').html();
      $('#temp5').remove();
    });
});
  if(!isMobile){
    $("nav.sidebar").children('.navigation').css('overflow-y','hidden');
  }
var isScrolling;
window.addEventListener('scroll', function ( event ) {
 document.getElementById("barStyle").innerHTML = '::-webkit-scrollbar-thumb{background-color: #ec1a23;}';
  window.clearTimeout( isScrolling );
	isScrolling = setTimeout(function() {
  document.getElementById("barStyle").innerHTML = '::-webkit-scrollbar-thumb{background-color: #000;}';
}, 200);
}, false);
function refreshStats() {
  $.get("/mafija2/public/stats",function(data){
    result = data.split('_');
    for (var i = 1; i <= 3; i++) {
      $('#stats-container').children("li:nth-child("+i+")").children('span').text(result[i-1]);
    }
    $('#stats-container').children("li:nth-child(4)").children('.progress').children('.progress-bar').css('width',result[3]+'%');
    $('#stats-container').children("li:nth-child(4)").children('.progress').children('.progress-bar').children('span').text(result[3]+'%');
    $('#stats-container').children("li:nth-child(4)").children('.progress').children('.progress-bar').children('svg').css('left',(result[3] - 10)+'%');
    $('#stats-container').children("li:nth-child(5)").children('.progress').children('.progress-bar').css('width',result[4]+'%');
    $('#stats-container').children("li:nth-child(5)").children('.progress').children('.progress-bar').children('span').text(result[4]+'%');
    $('#stats-container').children("li:nth-child(5)").children('.progress').children('.progress-bar').children('svg').css('left',(result[4] - 10)+'%');
  });
}
function refreshChatTiles() {
  $.get("/mafija2/public/chatTiles",function(data){
    $('.chat .content').html(data);
    temp = data;
  });
}
function countdown(endDate) {
  let days, hours, minutes, seconds;

  endDate = new Date(endDate).getTime();
  i=0;
  if (isNaN(endDate)) {
	return;
  }

  count = setInterval(calculate, 1000);

  function calculate() {
    let startDate = new Date();
    startDate = startDate.getTime();

    let timeRemaining = parseInt((endDate - startDate) / 1000);

    if (timeRemaining >= 0) {
      days = parseInt(timeRemaining / 86400);
      timeRemaining = (timeRemaining % 86400);

      hours = parseInt(timeRemaining / 3600);
      timeRemaining = (timeRemaining % 3600);

      minutes = parseInt(timeRemaining / 60);
      timeRemaining = (timeRemaining % 60);

      seconds = parseInt(timeRemaining);
    //  console.log(days+':'+hours+':'+minutes+':'+seconds);
      if(minutes > 0){
      $('#loading').find('h1').children('span').html(minutes+":"+seconds);
      $('#loading').find('.pocekaj').children('span').html(minutes+":"+seconds);
    }else{
      $('#loading').find('h1').children('span').html(seconds);
      $('#loading').find('.pocekaj').children('span').html(seconds);
    }
    }else{
      $("#loading").fadeOut("slow");//tuka loadingot stop
      clearTimeout(count);
    }
  }
}
