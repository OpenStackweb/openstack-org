(function($) {
    $.entwine("ss", function($) {

        $("#Form_ItemEditForm_Channel").entwine({
            onmatch: showUI,
            onchange: showUI
        });

        $('#Form_ItemEditForm_action_save').entwine({
            onclick: function(evt){
                var save = doSave();
                if(save) this._super(evt);
            }
        });

        $('#Form_ItemEditForm_action_doSaveAndQuit').entwine({
            onclick: function(evt){
                var save = doSave();
                if(save) this._super(evt);
            }
        });

        function showUI(){
            showEvents();
            showMembers();
            showGroups();
        }

        function showEvents(){
            if($('#Form_ItemEditForm_Channel').val() == 'EVENT') {
                $('#EventID').show();
                return;
            }
            $('#EventID').hide();
        }

        function showGroups(){
            if($('#Form_ItemEditForm_Channel').val() == 'GROUP') {
                $('#GroupID').show();
                return;
            }
            $('#GroupID').hide();
        }

        function showMembers () {
            if($('#Form_ItemEditForm_Channel').val() == 'MEMBERS') {
                $('#Form_ItemEditForm_Recipients').show();

                return;
            }
            $('#Form_ItemEditForm_Recipients').hide();
        }

        function doSave() {
            var message = $('#Form_ItemEditForm_Message').val();
            if(message == '') return false;
            var channel = $('#Form_ItemEditForm_Channel').val();
            if(channel == '') return false;
            if(channel == 'NONE')
            {
                alert('you should select a valid channel!');
                return false;
            }
            if(channel != 'MEMBERS'){
                return window.confirm('Are you sure?. You would be sending this push notification to several users ( '+channel+' )');
            }
            return true;
        }
    });
})(jQuery);
