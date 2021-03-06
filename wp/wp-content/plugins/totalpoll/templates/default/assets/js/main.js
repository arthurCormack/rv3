jQuery(function ($) {
    var imagesTosrus = $('.totalpoll-poll-container[data-template="default"] .totalpoll-choice-image.totalpoll-supports-full a').tosrus({
        buttons: {
            prev: false,
            next: false
        },
        wrapper: {
            onClick: 'close'
        }
    });

    var videosTosrus = $('.totalpoll-poll-container[data-template="default"] .totalpoll-choice-embed a').tosrus({
        buttons: {
            prev: false,
            next: false
        },
        wrapper: {
            onClick: 'close'
        },
        youtube: {
            imageLink: false
        }
    });

    $(document).on('totalpoll.after.ajax', function (e, data) {
        $(imagesTosrus).remove();
        $(videosTosrus).remove();
        var imagesTosrus = $('.totalpoll-choice-image.totalpoll-supports-full a', data.container).tosrus({
            buttons: {
                prev: false,
                next: false
            },
            wrapper: {
                onClick: 'close'
            }
        });

        var videosTosrus = $('.totalpoll-choice-embed a', data.container).tosrus({
            buttons: {
                prev: false,
                next: false
            },
            wrapper: {
                onClick: 'close'
            },
            youtube: {
                imageLink: false
            }
        });
    });
});