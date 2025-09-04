<div class="wlb-composer-plugin-wrapper">
    <form action="" method="POST" class="wlb-compose-plugin-form <?php echo esc_attr( $attr['class'] ?? '' ); ?>">
        <div class="form-group">
            <label class="control-label" for="plugin_name"><?php echo esc_html__( 'Plugin Name', 'plugin-composer' ); ?><span class="required">*</span></label>
            <div class="input-group">
                <input name="plugin_name" id="plugin_name" required class="input-control" placeholder="<?php echo esc_attr__( 'My Plugin', 'plugin-composer' ); ?>">
                <div class="error-message"><?php echo esc_html( $error_messages['plugin_name'] ?? '' ); ?></div>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label" for="plugin_description"><?php echo esc_html__( 'Plugin Description', 'plugin-composer' ); ?></label>
            <textarea name="plugin_description" id="plugin_description"  rows="2" class="input-control" placeholder="<?php echo esc_attr__( 'Plugin desc.', 'plugin-composer' ); ?>"></textarea>
        </div>
        <div class="from-fields-col-2 from-fields-col-style-1">
            <div class="form-group">
                <label class="control-label" for="plugin_requires"><?php echo esc_html__( 'Requires Plugins', 'plugin-composer' ); ?></label>
                <input name="plugin_requires" id="plugin_requires" class="input-control" placeholder="<?php echo esc_attr( 'e.i woocommerce, dokan-lite, dokan-pro' ); ?>">
            </div>
            <div class="form-group">
                <label class="control-label" for="plugin_license"><?php echo esc_html__( 'Plugin License', 'plugin-composer' ); ?></label>
                <input name="plugin_license" id="plugin_license" class="input-control" placeholder="<?php echo esc_attr__( 'License e.g., GPL2', 'plugin-composer' ); ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label" for="plugin_uri"><?php echo esc_html__( 'Plugin URL', 'plugin-composer' ); ?></label>
            <input name="plugin_uri" id="plugin_uri" type="url" class="input-control" placeholder="<?php echo esc_url( 'https://company.com/my-plugin' ); ?>" value="">
        </div>
        <div class="from-fields-col-2">
            <div class="form-group">
                <label class="control-label" for="plugin_author_name"><?php echo esc_html__( 'Author Name', 'plugin-composer' ); ?></label>
                <input name="plugin_author_name" id="plugin_author_name"  class="input-control" placeholder="<?php echo esc_attr__( 'weLabs', 'plugin-composer' ); ?>">
            </div>
            <div class="form-group">
                <label class="control-label" for="plugin_author_email"><?php echo esc_html__( 'Author Email', 'plugin-composer' ); ?></label>
                <input name="plugin_author_email" id="plugin_author_email" type="email" class="input-control" placeholder="<?php echo esc_attr( 'contact@welabs.dev' ); ?>">
            </div>
        </div>
        <div class="from-fields-col-2">
            <div class="form-group">
                <label class="control-label" for="plugin_author_uri"><?php echo esc_html__( 'Author URL', 'plugin-composer' ); ?></label>
                <input name="plugin_author_uri" id="plugin_author_uri" type="url"  class="input-control" placeholder="<?php echo esc_url( 'https://author.profile' ); ?>">
            </div>
            <div class="form-group">
                <label class="control-label" for="plugin_is_settings_included"><?php echo esc_html__( 'Include Plugin Settings?', 'plugin-composer' ); ?></label>
                <select name="plugin_is_settings_included" id="plugin_is_settings_included" class="input-control">
                    <option value="no"><?php echo esc_html__( 'No', 'plugin-composer' ); ?></option>
                    <option value="yes"><?php echo esc_html__( 'Yes', 'plugin-composer' ); ?></option>
                </select>
            </div>
        </div>

        <?php wp_nonce_field( 'wlb-compose-plugin', 'wlb-compose-plugin' ); ?>

        <div class="form-group wlb-submit-wrapper"> 
            <input type="submit" value="<?php echo esc_attr( $attr['submit-text'] ); ?>">
        </div>
    </form>
</div>