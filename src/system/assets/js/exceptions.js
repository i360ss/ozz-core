// Ozz Custom Exceptions
const ozz_exception_handler = document.querySelector('.ozz-exceptions .ozz-exceptions-container');
const exception_menu_items = ozz_exception_handler.querySelectorAll('.single-trace');

exception_menu_items.forEach(element => {
  element.addEventListener('click', (e) => {
    e.preventDefault();
    const menuKey = element.getAttribute('data-menu-key');
    const snippet = ozz_exception_handler.querySelector('.code-snippet-'+menuKey);
    const singleMenuItem = ozz_exception_handler.querySelectorAll('.single-trace');
    const singleCodeSnippet = ozz_exception_handler.querySelectorAll('.single-code-snippet');

    singleMenuItem.forEach(item => {
      item.classList.contains('active') ? item.classList.remove('active') : false;
    });

    singleCodeSnippet.forEach(snippet => {
      snippet.classList.contains('active') ? snippet.classList.remove('active') : false;
    });

    element.classList.add('active');
    snippet.classList.add('active');
  })
});