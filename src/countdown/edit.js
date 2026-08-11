import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	TextControl,
	CheckboxControl,
	Notice,
} from '@wordpress/components';

const UNITS = [
	{ key: 'days', label: __( 'Days', 'suitemart' ) },
	{ key: 'hours', label: __( 'Hours', 'suitemart' ) },
	{ key: 'minutes', label: __( 'Minutes', 'suitemart' ) },
	{ key: 'seconds', label: __( 'Seconds', 'suitemart' ) },
];

export default function Edit( { attributes, setAttributes } ) {
	const { endDate, units, expiredText, layout } = attributes;

	const blockProps = useBlockProps( {
		className: `sm-countdown sm-countdown--${ layout }`,
	} );

	const shown = UNITS.filter( ( u ) => units.includes( u.key ) );
	const hasPassed = endDate && new Date( endDate ).getTime() <= Date.now();

	const toggleUnit = ( key, checked ) => {
		// Preserve canonical order regardless of the order boxes were ticked,
		// so the rendered timer always reads days → seconds.
		const next = UNITS.filter( ( u ) =>
			u.key === key ? checked : units.includes( u.key )
		).map( ( u ) => u.key );

		setAttributes( { units: next } );
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Countdown', 'suitemart' ) }>
					<TextControl
						__nextHasNoMarginBottom
						type="datetime-local"
						label={ __( 'Ends at', 'suitemart' ) }
						help={ __(
							'Interpreted in the site’s timezone.',
							'suitemart'
						) }
						value={ endDate }
						onChange={ ( value ) =>
							setAttributes( { endDate: value } )
						}
					/>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Layout', 'suitemart' ) }
						value={ layout }
						options={ [
							{
								label: __( 'Boxed', 'suitemart' ),
								value: 'boxed',
							},
							{
								label: __( 'Inline', 'suitemart' ),
								value: 'inline',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { layout: value } )
						}
					/>
					{ UNITS.map( ( unit ) => (
						<CheckboxControl
							__nextHasNoMarginBottom
							key={ unit.key }
							label={ unit.label }
							checked={ units.includes( unit.key ) }
							onChange={ ( checked ) =>
								toggleUnit( unit.key, checked )
							}
						/>
					) ) }
					<TextControl
						__nextHasNoMarginBottom
						label={ __( 'Message when finished', 'suitemart' ) }
						value={ expiredText }
						placeholder={ __(
							'This offer has ended.',
							'suitemart'
						) }
						onChange={ ( value ) =>
							setAttributes( { expiredText: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				{ ! endDate && (
					<Notice status="warning" isDismissible={ false }>
						{ __(
							'Choose an end date. Until then this block renders nothing on the front end.',
							'suitemart'
						) }
					</Notice>
				) }
				{ hasPassed && (
					<Notice status="info" isDismissible={ false }>
						{ __(
							'This date has already passed, so visitors will see the finished message.',
							'suitemart'
						) }
					</Notice>
				) }
				<div className="sm-countdown__units" aria-hidden="true">
					{ shown.map( ( unit ) => (
						<div className="sm-countdown__unit" key={ unit.key }>
							<span className="sm-countdown__value">
								{ unit.key === 'days' ? '0' : '00' }
							</span>
							<span className="sm-countdown__label">
								{ unit.label }
							</span>
						</div>
					) ) }
				</div>
			</div>
		</>
	);
}
