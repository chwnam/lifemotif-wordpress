(function ($) {
    $(document).ready(function () {

        var d = new Date();
        var articles = dpObject.articles;

        var dp = $('#datepicker').datepicker({
            changeMonth: true,
            changeYear: true,
            minDate: dpObject.firstArticleDate,
            maxDate: d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate(),
            dateFormat: 'yy-mm-dd',
            onSelect: onDatePicked,
            onChangeMonthYear: onChangeMonthYear,
            beforeShowDay: function (date) {
                var d = date.getDate().toString();
                var p = articles[d] || 0;
                if (p) {
                    return [true, "article-found", ""];
                }
                return [true, "", ""];
            }
        });

        function onDatePicked(dateText, inst) {
            var d = new Date(dateText).getDate().toString();
            if (articles[d] || 0) {
                $.ajax({
                    url: dpObject.ajaxUrl,
                    method: 'get',
                    data: {
                        'action': 'get-article',
                        'post-id': articles[d],
                        'wp-nonce': dpObject.nonce,
                        'XDEBUG_SESSION_START': 18339
                    },
                    success: function (response) {
                            console.log(response);
                        if(response.hasOwnProperty('success') && response.success) {
                            var post = response.data.post;
                            var images = response.data.images;
                            var imageContent = '';
                            for(var i = 0; i < images.length; ++i) {
                                imageContent += images[i].url;
                            }
                            $('#diary-images').html(imageContent);
                            $('#diary-content').html(post.post_content);
                        }
                    }
                });
            }
        }

        function onChangeMonthYear(year, month, inst) {
            $.ajax({
                url: dpObject.ajaxUrl,
                method: 'get',
                data: {
                    'year': year,
                    'month': month,
                    'action': 'get-article-catalog',
                    'wp-nonce': dpObject.nonce,
                    'XDEBUG_SESSION_START':18339
                },
                success: function (response) {
                    console.log(response);
                    if (response.hasOwnProperty('success') && response.success && response.hasOwnProperty('data')) {
                        articles = response.data;
                    } else {
                        articles = {};
                    }
                    $('#datepicker').datepicker('refresh');
                }
            });
        }
    });
})(jQuery);
