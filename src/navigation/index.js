import { registerBlockType } from '@wordpress/blocks';
import metadata from './block.json';
import edit from './edit';

// wp-scripts compiles an imported `style.scss` to `style-index.css`, which is
// what block.json's `style` field points at. WordPress then loads it only on
// pages where this block appears.
import './style.scss';

registerBlockType( metadata.name, { edit } );
