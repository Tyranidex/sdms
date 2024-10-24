jQuery(document).ready(function($) {
    $('.smds-upload-icon-button').click(function(e) {
        e.preventDefault();
        var button = $(this);
        var fileType = button.data('file-type');
        var frame = wp.media({
            title: smdsIconUploader.title,
            button: { text: smdsIconUploader.button },
            library: { type: 'image' },
            multiple: false
        });

        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            button.prevAll('input').val(attachment.url);
            button.prevAll('img').attr('src', attachment.url);
        });

        frame.open();
    });
});