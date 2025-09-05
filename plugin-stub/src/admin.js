import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';
import {
	HashRouter as Router,
	Routes,
	Route
} from 'react-router-dom';
import './styles/styles.css';
import './styles/index.css';
import Layout from './Components/Layout';
import AppearanceSettings from './Components/AppearanceSettings';
import GeneralSettings from './Components/GeneralSettings';
import ProductSettings from './Components/ProductSettings';

const App = () => (
	<Router>
		<Routes>
			<Route path="/" element={ <Layout /> }>
                <Route index element={ <GeneralSettings /> } />
                <Route path="appearance-settings" element={ <AppearanceSettings /> } />
                <Route path="product-settings" element={ <ProductSettings /> } />
                {/* Add more routes here */}
            </Route>
		</Routes>
	</Router>
);

document.addEventListener( 'DOMContentLoaded', () => {
	const container = document.getElementById(
		'PluginStubSettings'
	);
	const root = createRoot( container );
	root.render( <App /> );
} );
