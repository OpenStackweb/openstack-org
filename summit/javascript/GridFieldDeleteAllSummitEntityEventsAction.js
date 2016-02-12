(function($) {
    $.entwine("ss", function($) {

        $(".delete-entity-events").entwine({
            onclick: function() {
                var $parent     = $(this).parents('.ss-gridfield-delete-all-entity-events-data-class');
                var link        = this.data("href");
                var $btn        = $(this);
                if(window.confirm('are you sure? This action can not be undone.')) {
                    $btn.button("disable");

                    $.ajax({
                        url: link,
                        type: "POST",
                        context: $(this)
                    }).done(function (data, textStatus, jqXHR) {
                        $btn.button("enable");
                        this.getGridField().reload();
                    }).fail(function( jqXHR, textStatus, errorThrown ) {
                        $btn.button("enable");
                        window.alert('server error : '+textStatus);
                    });
                }
                return false;
            }
        });


    });
})(jQuery);
