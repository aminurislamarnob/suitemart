import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';

export default function Edit( { attributes, setAttributes, context } ) {
	const { layout } = attributes;
	const { postId } = context;

	const blockProps = useBlockProps();

	return (
		<div { ...blockProps }>
			<InspectorControls>
				<PanelBody title={ __( 'Gallery Settings', 'suitemart' ) }>
					<SelectControl
						label={ __( 'Layout', 'suitemart' ) }
						value={ layout }
						options={ [
							{
								label: __(
									'Horizontal (Thumbnails below)',
									'suitemart'
								),
								value: 'horizontal',
							},
							{
								label: __(
									'Vertical (Thumbnails side)',
									'suitemart'
								),
								value: 'vertical',
							},
							{
								label: __( 'Grid (No carousel)', 'suitemart' ),
								value: 'grid',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { layout: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<ServerSideRender
				block="suitemart/product-gallery"
				attributes={ attributes }
				urlQueryArgs={ { post_id: postId } }
			/>
		</div>
	);
}
