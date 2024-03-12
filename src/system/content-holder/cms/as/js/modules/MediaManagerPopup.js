import { openPopup, closePopup } from "../utils/Popup";

export default (DOM=false) => {
  if (document.querySelectorAll('.ozz-fm__media-selector').length == 0) { return; }

  /**
 * Build Media manager and popup
 * @param {object} media Media elements
 * @param {object} trigger Selector Trigger clicked event
 */
  const BuildMediaManager = (media, trigger=false) => {
    let treeDOM = media.tree.join(' / '),
      itemsDOM = '',
      currentValues = false;

    if (trigger) {
      const
        fieldName = trigger.target.getAttribute('data-fieldname'),
        actualField = document.getElementById(fieldName),
        value = actualField.value;
      currentValues = value !== '' ? JSON.parse(value) : '';
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
          mediaElement = `<span title="${val.name}" class="icon ${val.format}"></span>`;
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

        itemsDOM += `
        <div class="ozz-media-popup__thumbnail ${val.type} ${active}" ${toolTip}>
          <input type="checkbox" ${checked} name="ozz-fm-media-selected-item[]" value="${value}" />${ mediaElement }
          <div class="ozz-media-popup__thumbnail-name">${val.name}</div>
        </div>`;
      }
    }

    const wrapperDOM = `
    <div class="ozz-media-popup">
      <div class="ozz-media-popup__tree">${treeDOM}</div>
      <div class="ozz-media-popup__grid-wrap"><div class="ozz-media-popup__grid">${itemsDOM}</div></div>
      <div class="ozz-media-popup__submit"><span class="button small">Select</span></div>
    </div>`;

    // Open Media Selector popup
    openPopup(wrapperDOM, (DOM) => {
      const thumbs = DOM.querySelectorAll('.ozz-media-popup__thumbnail');
      const submitBtn = DOM.querySelector('.ozz-media-popup__submit');

      // Select Items
      thumbs.forEach( tmb => {
        tmb.addEventListener('click', (e) => {
          tmb.classList.toggle('active');
          const selected = tmb.querySelector('input[name="ozz-fm-media-selected-item[]"]');
          selected.checked = !selected.checked;
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

        const finalValue = JSON.stringify(values),
          fieldName = trigger.target.getAttribute('data-fieldname'),
          actualField = document.getElementById(fieldName);
        actualField.value = finalValue;
        closePopup();

        // Update selected media
        listSelectedMedia(actualField);
      }, {once: true});
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

  // Initiate an trigger
  DOM = (DOM !== false) ? DOM : document;
  const selectors = DOM.querySelectorAll('.ozz-fm__media-selector .media-selector-trigger');

  const loadMedia = async (e) => {
    const response = await fetch(DATA.CMS_URL + "/media/items");
    const media = await response.json();
    BuildMediaManager(media, e);
  };

  selectors.forEach((mediaSelector) => {
    mediaSelector.addEventListener('click', loadMedia);

    // List down selected media items
    const fieldName = mediaSelector.getAttribute('data-fieldname');
    const actualField = document.getElementById(fieldName);
    listSelectedMedia(actualField);
  });
}
