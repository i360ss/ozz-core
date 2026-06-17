// Ozz Custom Exceptions
const rootContext = (typeof shadowRoot !== 'undefined') ? shadowRoot : document;

// Query elements scoped to rootContext
const ozz_exception_handler = rootContext.querySelector('.ozz-exceptions .ozz-exceptions-container');
const exception_menu_items = ozz_exception_handler?.querySelectorAll('.single-trace');

if (ozz_exception_handler && exception_menu_items) {
  exception_menu_items.forEach(element => {
    element.addEventListener('click', (e) => {
      e.preventDefault();

      const menuKey = element.getAttribute('data-menu-key');
      const snippet = ozz_exception_handler.querySelector('.code-snippet-' + menuKey);
      const singleMenuItem = ozz_exception_handler.querySelectorAll('.single-trace');
      const singleCodeSnippet = ozz_exception_handler.querySelectorAll('.single-code-snippet');

      // Clear previous active states safely
      singleMenuItem.forEach(item => {
        if (item.classList.contains('active')) {
          item.classList.remove('active');
        }
      });

      singleCodeSnippet.forEach(snippetItem => {
        if (snippetItem.classList.contains('active')) {
          snippetItem.classList.remove('active');
        }
      });

      // Activate clicked item and corresponding code snippet
      element.classList.add('active');
      if (snippet) {
        snippet.classList.add('active');
      }
    });
  });
}