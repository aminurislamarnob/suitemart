import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import DynamicPreview from '../_shared/DynamicPreview';

export default function Edit( { attributes, context, isSelected } ) {
	const blockProps = useBlockProps();

	return (
		<div { ...blockProps }>
			<DynamicPreview
				block="suitemart/stock-progress-bar"
				attributes={ attributes }
				postId={ context.postId }
				isSelected={ isSelected }
				emptyLabel={ __(
					'This product does not manage stock, so there is no quantity to show a bar for.',
					'suitemart'
				) }
			/>
		</div>
	);
}
