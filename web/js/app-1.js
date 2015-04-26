'use strict';

$(function () {
    var snapshotTag = $('.snapshot');
    var snapshotUrl = snapshotTag.data('default-src');

    function loadSnapshot() {
        var timestamp = (new Date()).getTime();
        var newSrc = snapshotUrl + '?' + timestamp;
        snapshotTag.attr('src', newSrc);
        setTimeout(loadSnapshot, 10000);
    }

    snapshotTag.click(function () {
        loadSnapshot();
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
