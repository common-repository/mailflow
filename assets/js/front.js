jQuery(document).ready(function ($) {

        var data = {
            'action': 'mailflow_tag_page_visit',
            'path': window.location.href,
            'security': mailflowAjax.security
        };
        console.log(mailflowAjax);
        $.post(mailflowAjax.ajaxurl, data, function (response) {
            console.log(response);
        });

});