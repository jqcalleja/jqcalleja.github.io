$(document).ready(function () {

    AOS.init();



    /* HERO INTRO ANIMATION */

    gsap.from("#couple", {

        y: -100,
        opacity: 0,
        duration: 2

    });



    /* SMOOTH SCROLL */

    $("#openInvite").click(function () {

        $("html, body").animate({

            scrollTop: $("#details").offset().top

        }, 1000);

    });



    /* COUNTDOWN TIMER */

    var weddingDate = new Date("June 18, 2026 16:00:00").getTime();

    setInterval(function () {

        var now = new Date().getTime();

        var distance = weddingDate - now;

        var days = Math.floor(distance / (1000 * 60 * 60 * 24));

        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));

        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));

        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        $("#days").text(days);
        $("#hours").text(hours);
        $("#minutes").text(minutes);
        $("#seconds").text(seconds);

    }, 1000);

});