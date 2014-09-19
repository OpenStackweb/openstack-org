jQuery(document).ready(function($){

    $('#add-new-product').click(function(event){
        event.preventDefault();
        event.stopPropagation();
        var marketplace_type_id = $("#marketplace_type_id").val();
        if(marketplace_type_id!=''){
            window.location = add_link+"?type_id="+marketplace_type_id;
        }
        return false;
    });

});
