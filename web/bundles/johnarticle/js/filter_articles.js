$(document).ready(function() {


    setFilterValues();


 $('#filter_form').submit(function (e){

     e.preventDefault();

     var active=$("#filter_articles_active").is(':checked');
    var publish = $("#filter_articles_published").is(':checked');
     var category = $("#filter_articles_category").val();

     //set this vaues in localstorage
     localStorage.setItem("active",active);
     localStorage.setItem("publish",publish);
     localStorage.setItem("category",category);


     var url = window.location.href.substr(0,window.location.href.search("articles"));
     var redirect_link=url+"articles"+(category?("/"+category):"")+"?active="+active+"&publish="+publish;
     $(location).attr('href', redirect_link);
 });


})

function setFilterValues()
{
    var category=localStorage.getItem("category");
    var active=localStorage.getItem("active");
    var publish=localStorage.getItem("publish");

    $("#filter_articles_category").val(category);
    $("#filter_articles_published").attr("checked",publish==="true"?true:false);
    $("#filter_articles_active").attr("checked",active==="true"?true:false);
}
