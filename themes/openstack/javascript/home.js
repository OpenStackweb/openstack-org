// Hero Credit Tooltip
$('.hero-credit').tooltip()

// Customer Stories, this should be improved
$(function() {
    $("#bloomberg-logo").hover(function() {
        $(".change-description").text("Bloomberg uses OpenStack for some really cool things.");
        $(".customer-logos.logo-hover").removeClass("logo-hover");
        $(this).toggleClass("logo-hover");
    });
});
$(function() {
    $("#bestbuy-logo").hover(function() {
        $(".change-description").text("BestBuy is pretty awesome and uses OpenStack in their stores.");
        $(".customer-logos.logo-hover").removeClass("logo-hover");
        $(this).toggleClass("logo-hover");
    });
});
$(function() {
    $("#sony-logo").hover(function() {
        $(".change-description").text("Sony's PS4 online network is run by OpenStack, allowing thousands to connect.");
        $(".customer-logos.logo-hover").removeClass("logo-hover");
        $(this).toggleClass("logo-hover");
    });
});
$(function() {
    $("#comcast-logo").hover(function() {
        $(".change-description").text("Comcast is using OpenStack to provide real-time programming guides and fast program search.");
        $(".customer-logos.logo-hover").removeClass("logo-hover");
        $(this).toggleClass("logo-hover");
    });
});
$(function() {
    $("#paypal-logo").hover(function() {
        $(".change-description").text("PayPal uses OpenStack to run thousands of racks and so many other things too.");
        $(".customer-logos.logo-hover").removeClass("logo-hover");
        $(this).toggleClass("logo-hover");
    });
});
$(function() {
    $("#wells-logo").hover(function() {
        $(".change-description").text("Wells Fargo built online versions of heaven with all of the clouds they connected with OpenStack");
        $(".customer-logos.logo-hover").removeClass("logo-hover");
        $(this).toggleClass("logo-hover");
    });
});