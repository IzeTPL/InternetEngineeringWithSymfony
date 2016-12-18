$(document).ready(function() {
    checkPosition();
});

$(document).on({
    ajaxStart: function() { $('#ajaxloadercontainer').addClass("loading");},
    ajaxStop: function() { $('#ajaxloadercontainer').removeClass("loading");}
});

var processing = false;
var triggered = 1;

function showMore() {
    $.ajax({
        url: '/ajax/post/show/more',
        context: document.body,
        dataType: 'json',
        success: function (content) {
            $('#post').append(content.template);
            triggered++;
            processing = false;
        }
    })
}

function showPagination() {

    $.ajax({
        url: '/ajax/post/show/pagination',
        context: document.body,
        dataType: 'json',
        success: function (content) {
            $('#pagination').append(content.template);
            triggered++;
            processing = false;
        }
    })

}

function checkPosition() {

    $(window).scroll(function () {
        if(processing) {
            $(window.setTimeout(ajaxRequest(), 500));
        } else {
            ajaxRequest();
        }
    });

}

function ajaxRequest() {
    if ( ($(window).scrollTop() + $(window).height() ) == $(document).height() && triggered < 10) {
        processing = true;
        showMore();
    } else if (($(window).scrollTop() + $(window).height() ) == $(document).height() && triggered == 10) {
        processing = true;
        showPagination()
    }
}
