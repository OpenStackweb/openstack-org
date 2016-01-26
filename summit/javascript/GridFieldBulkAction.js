(function($) {
    $.entwine("ss", function($) {

        $('.ss-gridfield-bulk-action-assign-entity').entwine({
            onmatch: function(){
                var $parent = this.parents('thead'),
                    $tr = $parent.find('tr'),

                    targets = ['.filter-header', '.sortable-header'],
                    $target = $parent.find(targets.join(',')),

                    index = $tr.index(this),
                    newIndex = $tr.length - 1
                    ;

                $target.each(function(index, Element){
                    var idx = $tr.index(Element);
                    if ( idx < newIndex )
                    {
                        newIndex = idx;
                    }
                });

                if ( index > newIndex )
                {
                    $tr.eq(newIndex).insertAfter($(this));
                }
            },
            onunmatch: function(){}
        });


        $(".doBulkActionButton").entwine({
            onclick: function() {
                var $parent     = $(this).parents('.ss-gridfield-bulk-action-assign-entity');
                var link        = this.data("href");
                var event_type  = $parent.find('select.select-entity:first').val();
                var $btn        = $(this);
                var url         = link.replace("{entityID}", event_type);
                var ids         = $parent.find('input.bulkSelectAll:first').getSelectRecordsID();
                if(ids.length === 0)
                {
                    alert('you must select at least one row!');
                    return false;
                }
                var data  = { records: ids };
                $btn.button("disable");
                $.ajax({
                    url: url,
                    data: data,
                    type: "POST",
                    context: $(this)
                }).done(function(data, textStatus, jqXHR) {
                    $btn.button("enable");
                    this.getGridField().reload();
                });
                return false;
            }
        });

        $("select.select-entity").entwine({
            onadd: function() {
                this.update();
            },
            onchange: function() {
                this.update();
            },
            update: function() {
                var $parent  = $(this).parents('.ss-gridfield-bulk-action-assign-entity');
                var btn      = $('.doBulkActionButton', $parent);

                if(this.val() && this.val().length) {
                    btn.button("enable");
                } else {
                    btn.button("disable");
                }
            }
        });

        $('td.col-bulkSelect').entwine({
            onmatch: function(){
            },
            onunmatch: function(){
            },
            onmouseover: function(){
                //disable default row click behaviour -> avoid navigation to edit form when clicking the checkbox
                $(this).parents('.ss-gridfield-item').find('.edit-link').removeClass('edit-link').addClass('tempDisabledEditLink');
            },
            onmouseout: function(){
                //re-enable default row click behaviour
                $(this).parents('.ss-gridfield-item').find('.tempDisabledEditLink').addClass('edit-link').removeClass('tempDisabledEditLink');
            },
            onclick: function(e) {
                //check/uncheck checkbox when clicking cell
                var cb = $(e.target).find('input');
                if ( !$(cb).prop('checked') ) $(cb).prop('checked', true);
                else $(cb).prop('checked', false);
            }
        });

        /**
         * Individual select checkbox behaviour
         */
        $('td.col-bulkSelect input').entwine({
            onmatch: function(){
            },
            onunmatch: function(){
            },
            onclick: function(e) {
                $('.ss-gridfield-bulk-action-assign-entity').find('input.bulkSelectAll').prop('checked', '');
            }
        });

        $('input.bulkSelectAll').entwine({
            onmatch: function(){
            },
            onunmatch: function(){
            },
            onclick: function()
            {
                var state = $(this).prop('checked');
                $(this).parents('.ss-gridfield-table')
                    .find('td.col-bulkSelect input')
                    .prop('checked', state)
                    .trigger('change');
            },
            getSelectRecordsID: function()
            {
                return $(this).parents('.ss-gridfield-table')
                    .find('td.col-bulkSelect input:checked')
                    .map(function() {
                        return parseInt( $(this).data('record') )
                    })
                    .get();
            }
        });

    });
})(jQuery);
