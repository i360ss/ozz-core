let ozz_app_state = {
  nav_collapsed: false,
  block_editor_expanded: false,
  block_editor_collapsed: false,
  popup_opened: false,
  block_editor_stock_layout: 'lay2',
  theme: 'light',
};

/**
 * Get State (Session storage)
 * @param {string} $key 
 * @returns value
 */
function GetState($key=false) {
  const
    hasItem = typeof sessionStorage !== 'undefined' && sessionStorage !== null && sessionStorage.getItem('ozz_app_state') !== null,
    state = hasItem ? JSON.parse(sessionStorage.getItem('ozz_app_state')) : ozz_app_state;

  return $key ? state[$key] : state;
}

/**
 * Set State (Session storage)
 * @param {string} $key
 * @param {*} $value
 */
function SetState($key, $value) {
  const hasItem = typeof sessionStorage !== 'undefined' && sessionStorage !== null && sessionStorage.getItem('ozz_app_state') !== null;
  const state = hasItem ? JSON.parse(sessionStorage.getItem('ozz_app_state')) : ozz_app_state;

  state[$key] = $value;
  ozz_app_state = state;

  if (typeof sessionStorage !== 'undefined' && sessionStorage !== null) {
    sessionStorage.setItem('ozz_app_state', JSON.stringify(state));
  }
}

export { SetState, GetState };