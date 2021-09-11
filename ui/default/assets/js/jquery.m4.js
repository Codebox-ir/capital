/**
 * M4 jQuery Plugins
 *
 * [ Example ]
 * m4_Example(options);
 *
 * [ Ajax Call ]
 * m4_Ajax(options, callback);
 *
 */

(function ( $ ) {

    let appToken = $("meta[name=app-t]").attr('content');

    /* Example */
    $.fn.m4_Example = function(options) {
        let settings = $.extend({
            sample: "test",
            callback: null
        }, options );
        this.each(function() {
            alert(settings.sample);
        });
        return this;
    };

    /* Ajax Call */
    let AjaxLock;
    $.fn.m4_Ajax = function(options, callback=null) {
        let thisID = $(this).attr('id');
        let settings = $.extend({
            call:'test',
            file:'global',
            plugin:0,
            plugin_call:0,
            crud:0,
            type:'',
            data:null,
            cache: false,
            global: true,
            async: true,
            processData: false,
            contentType: false
        }, options );

        if(settings.data===null && settings.type==='form')
            settings.data = new FormData(document.getElementById(thisID));
        if ( AjaxLock === (settings.call+'/'+settings.file) ) {
            console.log('AjaxLock: '+AjaxLock);
            return;
        }
        AjaxLock = settings.call+'/'+settings.file;

        let url = (settings.file == null) ? ("ajax/"+settings.call) : ("ajax/"+settings.call+"/"+settings.file);
        url += "?token="+appToken;

        if(settings.crud) url += "&crud="+settings.crud;
        if(settings.plugin) url += "&plugin="+settings.plugin;
        if(settings.plugin_call) url += "&call="+settings.plugin_call;

        $.ajax({
            type: "POST",
            url: url,
            data: settings.data,
            cache: settings.cache,
            global: settings.global,
            async: settings.async,
            processData: settings.processData,
            contentType: settings.contentType,
            success: callback,

            error: function(request, status, error) {
                console.log(error);
                return false;
            }
        });

        $( document ).ajaxComplete(function() {
            setTimeout(function() {
                AjaxLock = null;
            }, 50);
        });
        return true;
    };

}( jQuery ));
