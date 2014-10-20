jQuery(document).ready(function($){

    //hide job descriptions
    $('.jobDescription').hide();

    if(document.location.hash) {
        $('#' + document.location.hash.substring(1) + '.jobPosting div.jobDescription').slideDown();
    }

    // toggles the job descriptions
    $('a.jobTitle').live('click',function() {
        $(this).closest('div.jobPosting').find('div.jobDescription').slideToggle(400);
        return false;
    });


    setInterval(refresh_jobs,60000);

})
var xhr = null;
function refresh_jobs() {
    if(xhr!=null) return;
    xhr = jQuery.ajax({
        type: "POST",
        url: 'JobHolder_Controller/AjaxDateSortedJobs',
        success: function(result){
            jQuery('.jobPosting','.job_list').remove();
            jQuery('.job_list').append(result);
            jQuery('.jobDescription').hide();
            xhr = null;
        }
    });
}