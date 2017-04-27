(function($) {
    $.entwine("ss", function($) {

        $(".ss-gridfield .col-buttons .action.gridfield-button-approve-summit-notification").entwine({
            onclick: function(e) {
                if(!window.confirm('are you sure? This action can not be undone.')) {
                    e.preventDefault();
                    return false;
                } else {
                    this._super(e);
                }
            }
        });


    });
})(jQuery);
