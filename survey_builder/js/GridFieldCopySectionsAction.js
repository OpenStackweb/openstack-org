(function($) {
    $.entwine("ss", function($) {

        $(".copy-sections").entwine({
            onclick: function() {
                var $parent     = $(this).parents('.ss-gridfield-copy-sections-class');
                var link        = this.data("href");
                var action      = $parent.find('select.select-report-source:first').val();
                var $btn        = $(this);
                var url         = link.replace("{ReportID}", action);
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

        $("select.select-report-source").entwine({
            onadd: function() {
                this.update();
            },
            onchange: function() {
                this.update();
            },
            update: function() {
                var $parent  = $(this).parents('.ss-gridfield-copy-sections-class');
                var btn      = $('.copy-sections', $parent);
                if(this.val() && this.val().length) {
                    btn.button("enable");
                } else {
                    btn.button("disable");
                }
            }
        });

    });
})(jQuery);
