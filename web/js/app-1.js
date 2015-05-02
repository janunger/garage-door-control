'use strict';

$(function () {
    var videostreamTag = $('.videostream');

    videostreamTag.click(function () {
        videostreamTag.attr('src', videostreamTag.data('default-src'));
    });
});

$(function () {
    var triggerButton = $('.btn-trigger');

    triggerButton.click(function () {
        triggerButton.attr('disabled', 'disabled');
        $.ajax('/trigger', {
            complete: function () {
                triggerButton.removeAttr('disabled');
            }
        });
    });
});
