$(function () {
    if (!window.XenicalTracker) return;
    $(document).on('click', '[data-observer]', function () { XenicalTracker.click(this); });
});
