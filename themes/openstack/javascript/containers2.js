var slideArray = [
    {
        name: '#introduction',
        desc: 'Introduction',
        active: true
    },
    {
        name: '#high-level',
        desc: "I. A High Level View of Containers in OpenStack",
        active: false
    },
    {
        name: "#integration-points",
        desc: "II. OpenStack Container Integration Points",
        active: false
    },
    {
        name: "#case-studies",
        desc: "III. Case Studies",
        active: false
    },
    {
        name: "#proyect-index",
        desc: "IV. Open Source Project Index",
        active: false
    },
    {
        name: "#authors",
        desc: "V. Authors",
        active: false
    }
];


// console.log(slideArray);

function getActiveSlide() {
    return slideArray.find(function (e) {
        return (e.active);
    });
}

function getIndex(slide) {
    return slideArray.indexOf(slide);
}

function getSlideByName(hash) {
    return slideArray.find(function (e) {
        return e.name === hash;
    });
}

function getNextSlide() {
    var active = getActiveSlide();
    var activeIndex = getIndex(active);

    if (slideArray[activeIndex + 1])
        return slideArray[activeIndex + 1]

    return slideArray[0];
}

function getPrevSlide() {
    var active = getActiveSlide();
    var activeIndex = getIndex(active);

    if (slideArray[activeIndex - 1])
        return slideArray[activeIndex - 1]

    return slideArray[length - 1];
}

function updateDropdown(slide) {
    $('#dropdownMenu1').text(slide.desc);
}

function setArrows(slide) {
    var activeIndex = getIndex(slide);

    //console.log(activeIndex, slideArray.length - 1)

    // First
    if (activeIndex == 0) {
        $('#btnPrv').addClass('btn-link');
        $('#btnNxt').removeClass('btn-link');
        $('#btnPrv').prop('disabled', true);
        $('#btnNxt').prop('disabled', false);
        return
    }

    // Last 
    if (activeIndex == slideArray.length - 1) {
        $('#btnPrv').removeClass('btn-link');
        $('#btnNxt').addClass('btn-link');
        $('#btnPrv').prop('disabled', false);
        $('#btnNxt').prop('disabled', true);
        return
    }

    $('#btnPrv').removeClass('btn-link');
    $('#btnNxt').removeClass('btn-link');
    $('#btnPrv').prop('disabled', false);
    $('#btnNxt').prop('disabled', false);
    return
}

function hideSlide(slide) {
    var slideIndex = getIndex(slide);

    slideArray.find(function (e) {
        if (e.name === slide.name)
            e.active = false;
    });

    $(slideArray[slideIndex].name).removeClass("show");
    $(slideArray[slideIndex].name).addClass("hide");
}

function showSlide(slide) {
    var slideIndex = getIndex(slide);

    slideArray.find(function (e) {
        if (e.name === slide.name)
            e.active = true;
    });

    $(slideArray[slideIndex].name).removeClass("hide");
    $(slideArray[slideIndex].name).addClass("show");
}


function scrollFunction() {
    if (document.body.scrollTop > 50 || document.documentElement.scrollTop > 50) {
        document.getElementById("btn-top").style.display = "block";
    } else {
        document.getElementById("btn-top").style.display = "none";
    }
}

function topFunction() {
    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;
}



$(document).ready(function () {

    $("#btnNxt").click(function () {
        var activeSlide = getActiveSlide();
        var nextSlide = getNextSlide();

        updateDropdown(nextSlide);
        setArrows(nextSlide);
        hideSlide(activeSlide);
        showSlide(nextSlide);
    });

    $("#btnPrv").click(function () {
        var activeSlide = getActiveSlide();
        var prevSlide = getPrevSlide();

        updateDropdown(prevSlide);
        setArrows(prevSlide);
        hideSlide(activeSlide);
        showSlide(prevSlide);
    });

    $('.nav-item').children().click(function (e) {
        var activeSlide = getActiveSlide();
        var slide = getSlideByName($(this)[0].hash);

        updateDropdown(slide);
        setArrows(slide);
        hideSlide(activeSlide);
        showSlide(slide);
    });

    $('.stick-top').affix({
        offset: {top: 400}
    });


    window.onscroll = function () {
        scrollFunction()
    };

});