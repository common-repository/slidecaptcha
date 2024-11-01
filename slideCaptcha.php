<?php

/*
Plugin Name: slideCaptcha
Text Domain: slideCaptcha
Domain Path: /languages
Description: slide captcha.
Version: 1.0
Author: Andrejj derevjanko
Author URI: http://web-andryshka.ru
 */
add_filter('comment_form_default_fields', 'scap_show_captcha');

function scap_show_captcha($fields){
    $captchaBg = get_option('scap_captchaBg');
    $captchaBgColor = get_option('scap_captchaBgColor');
    $dontUseImageBg = get_option('scap_dontUseImageBg');
    $captchaStartText =get_option('scap_captchaStartText');
    if(!$captchaBg || empty($captchaBg) )
        if($dontUseImageBg)
        $captchaBg ='style="background-image: inherit; ';
        else
        $captchaBg ='';
    else
        $captchaBg =   ' style="background-image: url(\''.$captchaBg.'\'); ';
    if($captchaBgColor || !empty($captchaBgColor))
        $captchaBg .=' background-color:#'.$captchaBgColor.';"';
    else
        $captchaBg .= '"';

    if(!$captchaStartText || empty($captchaStartText))
        $captchaStartText =  __("Swipe to unlock!", 'sCaptcha' );;
    $captchaEndText = get_option('captchaEndText');
    if(!$captchaEndText || empty($captchaEndText))
        $captchaEndText = __("Unlocked", 'sCaptcha' );
    $fields[ 'is_bot' ] = '<input type="hidden" name="is_bot" id="is_bot" value="0">';
    $fields[ 'captcha' ] =  '   <div class="touchsurface not-finished" '.$captchaBg.'>
                            <div class="slide button" data-left="0" style=""></div>
                            <div class="text locked">'.$captchaStartText.'</div>
                            <div class="text unlocked">'.$captchaEndText.'</div>
                        </div>';
    wp_enqueue_style( 'sCaptcha',  plugin_dir_url(__FILE__). '/css/sCaptcha.css' );
    wp_register_script( 'sCaptcha', plugin_dir_url(__FILE__).  '/js/sCaptcha.js',  array( 'jquery' ));
    wp_enqueue_script( 'sCaptcha' );
    return $fields;
}

add_action( 'pre_comment_on_post', 'scap_is_query_from_bot' );

function scap_is_query_from_bot( $comment_post_ID ){
    if($_POST['is_bot'] != 'imNotBot')
       wp_die();
}

add_action( 'admin_menu', 'scap_captcha_menu' );

function scap_captcha_menu() {
    add_options_page( 'Captcha ', 'Captcha', 'manage_options', 'scap_sCaptcha_menu', 'scap_captcha_options' );
}

function scap_captcha_options() {
    if (!current_user_can('manage_options'))
    {
        wp_die( __('You do not have sufficient permissions to access this page.') );
    }

    wp_enqueue_media();
    wp_register_script( 'media-lib-uploader-js',  plugin_dir_url(__FILE__).  '/js/media-lib-uploader.js' , array('jquery') );
    wp_enqueue_script( 'media-lib-uploader-js' );
    wp_register_script( 'jscolor',  plugin_dir_url(__FILE__).  '/js/jscolor.min.js' , array('jquery') );
    wp_enqueue_script( 'jscolor' );
    $hidden_field_name = 'scap_submit_hidden';

    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
        if($_POST[ 'useDefaultBg' ]=='1')
            $newBg = '';
        else $newBg =  sanitize_text_field($_POST[ 'scap_captchaBg' ]);
        update_option( 'scap_captchaBg' , $newBg);
        update_option( 'scap_captchaStartText' , sanitize_text_field($_POST[ 'scap_captchaStartText' ]) );
        update_option( 'scap_captchaEndText' , sanitize_text_field($_POST[ 'scap_captchaEndText' ]) );
        update_option( 'scap_captchaBgColor' , sanitize_text_field($_POST[ 'scap_captchaBgColor' ] ));
        update_option( 'scap_dontUseImageBg' , sanitize_text_field($_POST[ 'scap_dontUseImageBg' ]) );
        ?>
        <div class="updated"><p><strong><?php echo __('Settings saved', 'sCaptcha' ); ?></strong></p></div>
        <?php

    }
    $captchaBg = get_option('scap_captchaBg');
    $captchaBgColor = get_option('scap_captchaBgColor');
    $captchaStartText = get_option('scap_captchaStartText');
    $captchaEndText = get_option('scap_captchaEndText');
    $dontUseImageBg = get_option('scap_dontUseImageBg');

    echo '<div class="wrap">';
    echo "<h2>" . __( 'Slide captcha Settings', 'sCaptcha' ) . "</h2>";
    ?>

    <form name="captchaForm" method="post"  action="">
        <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

        <p><?php __("Slide surface background image:", 'sCaptcha' ); ?>
        <input id="image-url" type="hidden" name="scap_captchaBg"  value="<?php echo $captchaBg; ?>" />
           <span class="image-path"><?php echo $captchaBg; ?></span>
            <br>
        <input id="upload-button" type="button" class="button" value="<?php echo __( 'Select image', 'sCaptcha' ) ?>" />
        </p><hr />
        <p>
            <label for="useDefaultBg">
                <input type='checkbox' name="scap_useDefaultBg" id="useDefaultBg" <?php echo (empty($captchaBg))?'checked':'';?>
                       value="<?php  echo (empty($captchaBg))?'1':'0'?>">
                <?php echo __( 'Use default background', 'sCaptcha' ) ?>
            </label>
        </p><hr />
        <p><?php __("Slide surface background color", 'sCaptcha' ); ?>
            <input  type="text" name="scap_captchaBgColor" class="jscolor"   value="<?php echo $captchaBgColor; ?>" />
            <br>
            <label for="dontUseImageBg">
                <input type='checkbox' name="scap_dontUseImageBg" id="dontUseImageBg" <?php echo (!empty($dontUseImageBg)||$dontUseImageBg==1)?'checked':'';?>
                       value="<?php echo $dontUseImageBg;?>">
                <?php echo __( 'Do not use image like slide surface background', 'sCaptcha' ) ?>
            </label>
        </p><hr />

        <p><?php __("Captcha start text:", 'sCaptcha' ); ?>
            <input  type="text" name="scap_captchaStartText"  value="<?php echo $captchaStartText; ?>" />

        </p><hr />

        <p><?php __("Captcha end text:", 'sCaptcha' ); ?>
            <input  type="text" name="scap_captchaEndText"  value="<?php echo $captchaEndText; ?>" />

        </p><hr />


        <p class="submit">
            <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
        </p>

    </form>
    </div>

    <?php

}