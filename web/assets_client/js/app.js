$(function() {

    var showPopover = $.fn.popover.Constructor.prototype.show;
    $.fn.popover.Constructor.prototype.show = function() {
        showPopover.call(this);
        if (this.options.showCallback) {
            this.options.showCallback.call(this);
        }
    };

    $('.slider-inner').slick({
        infinite: true,
        slidesToShow: 3,
        slidesToScroll: 1
    });

    var sliderTestimonial = $('#testimonial');

    sliderTestimonial.on('slide.bs.carousel', function(e) {
        var itemActiveIdentity = sliderTestimonial.find('.item.active').find('.identity'),
            idName = itemActiveIdentity.find('.name').text(),
            idImg = itemActiveIdentity.find('img').attr('src'),
            testimonialId = $('.testimonial-identity');

        $('.testimonial-identity .name-cont .name').text(idName);
        $('.testimonial-identity .img-cont img').attr('src', idImg);
    });

    $('[id^=item]').click(function(e) {
        e.preventDefault();
        var mainWrp = $('#main-content'),
            formWrp = $('#sidebar-content'),
            headerText = $(this).next().find('p').first().text(),
            frmCont = $(this).next().find('.content-form').html(),
            itmName = $(this).attr('id'),
            itmLCase = $(this).text().trim().toLowerCase().replace(' ', '-'),
            itemPos = parseInt($(this).offset().top),
            absPos = itemPos - 77;

        if (mainWrp.attr('class') == 'toggle in') {
            mainWrp.attr('class', 'toggle up');
            formWrp.attr('class', 'open')
                .attr('data-item', $(this).attr('id'));
            formWrp.find('.content-head').text(headerText);
            formWrp.find('.content-main').html(frmCont);
        } else {
            if (formWrp.attr('data-item') == $(this).attr('id')) {
                $(this).attr('class', 'item');
                mainWrp.attr('class', 'toggle in');
                formWrp.attr('class', 'closed');

                formWrp.removeAttr('data-item');

                formWrp.find('.content-main').html();
            } else {
                $(this).attr('class', 'item ui-state-active');
                formWrp.attr('data-item', $(this).attr('id'));

                formWrp.find('.content-head').text(headerText);
                formWrp.find('.content-main').html(frmCont);
            }
        }
    });

    // IMG POPUP

    $('.img-popup').magnificPopup({
        type: 'image',
        mainClass: 'mfp-with-zoom',
        zoom: {
            enabled: true,
            duration: 300,
            easing: 'ease-in-out'
        },
        gallery: {
            enabled: true
        }
    });

    // FLASH NEWS

    var itemTimeOut,
        itmList = $('#news-flash .item'),
        itmLen = itmList.length,
        currentActive = 0,
        numlet = 5;

    var flashnewsFunction = function() {
        currentActive = (currentActive + 1) % itmLen;
        itmList.removeClass('active')
            .eq(currentActive)
            .addClass('active');
    };

    itmList.first().addClass('active');

    var timer = setInterval(function() {
        flashnewsFunction();
    }, numlet * 1000);

    itmList.hover(function() {
        clearInterval(timer);
    }, function() {
        timer = setInterval(function() {
            flashnewsFunction();
        }, numlet * 1000);
    })

    var signIn = $('.sign-in');

    signIn.click(function(e) {
        e.preventDefault();
    })
        .popover({
        html: true,
        placement: "bottom",
        content: function () {
            return $(this).parent().find('.content').html();
        }
    });

});
