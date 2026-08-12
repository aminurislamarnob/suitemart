import { store, getContext } from '@wordpress/interactivity';

store( 'suitemart/visitor-counter', {
	callbacks: {
		start: () => {
			const context = getContext();

			// A measured count is reported as-is; drifting it would turn real
			// data back into an invention.
			if ( ! context.simulated ) {
				return;
			}

			let timer = 0;

			const scheduleNext = () => {
				// Update every 5-10 seconds.
				const delay = Math.floor( Math.random() * 5000 ) + 5000;
				timer = setTimeout( () => {
					// Fluctuate by -2 to +2.
					const change = Math.floor( Math.random() * 5 ) - 2;
					let next = context.current + change;

					if ( next < context.min ) {
						next = context.min;
					} else if ( next > context.max ) {
						next = context.max;
					}

					context.current = next;
					scheduleNext();
				}, delay );
			};

			scheduleNext();

			// Without this the chain outlives the element and keeps firing
			// after client-side navigation replaces the region.
			return () => clearTimeout( timer );
		},
	},
} );
