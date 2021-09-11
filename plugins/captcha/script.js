function loadCaptcha(){
    let appToken = $("meta[name=app-t]").attr('content');
    let captcha = "http://capital/ajax/plugins/?token="+appToken+"&plugin=captcha&call=rerender&crude=1";
    $(".plugin-captcha img").attr('src', captcha);
}

$(function() {
    loadCaptcha();
    $("body").on('click','.plugin-captcha .recaptcha',function () {
        loadCaptcha();
    })
});


