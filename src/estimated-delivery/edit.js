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
	const { minDays, maxDays } = attributes;

	const blockProps = useBlockProps();

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Delivery time', 'suitemart' ) }>
					<RangeControl
						label={ __( 'Minimum working days', 'suitemart' ) }
						value={ minDays }
						onChange={ ( value ) =>
							setAttributes( { minDays: value } )
						}
						min={ 0 }
						max={ 30 }
					/>
					<RangeControl
						label={ __( 'Maximum working days', 'suitemart' ) }
						value={ maxDays }
						onChange={ ( value ) =>
							setAttributes( { maxDays: value } )
						}
						min={ minDays }
						max={ 60 }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				{ /*
				 * The dates are counted forward in working days from today, so
				 * they cannot be written into a preview — a fixed pair here read
				 * as the real answer and went stale the day it was typed.
				 */ }
				<DynamicPreview
					block="suitemart/estimated-delivery"
					attributes={ attributes }
					postId={ context.postId }
					isSelected={ isSelected }
					emptyLabel={ __(
						'Virtual and downloadable products are not shipped, so no delivery estimate is shown.',
						'suitemart'
					) }
				/>
			</div>
		</>
	);
}
