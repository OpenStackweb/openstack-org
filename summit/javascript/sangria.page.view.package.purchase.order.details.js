/**
 * Created by smarcet on 5/12/15.
 */



    $(document).ready(function($){

        $('.reject-purchase-order').live('click', function (evt){
            evt.preventDefault();
            evt.stopPropagation();
            var id = $(this).attr('data-purchase-order-id');
            var url = urls.rejectPackagePurchaseOrder.replace('%ID%', id);
            $.ajax({
                type: 'PUT',
                url: url,
                dataType: "json",
                success: function (data,textStatus,jqXHR) {

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    ajaxError( jqXHR, textStatus, errorThrown);
                }
            });
            return false;
        });

        $('.approve-purchase-order').live('click', function (evt){
            evt.preventDefault();
            evt.stopPropagation();
            var id = $(this).attr('data-purchase-order-id');
            var url = urls.approvePackagePurchaseOrder.replace('%ID%', id);
            $.ajax({
                type: 'PUT',
                url: url,
                dataType: "json",
                success: function (data,textStatus,jqXHR) {

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    ajaxError( jqXHR, textStatus, errorThrown);
                }
            });
            return false;
        });
    });
