/**
 * Shared lock for the off-canvas store.
 *
 * The panel and its trigger are separate blocks that extend the same
 * `suitemart/off-canvas` namespace. A plain `lock: true` would stop the second
 * one from extending the first, so both pass this token instead: they can
 * extend each other, and nothing outside the theme can.
 */

export const OFF_CANVAS_LOCK = 'suitemart/off-canvas/6f2a1c';
