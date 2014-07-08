'use strict';

$(function () {
    var snapshotTag = $('.snapshot');
    var snapshotUrl = snapshotTag.attr('src');

    function reloadSnapshot() {
        snapshotTag.attr('src', snapshotUrl + '?' + (new Date()).getTime());
        setTimeout(reloadSnapshot, 5000);
    }
    reloadSnapshot();
});
