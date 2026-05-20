import { registerBlockType } from '@wordpress/blocks';

import metadata from './block.json';
import Edit from './edit';
import Icon from './icon';

registerBlockType( metadata.name, {
	icon: <Icon />,
	edit: Edit,
	save: () => null,
} );
