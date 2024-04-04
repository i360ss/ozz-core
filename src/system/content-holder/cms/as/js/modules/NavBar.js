import { SetState, GetState } from '../utils/State';

export default () => {
  const body = document.querySelector( 'body' );

  const collapseNav = (navbar) => {
    navbar.classList.add('collapsed');
    body.classList.add('nav-collapsed');
    SetState('nav_collapsed', true);
  }

  const revealNav = (navbar) => {
    navbar.classList.remove('collapsed');
    body.classList.remove('nav-collapsed');
    SetState('nav_collapsed', false);
  }

  const
    navbar = document.querySelector('.cms-nav'),
    navbarTrigger = navbar.querySelector('.cms-nav__nav-collapse-trigger'),
    navFirstUl = navbar.querySelector('ul'),
    currentLink = navFirstUl.getAttribute('data-active-link');

  if (GetState('nav_collapsed')) {
    collapseNav(navbar);
  }

  navbarTrigger.addEventListener('click', () => {
    navbar.classList.contains('collapsed') ? revealNav(navbar) : collapseNav(navbar);

    if (document.querySelector('.ozz-block-editor')) {
      const ozzBlockEditorWrapper = document.querySelector('.ozz-block-editor').closest('.ozz-fm__field');
      if ( GetState('nav_collapsed') ) {
        ozzBlockEditorWrapper.classList.add('collapsed');
        SetState('block_editor_collapsed', true);
      } else {
        ozzBlockEditorWrapper.classList.remove('collapsed');
        SetState('block_editor_collapsed', false);
      }
    }
  });

  // Activate Current nav item
  navFirstUl.querySelectorAll('li').forEach(li => {
    if (li.classList.contains(currentLink)) {
      li.classList.add('active');
    }
  });

  // Activate Tab
  const tabContainer = document.querySelectorAll('[data-active-tab]');
  tabContainer.forEach(element => {
    const activateClass = element.getAttribute('data-active-tab');
    if(activateClass == ''){
      element.querySelector('.all').classList.add('active');
    } else if(element.querySelector(`.${activateClass}`)){
      element.querySelector(`.${activateClass}`).classList.add('active');
    }
  });
}
