import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	CheckboxControl,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

export default function Edit( { attributes, setAttributes, context } ) {
	const { layout, units } = attributes;

	const blockProps = useBlockProps( {
		className: 'sm-product-countdown-wrapper',
	} );

	const toggleUnit = ( unit ) => {
		const newUnits = units.includes( unit )
			? units.filter( ( u ) => u !== unit )
			: [ ...units, unit ];
		setAttributes( { units: newUnits } );
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'suitemart' ) }>
					<SelectControl
						label={ __( 'Layout', 'suitemart' ) }
						value={ layout }
						options={ [
							{
								label: __( 'Inline', 'suitemart' ),
								value: 'inline',
							},
							{
								label: __( 'Boxed', 'suitemart' ),
								value: 'boxed',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { layout: value } )
						}
					/>
					<fieldset>
						<legend style={ { marginBottom: '8px' } }>
							{ __( 'Units', 'suitemart' ) }
						</legend>
						{ [ 'days', 'hours', 'minutes', 'seconds' ].map(
							( unit ) => (
								<CheckboxControl
									key={ unit }
									label={ unit }
									checked={ units.includes( unit ) }
									onChange={ () => toggleUnit( unit ) }
								/>
							)
						) }
					</fieldset>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<ServerSideRender
					block="suitemart/product-countdown"
					attributes={ attributes }
					urlQueryArgs={ { post_id: context.postId } }
				/>
			</div>
		</>
	);
}
