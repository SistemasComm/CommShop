define([
    'jquery'
], function($) {
    'use strict';

        let btnLoadMore = $('.load-more p');

        $(btnLoadMore).on('click', function () {
            let parentWrapper = $(this).closest('.content-wrapper');
            let contentDescription = parentWrapper.find('.content-description');
            
            contentDescription.toggleClass('active');
            
            let loadMoreTextElement = $(this).find('.load-more-text'); 
            


            if (contentDescription.hasClass('active')) {
                loadMoreTextElement.text('Ver menos');
            } else {
                loadMoreTextElement.text('Ver mais');
            }
        });
});