import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import DynamicPreview from '../_shared/DynamicPreview';

export default function Edit( { attributes, context, isSelected } ) {
	const blockProps = useBlockProps();

	return (
		<div { ...blockProps }>
			<DynamicPreview
				block="suitemart/fbt-products"
				attributes={ attributes }
				postId={ context.postId }
				isSelected={ isSelected }
				emptyLabel={ __(
					'This bundle needs at least two purchasable products: set cross-sells on this product to fill it.',
					'suitemart'
				) }
			/>
		</div>
	);
}
