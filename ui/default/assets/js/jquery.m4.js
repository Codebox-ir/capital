/**
 * Ajax Call
 */
(function ( $ ) {

    var appToken = $("meta[name=app-t]").attr('content');

    $.fn.m4_Ajax = function( options ) {
        var settings = $.extend({
            color: "#fff",
            backgroundColor: "white"
        }, options );
        this.each(function() {
            alert(appToken);
        });
        return this;
    };
}( jQuery ));