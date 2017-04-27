(function($) {
    $.entwine("ss", function($) {

        $(".ss-gridfield .col-buttons .action.gridfield-button-approve-summit-notification").entwine({
            onadd: function() {
                var $btn = $(this);
            },
            onclick: function(e) {
                var $btn = $(this);
                if($btn.hasClass("disabled")){
                    e.preventDefault();
                    return false;
                }
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
