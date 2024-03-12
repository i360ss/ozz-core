export default () => {
  if (document.querySelectorAll('.ozz-cms .post-edit-view').length === 0) return;

  // Ozz Activate Tab (On post create/edit)
  const activateTab = () => {
    const
      tab = window.location.hash?.substring(1) !== '' ? window.location.hash?.substring(1) : 'default',
      allTabs = document.querySelectorAll('.post-edit-view__tab'),
      tabMenuItemsBtn = document.querySelectorAll('.ozz-cms .post-edit-view__tab-menu > a > .button');

    // Remove active classes on tab menu
    tabMenuItemsBtn.forEach( button => {
      button.classList.remove('active');
    });

    // Remove active classes on tabs
    allTabs.forEach( thisTab => {
      thisTab.classList.remove('active');
    });

    document.querySelector(`.ozz-cms .post-edit-view__tab-menu > a > .button.${tab}`)?.classList.add('active');
    document.getElementById(`tab_id-${tab}`)?.classList.add('active');
  };

  const ozzFocusErrorTab = () => {
    const errors = document.querySelectorAll('.field-error', 'input.error', 'textarea.error', 'select.error');
    if (errors.length > 0) {
      const focusTab = errors[0].closest('.post-edit-view__tab');
      if (!focusTab) {
        return;
      }

      const tabName = focusTab.getAttribute('data-tab-name');
      window.history.replaceState(null, null, `#${tabName}`);
      activateTab();
    }
  };

  // Change Tab
  const tabMenuItems = document.querySelectorAll('.ozz-cms .post-edit-view__tab-menu > a');
  tabMenuItems.forEach( tabMenuItem => {
    tabMenuItem.addEventListener('click', (e) => {
      e.target.classList.add('active');
      setTimeout(() => {
        activateTab();
      }, 10);
    });
  });
  activateTab();
  ozzFocusErrorTab();
}
