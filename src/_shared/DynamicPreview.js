/**
 * Canvas preview for a block whose output only the server can produce.
 *
 * Several blocks here read the product being rendered — its badges, its stock,
 * its sales, its delivery window — and every one of them used to preview a
 * hand-written stand-in instead: all three badges on every card, "Only 5 left",
 * "124 units sold", a delivery date in October. The canvas showed the same
 * invented values for every product while the front end showed the real ones,
 * which makes the editor useless for judging what a card will actually look
 * like.
 *
 * Rendering through the block renderer endpoint removes the second
 * implementation entirely: the preview is the front end's own output, produced
 * by the same render.php, so the two cannot drift.
 *
 * The `post_id` argument is what makes it per-product. Inside a product template
 * each instance is given the id of the product it belongs to, and the endpoint
 * sets that post up before rendering — without it every card would render
 * against whatever post the editor happens to be on.
 */

import ServerSideRender from '@wordpress/server-side-render';

/**
 * Renders a block's real server output in the editor.
 *
 * @param {Object}  props              Component props.
 * @param {string}  props.block        Block name, e.g. `suitemart/sold-counter`.
 * @param {Object}  props.attributes   Current block attributes.
 * @param {number}  [props.postId]     Post the block renders against.
 * @param {boolean} [props.isSelected] Whether the block is selected.
 * @param {string}  [props.emptyLabel] Why this block renders nothing here.
 * @return {JSX.Element} The preview.
 */
export default function DynamicPreview( {
	block,
	attributes,
	postId,
	isSelected = false,
	emptyLabel = '',
} ) {
	/*
	 * These blocks render nothing at all for a product they do not apply to — a
	 * product with no badges, one that does not manage stock, one that has never
	 * sold. The front end shows an empty space, so the canvas does too, and the
	 * explanation appears only once the block is selected. Showing it always
	 * would fill a product grid with notices about blocks that are working
	 * correctly.
	 */
	const EmptyResponsePlaceholder = () =>
		isSelected && emptyLabel ? (
			<p className="sm-editor-note">{ emptyLabel }</p>
		) : null;

	return (
		<ServerSideRender
			block={ block }
			attributes={ attributes }
			urlQueryArgs={ postId ? { post_id: postId } : {} }
			EmptyResponsePlaceholder={ EmptyResponsePlaceholder }
		/>
	);
}
