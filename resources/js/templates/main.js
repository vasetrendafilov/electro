
function countdown(endDate) {
  let days, hours, minutes, seconds;

  endDate = new Date(endDate).getTime();
  i=0;
  if (isNaN(endDate)) return;
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
