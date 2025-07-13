jQuery(document).ready(function($) {
    $(document).on('click', '.add_to_cart_button', function(e) {
        var button = $(this);
        var productId = button.data('product_id');
        var selectedLocationId = getCookie('wcmlim_selected_location_termid');

        if (!selectedLocationId) {
            e.preventDefault();
            ensureNoticeWrapper();
            $('.woocommerce-notices-wrapper').prepend('<div class="woocommerce-error" role="alert">Vui lòng chọn cửa hàng trước khi thêm vào giỏ.</div>');
            return false;
        }

        $.ajax({
            url: wcmlim_ajax.ajax_url,
            method: 'POST',
            data: {
                action: 'wcmlim_check_stock',
                product_id: productId,
                location_id: selectedLocationId,
            },
            success: function(response) {
                if (response.error) {
                    e.preventDefault();
                    ensureNoticeWrapper();
                    $('.woocommerce-notices-wrapper').prepend('<div class="woocommerce-error" role="alert">' + response.message + '</div>');
                } else {
                    console.log('Còn hàng, tiếp tục Add to Cart.');
                }
            },
            async: false
        });
    });

    function ensureNoticeWrapper() {
        if ($('.woocommerce-notices-wrapper').length === 0) {
            $('body').prepend('<div class="woocommerce-notices-wrapper"></div>');
        }
    }

    function getCookie(name) {
        var dc = document.cookie;
        var prefix = name + "=";
        var begin = dc.indexOf("; " + prefix);
        if (begin == -1) {
            begin = dc.indexOf(prefix);
            if (begin != 0) return null;
        } else {
            begin += 2;
        }
        var end = dc.indexOf(";", begin);
        if (end == -1) {
            end = dc.length;
        }
        return decodeURIComponent(dc.substring(begin + prefix.length, end));
    }
});
