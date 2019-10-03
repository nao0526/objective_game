$(function(){
    $jsToggleActive = $('.js-toggle-active');
    $jsToggleActive.on('click', function(){
        $jsToggleActive.removeClass('active');
        $(this).addClass('active');
    }); 
});