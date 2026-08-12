import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
	InnerBlocks,
} from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';

import './editor.scss';

export default function Edit( { attributes, setAttributes } ) {
	const { title } = attributes;

	const blockProps = useBlockProps( {
		className: 'sm-size-guide sm-size-guide--editor',
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'suitemart' ) }>
					<TextControl
						label={ __( 'Modal title', 'suitemart' ) }
						value={ title }
						onChange={ ( value ) =>
							setAttributes( { title: value } )
						}
						placeholder={ __( 'Size guide', 'suitemart' ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<div className="sm-size-guide__dialog" role="dialog">
					<div className="sm-size-guide__header">
						<h2 className="sm-size-guide__title">
							{ title || __( 'Size guide', 'suitemart' ) }
						</h2>
					</div>
					<div className="sm-size-guide__content">
						<InnerBlocks />
					</div>
				</div>
			</div>
		</>
	);
}
