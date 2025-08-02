<form action="" method="POST" class="wlb-compose-plugin-form <?php echo esc_attr( $attr['class'] ?? '' ); ?>">
    <div class="form-group">
        <label class="control-label" for="plugin_name">
            Plugin Name *
        </label>
        <div class="input-group">
            <input name="plugin_name" id="plugin_name" required class="input-control" placeholder="My Plugin">
            <div class="error-message"><?php echo esc_html( $error_messages['plugin_name'] ?? '' ); ?></div>
        </div>
    </div>

        <div class="form-group">
        <label class="control-label" for="plugin_namespace">
            Plugin Namespace
        </label>
        <div class="input-group">
            <input name="plugin_namespace" id="plugin_namespace" class="input-control" 
                    placeholder="<?php echo esc_attr( $default_values['namespace'] ?? 'MyPlugin' ); ?>"
                    value="<?php echo esc_attr( $default_values['namespace'] ?? 'MyPlugin' ); ?>">
            <small class="help-text">Use forward slashes for multi-word namespaces (e.g., AB/AC)</small>
            <div class="error-message"><?php echo esc_html( $error_messages['plugin_namespace'] ?? '' ); ?></div>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label" for="plugin_type">
            Plugin Type
        </label>
        <select name="plugin_type" class="plugin-type">
            <option value="classic" <?php selected( $default_values['plugin_type'] ?? 'container_based', 'classic' ); ?>>Classic</option>
            <option value="container_based" <?php selected( $default_values['plugin_type'] ?? 'container_based', 'container_based' ); ?>>Container Based</option>
        </select>
    </div>
    <div class="form-group">
        <label class="control-label" for="plugin_description">
            Plugin Description
        </label>
        <textarea name="plugin_description" id="plugin_description"  rows="2" class="input-control" placeholder="Plugin desc."></textarea>
    </div>

    <div class="form-group">
        <label class="control-label" for="plugin_license">
            Plugin License
        </label>
        <input name="plugin_license" id="plugin_license" class="input-control" placeholder="License e.i GPL2">
    </div>

    <div class="form-group">
        <label class="control-label" for="plugin_uri">
            Plugin URL
        </label>
        <input name="plugin_uri" id="plugin_uri" type="url" class="input-control" placeholder="https://company.com/my-plugin" value="">
    </div>
    <div class="form-group">
        <label class="control-label" for="plugin_author_name">
            Author Name
        </label>
        <input name="plugin_author_name" id="plugin_author_name" class="input-control" 
                placeholder="<?php echo esc_attr( $default_values['author_name'] ?? 'Your Name' ); ?>"
                value="<?php echo esc_attr( $default_values['author_name'] ?? 'Your Name' ); ?>">
    </div>
    <div class="form-group">
        <label class="control-label" for="plugin_author_email">
            Author Email
        </label>
        <input name="plugin_author_email" id="plugin_author_email" type="email" class="input-control" placeholder="contact@welabs.dev">
    </div>
    <div class="form-group">
        <label class="control-label" for="plugin_author_uri">
            Author URL
        </label>
        <input name="plugin_author_uri" id="plugin_author_uri" type="url" class="input-control" 
                placeholder="<?php echo esc_attr( $default_values['author_url'] ?? 'https://example.com' ); ?>"
                value="<?php echo esc_attr( $default_values['author_url'] ?? 'https://example.com' ); ?>">
    </div>

    <?php wp_nonce_field( 'wlb-compose-plugin', 'wlb-compose-plugin' ); ?>

    <div class="form-group"> 
        <input type="submit" value="<?php echo esc_attr( $attr['submit-text'] ); ?>">
    </div>
</form>