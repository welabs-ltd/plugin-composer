<?php
$wlb_button_vars = array(
    '--wlb-btn-bg'         => $attr['button_bg_color'] ?? '',
    '--wlb-btn-text'       => $attr['button_text_color'] ?? '',
    '--wlb-btn-bg-hover'   => $attr['button_bg_hover_color'] ?? '',
    '--wlb-btn-text-hover' => $attr['button_text_hover_color'] ?? '',
);
$wlb_style = '';
foreach ( $wlb_button_vars as $wlb_var => $wlb_val ) {
    if ( $wlb_val !== '' ) {
        $wlb_style .= $wlb_var . ':' . $wlb_val . ';';
    }
}
?>
<div class="wlb-composer-plugin-wrapper"<?php echo $wlb_style ? ' style="' . esc_attr( $wlb_style ) . '"' : ''; ?>>
    <form action="" method="POST" class="wlb-compose-plugin-form <?php echo esc_attr( $attr['class'] ?? '' ); ?>">
        <div class="form-group">
            <label class="control-label" for="plugin_name"><?php echo esc_html__( 'Plugin Name', 'plugin-composer' ); ?><span class="required">*</span></label>
            <div class="input-group">
                <input name="plugin_name" id="plugin_name" required class="input-control" placeholder="<?php echo esc_attr( $attr['placeholder_plugin_name'] ); ?>">
                <div class="error-message"><?php echo esc_html( $error_messages['plugin_name'] ?? '' ); ?></div>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label" for="plugin_description"><?php echo esc_html__( 'Plugin Description', 'plugin-composer' ); ?></label>
            <textarea name="plugin_description" id="plugin_description"  rows="2" class="input-control" placeholder="<?php echo esc_attr( $attr['placeholder_plugin_description'] ); ?>"></textarea>
        </div>
        <div class="form-fields-col-2 form-fields-col-style-1">
            <div class="form-group">
                <label class="control-label" for="plugin_requires"><?php echo esc_html__( 'Requires Plugins', 'plugin-composer' ); ?></label>
                <input name="plugin_requires" id="plugin_requires" class="input-control" placeholder="<?php echo esc_attr( $attr['placeholder_plugin_requires'] ); ?>">
            </div>
            <div class="form-group">
                <label class="control-label" for="plugin_license"><?php echo esc_html__( 'Plugin License', 'plugin-composer' ); ?></label>
                <input name="plugin_license" id="plugin_license" class="input-control" placeholder="<?php echo esc_attr( $attr['placeholder_plugin_license'] ); ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label" for="plugin_uri"><?php echo esc_html__( 'Plugin URL', 'plugin-composer' ); ?></label>
            <input name="plugin_uri" id="plugin_uri" type="url" class="input-control" placeholder="<?php echo esc_attr( $attr['placeholder_plugin_uri'] ); ?>" value="">
        </div>
        <div class="form-fields-col-2">
            <div class="form-group">
                <label class="control-label" for="plugin_author_name"><?php echo esc_html__( 'Author Name', 'plugin-composer' ); ?></label>
                <input name="plugin_author_name" id="plugin_author_name"  class="input-control" placeholder="<?php echo esc_attr( $attr['placeholder_plugin_author_name'] ); ?>">
            </div>
            <div class="form-group">
                <label class="control-label" for="plugin_author_email"><?php echo esc_html__( 'Author Email', 'plugin-composer' ); ?></label>
                <input name="plugin_author_email" id="plugin_author_email" type="email" class="input-control" placeholder="<?php echo esc_attr( $attr['placeholder_plugin_author_email'] ); ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label" for="plugin_author_uri"><?php echo esc_html__( 'Author URL', 'plugin-composer' ); ?></label>
            <input name="plugin_author_uri" id="plugin_author_uri" type="url"  class="input-control" placeholder="<?php echo esc_attr( $attr['placeholder_plugin_author_uri'] ); ?>">
        </div>
        <?php
        $show_settings_field = ( $attr['show_settings_field'] ?? 'yes' ) === 'yes';
        $show_wpvip_field    = ( $attr['show_wpvip_field'] ?? 'yes' ) === 'yes';
        if ( $show_settings_field || $show_wpvip_field ) : ?>
        <div class="form-fields-col-2">
            <?php if ( $show_settings_field ) : ?>
            <div class="form-group">
                <label class="control-label" for="plugin_is_settings_included"><?php echo esc_html__( 'Include Plugin Settings?', 'plugin-composer' ); ?></label>
                <select name="plugin_is_settings_included" id="plugin_is_settings_included" class="input-control">
                    <option value="no"><?php echo esc_html__( 'No', 'plugin-composer' ); ?></option>
                    <option value="yes"><?php echo esc_html__( 'Yes', 'plugin-composer' ); ?></option>
                </select>
            </div>
            <?php endif; ?>
            <?php if ( $show_wpvip_field ) : ?>
            <div class="form-group">
                <label class="control-label" for="plugin_is_wpvip_supported"><?php echo esc_html__( 'Do You Want WP VIP Support?', 'plugin-composer' ); ?></label>
                <select name="plugin_is_wpvip_supported" id="plugin_is_wpvip_supported" class="input-control">
                    <option value="no"><?php echo esc_html__( 'No', 'plugin-composer' ); ?></option>
                    <option value="yes"><?php echo esc_html__( 'Yes', 'plugin-composer' ); ?></option>
                </select>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php wp_nonce_field( 'wlb-compose-plugin', 'wlb-compose-plugin' ); ?>

        <div class="form-group wlb-submit-wrapper">
            <input type="submit" value="<?php echo esc_attr( $attr['submit-text'] ); ?>">
        </div>
    </form>
</div>
