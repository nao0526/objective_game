$(function(){
    $jsToggleActive = $('.js-toggle-active');
    $jsToggleActive.on('click', function(){
        $jsToggleActive.removeClass('active');
        $(this).addClass('active');
    }); 
    $jsToggleAction = $('.js-toggle-action');
    $jsToggleAction.on('click', function(e){
        e.preventDefault();
        console.log('a');
        $('.js-set-action').toggleClass('active');
    });
});