$(document).ready(function(){
//    nav-li hover e
    var num;
    $('.nav-main>li[id]').hover(function(){
       /*图标向上旋转*/
        $(this).children().removeClass().addClass('hover-up');
        /*下拉框出现*/
        var Obj = $(this).attr('id');
        num = Obj.substring(3, Obj.length);
        $('#box-'+num).slideDown(300);
    },function(){
        /*图标向下旋转*/
        $(this).children().removeClass().addClass('hover-down');
        /*下拉框消失*/
        $('#box-'+num).hide();
    });
//    hidden-box hover e
    $('.hidden-box').hover(function(){
        /*保持图标向上*/
        $('#li-'+num).children().removeClass().addClass('hover-up');
        $(this).show();
    },function(){
        $(this).slideUp(200);
        $('#li-'+num).children().removeClass().addClass('hover-down');
    });
});

$(document).ready(function(){

	$(".section	 ul li").hover(function(){
		$(this).find(".text").stop().animate({top:'280'}, {duration: 500})	//左右上升位置 结束位置-40px
		$(this).find(".text_2").stop().animate({top:'105'}, {duration: 500}) //中间上升位置
	},function(){
		$(this).find(".text").stop().animate({top:'320'}, {duration: "fast"}) //结束位置（DIV-20px）
		$(this).find(".text").animate({top:'320'}, {duration: 0}) //下降位置 同上px
		$(this).find(".text_2").stop().animate({top:'145'}, {duration: "fast"})
		$(this).find(".text_2").animate({top:'145'}, {duration: 0})
	});

});