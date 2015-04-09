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

    $("#MarketPlaceReviewForm_MarketPlaceReviewForm_rating").rating({size:'xs',showCaption:false,showClear:false});

    $("#write_review").click(function(){
        $(".review_form_div").show();
        $(".review_list_div").hide();
    });

    $("#read_reviews").click(function(){
        $(".review_form_div").hide();
        $(".review_list_div").show();
    });

    if ($("#MarketPlaceReviewForm_MarketPlaceReviewForm_logged_in").val()) {
        $('.login_overlay').hide();
    } else {
        $('.login_overlay').show();
        $(".review_form_div").hide();
        $(".review_list_div").show();
    }


    // SAVE REVIEW
    var form_id  = 'MarketPlaceReviewForm_MarketPlaceReviewForm';
    var form     = $('#'+form_id);

    form.submit(function( event ) {
        event.preventDefault();

        if(!form.valid()) return false;
        var security_id = $('#'+form_id+'_SecurityID',form).val();
        var url     = 'api/v1/marketplace/reviews?SecurityID='+security_id;
        var request = {
            product_id : $('#'+form_id+'_product',form).val(),
            title   :  $('#'+form_id+'_title',form).val(),
            rating  :  $('#'+form_id+'_rating',form).val(),
            comment : $('#'+form_id+'_comment',form).val(),
            field_98438688 : $('#'+form_id+'_field_98438688',form).val()
        };

        $.ajax({
            type: 'POST',
            url: url,
            data: JSON.stringify(request),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function (data,textStatus,jqXHR) {
                $('.success_overlay').fadeIn();
                setTimeout(function(){$('.success_overlay').fadeOut()},3000);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                ajaxError(jqXHR, textStatus, errorThrown);
            }
        });
    });



});