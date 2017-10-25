window.FreeGift = {
    Models: {},
    Collections: {},
    Views: {},
    $: null
};
head.js(
   "/js/mw_freegift/lib/underscore.js",
    "/js/varien/product.js",
   "/js/varien/configurable.js",
    "/js/mw_freegift/lib/jquery.plugins.min.js",
     "/js/mw_freegift/lib/jquery-migrate-1.2.1.min.js",
     "/js/mw_freegift/lib/backbone-min.js",
    function(){
        _.extend(window.FreeGift, Backbone.Events);

        head.js("/js/mw_freegift/view.js");

    });