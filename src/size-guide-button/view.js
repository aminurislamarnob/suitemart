import { store } from '@wordpress/interactivity';

const { state } = store( 'suitemart/size-guide', {
	actions: {
		open: () => {
			state.isOpen = true;
		},
	},
} );
