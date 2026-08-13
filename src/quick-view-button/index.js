import { registerBlockType } from '@wordpress/blocks';
import metadata from './block.json';
import edit from './edit';

// The build only emits `style-index.css` for a sheet something imports, and
// `block.json` names that file whether or not it exists. Drop this line and the
// block still registers, still renders, and quietly loses every rule it has.
import './style.scss';

registerBlockType( metadata.name, {
	edit,
} );
