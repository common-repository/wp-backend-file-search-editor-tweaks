jQuery(function () {
    var gen_el_notice = jQuery(".backend-file-search-notice"),
        gen_btn_act = gen_el_notice.find(".backend-file-search-notice-act"),
        gen_btn_dismiss = gen_el_notice.find(".backend-file-search-notice-dismiss");
    gen_el_notice.fadeIn(500);

    // Hide the notice after a CTA button was clicked
    function gen_remove_notice() {
        gen_el_notice.fadeTo(100, 0, function () {
            gen_el_notice.slideUp(100, function () {
                gen_el_notice.remove();
            });
        });
    }

    /*gen_btn_act.click(function (ev) { 
        gen_remove_notice();
        gen_notify_wordpress(gen_btn_act.data("msg"));
    });*/
    gen_btn_dismiss.click(function (ev) {
        gen_remove_notice();
        gen_notify_wordpress(gen_btn_act.data("msg"));
    });

    // Notify WordPress about the users choice and close the message.
    function gen_notify_wordpress(message) { console.log(message);
        
        gen_el_notice.attr("data-message", message);
        gen_el_notice.addClass("loading");

        //Send a ajax request to save the dismissed notice option
        var param = {
            action: 'wp_backend_search_lite_dismiss_notice'
        };
        jQuery.post(ajaxurl, param);
    }

    

});