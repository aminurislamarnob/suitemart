import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import DynamicPreview from '../_shared/DynamicPreview';

export default function Edit( { attributes, context, isSelected } ) {
	const blockProps = useBlockProps();

	return (
		<div { ...blockProps }>
			<DynamicPreview
				block="suitemart/product-labels"
				attributes={ attributes }
				postId={ context.postId }
				isSelected={ isSelected }
				emptyLabel={ __(
					'This product has no Sale, New or Out of stock badge to show.',
					'suitemart'
				) }
			/>
		</div>
	);
}
