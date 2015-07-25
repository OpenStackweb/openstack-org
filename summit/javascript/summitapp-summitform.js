/**
 * Copyright 2014 Openstack Foundation
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

    $('.edit','#summittype_table').click(function(){
        editSummitType($(this));
    });

    $('.update','#summittype_table').click(function(){
        updateSummitType($(this));
    });

    $('.save','#summittype_table').click(function(){
        var summit_id = $('#summitid').val();
        var parent_tr = $(this).parents('tr').first();
        var request = {
            title: $('#new_title').val(),
            description: $('#new_description').val(),
            audience: $('#new_audience').val(),
            start_date: $('#new_start_date').val(),
            end_date: $('#new_end_date').val()
        };

        $.ajax({
            type: 'PUT',
            url: 'api/v1/summitapp/'+summit_id+'/save-summittype',
            data: JSON.stringify(request),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function (id) {
                var html =  '<tr data-summittypeid="'+id+'">';
                html     += '<td class="title">'+request.title+'</td>';
                html     += '<td class="description">'+request.description+'</td>';
                html     += '<td class="audience">'+request.audience+'</td>';
                html     += '<td class="start_date">'+request.start_date+'</td>';
                html     += '<td class="end_date">'+request.end_date+'</td>';
                html     += '<td class="center_text buttons">';
                html     += '<span class="edit glyphicon glyphicon-pencil"></span>';
                html     += '<span class="delete glyphicon glyphicon-trash"></span>';
                html     += '<span style="display:none;" class="update glyphicon glyphicon-ok"></span>';
                html     += '</td></tr>';

                parent_tr.before(html);

                $('#new_title').val('');
                $('#new_description').val('');
                $('#new_audience').val('');
                $('#new_start_date').val('');
                $('#new_end_date').val('');

                $('.edit','#summittype_table').click(function(){
                    editSummitType($(this));
                });

                $('.update','#summittype_table').click(function(){
                    updateSummitType($(this));
                });
            },
            error: function (jqXHR, textStatus, errorThrown) {
                ajaxError(jqXHR, textStatus, errorThrown);
            }
        });
    });
});

function editSummitType(caller) {
    var parent_tr = caller.parents('tr').first();
    $('td',parent_tr).not('.buttons').each(function(){
        var value = $(this).html();
        $(this).html('<input value="'+value+'" />');
    });
    $('.delete',parent_tr).hide();
    $('.edit',parent_tr).hide();
    $('.update',parent_tr).show();
}

function updateSummitType(caller) {
    var parent_tr = caller.parents('tr').first();
    var summit_id = $('#summitid').val();
    var request = {
        summittype_id: parent_tr.data('summittypeid'),
        title: $('.title',parent_tr).find('input').val(),
        description: $('.description',parent_tr).find('input').val(),
        audience: $('.audience',parent_tr).find('input').val(),
        start_date: $('.start_date',parent_tr).find('input').val(),
        end_date: $('.end_date',parent_tr).find('input').val()
    };

    $.ajax({
        type: 'PUT',
        url: 'api/v1/summitapp/'+summit_id+'/save-summittype',
        data: JSON.stringify(request),
        contentType: "application/json; charset=utf-8",
        success: function (data) {
            $('td',parent_tr).not('.buttons').each(function(){
                var value = $(this).find('input').val();
                $(this).html(value);
            });
            $('.delete',parent_tr).show();
            $('.edit',parent_tr).show();
            $('.update',parent_tr).hide();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            ajaxError(jqXHR, textStatus, errorThrown);
        }
    });
}

