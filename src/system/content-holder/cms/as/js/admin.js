// Ozz JS
import Ozz from './ozz.js';
import Sortable from './vendor/Sortable.js';
import OzzWyg from './vendor/ozz-wyg.js';

const ozz = new Ozz();

// ===============================================
// App State object
// ===============================================
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
function ozzGetState($key=false) {
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
function ozzSetState($key, $value) {
  const hasItem = typeof sessionStorage !== 'undefined' && sessionStorage !== null && sessionStorage.getItem('ozz_app_state') !== null;
  const state = hasItem ? JSON.parse(sessionStorage.getItem('ozz_app_state')) : ozz_app_state;

  state[$key] = $value;
  ozz_app_state = state;

  if (typeof sessionStorage !== 'undefined' && sessionStorage !== null) {
    sessionStorage.setItem('ozz_app_state', JSON.stringify(state));
  }
}


// ===============================================
// Ozz Navbar
// ===============================================
function collapseNav(navbar) {
  navbar.classList.add('collapsed');
  ozzSetState('nav_collapsed', true);
}
function revealNav(navbar) {
  navbar.classList.remove('collapsed');
  ozzSetState('nav_collapsed', false);
}

// CMS Navbar
function ozzCmsNavBar() {
  const
    navbar = document.querySelector('.cms-nav'),
    navbarTrigger = navbar.querySelector('.cms-nav__nav-collapse-trigger'),
    navFirstUl = navbar.querySelector('ul'),
    currentLink = navFirstUl.getAttribute('data-active-link');

  if (ozzGetState('nav_collapsed')) {
    collapseNav(navbar);
  }

  navbarTrigger.addEventListener('click', function() {
    navbar.classList.contains('collapsed') ? revealNav(navbar) : collapseNav(navbar);

    if (document.querySelector('.ozz-block-editor')) {
      const ozzBlockEditorWrapper = document.querySelector('.ozz-block-editor').closest('.ozz-fm__field');
      toggleBlockEditorCollapse(ozzBlockEditorWrapper);
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


// ===============================================
// Ozz Block editor
// ===============================================
function toggleBlockEditorCollapse(blockEditorWrapper) {
  if ( ozzGetState('nav_collapsed') ) {
    blockEditorWrapper.classList.add('collapsed');
    ozzSetState('block_editor_collapsed', true);
  } else {
    blockEditorWrapper.classList.remove('collapsed');
    ozzSetState('block_editor_collapsed', false);
  }
}

function toggleBlockEditorExpand(blockEditorWrapper) {
  if (blockEditorWrapper.classList.contains('expanded')) {
    blockEditorWrapper.classList.remove('expanded');
    ozzSetState('block_editor_expanded', false);
  } else {
    blockEditorWrapper.classList.add('expanded');
    ozzSetState('block_editor_expanded', true);
  }
}

// Toggle Block Accordion
function ozzToggleBlock(block, state=true) {
  block.setAttribute('data-expand', state);
  block.querySelector(':scope > .ozz-block-accordion-bar').classList.toggle('active', state);
  block.querySelector(':scope > .ozz-accordion-body').classList.toggle('active', state);
}

// Expand block if error field inside
function ozzExpandBlockIfError() {
  const usedBlocks = document.querySelectorAll('.ozz-block-editor .ozz-used-block');
  usedBlocks.forEach(block => {
    const errors = block.querySelectorAll('.field-error', 'input.error', 'textarea.error', 'select.error');
    if (errors.length > 0) {
      ozzToggleBlock(block);
    }
  });
}

// Ozz Block Editor
function ozzCmsBlockEditor() {
  if(document.querySelectorAll('.ozz-block-editor')){
    const ozzBlockEditor = document.querySelectorAll('.ozz-block-editor');
    ozzBlockEditor.forEach(blockEditor => {
      // Add wrapper class
      const blockEditorWrapper = blockEditor.closest('.ozz-fm__field');
      const blockEditorStockHead = blockEditor.querySelector('.ozz-block-editor__block-picker-head');
      blockEditorWrapper.classList.add('ozz-block-editor-wrapper', 'lay2');
      addCommonEvents();

      const
        blocksArr = JSON.parse(blockEditor.getAttribute('data-blocks')),
        blockPicker = blockEditor.querySelector('.ozz-block-editor__block-picker-content'),
        blockFormLoader = blockEditor.querySelector('.ozz-block-editor__form-loader');

      let blockListDOM = ``;
      let blocksObj = {};
      blocksArr.forEach(block => {
        blocksObj[block.name] = block;
        blockListDOM += `<li class="pick-block ${block.name}" data-blockName="${block.name}">${block.label}</li>`;
      });

      // Make the Block store DOM
      blockPicker.innerHTML = blockListDOM;

      // Make editor sortable and draggable
      new Sortable(blockPicker, {
        group: {
          name: 'ozz-block-editor',
          pull: 'clone',
          put: false
        },
        animation: 150,
        sort: false
      });

      new Sortable(blockFormLoader, {
        group: 'ozz-block-editor',
        animation: 150,
        handle: '.ozz-block-accordion-bar',
        onSort: function() {
          indexFieldNames();
        },
        onAdd: function(evt) {
          let draggedItem = evt.item;
          const 
            blockName = evt.clone.getAttribute('data-blockName'),
            thisBlockFormDOM = document.querySelector(`.ozz-block-editor-hidden-form-dom #${blockName}`),
            initialDOM = `<div class="ozz-block-accordion-bar">
              <span class="ozz-handle"></span>
              <div>
                <h4>${blocksObj[blockName].label}</h4>
                <p class="light-text">${blocksObj[blockName].note ? blocksObj[blockName].note : ''}</p>
              </div>
              <div class="ozz-block-actions">
                <span class="ozz-block-duplicate-trigger"></span>
                <span class="ozz-block-delete-trigger"></span>
              </div>
              <span class="ozz-accordion-arrow"></span>
            </div>`;

          indexFieldNames();

          draggedItem.classList.add('ozz-used-block');
          draggedItem.innerHTML = `${initialDOM} <div class="ozz-accordion-body">${thisBlockFormDOM.innerHTML}</div>`;

          addCommonEvents(draggedItem);
          ozz.initRepeater(draggedItem, ozzPopupMediaManager);
          ozzPopupMediaManager(draggedItem);
          initOzzWyg();
        }
      });

      // On Submit with block editor
      blockEditor.closest('form.ozz-fm').addEventListener('submit', ozzCmsFormHandler);

      // Add index to field names
      function indexFieldNames() {
        const usedBlocks = blockFormLoader.querySelectorAll('.ozz-used-block');
        usedBlocks.forEach((block, ind) => {
          const thisBlockFields = block.querySelectorAll('input, textarea, button, progress, meter, select, datalist, [data-ozz-wyg]');
          thisBlockFields.forEach((field) => {
            if(field.name){
              const newName = `i-${ind}_${ field.name.replace(/^i-\d+_/, '') }`;
              field.name = newName;
            } else if (field.getAttribute('data-field-name')) {
              const newName = `i-${ind}_${ field.getAttribute('data-field-name').replace(/^i-\d+_/, '') }`;
              field.setAttribute('data-field-name', newName);
            }
          });
        });
      }

      // Block editor state
      if (ozzGetState('block_editor_expanded')) {
        blockEditorWrapper.classList.add('expanded');
      }
      if ( ozzGetState('nav_collapsed') ) {
        blockEditorWrapper.classList.add('collapsed');
      }

      // Add common events to each block
      function addCommonEvents(block=false) {
        function blockEvents(block) {
          // Remove Block
          block.querySelector('.ozz-block-delete-trigger').addEventListener('click', () => {
            block.remove();
            indexFieldNames();
          });

          // Duplicate Block
          block.querySelector('.ozz-block-duplicate-trigger').addEventListener('click', (e) => {
            e.preventDefault();
            const blockClone = block.cloneNode(true);
            addCommonEvents(blockClone);
            blockFormLoader.appendChild(blockClone);
            indexFieldNames();
            ozz.initRepeater(blockClone);
            ozzPopupMediaManager(blockClone);
          });

          // Block accordion event
          block.querySelector('.ozz-block-accordion-bar').addEventListener('click', (e) => {
            if (e.target.closest('.ozz-block-actions')) {
              return false;
            }

            if (block.getAttribute('data-expand') == 'true') {
              ozzToggleBlock(block, false);
            } else {
              ozzToggleBlock(block, true);
            }
          });
        }

        if(block){
          blockEvents(block);
        } else {
          blockEditor.querySelectorAll('li.pick-block').forEach(block => {
            blockEvents(block);
          });

          // Expand editor
          blockEditorWrapper.querySelector('.ozz-block-editor-expand-button').addEventListener('click', () => {
            toggleBlockEditorCollapse(blockEditorWrapper);
            toggleBlockEditorExpand(blockEditorWrapper);
          });

          // Set Block stock layout
          const layouts = blockEditorStockHead.querySelectorAll('.lay');
          function setStockLayout() {
            const layout = ozzGetState('block_editor_stock_layout');
            layouts.forEach((lay, i) => { 
              lay.classList.remove('active');
              blockEditorWrapper.classList.remove(`lay${i+1}`);
            });
            blockEditorStockHead.querySelector(`span.button.${layout}`).classList.add('active');
            blockEditorWrapper.classList.add(layout);

            layouts.forEach(layout => {
              layout.addEventListener('click', (el) => {
                const layoutName = el.target.getAttribute('data-lay');
                ozzSetState('block_editor_stock_layout', layoutName);
                setStockLayout();
              })
            });
          }
          setStockLayout();
        }
      }
    });

    // Expand Block if there any field error inside the block
    ozzExpandBlockIfError();
  }
}


// ===============================================
// Ozz Form handler
// ===============================================
function ozzCmsFormHandler(e) {
  e.preventDefault();
  const thisForm = e.target;

  // Store OzzWyg data in a hidden field
  const ozzWygEditors = thisForm.querySelectorAll('[data-ozz-wyg]');
  ozzWygEditors.forEach(wygEditor => {
    const name = wygEditor.getAttribute('data-field-name');
    const value = wygEditor.querySelector('[data-editor-area]').innerHTML;
    const existingHiddenField = thisForm.querySelector(`input[name="${name}"]`);

    if (!existingHiddenField) {
      const hdnField = document.createElement('input');
      hdnField.type = 'hidden';
      hdnField.name = name;
      hdnField.value = value;
      thisForm.appendChild(hdnField);
    } else {
      existingHiddenField.value = value;
    }
  });

  thisForm.submit();
}


// ===============================================
// Ozz Update slug on title change
// ===============================================
function ozzCmsSlugUpdate() {
  const
    updateThis = document.querySelector('.j-title-onchange'),
    getFrom = document.querySelector('.ozz-fm .default-post-title');

  if (getFrom) {
    getFrom.addEventListener('keyup', () => {
      const slug = getFrom.value.replace(/\s+/g, '-').toLowerCase().replace(/[^\w\-]+/g, '');
      document.querySelector('.ozz-fm .default-post-slug').value = slug;
      if (updateThis) {
        updateThis.innerHTML = getFrom.value;
      }
    });
  }
}


// ===============================================
// Ozz media Manager
// ===============================================
function ozzCmsMediaManager() {
  if(document.querySelector('.ozz-media-manager')){
    const
    ozzMediaManager = document.querySelector('.ozz-media-manager'),
    mediaFileItems = ozzMediaManager.querySelectorAll('.ozz-media-manager__item.media-file'),
    mediaViewer = ozzMediaManager.querySelector('.ozz-media-manager__viewer'),
    actionButtons = ozzMediaManager.querySelectorAll('.ozz-media-manager .popup-trigger');

    mediaFileItems.forEach(mediaFile => {
      mediaFile.addEventListener('click', (e) => {
        e.preventDefault();
        mediaFileItems.forEach(el => {
          el.classList.remove('active');
        });
        mediaFile.classList.add('active');

        const fileInfo = JSON.parse(mediaFile.getAttribute('data-fileInfo'));

        // Media item Embed DOM
        let fileInfoThumbnail = ozzEmbedMediaElement(fileInfo);

        // File Info DOM
        const fileInfoDOM = `<div class="ozz-media-manager__info">
        <div class="ozz-media-manager__info-thumbnail">${fileInfoThumbnail}</div>
          <ul class="ozz-media-manager__info-info">
            <li><strong>Name:</strong> ${fileInfo.name}</li>
            <li><strong>Size:</strong> ${fileInfo.size}</li>
            <li><strong>URL:</strong> <a href="${fileInfo.absolute_url}" class="link" target="_blank">${fileInfo.absolute_url}</a></li>
            <li><strong>Created:</strong> ${fileInfo.created}</li>
            <li><strong>Modified:</strong> ${fileInfo.modified}</li>
            <li><strong>Access:</strong> ${fileInfo.access}</li>
            <li>
              <form action="/admin/media/action?q=delete_file" method="post">
                <input type="hidden" value="${fileInfo.dir + fileInfo.name}" name="ozz_media_file_name_delete">
                <input type="submit" value="Delete File" class="button mini danger">
              </form>
            </li>
          </ul>
        </div>`;
        mediaViewer.innerHTML = fileInfoDOM;
        ozzMediaManager.classList.add('viewer-active');
        mediaViewer.classList.add('active');
      });
    });

    // Media Actions
    actionButtons.forEach(action => {
      const actionFormDOM = action.querySelector('.hidden-action-form');
      if (actionFormDOM) {
        action.addEventListener('click', () => {
          ozzOpenPopup(actionFormDOM.outerHTML);
        })
      }
    });
  }
}


// ===============================================
// Popup Media Manager
// ===============================================
function ozzPopupMediaManager(DOM=false) {
  if (document.querySelectorAll('.ozz-fm__media-selector').length == 0) {
    return;
  }

  DOM = (DOM !== false) ? DOM : document;
  const selectors = DOM.querySelectorAll('.ozz-fm__media-selector .media-selector-trigger');

  const ozzLoadMedia = async (e) => {
    const response = await fetch(DATA.CMS_URL + "/media/items");
    const media = await response.json();
    ozzBuildMediaManager(media, e);
  };

  selectors.forEach((mediaSelector) => {
    mediaSelector.addEventListener('click', ozzLoadMedia);
  });
}


/**
 * Build Media manager and popup
 * @param {object} media Media elements
 * @param {object} trigger Selector Trigger clicked event
 */
const ozzBuildMediaManager = (media, trigger=false) => {
  let treeDOM = media.tree.join(' / ');
  let itemsDOM = '';
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

      itemsDOM += `
      <div class="ozz-media-popup__thumbnail ${val.type}" ${toolTip}>
        <input type="checkbox" name="ozz-fm-media-selected-item[]" value="${value}" />${ mediaElement }
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
  ozzOpenPopup(wrapperDOM, (DOM) => {
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
      const finalValue = JSON.stringify(values);
      const actualField = trigger.target.parentNode.querySelector('input[type="hidden"]');
      actualField.value = finalValue;
      ozzClosePopup();

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
  const selectedItems = JSON.parse(actualField.value);
  const thisWrapper = actualField.parentNode.querySelector('.ozz-fm__media-embed-wrapper');

  let listingDOM = '';
  selectedItems.forEach(item => {
    listingDOM += `<div class="embed-wrapper-item">
      <img src="/${item.url}" alt="${item.name}">
      <span class="name">${item.name}</span>
    </div>`;
  });

  thisWrapper.innerHTML = listingDOM;
}


// ===============================================
// Ozz Embed Media element
// ===============================================
function ozzEmbedMediaElement(fileInfo) {
  let fileInfoThumbnail = '';
  if ( fileInfo.format == 'image' ) {
    fileInfoThumbnail = `<img src="${fileInfo.absolute_url}">`;
  }
  else if ( fileInfo.format == 'svg' ) {
    fileInfoThumbnail = `<object type="image/svg+xml" data="${fileInfo.absolute_url}">
      <img src="${fileInfo.absolute_url}" />
    </object>`;
  }
  else if ( fileInfo.format == 'video' ) {
    fileInfoThumbnail = `<video width="100%" controls>
      <source src="${fileInfo.absolute_url}" type="video/mp4">
      <source src="${fileInfo.absolute_url}" type="video/ogg">
    </video>`;
  }
  else if ( fileInfo.format == 'audio' ) {
    fileInfoThumbnail = `<audio controls>
      <source src="${fileInfo.absolute_url}" type="audio/ogg">
      <source src="${fileInfo.absolute_url}" type="audio/mpeg">
    </audio>`;
  }
  else if ( fileInfo.format == 'vimeo' ) {
    fileInfoThumbnail = `<iframe src="${fileInfo.absolute_url}" width="100%" frameborder="0" picture-in-picture allowfullscreen></iframe>`;
  }
  else if ( fileInfo.format == 'youtube' ) {
    fileInfoThumbnail = `<iframe width="100%" src="${fileInfo.absolute_url}"></iframe>`;
  }
  else if ( fileInfo.format == 'pdf' ) {
    fileInfoThumbnail = `<object data="${fileInfo.absolute_url}" type="application/pdf" width="100%" height="400px">
      <p>Unable to display PDF file. <a href="${fileInfo.absolute_url}">Download</a> instead.</p>
    </object>`;
  }
  else if ( ['word', 'powerpoint', 'pptx', 'excel', 'odt', 'ods'].includes(fileInfo.format) ) {
    fileInfoThumbnail = `<object data="${fileInfo.absolute_url}"
      type="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" width="100%" height="400px">
      <p>Unable to display the file.<br>Click <a href="${fileInfo.absolute_url}" target="_blank">here</a> to open it</p>
    </object>`;
  }
  else if ( ['text', 'swf', 'json'].includes(fileInfo.format) ) {
    fileInfoThumbnail = `<iframe width="100%" height="400px" src="${fileInfo.absolute_url}" frameborder="0"></iframe>`;
  }
  else {
    fileInfoThumbnail = `<span class="icon ${fileInfo.format}"></span>`;
  }

  return fileInfoThumbnail;
}


// ===============================================
// Ozz Close Popup
// ===============================================
function ozzClosePopup(bindAfterClose=false) {
  const
    popup = document.querySelector('.ozz-cms-popup'),
    popupContent = document.querySelector('#cms-popup-content');

  popupContent.innerHTML = '';
  popup.classList.remove('active');
  ozzSetState('popup_opened', false);

  if (typeof bindAfterClose == 'function') {
    bindAfterClose();
  }
}


// ===============================================
// Ozz Open Popup
// ===============================================
function ozzOpenPopup(popupDOM, bindEvent=false, afterCloseCallback=false) {
  const
    popup = document.querySelector('.ozz-cms-popup'),
    popupContent = popup.querySelector('#cms-popup-content'),
    closeTrigger = popup.querySelector('#cms-popup-close');

  popupContent.innerHTML = '';
  popupContent.insertAdjacentHTML('beforeend', typeof popupDOM === 'object' ? popupDOM.outerHTML : popupDOM);
  popup.classList.add('active');
  ozzSetState('popup_opened', true);

  // Binding Events
  if (typeof bindEvent == 'function') {
    bindEvent(popupContent);
  }

  // Focus field
  const inputsOnPopup = popupContent.querySelectorAll('input[type="text"]');
  if ( inputsOnPopup.length > 0 ) {
    inputsOnPopup[0].focus();
  }

  // Close popup by trigger
  closeTrigger.addEventListener('click', () => {
    ozzClosePopup(afterCloseCallback);
  }, {once: true});

  // Close popup by click on Esc
  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      ozzClosePopup(afterCloseCallback);
    }
  }, {once: true});
}


// ===============================================
// Ozz Common Alert Bar
// ===============================================
function ozzAlertBar() {
  const alertBar = document.querySelector('.common-alert-bar');
  if (alertBar && alertBar.querySelectorAll('.message').length > 0) {
    setTimeout(() => {
      alertBar.classList.add('active');
      setTimeout(() => {
        alertBar.classList.remove('active');
        setTimeout(() => { alertBar.innerHTML = ''; }, 300);
      }, 8000);
    }, 300);
  }
}


// ===============================================
// Ozz Change Theme
// ===============================================
function ozzChangeTheme() {
  const
    appBody = document.querySelector('body.ozz-cms'),
    changeTrigger = document.getElementById('ozz-color-theme-switcher');

  changeTrigger.addEventListener('change', (e) => {
    ozzSetState('theme', e.target.checked ? 'dark' : 'light');
    appBody.setAttribute('data-theme', ozzGetState('theme'));
  });

  appBody.setAttribute('data-theme', ozzGetState('theme'));
  if (ozzGetState('theme') == 'dark') {
    changeTrigger.checked = true;
  }
}


// ===============================================
// Ozz Init OzzWyg editor if exist
// =============================================== 
function initOzzWyg() {
  if (typeof OzzWyg === 'function') {
    new OzzWyg({ selector: '[data-ozz-wyg]' });
  }
}


(function() {
  ozzCmsNavBar();
  ozzCmsBlockEditor();
  ozzCmsSlugUpdate();
  ozzCmsMediaManager();
  ozzAlertBar();
  ozzChangeTheme();
  ozzPopupMediaManager();

  ozz.initRepeater(false, ozzPopupMediaManager);
  initOzzWyg();
})();
