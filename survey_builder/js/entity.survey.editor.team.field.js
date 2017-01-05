(function ($) {

    var loadTeamMemberList = function(){

        var entity_survey_id   = $('#ENTITY_SURVEY_ID').val();
        $.ajax(
            {
                type: "GET",
                url: '/api/v1/surveys/entity-surveys/'+entity_survey_id+'/team-members',
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                success: function (data) {

                    if(data.length === 0)
                    {
                        $('#team-members-container').hide();
                        return;
                    }

                    var template = $('<tbody>' +
                        '<tr>'+
                        '<td class="pic"></td>'+
                        '<td class="fname"></td>'+
                        '<td class="lname"></td>'+
                        '<td><a class="delete-team-member-btn" data-member-id="$ID" href="#">Delete</a></td>'+
                        '</tr>'+
                        '</tbody>');

                    var directives = {
                        'tr':{
                            'member<-context':{
                                '.fname':'member.fname',
                                '.pic'  :function(arg){
                                    var pic_url = arg.item.pic_url;
                                    if(pic_url !== ''){
                                        return '<img width="50" height="50" src="'+pic_url+'"/>';
                                    }
                                    return '';
                                },
                                '.lname':'member.lname',
                                '.delete-team-member-btn@data-member-id':'member.id'
                            }
                        }
                    };

                    var body = $('#team-members-body');
                    var html = template.render(data, directives);
                    body.html(html.html());
                    $('#team-members-container').show();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert( "Request failed: " + textStatus );
                }
            }
        );
    };

    $(document).ready(function() {

        var $auto_complete = $("#txt_autocomplete_member");

        $('.add-team-member-btn').click(function(event){
            $('#TeamModal').modal('toggle');
            event.preventDefault();
            return false;
        });

        $('.select-team-member-btn').click(function(event)
        {
           var new_team_member_id = $('#new-team-member-id').val();
           var entity_survey_id   = $('#ENTITY_SURVEY_ID').val();

           if(new_team_member_id === '')
           {
               alert('You must select a valid member!');
               return false;
           }
           // clean selection
           $("#txt_autocomplete_member").val('');
           $('#new-team-member-id').val('');

            $.ajax(
                {
                    type: "POST",
                    url: '/api/v1/surveys/entity-surveys/'+entity_survey_id+'/team-members/'+new_team_member_id,
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function () {
                        loadTeamMemberList();
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        if(jqXHR.status === 401)
                        {
                            alert(jqXHR.responseText);
                            return;
                        }
                        alert( "Request failed: " + textStatus );
                    }
                }
            );

           return false;
        });

        $('.delete-team-member-btn').live('click', function(event){
            event.preventDefault();
            if(window.confirm('Are you sure?'))
            {
                var btn              = $(this);
                var member_id        = btn.attr('data-member-id');
                var entity_survey_id = $('#ENTITY_SURVEY_ID').val();

                $.ajax(
                    {
                        type: "DELETE",
                        url: '/api/v1/surveys/entity-surveys/'+entity_survey_id+'/team-members/'+member_id,
                        dataType: "json",
                        success: function () {
                            var tr = btn.parent().parent();
                            tr.fadeTo("slow",0.7, function(){
                                $(this).remove();
                                var body = $('#team-members-body');
                                //check # rows
                                if(body.children().length === 0)
                                {
                                    $('#team-members-container').hide();
                                }
                            })
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            alert( "Request failed: " + textStatus );
                        }
                    }
                );
            }
            return false;
        });

        $auto_complete.autocomplete({
            source: '/api/v1/surveys/team-members/suggest',
            minLength: 2,
            select: function( event, ui )
            {
                if(ui.item){
                    $('#new-team-member-id').val(ui.item.id);
                }
                else
                {
                    $('#new-team-member-id').val('');
                }
            },
            appendTo: "#formSearchTeamMember",
            response: function( event, ui ) {
                $('#new-team-member-id').val('');
            }
        });
    });

})(jQuery);