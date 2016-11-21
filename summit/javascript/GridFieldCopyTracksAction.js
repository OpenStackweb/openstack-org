(function($) {
    $.entwine("ss", function($) {

        $(".copy-tracks").entwine({
            onclick: function() {
                var $parent     = $(this).parents('.ss-gridfield-copy-tracks-class');
                var link        = this.data("href");
                var action      = $parent.find('select.select-summit-source:first').val();
                var $btn        = $(this);
                var url         = link.replace("{SummitID}", action);
                $btn.button("disable");

                $.ajax({
                    url: url,
                    type: "POST",
                    context: $(this)
                }).done(function(data, textStatus, jqXHR) {
                    $btn.button("enable");
                    this.getGridField().reload();
                });
                return false;
            }
        });

        $("select.select-summit-source").entwine({
            onadd: function() {
                this.update();
            },
            onchange: function() {
                this.update();
            },
            update: function() {
                var $parent  = $(this).parents('.ss-gridfield-copy-tracks-class');
                var btn      = $('.copy-tracks', $parent);
                if(this.val() && this.val().length) {
                    btn.button("enable");
                } else {
                    btn.button("disable");
                }
            }
        });

    });
})(jQuery);
