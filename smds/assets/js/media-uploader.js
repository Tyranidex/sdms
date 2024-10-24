jQuery(document).ready(function($){
    var mediaUploader;

    $('.smds-upload-button').click(function(e) {
        e.preventDefault();
        var button = $(this);
        var language = button.data('language');
        var container = button.closest('.smds-language-file');

        // Create the media frame.
        mediaUploader = wp.media({
            title: smdsUploader.title,
            button: {
                text: smdsUploader.button
            },
            multiple: false
        });

        // When a file is selected, run a callback.
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            container.find('input[name="smds_file_' + language + '"]').val(attachment.id);

            // Update the UI
            var fileInfo = container.find('.smds-file-info');
            fileInfo.html(
                '<a href="' + attachment.url + '" target="_blank" class="button">' + smdsUploader.viewFile + '</a>' +
                ' <button type="button" class="button smds-remove-file-button" data-language="' + language + '">' + smdsUploader.removeFile + '</button>' +
                ' <span class="smds-file-name">' + attachment.filename + '</span>'
            ).show();
            button.hide();
        });

        // Open the uploader dialog.
        mediaUploader.open();
    });

    $(document).on('click', '.smds-remove-file-button', function(e) {
        e.preventDefault();
        var button = $(this);
        var language = button.data('language');
        var container = button.closest('.smds-language-file');

        // Clear the hidden input
        container.find('input[name="smds_file_' + language + '"]').val('');

        // Update the UI
        container.find('.smds-file-info').hide().empty();
        container.find('.smds-upload-button').show();
    });
});