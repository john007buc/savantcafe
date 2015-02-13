
$(document).ready(function(){
   $("#refresh-btn").on('click',function(){
       var random=Math.random();
       $("#captcha-img").attr("src",CAPTCHA_URL+"/"+random)
   });


})
