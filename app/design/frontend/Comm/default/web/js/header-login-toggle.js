define([
    'jquery'
], function($) {
    'use strict';

    return function(){
        $(".login-block").on("click", function(){
            $(".login-block .popover").toggleClass("active");
        });

        $('.grid-featured-headline-button').on('click', function () {
            $('.feature-card-home').addClass('active')
        })

        $('.feature-card-home-close').on('click', function () {
            $('.feature-card-home').removeClass('active')
        })
    }
});
