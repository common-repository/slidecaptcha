/**
 * Created by Андрей on 14.06.2017.
 */
jQuery(document).ready(function($){

    var mediaUploader;

    $('#upload-button').click(function(e) {
        e.preventDefault();
        // If the uploader object has already been created, reopen the dialog
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }
        // Extend the wp.media object
        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            }, multiple: false });

        // When a file is selected, grab the URL and set it as the text field's value
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#image-url').val(attachment.url);
            console.log(attachment);
            jQuery('.image-path').html(attachment.url);
        });
        // Open the uploader dialog
        mediaUploader.open();
    });
    jQuery('#useDefaultBg, #dontUseImageBg').on('change', function(){
        if(jQuery(this).is(':checked'))
            jQuery(this).val(1);
        else
            jQuery(this).val(0);

    });
});