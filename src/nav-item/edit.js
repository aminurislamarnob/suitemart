import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
	RichText,
} from '@wordpress/block-editor';
import { PanelBody, ToggleControl, TextControl } from '@wordpress/components';

export default function Edit( { attributes, setAttributes } ) {
	const { label, url, hasPanel, badge, opensInNewTab, rel } = attributes;

	const blockProps = useBlockProps( {
		className: `sm-nav-item${ hasPanel ? ' sm-nav-item--has-panel' : '' }`,
	} );

	// Only a panel-bearing item has inner blocks, and it may hold exactly one
	// mega panel — nesting more would produce ambiguous ARIA wiring.
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'sm-nav-item__panel-wrap' },
		{
			allowedBlocks: [ 'suitemart/mega-panel' ],
			template: [ [ 'suitemart/mega-panel', {} ] ],
			templateLock: 'all',
			renderAppender: false,
		}
	);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Item', 'suitemart' ) }>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Opens a mega panel', 'suitemart' ) }
						help={
							hasPanel
								? __(
										'Rendered as a button that discloses a panel. Add any blocks inside it.',
										'suitemart'
								  )
								: __( 'Rendered as a plain link.', 'suitemart' )
						}
						checked={ hasPanel }
						onChange={ ( value ) =>
							setAttributes( { hasPanel: value } )
						}
					/>
					{ ! hasPanel && (
						<>
							<TextControl
								__nextHasNoMarginBottom
								label={ __( 'URL', 'suitemart' ) }
								type="url"
								value={ url }
								onChange={ ( value ) =>
									setAttributes( { url: value } )
								}
							/>
							<ToggleControl
								__nextHasNoMarginBottom
								label={ __( 'Open in new tab', 'suitemart' ) }
								checked={ opensInNewTab }
								onChange={ ( value ) =>
									setAttributes( { opensInNewTab: value } )
								}
							/>
							<TextControl
								__nextHasNoMarginBottom
								label={ __( 'Link rel', 'suitemart' ) }
								help={ __(
									'Optional. `noopener` is added automatically for links that open in a new tab.',
									'suitemart'
								) }
								value={ rel }
								onChange={ ( value ) =>
									setAttributes( { rel: value } )
								}
							/>
						</>
					) }
					<TextControl
						__nextHasNoMarginBottom
						label={ __( 'Badge', 'suitemart' ) }
						help={ __(
							'A short label such as “Sale” or “New”.',
							'suitemart'
						) }
						value={ badge }
						onChange={ ( value ) =>
							setAttributes( { badge: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<li { ...blockProps }>
				<span
					className={
						hasPanel ? 'sm-nav-item__trigger' : 'sm-nav-item__link'
					}
				>
					<RichText
						identifier="label"
						tagName="span"
						className="sm-nav-item__label"
						value={ label }
						onChange={ ( value ) =>
							setAttributes( { label: value } )
						}
						placeholder={ __( 'Menu item…', 'suitemart' ) }
						allowedFormats={ [] }
						withoutInteractiveFormatting
					/>
					{ badge && (
						<span className="sm-nav-item__badge">{ badge }</span>
					) }
				</span>

				{ hasPanel && <div { ...innerBlocksProps } /> }
			</li>
		</>
	);
}
