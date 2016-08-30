
function addToMySchedule(summit_id,event_id){
    var url = 'api/v1/summits/'+summit_id+'/schedule/'+event_id;

    $.ajax({
        type: 'PUT',
        url:  url,
        contentType: "application/json; charset=utf-8",
        success: function (data) {
            setAdded2MySchedule();
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
            setRemovedFromMySchedule();
        }
    });
}

function setAdded2MySchedule(){
    $('#remove_from_my_schedule').show();
    $('#add_to_my_schedule').hide();
}

function setRemovedFromMySchedule(){
    $('#remove_from_my_schedule').hide();
    $('#add_to_my_schedule').show();
}