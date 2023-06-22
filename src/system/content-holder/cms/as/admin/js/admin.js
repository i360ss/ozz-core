(function() {
  // CMS Navbar Collapse
  const
    navbar = document.querySelector('.cms-nav'),
    navbarTrigger = navbar.querySelector('.nav-collapse-trigger'),
    navFirstUl = navbar.querySelector('ul'),
    currentLink = navFirstUl.getAttribute('data-active-link');

  if (sessionStorage.getItem('ozzCmsNavCollapsed') === 'true') {
    navbar.classList.add('collapsed');
  }

  navbarTrigger.addEventListener('click', function() {
    if (navbar.classList.contains('collapsed')) {
      navbar.classList.remove('collapsed');
      sessionStorage.setItem('ozzCmsNavCollapsed', 'false');
    } else {
      navbar.classList.add('collapsed');
      sessionStorage.setItem('ozzCmsNavCollapsed', 'true');
    }
  });

  // Activate Current nav item
  navFirstUl.querySelectorAll('li').forEach(li => {
    li.classList.contains(currentLink) ? li.classList.add('active') : false;
  });

  // Update Title on change
  const
    updateThis = document.querySelector('.j-title-onchange'),
    getFrom = document.querySelector('.ozz-form .default-post-title');

  if (getFrom) {
    getFrom.addEventListener('keyup', () => {
      const slug = getFrom.value.replace(/\s+/g, '-').toLowerCase().replace(/[^\w\-]+/g, '');
      document.querySelector('.ozz-form .default-post-slug').value = slug;
      if (getFrom && updateThis) {
        updateThis.innerHTML = getFrom.value;
      }
    });
  }

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

})();
