import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
	RichText,
} from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	RangeControl,
	CheckboxControl,
} from '@wordpress/components';
import Icon from '../_shared/Icon';
import { NETWORKS } from './networks';

export default function Edit( { attributes, setAttributes } ) {
	const { networks, appearance, shape, iconSize, heading } = attributes;

	const blockProps = useBlockProps( {
		className: `sm-share sm-share--${ appearance } sm-share--${ shape }`,
	} );

	const toggleNetwork = ( id, enabled ) =>
		setAttributes( {
			// Rebuilt from NETWORKS rather than appended to, so the rendered
			// order always matches the order shown in this panel.
			networks: NETWORKS.filter( ( n ) =>
				n.id === id ? enabled : networks.includes( n.id )
			).map( ( n ) => n.id ),
		} );

	const selected = NETWORKS.filter( ( n ) => networks.includes( n.id ) );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Networks', 'suitemart' ) }>
					{ NETWORKS.map( ( network ) => (
						<CheckboxControl
							__nextHasNoMarginBottom
							key={ network.id }
							label={ network.label }
							checked={ networks.includes( network.id ) }
							onChange={ ( enabled ) =>
								toggleNetwork( network.id, enabled )
							}
						/>
					) ) }
				</PanelBody>
				<PanelBody title={ __( 'Appearance', 'suitemart' ) }>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Style', 'suitemart' ) }
						help={ __(
							'Icon-only controls still carry a text label for screen readers.',
							'suitemart'
						) }
						value={ appearance }
						options={ [
							{
								label: __( 'Icon only', 'suitemart' ),
								value: 'icon',
							},
							{
								label: __( 'Icon and label', 'suitemart' ),
								value: 'icon-label',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { appearance: value } )
						}
					/>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Shape', 'suitemart' ) }
						value={ shape }
						options={ [
							{
								label: __( 'Circle', 'suitemart' ),
								value: 'circle',
							},
							{
								label: __( 'Rounded', 'suitemart' ),
								value: 'rounded',
							},
							{
								label: __( 'Square', 'suitemart' ),
								value: 'square',
							},
							{
								label: __( 'No background', 'suitemart' ),
								value: 'bare',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { shape: value } )
						}
					/>
					<RangeControl
						__nextHasNoMarginBottom
						label={ __( 'Icon size', 'suitemart' ) }
						value={ iconSize }
						min={ 12 }
						max={ 48 }
						onChange={ ( value ) =>
							setAttributes( { iconSize: value ?? 18 } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<RichText
					identifier="heading"
					tagName="p"
					className="sm-share__heading"
					value={ heading }
					onChange={ ( value ) =>
						setAttributes( { heading: value } )
					}
					placeholder={ __( 'Share this…', 'suitemart' ) }
					allowedFormats={ [] }
				/>
				<ul className="sm-share__list">
					{ selected.map( ( network ) => (
						<li key={ network.id } className="sm-share__item">
							<span className="sm-share__link">
								<Icon
									name={
										network.id === 'copy'
											? 'link'
											: `share-${ network.id }`
									}
									size={ iconSize }
								/>
								{ appearance === 'icon-label' && (
									<span className="sm-share__label">
										{ network.label }
									</span>
								) }
							</span>
						</li>
					) ) }
				</ul>
			</div>
		</>
	);
}
