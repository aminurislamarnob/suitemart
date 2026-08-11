/**
 * The share targets this block supports.
 *
 * Shared by the editor and mirrored in render.php, which owns the URL building
 * because only the server knows the permalink and title of the post being
 * rendered.
 *
 * `copy` is not a network: it copies the page URL to the clipboard, which is
 * the one share action that works everywhere and needs no third party.
 */

export const NETWORKS = [
	{ id: 'facebook', label: 'Facebook' },
	{ id: 'x', label: 'X' },
	{ id: 'linkedin', label: 'LinkedIn' },
	{ id: 'pinterest', label: 'Pinterest' },
	{ id: 'whatsapp', label: 'WhatsApp' },
	{ id: 'telegram', label: 'Telegram' },
	{ id: 'reddit', label: 'Reddit' },
	{ id: 'email', label: 'Email' },
	{ id: 'copy', label: 'Copy link' },
];
