/**
 * Copyright 2016 OpenStack Foundation
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
(function( $ ){

    $(function(){

        $.validator.addMethod("time12", function(value, element, param) {
            var time = value.split(' ');
            if(time.length !== 2) return false;
            var designator = time[1];
            if(designator !== 'AM' && designator !== 'PM' ) return false;
            var time_parts = time[0].split(':');
            if(time_parts.length !== 3) return false;
            if (time_parts[0] > 12 || time_parts[1] > 59 || time_parts[2] > 59) return false;
            return true;
        }, 'Enter a valid time format (hh:mm:ss AM/PM).');

        $.validator.addMethod("datetimeGreaterThan", function(value, element, param) {
            console.log(param);
            var params = param.split(',');
            var start_date = value;
            var start_time = $('#'+params[0]).val();
            var end_date   = $('#'+params[1]).val();
            var end_time   = $('#'+params[2]).val();
            console.log(' start_date '+start_date+' start_time '+start_time+' end_date '+end_date+' end_time '+end_time);

            start_date = moment(start_date+', '+start_time, "YYYY-MM-DD, h:mm:ss a");
            end_date   = moment(end_date+', '+end_time, "YYYY-MM-DD, h:mm:ss a");
            return start_date.isBefore(end_date);
        }, 'Start DateTime value should be before than End DateTime.');

        $.validator.setDefaults({
            ignore: [],
            // any other default options and/or rules
        });

        var validator = $('#events-form').validate({
            ignore:[],
            errorClass:'error',
            errorPlacement: function(error, element) {
                var position = $(element).offset();
                $(error).removeClass('error');
                $(error).addClass('alert');
                $(error).addClass('alert-danger');
                $(error).css('position', 'absolute');
                $(error).css('top', (position.top + 30)+'px');
                $(error).css('left', position.left+'px');
                $('body').append(error);
                $(error).fadeOut(5000);
            },
            errorElement: "div",
            focusCleanup: true,
        });

        $('.clockpicker').clockpicker({
            placement: 'bottom',
            align: 'left',
            autoclose: true,
            'default': 'now',
            twelvehour:  true,
        });

        $('#location_all').change(function(evt)
        {
            var location_id = $(this).val();
            $('.location').val(location_id);
        })

        $('#start_date_all').change(function(evt){
            $('.start-date').val($(this).val());
        });

        $('#end_date_all').change(function(evt){
            $('.end-date').val($(this).val());
        });

        $('#apply_changes').click(function(evt){
            evt.preventDefault();
            swal({
                    title: "Are you sure?",
                    text: "You will be APPLYING all this changes!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, go for it!",
                    closeOnConfirm: true,
                    allowEscapeKey: false,
                },
                function(isConfirm){

                    if (isConfirm) {
                        $('#validation_errors').empty();
                        if(!$('#events-form').valid()) return;

                        $('.btn-apply-bulk-action').attr('disabled','disabled');

                        var summit_id = $('#summit_id').val();

                        var request = {
                            events : getEvents(),
                            conflicts : [],
                        };
                        $('body').ajax_loader();
                        $.ajax({
                            type: 'PUT',
                            url: 'api/v1/summits/'+summit_id+'/events/bulk',
                            data: JSON.stringify(request),
                            contentType: "application/json; charset=utf-8",
                            dataType: "json"
                        }).done(function(saved_event) {
                            $('body').ajax_loader('stop');
                            $('.btn-apply-bulk-action').removeAttr('disabled');
                            swal("Updated!", "Events were updated successfully.", "success");
                        }).fail(function(jqXHR) {
                            $('body').ajax_loader('stop');
                            $('.btn-apply-bulk-action').removeAttr('disabled');
                            var responseCode = jqXHR.status;
                            if(responseCode == 412) {
                                var response = $.parseJSON(jqXHR.responseText);
                                swal('Validation error', response.messages[0].message, 'warning');
                                return;
                            }
                            swal('Error', 'There was a problem saving the events, please contact admin.', 'warning');
                        });
                    }
                }
            );
            return false;
        });

        $('#apply_changes_publish').click(function(evt){
            evt.preventDefault();
            swal({
                    title: "Are you sure?",
                    text: "You will be APPLYING & PUBLISHING all this changes!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, go for it!",
                    closeOnConfirm: true,
                    allowEscapeKey: false,
                },
                function(isConfirm){

                    if (isConfirm) {
                        $('#validation_errors').empty();
                        if(!$('#events-form').valid()) return;

                        $('.btn-apply-bulk-action').attr('disabled','disabled');

                        var summit_id = $('#summit_id').val();

                        var request = {
                          events : getEvents(),
                          conflicts : [],
                        };
                        $('body').ajax_loader();
                        $.ajax({
                            type: 'PUT',
                            url: 'api/v1/summits/'+summit_id+'/events/publish/bulk',
                            data: JSON.stringify(request),
                            contentType: "application/json; charset=utf-8",
                            dataType: "json"
                        }).done(function(saved_event) {
                            $('body').ajax_loader('stop');
                            $('.btn-apply-bulk-action').removeAttr('disabled');
                            swal("Updated!", "Events were updated and published successfully.", "success");
                        }).fail(function(jqXHR) {
                            $('body').ajax_loader('stop');
                            $('.btn-apply-bulk-action').removeAttr('disabled');
                            var responseCode = jqXHR.status;
                            if(responseCode == 412) {
                                var response = $.parseJSON(jqXHR.responseText);
                                swal('Validation error', response.messages[0].message, 'warning');
                                return;
                            }
                            swal('Error', 'There was a problem saving the events, please contact admin.', 'warning');
                        });
                    }
                }
            );
            return false;
        });


        function getEvents()
        {
            var events = [];
            $('.row-event').each(function(){
                events.push({
                    id          : parseInt($(this).attr('data-event-id')),
                    location_id : parseInt($('.location', $(this)).val()),
                    start_date  : $('.start-date', $(this)).val(),
                    start_time  : $('.start-time', $(this)).val(),
                    end_date    : $('.end-date', $(this)).val(),
                    end_time    : $('.end-time', $(this)).val()
                })
            });
            return events;
        }

    });
// End of closure.
}( jQuery ));

