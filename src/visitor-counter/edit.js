import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RangeControl } from '@wordpress/components';
import DynamicPreview from '../_shared/DynamicPreview';

export default function Edit( {
	attributes,
	setAttributes,
	context,
	isSelected,
} ) {
	const { minVisitors, maxVisitors } = attributes;

	const blockProps = useBlockProps();

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'suitemart' ) }>
					<RangeControl
						label={ __( 'Minimum visitors', 'suitemart' ) }
						value={ minVisitors }
						onChange={ ( value ) =>
							setAttributes( { minVisitors: value } )
						}
						min={ 1 }
						max={ 1000 }
					/>
					<RangeControl
						label={ __( 'Maximum visitors', 'suitemart' ) }
						value={ maxVisitors }
						onChange={ ( value ) =>
							setAttributes( { maxVisitors: value } )
						}
						min={ minVisitors }
						max={ 2000 }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				{ /*
				 * The preview used to print `maxVisitors`, which is the one
				 * number the front end will not show: it picks a figure inside
				 * the range, or takes a real one from the
				 * `suitemart_visitor_count` filter. Rendering through the server
				 * means a site that has wired that filter up previews its own
				 * measured figure instead of a fabricated one.
				 */ }
				<DynamicPreview
					block="suitemart/visitor-counter"
					attributes={ attributes }
					postId={ context.postId }
					isSelected={ isSelected }
				/>
			</div>
		</>
	);
}
