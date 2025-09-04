define([
    'jquery',
    './owl.carousel'
], function($) {
    'use strict';

    return function(conf, elem) {
        $(elem).owlCarousel({
            items: 4,
            margin: 20,
            nav: true,
            navText: ['', ''],
            dots: false,
            responsive : {
                0 : {
                    items : 2
                },
                500 : {
                    items : 3
                },
                768 : {
                    items : 4
                },
                960 : {
                    items : 4
                }
            }
        });
    }
});
