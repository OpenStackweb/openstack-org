jQuery(document).ready(function($){
    $( "#slider_sortable, #featured_sortable" ).sortable({
        items: "li:not(.placeholder_empty)",
        connectWith: ".connected",
        revert: false,
        placeholder: "placeholder",
        over: function(event,ui) {
            if (ui.sender) {
                $(".placeholder_empty",this).first().before(ui.placeholder);
                $(ui.placeholder).hide();
            }
        },
        remove: function(event,ui) {
            $(this).append('<li class="placeholder_empty">Drop<br> here</li>');
        },
        update: function(event,ui) {
            if (ui.sender) {
                if ($(".placeholder_empty",this).length == 0) {
                    $(ui.sender).sortable('cancel');
                    $(".placeholder_empty",ui.sender).first().remove();
                } else {
                    $(".placeholder_empty",this).first().remove();
                    saveSortArticle(ui.item,true);
                }
            } else {
                saveSortArticle(ui.item,false);
            }
        }
    }).disableSelection();

    $( "#recent_sortable, #standby_sortable" ).sortable({
        connectWith: ".connected",
        revert: false,
        placeholder: "placeholder",
        update: function(event,ui) {
            var is_new = (ui.sender);
            saveSortArticle(ui.item,is_new);
        }
    }).disableSelection();

});

function saveSortArticle(item,is_new) {
    var old_rank = $('.article_rank',item).val();
    var new_rank = item.index() + 1;
    var article_id = $('.article_id',item).val();
    var article_type = $('.article_type',item).val();
    var target = $(item).parents('ul').attr('id').split('_')[0];

    is_new = (is_new) ? 1 : 0;

    $.ajax({
        type: "POST",
        url: 'NewsAdminPage_Controller/setArticleRank',
        data: { id : article_id, old_rank : old_rank, new_rank : new_rank, type : article_type, target: target, is_new : is_new },
        success: function(){ //update ranks
            if (article_type == target) {
                $('.article_rank','#'+article_type+'_sortable').each(function(index){
                    $(this).val(index+1);
                });
            } else {
                $('.article_type',item).val(target);
                $('.article_rank','#'+target+'_sortable').each(function(index){
                    $(this).val(index+1);
                });
            }
        }
    });
}

