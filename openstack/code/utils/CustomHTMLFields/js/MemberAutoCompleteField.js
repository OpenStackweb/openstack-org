(function ($) {

    $.entwine('ss', function ($) {

        $(".ss-member-autocomplete-field").entwine({
            initialize: function()
            {
                var url    = $(this).attr('data-ss-member-field-suggest-url');
                var self   = $(this);
                var hidden = $('#'+self.attr('data-hidden-value-id'));
                $(this).autocomplete({
                    source: url,
                    minLength: 2,
                    select: function( event, ui ) {
                        hidden.val(ui.item.id);
                    }
                });
            },
            onmatch: function ()
            {
                this.initialize();
            }
        });

    });

})(jQuery);