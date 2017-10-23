/**
 * Copyright 2017 Openstack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/
jQuery(document).ready(function($){

    var form = $("#book_form");
    if(form.length > 0){
        if(typeof(book)!=='undefined'){
            $("#id",form).val(book.id);
            $("#company_id",form).val(book.company_id);
            $("#company_id").trigger("chosen:updated");
            $("#title",form).val(book.title);
            $("#link",form).val(book.link);
            $("#description",form).val(book.description);
            $("#image_preview",form).attr('src', book.image_url);

            for(i in book.authors){
                addAuthor(book.authors[i]);
            }
        }
    }


    $('#add-new-author').click(function(event){
        var first_name = $('#add_author_first').val();
        var last_name = $('#add_author_last').val();
        event.preventDefault();
        event.stopPropagation();

        if (first_name && last_name){
            var new_author = {};
            new_author.first_name = first_name;
            new_author.last_name = last_name;
            new_author.id   = 0;
            addAuthor(new_author);
            $('#add_author_first').val('');
            $('#add_author_last').val('');
        }
        return false;
    });

    $(document).on('click',".remove-author", function(event){
        $(this).parent().parent().remove();
        event.preventDefault();
        event.stopPropagation();
        return false;
    });

    $('.save-book').click(function(event){
        event.preventDefault();
        event.stopPropagation();
        var button =  $(this);
        if(button.prop('disabled')){
            return false;
        }

        ajaxIndicatorStart('saving data.. please wait..');

        var authors = [];
        var rows = $("tbody > tr",'#authors-table');
        for(var i=0;i<rows.length-1;i++){
            var author = {};
            author.first_name = $('input.author-firstname',rows[i]).val();
            author.last_name = $('input.author-lastname',rows[i]).val();
            authors.push(author);
        }

        var book = {
            id          : parseInt($("#id",form).val()),
            title       : $("#title",form).val().trim(),
            company_id  : parseInt($("#company_id",form).val()),
            link        : $("#link",form).val().trim(),
            description : $("#description",form).val().trim(),
            authors    : authors,
        }

        $('.save-book').prop('disabled',true);
        var type   = book.id > 0 ?'PUT':'POST';

        $.ajax({
            type: type,
            url: 'api/v1/marketplace/books',
            data: JSON.stringify(book),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function (book_id,textStatus,jqXHR) {
                if($('#image').val()){
                    uploadAttachment(book_id);
                    return;
                }

                finishEventSaveOrUpdate();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                ajaxIndicatorStop();
                $('.save-book').prop('disabled',false);
                ajaxError(jqXHR, textStatus, errorThrown);
            }
        });

    });



});

function finishEventSaveOrUpdate()
{
    $('.save-book').prop('disabled',false);
    //window.location = listing_url;
    ajaxIndicatorStop();
}

function uploadAttachment(book_id)
{
    var summit_id  = $('#summit_id').val();
    var url        = 'api/v1/marketplace/books/'+book_id+'/attach';
    var file_data  = $("#image").prop("files")[0];
    var form_data  = new FormData();

    form_data.append("file", file_data);

    if ($('#image').val()) {
        $.ajax({
            url: url,
            dataType: 'JSON',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'POST',
            success: function(attachment_id){
                finishEventSaveOrUpdate();
            },
            error: function(response,status,error) {
                swal('Validation error', response.responseJSON.messages[0].message, 'warning');
            }
        });
    }
}

function addAuthor(new_author){
    var rows_number = $("tbody > tr",'#authors-table').length;

    var row_template = $('<tr><td style="border: 1px solid #ccc;width:30%;background:#fff;">' +
        '<input type="text" style="width:300px;" class="author-firstname text autocompleteoff"></td>' +
        '<th style="border: 1px solid #ccc;width:30%;background:#fff;">' +
        '<input type="text" style="width:300px;" class="author-lastname text autocompleteoff"></td>' +
        '<td style="border: 1px solid #ccc;background:#eaeaea;width:10%;color:#cc0000;">' +
        '<a href="#" class="remove-author">x&nbsp;Remove</a></td></tr>>');

    var directives = {
        'input.author-firstname@value':'first_name',
        'input.author-lastname@value':'last_name',
        'input.author-firstname@id'   : function(arg){ return 'author-firstname_'+(rows_number);},
        'input.author-lastname@id'   : function(arg){ return 'author-lastname_'+(rows_number);},
        'input.author-firstname@name'   : function(arg){ return 'author-firstname_'+(rows_number);},
        'input.author-lastname@name'   : function(arg){ return 'author-lastname_'+(rows_number);}
    };
    var html = row_template.render(new_author, directives);
    $(".add-authors",'#authors-table').before(html);

}
