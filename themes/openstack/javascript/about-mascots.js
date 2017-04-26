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

function copyToClipboard(filename) {
    var temp = $('<input>');
    $("body").append(temp);
    temp.val('<img src="' + filename + '" />').select();
    document.execCommand("copy");
    temp.remove();
}

jQuery(document).ready(function($) {

    $('body').filetracking();

    // need this for copy to clipboard to work
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};

    $(document).on('click', '.outbound-link', function(event){
        var href = $(this).attr('href');
        recordOutboundLink(this,'Outbound Links',href);
        event.preventDefault();
        event.stopPropagation()
        return false;
    });

    $('#mascots_modal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var component = button.data('component');
        var mascot_images = button.data('images').split(',');
        var modal = $(this);
        var body_html = '<div class="row mascot_row">';

        for (var i in mascot_images) {
            var filename = mascot_images[i];
            var extension = filename.split('.')[1];
            var image_file = mascots_dir + component + '/' + filename;
            var thumb = (extension == 'eps') ? mascots_dir + 'eps_thumb.png' : image_file;

            body_html += '<div class="col-md-4"><div class="row mascot_box">';
            body_html += '<div class="col-md-12 mascot_image_box"><img class="mascot_image" src="'+thumb+'" title="'+filename+'" /></div>';
            body_html += '<div class="col-md-12">';
            body_html += '<a href="'+image_file+'" class="btn btn-primary btn-xs download_link" target="_blank">Download</a>';
            body_html += '<button type="button" class="btn btn-default btn-xs" onclick="copyToClipboard(\''+image_file+'\')">Copy Code</button>';
            body_html += '</div></div></div>';

            if ( (i+1) % 3 == 0) {
                body_html += '</div><div class="row mascot_row">';
            }
        }

        body_html += '</div>';


        modal.find('.modal-title').text(component + ' Mascot');
        modal.find('.modal-body').html(body_html);
    })

});