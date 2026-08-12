import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';

export default function Edit() {
	const blockProps = useBlockProps( {
		className: 'sm-fbt-products',
	} );

	return (
		<div { ...blockProps }>
			<div className="sm-fbt-products__placeholder">
				<span className="sm-fbt-products__placeholder-icon">📦</span>
				<span className="sm-fbt-products__placeholder-text">
					{ __(
						'Frequently bought together products will render here.',
						'suitemart'
					) }
				</span>
			</div>
		</div>
	);
}
