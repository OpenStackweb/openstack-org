// Hero Credit Tooltip
$('.hero-credit').tooltip()

// Customer Stories, this should be improved
$(function() {
    $("#bloomberg-logo").hover(function() {
        $(".change-description").text("The world relies on Bloomberg for billions of financial data points per day.");
        $(".customer-logos.logo-hover").removeClass("logo-hover");
        $(this).toggleClass("logo-hover");
    });
});
$(function() {
    $("#bestbuy-logo").hover(function() {
        $(".change-description").text("Development teams at BestBuy rely on OpenStack to continuously deploy new features.");
        $(".customer-logos.logo-hover").removeClass("logo-hover");
        $(this).toggleClass("logo-hover");
    });
});
$(function() {
    $("#sony-logo").hover(function() {
        $(".change-description").text("Sony relies on Openstack to deliver connected gaming experiences to millions of gamers.");
        $(".customer-logos.logo-hover").removeClass("logo-hover");
        $(this).toggleClass("logo-hover");
    });
});
$(function() {
    $("#comcast-logo").hover(function() {
        $(".change-description").text("Comcast delivers interactive entertainment to millions of living rooms.");
        $(".customer-logos.logo-hover").removeClass("logo-hover");
        $(this).toggleClass("logo-hover");
    });
});
$(function() {
    $("#paypal-logo").hover(function() {
        $(".change-description").text("PayPal delivers features faster with their OpenStack private cloud.");
        $(".customer-logos.logo-hover").removeClass("logo-hover");
        $(this).toggleClass("logo-hover");
    });
});
$(function() {
    $("#wells-logo").hover(function() {
        $(".change-description").text("The world’s most valuable bank relies on OpenStack.");
        $(".customer-logos.logo-hover").removeClass("logo-hover");
        $(this).toggleClass("logo-hover");
    });
});