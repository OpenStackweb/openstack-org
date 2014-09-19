jQuery(document).ready(function($) {

    $('.delete-course').live('click',function (event){
            if(!confirm("Are you sure you want to delete this course?")){
                event.preventDefault();
                return false;
            }
            return true;
    });

});
