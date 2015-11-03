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
                        '<td><button class="btn btn-danger active btn-sm delete-team-member" data-member-id="$ID">Delete</button></td>'+
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
                                '.delete-team-member@data-member-id':'member.id'
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

        var $auto_complete = $(".ss-member-autocomplete-field");
        var url    = $('.entity_survey_editors_team_container').attr('data-ss-member-field-suggest-url');

        $('#add-new-member').click(function(event)
        {
           var new_team_member_id = $('#new-team-member-id').val();
           var entity_survey_id   = $('#ENTITY_SURVEY_ID').val();

           if(new_team_member_id === '')
           {
               alert('You must select a valid member!');
               return false;
           }
           // clean selection
           $(".ss-member-autocomplete-field").val('');
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

        $('.delete-team-member').live('click', function(event){
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
            response: function( event, ui ) {
                $('#new-team-member-id').val('');
            }
        });
    });

})(jQuery);