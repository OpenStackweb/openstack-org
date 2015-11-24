
// Sponsor Nav Affix
var num = 425; //number of pixels before modifying styles

$(window).bind('scroll', function () {
    if ($(window).scrollTop() > num) {
        $('.city-nav.sponsor').addClass('fixed');
    } else {
        $('.city-nav.sponsor').removeClass('fixed');
    }
});

// Sponsor active on scroll
$(document).ready(function () {
    $(document).on("scroll", onScroll);
    
    //smoothscroll
    $('a[href^="#"]').on('click', function (e) {
        e.preventDefault();
        $(document).off("scroll");
        
        $('a').each(function () {
            $(this).removeClass('active');
        })
        $(this).addClass('active');
      
        var target = this.hash,
            menu = target;
        $target = $(target);
        $('html, body').stop().animate({
            'scrollTop': $target.offset().top+0
        }, 500, 'swing', function () {
            window.location.hash = target;
            $(document).on("scroll", onScroll);
        });
    });
});

function onScroll(event){
    var scrollPos = $(document).scrollTop();
    $('.city-nav a').each(function () {
        var currLink = $(this);
        var refElement = $(currLink.attr("href"));
        if (refElement.position().top <= scrollPos && refElement.position().top + refElement.outerHeight() > scrollPos) {
            $('.city-nav ul li a').removeClass("active");
            currLink.addClass("active");
        }
        else{
            currLink.removeClass("active");
        }
    });
}

window.onload = function(){

             var pieDataRegion = [];pieDataRegion.push({
                value: 75,
                color: "#29abe3",
                label: "USA / Canada / Mexico"
            });pieDataRegion.push({
                value: 12,
                color: "#d03427",
                label: "Europe"
            });pieDataRegion.push({
                value: 10,
                color: "#294d68",
                label: "APAC"
            });pieDataRegion.push({
                value: 2,
                color: "#5bb6a7",
                label: "Middle East"
            });pieDataRegion.push({
                value: 1,
                color: "#faaf3b",
                label: "Latin America"
            });

             var pieDataRole = [];pieDataRole.push({
                value: 30,
                color: "#28abe2",
                label: "Developer"
            });pieDataRole.push({
                value: 25,
                color: "#cf3327",
                label: "Prdt Strgy, Mgt, Archt"
            });pieDataRole.push({
                value: 16,
                color: "#294d68",
                label: "Oprtr / Usr / Ops SysAdmn "
            });pieDataRole.push({
                value: 13,
                color: "#5cb7a7",
                label: "Biz Dev / Mrktng"
            });pieDataRole.push({
                value: 10,
                color: "#faaf3b",
                label: "CEO / CIO / IT Mgr"
            });pieDataRole.push({
                value: 7,
                color: "#000000",
                label: "Other"
            });

    var helpers = Chart.helpers;
    var attendeesRegion = new Chart(document.getElementById("attendeesRegion").getContext("2d")).Doughnut(pieDataRegion, {
        tooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= value %>%",
        animateRotate: true
    });
    var attendeesRole = new Chart(document.getElementById("attendeesRole").getContext("2d")).Doughnut(pieDataRole, {
        tooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= value %>%",
        animateRotate: true
    });
    var legendHolderRegion = document.createElement('div');
    var legendHolderRole = document.createElement('div');
    legendHolderRegion.innerHTML = attendeesRegion.generateLegend();
    legendHolderRole.innerHTML = attendeesRole.generateLegend();

    // Include a html legend template after the module doughnut itself
    helpers.each(legendHolderRegion.firstChild.childNodes, function (legendNode, index) {
        helpers.addEvent(legendNode, 'mouseover', function () {
            var activeSegment = attendeesRegion.segments[index];
            activeSegment.save();
            activeSegment.fillColor = activeSegment.highlightColor;
            attendeesRegion.showTooltip([activeSegment]);
            activeSegment.restore();
        });
    });
        // Include a html legend template after the module doughnut itself
    helpers.each(legendHolderRole.firstChild.childNodes, function (legendNode, index) {
        helpers.addEvent(legendNode, 'mouseover', function () {
            var activeSegment = attendeesRole.segments[index];
            activeSegment.save();
            activeSegment.fillColor = activeSegment.highlightColor;
            attendeesRole.showTooltip([activeSegment]);
            activeSegment.restore();
        });
    });
    helpers.addEvent(legendHolderRegion.firstChild, 'mouseout', function () {
        attendeesRegion.draw();
    });

    helpers.addEvent(legendHolderRole.firstChild, 'mouseout', function () {
        attendeesRole.draw();
    });

    attendeesRegion.chart.canvas.parentNode.parentNode.appendChild(legendHolderRegion.firstChild);
    attendeesRole.chart.canvas.parentNode.parentNode.appendChild(legendHolderRole.firstChild);
};

