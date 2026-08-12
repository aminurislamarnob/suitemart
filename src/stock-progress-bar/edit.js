import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';

export default function Edit() {
	const blockProps = useBlockProps( {
		className: 'sm-stock-progress-bar',
	} );

	return (
		<div { ...blockProps }>
			<div className="sm-stock-progress-bar__message">
				{ /* translators: %d: quantity in stock. */ }
				{ __( 'Only 5 left', 'suitemart' ) }
			</div>
			<div className="sm-stock-progress-bar__track">
				<div
					className="sm-stock-progress-bar__fill"
					style={ { width: '25%' } }
				></div>
			</div>
		</div>
	);
}
