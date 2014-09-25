jQuery(document).ready(function($){

    //hide job descriptions
    $('.jobDescription').hide();

    if(document.location.hash) {
        $('#' + document.location.hash.substring(1) + '.jobPosting div.jobDescription').slideDown();
    }

    // toggles the job descriptions
    $('a.jobTitle').click(function() {
        $(this).closest('div.jobPosting').find('div.jobDescription').slideToggle(400);
        return false;
    });


    setInterval(refresh_jobs,120000);

})

function refresh_jobs() {
    jQuery.ajax({
        type: "POST",
        url: 'JobHolder_Controller/AjaxDateSortedJobs',
        success: function(result){
            jQuery('.job_list').html(result);
            jQuery('.jobDescription').hide();
        }
    });
}