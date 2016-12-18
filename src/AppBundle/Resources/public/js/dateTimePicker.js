$( document ).ready(function() {
    startTime();
});
function startTime() {
    var today = new Date();
    var year = today.getFullYear();
    var month = today.getMonth();
    var day = today.getDate();
    var h = today.getHours();
    var m = today.getMinutes();
    var s = today.getSeconds();
    m = addZero(m);
    s = addZero(s);
    month = addZero(month);
    day = addZero(day);
    document.getElementById('appbundle_post_publishedAt').value =
        day + "-" + month + "-" + year + " " +
        h + ":" + m + ":" + s;
    var t = setTimeout(startTime, 500);
}
function addZero(i) {
    if (i < 10) {i = "0" + i};
    return i;
}