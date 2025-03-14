define([
    'jquery'
], function($) {
    'use strict';

    return function(){
        $(".login-block").on("click", function(){
            $(".login-block .popover").toggleClass("active");
        });
    }
});
