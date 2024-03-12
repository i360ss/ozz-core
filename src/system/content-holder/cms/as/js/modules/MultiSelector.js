export default () => {
  const selectors = document.querySelectorAll( '.ozz-fm__multiselect' );

  if (selectors.length === 0) return;

  selectors.forEach((selector) => {
    const selectedItemsDiv = selector.querySelector( '.ozz-fm__multiselect--selected' );
    const options = selector.querySelectorAll( 'ul li' );
    const realField = selector.querySelector( 'input[type="hidden"]' );
    const dataOptions = Array.from(options, option => ({
      label: option.textContent,
      value: option.dataset.value,
    }));
    const dataAvailable = dataOptions.slice();

    // Get current values
    let dataSelected = [];
    const defaultValuesObj = JSON.parse(realField.value);
    if (typeof defaultValuesObj.terms === 'object' && defaultValuesObj.terms.length > 0) {
      defaultValuesObj['terms'].forEach(term => {
        const label = dataAvailable.find(obj => obj.value == term);
        dataSelected.push(label);
      });
    }

    selector.setAttribute( 'data-name', realField.name );

    // Add search field
    const searchField = document.createElement( 'input' );
    searchField.type = 'search';
    selector.insertBefore( searchField, selector.querySelector( 'ul' ) );

    // Add dropdown element
    const ddDOM = document.createElement('ul');
    ddDOM.classList.add('ozz-fm__multiselect-dropdown');
    selector.insertBefore( ddDOM, selector.querySelector( 'ul' ) );

    // Bind search field events
    searchField.addEventListener( 'focus', () => {
      openDropdown();
      const closeEvent = ( e ) => {
        if ( selector.contains(e.target) === false ) {
          closeDropdown();
          document.removeEventListener( 'click', closeEvent);
        }
      };
      document.addEventListener( 'click', closeEvent);
    });

    // Setup search input event
    searchField.addEventListener( 'input', ( e ) => {
      const li = document.querySelectorAll( '.ozz-fm__multiselect-dropdown li' );
      li.forEach( item => {
        const val = e.target.value.toLowerCase();
        const itemTxt = item.textContent.toLowerCase();
        if (itemTxt.includes(val)) {
          item.style.display = 'block';
        } else {
          item.style.display = 'none';
        }
      });
    });

    // Setup dropdown items
    const setupDDItems = () => {
      let DOM = document.createElement('ul');
      dataAvailable.forEach(option => {
        const selected = dataSelected.some(it => it.value === option.value);
        const cls = selected ? 'selected' : 'not';
        const li = document.createElement('li');
        li.dataset.value = option.value;
        li.dataset.selected = selected;
        li.classList.add(cls);
        li.textContent = option.label;
        DOM.appendChild(li);
      });

      const dd = selector.querySelector('.ozz-fm__multiselect-dropdown');
      dd.innerHTML = DOM.innerHTML;

      dd.querySelectorAll('li').forEach(li => {
        li.addEventListener('click', () => {
          toggleTermSelectState(li, li.classList.contains('selected'));
          modifySelectedItems();
        });
      });

      modifySelectedItems();
    };

    // Set and modify selected items DOM
    const modifySelectedItems = () => {
      let DOM = '';
      dataSelected.forEach( item => {
        DOM += `<span class="button light mini" data-value="${item.value}">${item.label}<span class="remove-btn"></span></span>`;
      });
      selectedItemsDiv.innerHTML = DOM;

      // Bind remove event
      selectedItemsDiv.querySelectorAll('span.remove-btn').forEach( removeBtn => {
        removeBtn.addEventListener('click', (e) => {
          e.preventDefault();
          const removed = removeBtn.closest('.button');
          const value = removed.getAttribute('data-value');
          dataSelected = dataSelected.filter(item => item.value != value);
          storeValues( dataSelected );
          removeBtn.closest('.button').remove();
          modifySelectedItems();
        });
      });
    };

    // Toggle term select state
    const toggleTermSelectState = ( li, state=true ) => {
      const value = li.getAttribute('data-value');
      const label = li.textContent;

      if (state) {
        li.classList.remove('selected');
        li.setAttribute( 'selected', false );
        dataSelected = dataSelected.filter(item => item.value !== value);
      } else {
        li.classList.add('selected');
        li.setAttribute( 'selected', true );
        dataSelected.push({
          label: label,
          value: value
        });
      }

      storeValues( dataSelected );
    };

    // Store values in hidden field
    const storeValues = ( values ) => {
      const taxonomyID = realField.getAttribute( 'data-taxonomy-id' ) ?? null;
      const valueData = [];
      if (taxonomyID !== null) {
        valueData.push({
          'taxonomy': taxonomyID,
          'terms': values.map(it => it.value)
        });
      } else {
        valueData.push(values.map(it => it.value));
      }
      realField.value = JSON.stringify(valueData);
    };

    // Open dropdown
    const openDropdown = () => {
      setupDDItems();
      selector.querySelector('.ozz-fm__multiselect-dropdown').classList.add( 'active' );
    };

    // Close multiselect dropdown
    const closeDropdown = () => {
      selector.querySelector('.ozz-fm__multiselect-dropdown').classList.remove('active');
    };

    // Set initial values as selected
    if (dataSelected.length > 0) {
      modifySelectedItems();
    }
  });
};