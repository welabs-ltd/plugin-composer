import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
	PanelColorSettings,
} from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	ToggleControl,
	Disabled,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

const PLACEHOLDER_FIELDS = [
	[ 'placeholderPluginName', __( 'Plugin Name', 'plugin-composer' ) ],
	[ 'placeholderPluginDescription', __( 'Plugin Description', 'plugin-composer' ) ],
	[ 'placeholderPluginRequires', __( 'Requires Plugins', 'plugin-composer' ) ],
	[ 'placeholderPluginLicense', __( 'Plugin License', 'plugin-composer' ) ],
	[ 'placeholderPluginUri', __( 'Plugin URL', 'plugin-composer' ) ],
	[ 'placeholderPluginAuthorName', __( 'Author Name', 'plugin-composer' ) ],
	[ 'placeholderPluginAuthorEmail', __( 'Author Email', 'plugin-composer' ) ],
	[ 'placeholderPluginAuthorUri', __( 'Author URL', 'plugin-composer' ) ],
];

export default function Edit( { attributes, setAttributes } ) {
	const {
		submitText,
		showSettingsField,
		showWpvipField,
		buttonBgColor,
		buttonTextColor,
		buttonBgHoverColor,
		buttonTextHoverColor,
	} = attributes;
	const blockProps = useBlockProps();

	return (
		<div { ...blockProps }>
			<InspectorControls>
				<PanelBody title={ __( 'Form settings', 'plugin-composer' ) }>
					<TextControl
						label={ __( 'Submit button text', 'plugin-composer' ) }
						value={ submitText }
						onChange={ ( value ) =>
							setAttributes( { submitText: value } )
						}
					/>
					<ToggleControl
						label={ __(
							'Show "Include Plugin Settings?" field',
							'plugin-composer'
						) }
						checked={ showSettingsField }
						onChange={ ( value ) =>
							setAttributes( { showSettingsField: value } )
						}
					/>
					<ToggleControl
						label={ __(
							'Show "WP VIP Support" field',
							'plugin-composer'
						) }
						checked={ showWpvipField }
						onChange={ ( value ) =>
							setAttributes( { showWpvipField: value } )
						}
					/>
				</PanelBody>
				<PanelColorSettings
					title={ __( 'Submit button colors', 'plugin-composer' ) }
					initialOpen={ false }
					colorSettings={ [
						{
							value: buttonBgColor,
							onChange: ( value ) =>
								setAttributes( { buttonBgColor: value } ),
							label: __( 'Background', 'plugin-composer' ),
						},
						{
							value: buttonTextColor,
							onChange: ( value ) =>
								setAttributes( { buttonTextColor: value } ),
							label: __( 'Text', 'plugin-composer' ),
						},
						{
							value: buttonBgHoverColor,
							onChange: ( value ) =>
								setAttributes( { buttonBgHoverColor: value } ),
							label: __( 'Background (hover)', 'plugin-composer' ),
						},
						{
							value: buttonTextHoverColor,
							onChange: ( value ) =>
								setAttributes( { buttonTextHoverColor: value } ),
							label: __( 'Text (hover)', 'plugin-composer' ),
						},
					] }
				/>
				<PanelBody
					title={ __( 'Field placeholders', 'plugin-composer' ) }
					initialOpen={ false }
				>
					{ PLACEHOLDER_FIELDS.map( ( [ key, label ] ) => (
						<TextControl
							key={ key }
							label={ label }
							value={ attributes[ key ] || '' }
							onChange={ ( value ) =>
								setAttributes( { [ key ]: value } )
							}
						/>
					) ) }
				</PanelBody>
			</InspectorControls>
			<Disabled>
				<ServerSideRender
					block="welabs/plugin-composer"
					attributes={ attributes }
				/>
			</Disabled>
		</div>
	);
}
