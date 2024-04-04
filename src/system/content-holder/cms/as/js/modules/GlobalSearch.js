import { send } from '../utils/Fetch';

export default () => {
  const
    DOM = document.querySelector( '.cms-global-search' ),
    searchField = DOM.querySelector( 'input[name="cms-global-search-field"]' ),
    resultWrapper = DOM.querySelector( '.cms-global-search__result-wrapper' ),
    resultDOM = DOM.querySelector( '.cms-global-search__result' );

  searchField.addEventListener( 'focus', () => {
    resultWrapper.classList.add( 'active' );
  });

  searchField.addEventListener( 'blur', () => {
    if ( searchField.value == '' ) {
      resultWrapper.classList.remove( 'active' );
    }
  });

  let processing = false;
  searchField.addEventListener( 'input', ( e) => {
    e.preventDefault();
    resultWrapper.classList.add( 'active' );

    if (searchField.value !== '') {
      if ( processing === false) {
        const getResult = send(DATA.CMS_URL+'global-search', 'POST', JSON.stringify({ keyword: searchField.value }));
        getResult.then(response => {
          let result = '';
          response.forEach( item => {
            result += `<li>
              <a href="${item.url}">
                <span>${item.title}</span>
                <span class="button micro light">${item.type}</span>
              </a>
            </li>`;
          });
          resultDOM.style.opacity = 1;
          resultDOM.innerHTML = result;
        });
        processing = true;
        setTimeout(() => { processing = false; }, 500);
      }
    } else {
      processing = false;
      resultDOM.innerHTML = '';
      resultDOM.style.opacity = 0;
    }
  });
}