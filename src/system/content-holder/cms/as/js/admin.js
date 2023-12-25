// ===============================================
// State object
// ===============================================
let ozz_app_state = {
  nav_collapsed: false,
  block_editor_expanded: false,
  block_editor_collapsed: false,
  popup_opened: false,
};


// ===============================================
// Ozz Navbar
// ===============================================
function collapseNav(navbar) {
  navbar.classList.add('collapsed');
  sessionStorage.setItem('ozzCmsNavCollapsed', 'true');
  ozz_app_state.nav_collapsed = true;
}
function revealNav(navbar) {
  navbar.classList.remove('collapsed');
  sessionStorage.setItem('ozzCmsNavCollapsed', 'false');
  ozz_app_state.nav_collapsed = false;
}
function ozzCmsNavBar() {
  // CMS Navbar Collapse
  const
    navbar = document.querySelector('.cms-nav'),
    navbarTrigger = navbar.querySelector('.nav-collapse-trigger'),
    navFirstUl = navbar.querySelector('ul'),
    currentLink = navFirstUl.getAttribute('data-active-link');

  if (sessionStorage.getItem('ozzCmsNavCollapsed') === 'true') {
    collapseNav(navbar);
  }

  navbarTrigger.addEventListener('click', function() {
    if (navbar.classList.contains('collapsed')) {
      revealNav(navbar);
    } else {
      collapseNav(navbar);
    }
    const ozzBlockEditorWrapper = document.querySelector('.ozz-block-editor').closest('.ozz-fm__field');
    toggleBlockEditorCollapse(ozzBlockEditorWrapper);
  });

  // Activate Current nav item
  navFirstUl.querySelectorAll('li').forEach(li => {
    li.classList.contains(currentLink) ? li.classList.add('active') : false;
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
  if ( ozz_app_state.nav_collapsed === true ) {
    blockEditorWrapper.classList.add('collapsed');
    ozz_app_state.block_editor_collapsed = true;
  } else {
    blockEditorWrapper.classList.remove('collapsed');
    ozz_app_state.block_editor_collapsed = false;
  }
}
function toggleBlockEditorExpand(blockEditorWrapper) {
  if (blockEditorWrapper.classList.contains('expanded')) {
    blockEditorWrapper.classList.remove('expanded');
    ozz_app_state.block_editor_expanded = false;
  } else {
    blockEditorWrapper.classList.add('expanded');
    ozz_app_state.block_editor_expanded = true;
  }
}
function ozzCmsBlockEditor() {
  if(document.querySelectorAll('.ozz-block-editor')){
    const ozzBlockEditor = document.querySelectorAll('.ozz-block-editor');
    ozzBlockEditor.forEach(blockEditor => {
      // Add wrapper class
      const blockEditorWrapper = blockEditor.closest('.ozz-fm__field');
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
              <div><span class="ozz-block-delete-trigger"></span></div>
              <span class="ozz-accordion-arrow"></span>
            </div>`;

          indexFieldNames();

          draggedItem.classList.add('ozz-used-block');
          draggedItem.innerHTML = `${initialDOM} <div class="ozz-accordion-body">${thisBlockFormDOM.innerHTML}</div>`;

          addCommonEvents(draggedItem);
        }
      });

      // On Submit with block editor
      blockEditor.closest('form.ozz-fm').addEventListener('submit', ozzCmsFormHandler);

      // Add index to field names
      function indexFieldNames() {
        const usedBlocks = blockFormLoader.querySelectorAll('.ozz-used-block');
        usedBlocks.forEach((block, ind) => {
          const thisBlockFields = block.querySelectorAll('input, textarea, button, progress, meter, select, datalist');
          thisBlockFields.forEach((field) => {
            const newName = `i-${ind}_${ field.name.replace(/^i-\d+_/, '') }`;
            field.name = newName;
          });
        });
      }

      // Add common events to each block
      function addCommonEvents(block=false) {
        if(block){
          block.querySelector('.ozz-block-delete-trigger').addEventListener('click', () => {
            block.remove();
            indexFieldNames();
          })

          // Block accordion event
          block.querySelector('.ozz-block-accordion-bar').addEventListener('click', () => {
            block.querySelector('.ozz-accordion-body').classList.toggle('active');
            block.querySelector('.ozz-block-accordion-bar').classList.toggle('active');
          });
        } else {
          blockEditor.querySelectorAll('li.pick-block').forEach(block => {
            // Remove Block
            block.querySelector('.ozz-block-delete-trigger').addEventListener('click', () => {
              block.remove();
              indexFieldNames();
            })

            // Block accordion event
            block.querySelector('.ozz-block-accordion-bar').addEventListener('click', () => {
              block.querySelector('.ozz-accordion-body').classList.toggle('active');
              block.querySelector('.ozz-block-accordion-bar').classList.toggle('active');
            });
          });

          // Expand editor
          blockEditorWrapper.querySelector('.ozz-block-editor-expand-button').addEventListener('click', () => {
            toggleBlockEditorCollapse(blockEditorWrapper);
            toggleBlockEditorExpand(blockEditorWrapper);
          });

          // Block stock layout
          const layouts = blockEditorWrapper.querySelectorAll('.ozz-block-editor__block-picker-head .lay');
          layouts.forEach(layout => {
            layout.addEventListener('click', (el) => {
              const layoutName = el.target.getAttribute('data-lay');
              layouts.forEach(lay => { lay.classList.remove('active'); });
              el.target.classList.add('active');
              blockEditorWrapper.classList.remove('lay1', 'lay2');
              blockEditorWrapper.classList.add(layoutName);
            })
          });
        }
      }
    });
  }
}


// ===============================================
// Ozz Form handler
// ===============================================
function ozzCmsFormHandler(e) {
  e.preventDefault();
  e.target.submit();
}


// ===============================================
// Ozz Update slug on title change
// ===============================================
function ozzCmsSlugUpdate() {
  // Update Slug in Title change
  const
    updateThis = document.querySelector('.j-title-onchange'),
    getFrom = document.querySelector('.ozz-fm .default-post-title');

  if (getFrom) {
    getFrom.addEventListener('keyup', () => {
      const slug = getFrom.value.replace(/\s+/g, '-').toLowerCase().replace(/[^\w\-]+/g, '');
      document.querySelector('.ozz-fm .default-post-slug').value = slug;
      if (getFrom && updateThis) {
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
        let fileInfoThumbnail = '';

        // Media item Embed DOM
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
                <input type="hidden" value="${fileInfo.dir + fileInfo.name}" name="ozz_media_file_name">
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
// Ozz Close Popup
// ===============================================
function ozzClosePopup() {
  const
    popup = document.querySelector('.ozz-cms-popup'),
    popupContent = document.querySelector('#cms-popup-content');

  popupContent.innerHTML = '';
  popup.classList.remove('active');
  ozz_app_state.popup_opened = false;
}


// ===============================================
// Ozz Open Popup
// ===============================================
function ozzOpenPopup(popupDOM) {
  const
    popup = document.querySelector('.ozz-cms-popup'),
    popupContent = popup.querySelector('#cms-popup-content'),
    closeTrigger = popup.querySelector('#cms-popup-close');

  popupContent.innerHTML = popupDOM;
  popup.classList.add('active');
  ozz_app_state.popup_opened = true;

  // Focus field
  const inputsOnPopup = popupContent.querySelectorAll('input[type="text"]');
  if ( inputsOnPopup.length > 0 ) {
    inputsOnPopup[0].focus();
  }

  // Close popup
  closeTrigger.addEventListener('click', ozzClosePopup);
  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      ozzClosePopup();
    }
  });
}


(function() {
  ozzCmsNavBar();
  ozzCmsBlockEditor();
  ozzCmsSlugUpdate();
  ozzCmsMediaManager();
})();
