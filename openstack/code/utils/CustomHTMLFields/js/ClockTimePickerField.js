(function ($) {

    $.entwine('ss', function ($) {

        $(".ss-timeclock-field").entwine({
            initialize: function()
            {

                $(this).clockpicker(
                    {
                        placement: 'bottom',
                        align: 'left',
                        autoclose: true,
                        'default': 'now',
                        twelvehour:  false,
                    }
                );
            },
            onmatch: function ()
            {
                this.initialize();
            }
        });

    });

})(jQuery);