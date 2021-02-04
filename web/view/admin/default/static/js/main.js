$(document).ready(function(){


 //tab
 $("body").on("click",'.ui-tab-label .ui-tab-label-item',function(){
     let $parents=$(this).parents(".ui-tab-box");
     if(!$(this).hasClass("active")){
        $(this).siblings().removeClass("active");
        $(this).addClass("active");
        $parents.find(".ui-tab-content-item").removeClass("active");
        $parents.find(".ui-tab-content-item").eq($(this).index()).addClass("active");
     }
 })


})

