(function($) {
    $.entwine("ss", function($) {

        $(".wipe-device-data").entwine({
            onclick: function() {
                var $parent     = $(this).parents('.ss-gridfield-wipe-device-data-class');
                var link        = this.data("href");
                var action      = $parent.find('select.select-wipe-action:first').val();
                var $btn        = $(this);

                if(action === 'wipe-user')
                {
                    // open a model to enter the member
                    var dialog = $("div.modal-wipe-user-device");
                    dialog.dialog( "open" );
                    return false;
                }
                var url         = link.replace("{ActionID}", action);
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

        $("div.modal-wipe-user-device").entwine({
            onmatch: function() {
                // init modal form

                $("#attendee").autocomplete({
                    source: $("#attendee").attr('data-href'),
                    minLength: 2,
                    select: function( event, ui ) {
                        $("#attendee").attr('attendee-id',ui.item.id );
                    }
                });

                var dialog = $(this).dialog({
                    autoOpen: false,
                    height: 150,
                    width: 350,
                    modal: true,
                    buttons: {
                        "Wipe User Device Data": wipeUserDeviceData,
                        Cancel: function() {
                            dialog.dialog( "close" );
                        }
                    },
                    close: function() {
                        $("#attendee").attr('attendee-id', '');
                        $("#attendee").val('');
                    }
                });

               function wipeUserDeviceData() {
                    event.preventDefault();
                    event.stopPropagation();

                    var link        = $(this).attr("data-href");;
                    var url         = link.replace("{ActionID}", 'wipe-user');
                    var attendee_id = $("#attendee").attr('attendee-id');

                    if(typeof(attendee_id) === 'undefined') {
                        alert('you must select a valid attendee!');
                        return false;
                    }
                    $.ajax({
                        url: url+'&attendee_id='+attendee_id,
                        type: "POST",
                        context: $(this)
                    }).done(function(data, textStatus, jqXHR) {
                        $("#attendee").attr('attendee-id', '');
                        $("#attendee").val('');
                        $('div.modal-wipe-user-device').dialog( "close" );
                        this.getGridField().reload();
                    });
                    return false;
               };
            }
        });

        $("select.select-wipe-action").entwine({
            onadd: function() {
                this.update();
            },
            onchange: function() {
                this.update();
            },
            update: function() {
                var $parent  = $(this).parents('.ss-gridfield-wipe-device-data-class');
                var btn      = $('.wipe-device-data', $parent);
                if(this.val() && this.val().length) {
                    btn.button("enable");
                } else {
                    btn.button("disable");
                }
            }
        });

    });
})(jQuery);
