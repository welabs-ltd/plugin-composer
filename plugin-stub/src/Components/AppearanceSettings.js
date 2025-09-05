import { __ } from '@wordpress/i18n';
import { useState, useEffect } from 'react';
import { Button, Card, CardBody, Notice, ColorPicker, Spinner } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';

const OrderSettings = () => {
	const [ primaryColor, setPrimaryColor ] = useState('');
	const [ textColor, setTextColor ] = useState('');
	const [ isLoading, setIsLoading ] = useState( false );
	const [ message, setMessage ] = useState( '' );
	const [ error, setError ] = useState( '' );

	useEffect(() => {
		setIsLoading( true );
        const fetchSettings = async () => {
            try {
                const response = await apiFetch({
                    path: '/plugin-stub/v1/settings',
                });

                if( response.plugin_stub_primary_color ){
                	setPrimaryColor(response.plugin_stub_primary_color);
				}

                if( response.plugin_stub_text_color ){
                	setTextColor(response.plugin_stub_text_color);
				}
                setError(null); // Clear any previous errors
				setIsLoading( false );
            } catch (err) {
                setError( err.message );
				setIsLoading( false );
            }
        };

        fetchSettings();
    }, []);

	const handleSubmit = async ( event ) => {
		event.preventDefault();
		setIsLoading( true );
		try {
			const res = await apiFetch( {
				path: '/plugin-stub/v1/settings',
				method: 'POST',
				data: {
					plugin_stub_primary_color: primaryColor,
					plugin_stub_text_color: textColor,
				},
			} );
			setMessage(
				__( 'Settings saved successfully!', 'plugin-stub' )
			);
			setError( '' );
			setIsLoading( false );
		} catch (error) {
			setError( error.message );
			setMessage( '' );
			setIsLoading( false );
		}
		
	};

	return (
		<div>
			<div className='settings-header'>
				<div className='settings-header-icon'>
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" className="bi bi-gear" viewBox="0 0 16 16">
						<path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0"/>
						<path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z"/>
					</svg>
				</div>
				<h2>{ __( 'Appearance', 'plugin-stub' ) }</h2>
			</div>
			{ message && (
				<Notice
					className="w-full mb-4"
					status="success"
					isDismissible
					onDismiss={ () => setMessage( '' ) }
				>
					{ message }
				</Notice>
			) }
			{ error && (
				<Notice
					className="w-full mb-4"
					status="error"
					isDismissible
					onDismiss={ () => setError( '' ) }
				>
					{ error }
				</Notice>
			) }

			<form onSubmit={ handleSubmit }>
				<Card>
					<CardBody>
						<div>
							<label>{ __( 'Primary color', 'plugin-stub' ) }</label>
							<ColorPicker
								color={primaryColor}
								onChange={setPrimaryColor}
								enableAlpha
								defaultValue="#fff"
							/>
						</div>
						<div>
							<label>{ __( 'Text color', 'plugin-stub' ) }</label>
							<ColorPicker
								color={textColor}
								onChange={setTextColor}
								enableAlpha
								defaultValue="#000"
							/>
						</div>
						<Button variant="primary" type="submit" disabled={ isLoading }>
							{ isLoading && <Spinner /> }
							{ __( 'Save Changes', 'plugin-stub' ) }
						</Button>
					</CardBody>
				</Card>
			</form>
		</div>
	);
};

export default OrderSettings;