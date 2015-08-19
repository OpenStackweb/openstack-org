(function ($) {

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
                    url: '/surveys/entity-surveys/'+entity_survey_id+'/team-members/'+new_team_member_id,
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    complete: function (jqXHR,textStatus) {

                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        alert( "Request failed: " + textStatus );
                    }
                }
            );

           return false;
        });

        $('.delete-team-member').live('click', function(event){

            if(window.confirm('Are you sure?'))
            {
                var member_id = $(this).attr('data-member-id');
                var entity_survey_id   = $('#ENTITY_SURVEY_ID').val();

                $.ajax(
                    {
                        type: "DELETE",
                        url: '/surveys/entity-surveys/'+entity_survey_id+'/team-members/'+member_id,
                        dataType: "json",
                        complete: function (jqXHR,textStatus) {

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
            source: 'surveys/team-members/suggest',
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