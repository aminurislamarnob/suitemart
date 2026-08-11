import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	TextControl,
	TextareaControl,
	RangeControl,
	ToggleControl,
	Notice,
	Placeholder,
} from '@wordpress/components';
import Icon from '../_shared/Icon';

export default function Edit( { attributes, setAttributes } ) {
	const {
		source,
		embedUrl,
		address,
		apiKey,
		zoom,
		height,
		heightMobile,
		title,
		requireConsent,
	} = attributes;

	const blockProps = useBlockProps( {
		className: 'sm-map',
		style: {
			'--sm-map-height': `${ height }px`,
			'--sm-map-height-mobile': `${ heightMobile }px`,
		},
	} );

	// Mirrors suitemart_map_validate_embed_url(). The editor only uses it to
	// warn; the server validates again before anything is framed.
	const looksLikeEmbed =
		/^https:\/\/(www\.|maps\.)?google\.com\/maps/.test( embedUrl ) &&
		( embedUrl.includes( '/maps/embed' ) ||
			embedUrl.includes( 'output=embed' ) );

	const configured = source === 'embed' ? looksLikeEmbed : address.trim();

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Map', 'suitemart' ) }>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Source', 'suitemart' ) }
						value={ source }
						options={ [
							{
								label: __( 'Embed link', 'suitemart' ),
								value: 'embed',
							},
							{
								label: __( 'Address', 'suitemart' ),
								value: 'address',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { source: value } )
						}
					/>

					{ source === 'embed' ? (
						<TextareaControl
							__nextHasNoMarginBottom
							label={ __( 'Embed link', 'suitemart' ) }
							help={ __(
								'In Google Maps choose Share, then “Embed a map”, and paste the src URL from the code it gives you. No API key is needed.',
								'suitemart'
							) }
							value={ embedUrl }
							onChange={ ( value ) =>
								setAttributes( { embedUrl: value } )
							}
						/>
					) : (
						<>
							<TextControl
								__nextHasNoMarginBottom
								label={ __( 'Address or place', 'suitemart' ) }
								value={ address }
								onChange={ ( value ) =>
									setAttributes( { address: value } )
								}
							/>
							<TextControl
								__nextHasNoMarginBottom
								label={ __(
									'Maps Embed API key (optional)',
									'suitemart'
								) }
								help={ __(
									'With a key the map uses Google’s documented Embed API. Without one it uses an older address format that works but is undocumented, so Google could change it.',
									'suitemart'
								) }
								value={ apiKey }
								onChange={ ( value ) =>
									setAttributes( { apiKey: value } )
								}
							/>
							<RangeControl
								__nextHasNoMarginBottom
								label={ __( 'Zoom', 'suitemart' ) }
								value={ zoom }
								min={ 1 }
								max={ 21 }
								onChange={ ( value ) =>
									setAttributes( { zoom: value ?? 14 } )
								}
							/>
						</>
					) }

					<TextControl
						__nextHasNoMarginBottom
						label={ __( 'Accessible title', 'suitemart' ) }
						help={ __(
							'A screen reader announces a frame by its title and nothing else. Say where the map shows, for example “Map of the Brighton shop”.',
							'suitemart'
						) }
						value={ title }
						onChange={ ( value ) =>
							setAttributes( { title: value } )
						}
					/>
				</PanelBody>

				<PanelBody title={ __( 'Size and privacy', 'suitemart' ) }>
					<RangeControl
						__nextHasNoMarginBottom
						label={ __( 'Height', 'suitemart' ) }
						value={ height }
						min={ 120 }
						max={ 1200 }
						step={ 10 }
						onChange={ ( value ) =>
							setAttributes( { height: value ?? 420 } )
						}
					/>
					<RangeControl
						__nextHasNoMarginBottom
						label={ __( 'Height on mobile', 'suitemart' ) }
						value={ heightMobile }
						min={ 120 }
						max={ 1200 }
						step={ 10 }
						onChange={ ( value ) =>
							setAttributes( { heightMobile: value ?? 280 } )
						}
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Ask before loading', 'suitemart' ) }
						help={ __(
							'Shows a placeholder until the reader asks for the map, so Google receives nothing on page load. Worth turning on where consent rules apply.',
							'suitemart'
						) }
						checked={ requireConsent }
						onChange={ ( value ) =>
							setAttributes( { requireConsent: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				{ configured ? (
					// A live iframe in the editor would swallow clicks meant for
					// the block, so the canvas shows a stand-in instead.
					<div className="sm-map__consent">
						<span className="sm-map__consent-icon">
							<Icon name="map-pin" size={ 28 } />
						</span>
						<p className="sm-map__consent-text">
							{ source === 'embed'
								? __(
										'Map from the pasted embed link. It appears on the published page.',
										'suitemart'
								  )
								: address }
						</p>
					</div>
				) : (
					<Placeholder
						icon={ <Icon name="map-pin" size={ 24 } /> }
						label={ __( 'Map', 'suitemart' ) }
						instructions={
							source === 'embed'
								? __(
										'Paste an embed link from Google Maps in the block settings.',
										'suitemart'
								  )
								: __(
										'Enter an address in the block settings.',
										'suitemart'
								  )
						}
					>
						{ source === 'embed' &&
							embedUrl &&
							! looksLikeEmbed && (
								<Notice
									status="warning"
									isDismissible={ false }
								>
									{ __(
										'That does not look like a Google Maps embed link, so nothing will be shown. Only Google Maps URLs are allowed.',
										'suitemart'
									) }
								</Notice>
							) }
					</Placeholder>
				) }
			</div>
		</>
	);
}
