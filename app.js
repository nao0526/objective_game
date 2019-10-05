$(function(){
    // 初期画面で選択されたモンスターにactiveクラスを追加
    $jsToggleActive = $('.js-toggle-active');
    $jsToggleActive.on('click', function(){
        $jsToggleActive.removeClass('active');
        $(this).addClass('active');
    }); 
    // たたかうを選択した際に表示させたいフォームにactiveクラスを追加
    $jsToggleAction = $('.js-toggle-action');
    $jsToggleAction.on('click', function(e){
        e.preventDefault();
        $('.js-set-action').toggleClass('active');
    });
    // フォームを送信するときに位置を保持
    $jsSubmitScroll = $('.js-submit-scroll');
    $jsSubmitScroll.submit(function(){
        var scroll_top = $(window).scrollTop();
        $('.js-scroll-top', this).prop('value', scroll_top);
    });
});