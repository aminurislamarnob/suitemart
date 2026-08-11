import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';
import { useState } from '@wordpress/element';

const TEMPLATE = [
	[ 'suitemart/tab', { label: __( 'Description', 'suitemart' ) } ],
	[ 'suitemart/tab', { label: __( 'Details', 'suitemart' ) } ],
];

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { orientation, activation } = attributes;
	const [ active, setActive ] = useState( 0 );

	// Read the children's labels so the editor can render a real tab list,
	// exactly as the front end does.
	const tabs = useSelect(
		( select ) => {
			const { getBlock } = select( blockEditorStore );
			const block = getBlock( clientId );

			return ( block?.innerBlocks ?? [] ).map( ( child, index ) => ( {
				clientId: child.clientId,
				label:
					child.attributes?.label ||
					// translators: %d: tab number.
					__( 'Tab', 'suitemart' ) + ` ${ index + 1 }`,
			} ) );
		},
		[ clientId ]
	);

	const { selectBlock } = useDispatch( blockEditorStore );

	const blockProps = useBlockProps( {
		className: `sm-tabs sm-tabs--${ orientation }`,
	} );

	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'sm-tabs__panels' },
		{
			allowedBlocks: [ 'suitemart/tab' ],
			template: TEMPLATE,
			templateLock: false,
			// Only the selected panel is shown, matching the front end. Without
			// this the canvas stacks every panel and the block is unreadable.
			__experimentalCaptureToolbars: true,
		}
	);

	const index = Math.min( active, Math.max( 0, tabs.length - 1 ) );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Tabs', 'suitemart' ) }>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Orientation', 'suitemart' ) }
						value={ orientation }
						options={ [
							{
								label: __( 'Horizontal', 'suitemart' ),
								value: 'horizontal',
							},
							{
								label: __( 'Vertical', 'suitemart' ),
								value: 'vertical',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { orientation: value } )
						}
					/>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Activation', 'suitemart' ) }
						help={ __(
							'Automatic shows a panel as soon as its tab is focused. Manual waits for Enter or Space — better when panels are heavy.',
							'suitemart'
						) }
						value={ activation }
						options={ [
							{
								label: __( 'Automatic', 'suitemart' ),
								value: 'automatic',
							},
							{
								label: __( 'Manual', 'suitemart' ),
								value: 'manual',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { activation: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<div className="sm-tabs__list" role="tablist">
					{ tabs.map( ( tab, i ) => (
						<button
							type="button"
							key={ tab.clientId }
							role="tab"
							aria-selected={ i === index }
							tabIndex={ i === index ? 0 : -1 }
							className={ `sm-tabs__tab${
								i === index ? ' is-active' : ''
							}` }
							onClick={ () => {
								setActive( i );
								selectBlock( tab.clientId );
							} }
						>
							<span className="sm-tabs__tab-label">
								{ tab.label }
							</span>
						</button>
					) ) }
				</div>
				<div
					{ ...innerBlocksProps }
					className={ `${ innerBlocksProps.className } sm-tabs__panels--editing` }
					data-active-index={ index }
				/>
			</div>
		</>
	);
}
