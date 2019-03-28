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
// Hero Credit Tooltip
$(function() {
    $('.hero-credit').tooltip();
});

// Customer Stories, this should be improved
$(function() {
    $("#chinamobile-logo").on("mouseenter", function() {
        $(".change-description").text("China Mobile’s telecom network has more than 800 million subscribers and 3 million base stations.");
        $(".customer-logos.logo-hover").removeClass("logo-hover");
        $(this).toggleClass("logo-hover");
    });
});
$(function() {
    $("#target-logo").on("mouseenter", function() {
        $(".change-description").text("One of the largest retailers in the US, Target customizes pieces of OpenStack to pull updates and backport patches more rapidly.");
        $(".customer-logos.logo-hover").removeClass("logo-hover");
        $(this).toggleClass("logo-hover");
    });
});
$(function() {
    $("#progressive-logo").on("mouseenter", function() {
        $(".change-description").text("Progressive Insurance leveraged OpenStack's open APIs to drastically improve data analytics to provide usage-based insurance to its customers.");
        $(".customer-logos.logo-hover").removeClass("logo-hover");
        $(this).toggleClass("logo-hover");
    });
});
$(function() {
    $("#cathay-logo").on("mouseenter", function() {
        $(".change-description").text("Leading airline Cathay Pacific transformed its legacy infrastructure into a modern hybrid cloud architecture using Red Hat OpenStack Platform.");
        $(".customer-logos.logo-hover").removeClass("logo-hover");
        $(this).toggleClass("logo-hover");
    });
});
$(function() {
    $("#paypal-logo").on("mouseenter", function() {
        $(".change-description").text("PayPal delivers features faster with their OpenStack private cloud.");
        $(".customer-logos.logo-hover").removeClass("logo-hover");
        $(this).toggleClass("logo-hover");
    });
});
$(function() {
    $("#wells-logo").on("mouseenter", function() {
        $(".change-description").text("The world’s most valuable bank relies on OpenStack.");
        $(".customer-logos.logo-hover").removeClass("logo-hover");
        $(this).toggleClass("logo-hover");
    });
});