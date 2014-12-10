(function($) {
    //cancel action

    $('.ss-uploadfield-item-cancel').entwine({
        // This will execute when the class is added to the element.
        onmatch: function(e) {
            var btn = $('button',$(this));
            btn.button();
            btn.removeClass('icon-16');
            this._super(e);
        },
        // Entwine requires us to define this, even if we don't use it.
        onunmatch: function(e) {
            this._super(e);
        }
    });

    $('.ss-uploadfield-item-remove').entwine({
        // This will execute when the class is added to the element.
        onmatch: function(e) {
            var btn = $(this);
            btn.button();
            this._super(e);
        },
        // Entwine requires us to define this, even if we don't use it.
        onunmatch: function(e) {
            this._super(e);
        }
    });

    $('.ss-uploadfield-item-delete').entwine({
        // This will execute when the class is added to the element.
        onmatch: function(e) {
            var btn = $(this);
            btn.button();
            this._super(e);
        },
        // Entwine requires us to define this, even if we don't use it.
        onunmatch: function(e) {
            this._super(e);
        }
    });

}(jQuery));