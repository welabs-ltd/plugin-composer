import { useState, useEffect } from '@wordpress/element';
import { 
    Card, 
    CardBody, 
    CardHeader,
    Button, 
    TextControl, 
    ToggleControl, 
    SelectControl, 
    RangeControl,
    Notice,
    Spinner,
    PanelBody,
    PanelRow,
    TabPanel,
    Modal
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';

function PluginComposerSettings() {
    const [settings, setSettings] = useState({});
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const [notice, setNotice] = useState(null);
    const [activeTab, setActiveTab] = useState('general');
    const [showResetModal, setShowResetModal] = useState(false);

    // Persist active tab in localStorage
    useEffect(() => {
        const savedTab = localStorage.getItem('plugin-composer-active-tab');
        if (savedTab) {
            setActiveTab(savedTab);
        }
    }, []);

    const handleTabChange = (tabName) => {
        setActiveTab(tabName);
        localStorage.setItem('plugin-composer-active-tab', tabName);
    };

    useEffect(() => {
        loadSettings();
    }, []);

    const loadSettings = async () => {
        try {
            const response = await apiFetch({
                path: '/plugin-composer/v1/settings',
                method: 'GET',
            });
            console.log('Loaded settings:', response);
            console.log('Loaded settings keys:', Object.keys(response));
            setSettings(response);
        } catch (error) {
            console.error('Error loading settings:', error);
            setNotice({
                type: 'error',
                message: __('Error loading settings.', 'welabs-plugin-composer')
            });
        } finally {
            setLoading(false);
        }
    };

    const saveSettings = async () => {
        setSaving(true);
        setNotice(null);

        console.log('Saving settings:', settings);
        console.log('Settings keys:', Object.keys(settings));
        console.log('Rate limit attempts value:', settings.rate_limit_attempts, 'Type:', typeof settings.rate_limit_attempts);

        try {
            const response = await apiFetch({
                path: '/plugin-composer/v1/settings',
                method: 'POST',
                data: settings,
            });
            
            if (response.success) {
                // Reload settings to ensure UI reflects saved values
                await loadSettings();
                setNotice({
                    type: 'success',
                    message: __('Settings saved successfully!', 'welabs-plugin-composer')
                });
            } else {
                setNotice({
                    type: 'error',
                    message: response.errors ? response.errors.join(', ') : __('Error saving settings.', 'welabs-plugin-composer')
                });
            }
        } catch (error) {
            console.error('Error saving settings:', error);
            let errorMessage = __('Error saving settings.', 'welabs-plugin-composer');
            
            if (error.data && error.data.errors) {
                errorMessage = Array.isArray(error.data.errors) ? error.data.errors.join(', ') : error.data.errors;
            }
            
            setNotice({
                type: 'error',
                message: errorMessage
            });
        } finally {
            setSaving(false);
        }
    };

    const updateSetting = (key, value) => {
        setSettings(prev => ({
            ...prev,
            [key]: value
        }));
    };

    // Helper function to safely get numeric values
    const getNumericValue = (value, defaultValue = 0) => {
        const parsed = parseInt(value);
        return isNaN(parsed) ? defaultValue : parsed;
    };

    const handleResetClick = () => {
        setShowResetModal(true);
    };

    const handleResetConfirm = async () => {
        setShowResetModal(false);
        setSaving(true);
        setNotice(null);

        try {
            const response = await apiFetch({
                path: '/plugin-composer/v1/settings/reset',
                method: 'POST',
            });
            
            if (response.success) {
                await loadSettings();
                setNotice({
                    type: 'success',
                    message: __('Settings reset to defaults successfully!', 'welabs-plugin-composer')
                });
            } else {
                setNotice({
                    type: 'error',
                    message: __('Error resetting settings.', 'welabs-plugin-composer')
                });
            }
        } catch (error) {
            console.error('Error resetting settings:', error);
            setNotice({
                type: 'error',
                message: __('Error resetting settings.', 'welabs-plugin-composer')
            });
        } finally {
            setSaving(false);
        }
    };

    const handleResetCancel = () => {
        setShowResetModal(false);
    };

    const getTabTitle = (tabName) => {
        const tabTitles = {
            'general': __('General', 'welabs-plugin-composer'),
            'rate-limiting': __('Rate Limiting', 'welabs-plugin-composer'),
            'validation': __('Validation Rules', 'welabs-plugin-composer'),
            'file-settings': __('File Settings', 'welabs-plugin-composer'),
            'advanced': __('Advanced', 'welabs-plugin-composer'),
        };
        return tabTitles[tabName] || tabName;
    };

    if (loading) {
        return (
            <div style={{ textAlign: 'center', padding: '20px' }}>
                <Spinner />
                <p>{__('Loading settings...', 'welabs-plugin-composer')}</p>
            </div>
        );
    }

    return (
        <div className="plugin-composer-settings">
            {notice && (
                <Notice 
                    status={notice.type} 
                    isDismissible={true}
                    onRemove={() => setNotice(null)}
                >
                    {notice.message}
                </Notice>
            )}

            <TabPanel
                className="plugin-composer-tabs"
                activeClass="active-tab"
                initialTabName={activeTab}
                onSelect={handleTabChange}
                tabs={[
                    {
                        name: 'general',
                        title: __('General', 'welabs-plugin-composer'),
                        className: 'tab-general',
                    },
                    {
                        name: 'rate-limiting',
                        title: __('Rate Limiting', 'welabs-plugin-composer'),
                        className: 'tab-rate-limiting',
                    },
                    {
                        name: 'validation',
                        title: __('Validation Rules', 'welabs-plugin-composer'),
                        className: 'tab-validation',
                    },
                    {
                        name: 'file-settings',
                        title: __('File Settings', 'welabs-plugin-composer'),
                        className: 'tab-file-settings',
                    },
                    {
                        name: 'advanced',
                        title: __('Advanced', 'welabs-plugin-composer'),
                        className: 'tab-advanced',
                    },
                ]}
            >
                {(tab) => (
                    <div className={`tab-content tab-${tab.name}`}>
                        <Card>
                            <CardHeader>
                                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                                    <div>
                                        <h3 style={{ margin: 0, fontSize: '1.2em', fontWeight: '500' }}>
                                            {getTabTitle(tab.name)}
                                        </h3>
                                    </div>
                                    <Button
                                        isSecondary
                                        onClick={handleResetClick}
                                        disabled={saving}
                                        style={{ marginLeft: 'auto' }}
                                    >
                                        {__('Reset to Defaults', 'welabs-plugin-composer')}
                                    </Button>
                                </div>
                            </CardHeader>
                            <CardBody>
                                {tab.name === 'general' && (
                                    <PanelBody>
                                        <PanelRow>
                                            <ToggleControl
                                                label={__('Allow Guest Access', 'welabs-plugin-composer')}
                                                help={__('Allow non-logged-in users to generate plugins', 'welabs-plugin-composer')}
                                                checked={settings.allow_guest_access || false}
                                                onChange={(value) => updateSetting('allow_guest_access', value)}
                                            />
                                        </PanelRow>
                                        
                                        <PanelRow>
                                            <SelectControl
                                                label={__('Required Capability', 'welabs-plugin-composer')}
                                                help={__('Minimum capability required for logged-in users', 'welabs-plugin-composer')}
                                                value={settings.required_capability || 'edit_posts'}
                                                options={[
                                                    { label: __('Read', 'welabs-plugin-composer'), value: 'read' },
                                                    { label: __('Edit Posts', 'welabs-plugin-composer'), value: 'edit_posts' },
                                                    { label: __('Publish Posts', 'welabs-plugin-composer'), value: 'publish_posts' },
                                                    { label: __('Manage Options', 'welabs-plugin-composer'), value: 'manage_options' },
                                                ]}
                                                onChange={(value) => updateSetting('required_capability', value)}
                                            />
                                        </PanelRow>

                                        <PanelRow>
                                            <SelectControl
                                                label={__('Default Plugin Type', 'welabs-plugin-composer')}
                                                help={__('Default plugin type when generating new plugins', 'welabs-plugin-composer')}
                                                value={settings.default_plugin_type || 'container_based'}
                                                options={[
                                                    { label: __('Container Based', 'welabs-plugin-composer'), value: 'container_based' },
                                                    { label: __('Classic', 'welabs-plugin-composer'), value: 'classic' },
                                                ]}
                                                onChange={(value) => updateSetting('default_plugin_type', value)}
                                            />
                                        </PanelRow>

                                        <PanelRow>
                                            <TextControl
                                                label={__('Default Namespace', 'welabs-plugin-composer')}
                                                help={__('Default namespace for generated plugins (e.g., MyPlugin or AB/AC for multi-word)', 'welabs-plugin-composer')}
                                                value={settings.default_namespace ?? 'MyPlugin'}
                                                onChange={(value) => updateSetting('default_namespace', value)}
                                                placeholder="MyPlugin"
                                            />
                                        </PanelRow>

                                        <PanelRow>
                                            <TextControl
                                                label={__('Default Author Name', 'welabs-plugin-composer')}
                                                help={__('Default author name for generated plugins', 'welabs-plugin-composer')}
                                                value={settings.default_author_name ?? 'Your Name'}
                                                onChange={(value) => updateSetting('default_author_name', value)}
                                                placeholder="Your Name"
                                            />
                                        </PanelRow>

                                        <PanelRow>
                                            <TextControl
                                                label={__('Default Author URL', 'welabs-plugin-composer')}
                                                help={__('Default author URL for generated plugins', 'welabs-plugin-composer')}
                                                value={settings.default_author_url ?? 'https://example.com'}
                                                onChange={(value) => updateSetting('default_author_url', value)}
                                                placeholder="https://example.com"
                                            />
                                        </PanelRow>
                                    </PanelBody>
                                )}

                                {tab.name === 'rate-limiting' && (
                                    <PanelBody>
                                        <PanelRow>
                                            <RangeControl
                                                label={__('Rate Limit Attempts', 'welabs-plugin-composer')}
                                                help={__('Maximum number of plugin generation attempts per time period', 'welabs-plugin-composer')}
                                                value={settings.rate_limit_attempts || 5}
                                                onChange={(value) => updateSetting('rate_limit_attempts', value)}
                                                min={1}
                                                max={100}
                                            />
                                        </PanelRow>
                                        
                                        <PanelRow>
                                            <TextControl
                                                type="number"
                                                label={__('Rate Limit Duration (seconds)', 'welabs-plugin-composer')}
                                                help={__('Time period for rate limiting in seconds', 'welabs-plugin-composer')}
                                                value={settings.rate_limit_duration || 3600}
                                                onChange={(value) => updateSetting('rate_limit_duration', value)}
                                            />
                                        </PanelRow>
                                    </PanelBody>
                                )}

                                {tab.name === 'validation' && (
                                    <PanelBody>
                                        <PanelRow>
                                            <TextControl
                                                type="number"
                                                label={__('Max Plugin Name Length', 'welabs-plugin-composer')}
                                                help={__('Maximum allowed length for plugin names', 'welabs-plugin-composer')}
                                                value={settings.max_plugin_name_length || 100}
                                                onChange={(value) => updateSetting('max_plugin_name_length', value)}
                                            />
                                        </PanelRow>
                                        
                                        <PanelRow>
                                            <TextControl
                                                type="number"
                                                label={__('Max Description Length', 'welabs-plugin-composer')}
                                                help={__('Maximum allowed length for plugin descriptions', 'welabs-plugin-composer')}
                                                value={settings.max_description_length || 500}
                                                onChange={(value) => updateSetting('max_description_length', value)}
                                            />
                                        </PanelRow>
                                        
                                        <PanelRow>
                                            <TextControl
                                                type="number"
                                                label={__('Max License Length', 'welabs-plugin-composer')}
                                                help={__('Maximum allowed length for license text', 'welabs-plugin-composer')}
                                                value={settings.max_license_length || 50}
                                                onChange={(value) => updateSetting('max_license_length', value)}
                                            />
                                        </PanelRow>
                                        
                                        <PanelRow>
                                            <TextControl
                                                type="number"
                                                label={__('Max Author Name Length', 'welabs-plugin-composer')}
                                                help={__('Maximum allowed length for author names', 'welabs-plugin-composer')}
                                                value={settings.max_author_name_length || 100}
                                                onChange={(value) => updateSetting('max_author_name_length', value)}
                                            />
                                        </PanelRow>
                                    </PanelBody>
                                )}

                                {tab.name === 'file-settings' && (
                                    <PanelBody>
                                        <PanelRow>
                                            <TextControl
                                                type="number"
                                                label={__('File Permissions', 'welabs-plugin-composer')}
                                                help={__('Octal permissions for generated files (e.g., 755, 644)', 'welabs-plugin-composer')}
                                                value={settings.file_permissions || 755}
                                                onChange={(value) => updateSetting('file_permissions', value)}
                                            />
                                        </PanelRow>
                                        
                                        <PanelRow>
                                            <div>
                                                <label className="components-base-control__label">
                                                    {__('Allowed Plugin Types', 'welabs-plugin-composer')}
                                                </label>
                                                <p className="components-base-control__help">
                                                    {__('Plugin types that users can generate. At least one must be selected.', 'welabs-plugin-composer')}
                                                </p>
                                                <div style={{ marginTop: '8px' }}>
                                                    <label style={{ display: 'flex', alignItems: 'center', marginBottom: '8px' }}>
                                                        <input
                                                            type="checkbox"
                                                            checked={settings.allowed_plugin_types?.includes('container_based') || false}
                                                            onChange={(e) => {
                                                                const currentTypes = settings.allowed_plugin_types || ['container_based', 'classic'];
                                                                if (e.target.checked) {
                                                                    updateSetting('allowed_plugin_types', [...currentTypes, 'container_based']);
                                                                } else {
                                                                    const newTypes = currentTypes.filter(type => type !== 'container_based');
                                                                    if (newTypes.length > 0) {
                                                                        updateSetting('allowed_plugin_types', newTypes);
                                                                        if (settings.default_plugin_type === 'container_based') {
                                                                            updateSetting('default_plugin_type', newTypes[0]);
                                                                        }
                                                                    }
                                                                }
                                                            }}
                                                            style={{ marginRight: '8px' }}
                                                        />
                                                        {__('Container Based', 'welabs-plugin-composer')}
                                                    </label>
                                                    <label style={{ display: 'flex', alignItems: 'center' }}>
                                                        <input
                                                            type="checkbox"
                                                            checked={settings.allowed_plugin_types?.includes('classic') || false}
                                                            onChange={(e) => {
                                                                const currentTypes = settings.allowed_plugin_types || ['container_based', 'classic'];
                                                                if (e.target.checked) {
                                                                    updateSetting('allowed_plugin_types', [...currentTypes, 'classic']);
                                                                } else {
                                                                    const newTypes = currentTypes.filter(type => type !== 'classic');
                                                                    if (newTypes.length > 0) {
                                                                        updateSetting('allowed_plugin_types', newTypes);
                                                                        if (settings.default_plugin_type === 'classic') {
                                                                            updateSetting('default_plugin_type', newTypes[0]);
                                                                        }
                                                                    }
                                                                }
                                                            }}
                                                            style={{ marginRight: '8px' }}
                                                        />
                                                        {__('Classic', 'welabs-plugin-composer')}
                                                    </label>
                                                </div>
                                                {settings.allowed_plugin_types && settings.allowed_plugin_types.length === 0 && (
                                                    <p style={{ color: '#d63638', fontSize: '12px', marginTop: '4px' }}>
                                                        {__('At least one plugin type must be selected.', 'welabs-plugin-composer')}
                                                    </p>
                                                )}
                                            </div>
                                        </PanelRow>
                                        
                                        <PanelRow>
                                            <TextControl
                                                label={__('Allowed File Extensions', 'welabs-plugin-composer')}
                                                help={__('Comma-separated list of allowed file extensions', 'welabs-plugin-composer')}
                                                value={Array.isArray(settings.allowed_file_extensions) ? settings.allowed_file_extensions.join(', ') : ''}
                                                onChange={(value) => updateSetting('allowed_file_extensions', value.split(',').map(ext => ext.trim()).filter(ext => ext))}
                                                placeholder="php, js, css, json, md, txt, xml"
                                            />
                                        </PanelRow>
                                    </PanelBody>
                                )}

                                {tab.name === 'advanced' && (
                                    <PanelBody>
                                        <PanelRow>
                                            <ToggleControl
                                                label={__('Enable Debug Mode', 'welabs-plugin-composer')}
                                                help={__('Enable detailed logging for troubleshooting', 'welabs-plugin-composer')}
                                                checked={settings.enable_debug_mode || false}
                                                onChange={(value) => updateSetting('enable_debug_mode', value)}
                                            />
                                        </PanelRow>
                                        
                                        <PanelRow>
                                            <ToggleControl
                                                label={__('Auto-cleanup Generated Files', 'welabs-plugin-composer')}
                                                help={__('Automatically clean up generated files after download', 'welabs-plugin-composer')}
                                                checked={settings.auto_cleanup_files || true}
                                                onChange={(value) => updateSetting('auto_cleanup_files', value)}
                                            />
                                        </PanelRow>
                                        
                                        <PanelRow>
                                            <TextControl
                                                type="number"
                                                label={__('File Cleanup Delay (minutes)', 'welabs-plugin-composer')}
                                                help={__('Time to wait before cleaning up generated files', 'welabs-plugin-composer')}
                                                value={settings.file_cleanup_delay || 30}
                                                onChange={(value) => updateSetting('file_cleanup_delay', value)}
                                            />
                                        </PanelRow>
                                        
                                        <PanelRow>
                                            <ToggleControl
                                                label={__('Enable Plugin Preview', 'welabs-plugin-composer')}
                                                help={__('Allow users to preview generated plugins before download', 'welabs-plugin-composer')}
                                                checked={settings.enable_plugin_preview || false}
                                                onChange={(value) => updateSetting('enable_plugin_preview', value)}
                                            />
                                        </PanelRow>
                                    </PanelBody>
                                )}
                            </CardBody>
                        </Card>
                    </div>
                )}
            </TabPanel>

            <div style={{ marginTop: '20px', display: 'flex', justifyContent: 'flex-start', alignItems: 'center' }}>
                <Button
                    isPrimary
                    onClick={saveSettings}
                    disabled={saving}
                >
                    {saving ? (
                        <>
                            <Spinner />
                            {__('Saving...', 'welabs-plugin-composer')}
                        </>
                    ) : (
                        __('Save Settings', 'welabs-plugin-composer')
                    )}
                </Button>
            </div>

            {showResetModal && (
                <Modal
                    title={__('Confirm Reset', 'welabs-plugin-composer')}
                    onClose={handleResetCancel}
                    shouldCloseOnClickOutside={false}
                >
                    <div style={{ padding: '20px' }}>
                        <p>{__('Are you sure you want to reset all settings to their default values?', 'welabs-plugin-composer')}</p>
                        <p style={{ color: '#d63638', fontSize: '12px', marginTop: '4px' }}>
                            {__('This action cannot be undone.', 'welabs-plugin-composer')}
                        </p>
                    </div>
                    <div style={{ padding: '20px', display: 'flex', justifyContent: 'flex-end', gap: '10px' }}>
                        <Button 
                            isSecondary
                            onClick={handleResetCancel}
                        >
                            {__('Cancel', 'welabs-plugin-composer')}
                        </Button>
                        <Button 
                            isPrimary
                            onClick={handleResetConfirm} 
                            disabled={saving}
                        >
                            {saving ? (
                                <>
                                    <Spinner />
                                    {__('Resetting...', 'welabs-plugin-composer')}
                                </>
                            ) : (
                                __('Reset Settings', 'welabs-plugin-composer')
                            )}
                        </Button>
                    </div>
                </Modal>
            )}
        </div>
    );
}

// Export for use in WordPress
window.PluginComposerSettings = PluginComposerSettings;

// Render the component when DOM is ready
wp.domReady(() => {
    const container = document.getElementById('plugin-composer-settings-app');
    if (container) {
        wp.element.render(<PluginComposerSettings />, container);
    }
}); 