jQuery(function($){

    $('.news').on('click', '.md-fav-toggle', function(e){
        e.preventDefault();

        let $link = $(this);
        let post_id = $link.data('post');

        if (!md_fav_ajax.logged_in) {
            alert('Please log in to save favourites.');
            return;
        }

        $.post(md_fav_ajax.ajax_url, {
            action: 'md_toggle_favourite',
            post_id: post_id
        }, function(response){
            if (response.success) {
                let data = response.data;

                // Update icon
                let icon = $link.find('i');
                icon.removeClass('fa-solid fa-regular');
                icon.addClass(data.favourited ? 'fa-solid' : 'fa-regular');

                // Update number
                $link.find('.news__link-icon-number').text(data.count);
            }
        });
    });

});
