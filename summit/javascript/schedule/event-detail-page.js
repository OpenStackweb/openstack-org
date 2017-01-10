
function addToMySchedule(summit_id,event_id){
    var url = 'api/v1/summits/'+summit_id+'/schedule/'+event_id;

    $.ajax({
        type: 'PUT',
        url:  url,
        contentType: "application/json; charset=utf-8",
        success: function (data) {
            setAdded2MySchedule(event_id);
        }
    });
}

function removeFromMySchedule(summit_id,event_id){
    var url = 'api/v1/summits/'+summit_id+'/schedule/'+event_id;

    $.ajax({
        type: 'DELETE',
        url:  url,
        contentType: "application/json; charset=utf-8",
        success: function (data) {
            setRemovedFromMySchedule(event_id);
        }
    });
}

function setAdded2MySchedule(event_id){
    $('#remove_from_my_schedule_'+event_id).show();
    $('#add_to_my_schedule_'+event_id).hide();
}

function setRemovedFromMySchedule(event_id){
    $('#remove_from_my_schedule_'+event_id).hide();
    $('#add_to_my_schedule_'+event_id).show();
}