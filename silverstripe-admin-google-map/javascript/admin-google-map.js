/**
 * Influenced by: https://github.com/kinglozzer/SilverStripe-GMapsObject/blob/master/javascript/GMapsObject.js
 */

var ss = ss || {};

ss.loadedGoogleMapsApi = false;


(function ($) {


    $.entwine('ss', function ($) {


        $(".admin-google-map").entwine({

            onmatch: function () {

                var script = null;
                var opts = $(this).data("setup");

                if (ss.loadedGoogleMapsApi === false) {

                    script = document.createElement("script");

                    script.type = "text/javascript";

                    script.src = "//maps.googleapis.com/maps/api/js?key=" + opts.key + "&sensor=false&callback=initAdminGoogleMaps";

                    document.body.appendChild(script);

                    ss.loadedGoogleMapsApi = true;

                }
                else {
                    initAdminGoogleMaps();
                }


            }


        });

    });

})(jQuery);


function initAdminGoogleMaps() {

    (function ($) {

        var geocoder = new google.maps.Geocoder();

        $(".admin-google-map").each(function () {

            var $this = $(this);

            var $addressField = null;
            var $zipCodeField = null;
            var $cityField = null;
            var $stateField = null;
            var $countryField = null;
            var $updateMapBtn = null;

            var opts = $this.data("setup");
            var geocodeSearchTimer = null;
            var latField = $("#" + opts.lat_field);
            var lngField = $("#" + opts.lng_field);
            var latVal = latField.val() || opts.start_lat;
            var lngVal = lngField.val() || opts.start_lng;
            var addressVal = "";
            var newAddress = "";
            var latLng = new google.maps.LatLng(latVal, lngVal);
            var mapOptions = {
                center: latLng,
                zoom: opts.zoom
            };
            var map = new google.maps.Map($this[0], mapOptions);
            var marker = new google.maps.Marker({
                position: latLng,
                map: map,
                draggable: true,
                animation: google.maps.Animation.DROP
            });

            map.setCenter(latLng);

            function setLatLng(lat, lng, panMap) {
                var center = new google.maps.LatLng(lat, lng)
                marker.setPosition(center);
                latField.val(lat);
                lngField.val(lng);
                if (panMap !== undefined) {
                    map.panTo(center);
                }
            }

            google.maps.event.addListener(marker, 'dragend', function () {

                var markerPos = marker.getPosition();
                var newLat = markerPos.lat();
                var newLng = markerPos.lng();

                setLatLng(newLat, newLng);

            });

            $('#' + opts.tab + '[aria-hidden="false"]').entwine({
                onmatch: function () {

                    google.maps.event.trigger(map, 'resize');
                    map.panTo(marker.getPosition());

                }
            });

            if (opts.address_field !== undefined) {


                $addressField = $('.cms-edit-form input[name=' + opts.address_field.address1 + ']');
                $zipCodeField = $('.cms-edit-form input[name=' + opts.address_field.zip_code + ']');
                $cityField    = $('.cms-edit-form input[name=' + opts.address_field.city + ']');
                $stateField   = $('.cms-edit-form input[name=' + opts.address_field.state + ']');
                $countryField = $('.cms-edit-form select[name=' + opts.address_field.country + ']');

                if (!$addressField.next().is("button")) {

                    $updateMapBtn = $("<button type='button'>Update map</button>")
                        .insertAfter($addressField)
                        .on("click", function () {

                            newAddress = $addressField.val();
                            if($stateField.val() !== '')
                                newAddress +=', '+ $stateField.val();
                            if($cityField.val() !== '')
                                newAddress +=', '+ $cityField.val();
                            if($countryField.val() !== '')
                                newAddress +=', '+ $countryField.val();

                                geocoder.geocode
                                (
                                    {
                                        'address': newAddress
                                    },
                                    function (results, status)
                                    {
                                        if (status == google.maps.GeocoderStatus.OK) {
                                            if (results && results.length > 0) {
                                                var lat = results[0].geometry.location.lat();
                                                var lng = results[0].geometry.location.lng();

                                                setLatLng(lat, lng, true);
                                            }
                                        }
                                        else
                                        {
                                            alert('Geocode was not successful for the following reason: ' + status);
                                        }
                                    }
                                );
                        });
                }
            }
        });
    })(jQuery);
}