import { openPopup, closePopup } from "../utils/Popup";

export default (DOM=false) => {
  if (document.querySelectorAll('.ozz-fm__media-selector').length == 0) { return; }

  let initialTrigger = false; // Popup opened by this

  /**
   * Build Media manager and popup
   * @param {object} media Media elements
   * @param {object} trigger Selector Trigger clicked event
   * @param {string} breadCrumb Directory URL
   */
  const BuildMediaManager = (media, trigger=false, breadCrumb=false) => {
    let itemsDOM = '',
      currentValues = false;

    if (trigger) {
      initialTrigger = trigger;
    }

    const actualField = initialTrigger.target.closest( '.ozz-fm__media-selector' )?.querySelector( 'input[type=hidden]' );
    const actualFieldValue = actualField?.value;
    currentValues = actualFieldValue !== '' ? JSON.parse(actualFieldValue) : '';

    // Setup breadcrumb
    const breadCrumbDOM = document.createElement( 'div' );
    breadCrumbDOM.classList.add('ozz-media-popup__breadcrumb');
    breadCrumbDOM.innerHTML = `<span class="ozz-media-popup__breadcrumb-item home" data-dir="/"></span>`;

    const buildBreadcrumb = (url) => {
      const parts = url.split('/');
      let breadcrumb = '';
      let cumulativePath = '';

      parts.forEach((part, index) => {
        cumulativePath += part + '/';
        if (index === parts.length - 1) {
          breadcrumb += `<span class="ozz-media-popup__breadcrumb-current">${part}</span>`;
        } else {
          breadcrumb += `<span class="ozz-media-popup__breadcrumb-item" data-dir="${part}">${part}</span> / `;
        }
      });

      return `<span class="ozz-media-popup__breadcrumb-item home" data-dir="/"></span> ${breadcrumb}`;
    };

    if (breadCrumb) {
      breadCrumbDOM.innerHTML = buildBreadcrumb(breadCrumb);
    }

    for (const key in media.items.data) {
      if (media.items.data.hasOwnProperty(key)) {
        const val = media.items.data[key];
        let toolTip = val.type !== 'folder' ? `title="Size: ${val.size}&#013;Created: ${val.created}&#013;Modified: ${val.modified}&#013;"` : '';
        let mediaElement;
        if (val.format == 'image') {
          mediaElement = `<img src="${val.absolute_url}" class="ozz-media-popup__thumbnail-image" alt="${val.name}">`;
        } else if (val.format == 'svg') {
          mediaElement = `<object data="${val.absolute_url}"></object>`;
        } else {
          mediaElement = `<span title="${val.name}" class="icon ${val.format ?? val.type}"></span>`;
        }

        // Set item value
        const value = encodeURIComponent(JSON.stringify({
          url: val.url,
          name: val.name
        }));

        let { checked, active } = '';
        if (currentValues) {
          currentValues.forEach( item => {
            if (item.url == val.url) {
              checked = 'checked="true"';
              active = 'active';
            }
          });
        }

        const showCheckbox = `<input type="checkbox" ${checked ?? ''} name="ozz-fm-media-selected-item[]" value="${value}" />${ mediaElement }`;
        const folderURL = val.type == 'folder' ? `data-dir=${val.url}` : '';
        itemsDOM += `
        <div class="ozz-media-popup__thumbnail ${val.type} ${active ?? ''}" ${toolTip} ${folderURL}>
          ${val.type !== 'folder' ? showCheckbox : mediaElement}
          <div class="ozz-media-popup__thumbnail-name">${val.name}</div>
        </div>`;
      }
    }

    const wrapperDOM = `
    <div class="ozz-media-popup">
      ${breadCrumbDOM.outerHTML}
      <div class="ozz-media-popup__grid-wrap"><div class="ozz-media-popup__grid">${itemsDOM}</div></div>
      <div class="ozz-media-popup__submit"><span class="button small">Select</span></div>
    </div>`;

    // Open Media Selector popup
    openPopup(wrapperDOM, (DOM) => {
      const thumbs = DOM.querySelectorAll('.ozz-media-popup__thumbnail');
      const submitBtn = DOM.querySelector('.ozz-media-popup__submit');
      const links= DOM.querySelectorAll('.ozz-media-popup__breadcrumb-item');

      // Select Items
      thumbs.forEach( tmb => {
        tmb.addEventListener('click', (e) => {
          if (tmb.classList.contains( 'folder' )) {
            // open sub folder
            loadMedia(e, tmb.getAttribute('data-dir') ?? false);
          } else {
            tmb.classList.toggle('active');
            const selected = tmb.querySelector('input[name="ozz-fm-media-selected-item[]"]');
            selected.checked = !selected.checked;

            // Remove un selected item
            if (!selected.checked) {
              const urlToRemove = JSON.parse(decodeURIComponent(selected.value)).url;
              const updatedValues = currentValues.filter(item => item.url !== urlToRemove);
              actualField.value = JSON.stringify(updatedValues);
            }
          }
        });
      });

      // Insert Selected items into hidden field
      submitBtn.addEventListener('click', () => {
        const checked = DOM.querySelectorAll('input[name="ozz-fm-media-selected-item[]"]:checked');
        let values = [];
        checked.forEach(checkbox => {
          const val = JSON.parse(decodeURIComponent(checkbox.value));
          values.push(val);
        });

        const actualField = initialTrigger.target.closest( '.ozz-fm__media-selector' ).querySelector( 'input[type=hidden]' );
        const finalVals = [...JSON.parse(actualField.value), ...values].filter(
          (obj, index, self) => index === self.findIndex((o) => o.url === obj.url)
        );
        const finalValues = JSON.stringify( finalVals );
        actualField.value = finalValues;
        closePopup();

        // Update selected media
        listSelectedMedia(actualField);
      }, {once: true});

      // Breadcrumb trigger
      links.forEach( link => {
        link.addEventListener( 'click', (e) => {
          return loadMedia(e, e.target.getAttribute( 'data-dir' ));
        } );
      });
    });
  }

  /**
 * Update Selector with selected media items
 * @param {DOM} actualField 
 */
  const listSelectedMedia = (actualField) => {
    if ('' === actualField.value) {
      return;
    }

    const selectedItems = JSON.parse(actualField.value);
    const thisWrapper = actualField.parentNode.querySelector('.ozz-fm__media-embed-wrapper');

    let listingDOM = '';
    selectedItems.forEach(item => {
      listingDOM += `<div class="embed-wrapper-item">
        <img src="/${item.url}" alt="${item.name}" title="${item.name}">
      </div>`;
    });

    thisWrapper.innerHTML = listingDOM;
  }

  // Initiate a trigger
  DOM = (DOM !== false) ? DOM : document;
  const selectors = DOM.querySelectorAll('.ozz-fm__media-selector .media-selector-trigger');

  const loadMedia = async (e, dir=false) => {
    if (dir) {
      const response = await fetch(`${DATA.CMS_URL}media/items?dir=${dir}`);
      const media = await response.json();
      BuildMediaManager(media, false, dir);
    } else {
      const response = await fetch(`${DATA.CMS_URL}media/items`);
      const media = await response.json();
      BuildMediaManager(media, e);
    }
  };

  selectors.forEach((mediaSelector) => {
    mediaSelector.addEventListener('click', loadMedia);

    // List down selected media items
    const actualField = mediaSelector.closest( '.ozz-fm__media-selector' ).querySelector( 'input[type=hidden]' );;
    listSelectedMedia(actualField);
  });
}
