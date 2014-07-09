'use strict';

$(function () {
    var snapshotTag = $('.snapshot');
    var snapshotUrl = snapshotTag.attr('src');

    function reloadSnapshot() {
        var timestamp = (new Date()).getTime();
        var newSrc = snapshotUrl + '?' + timestamp;
        snapshotTag.attr('src', newSrc);
        setTimeout(reloadSnapshot, 10000);
    }
    reloadSnapshot();
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
