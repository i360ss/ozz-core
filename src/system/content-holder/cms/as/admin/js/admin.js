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

  // ===============================================
  // Ozz Form handler
  // ===============================================
  function ozzFormHandler(e) {
    e.preventDefault();
    e.target.submit();
  }

  // ===============================================
  // Ozz Block editor
  // ===============================================
  if(document.querySelectorAll('.ozz-block-editor')){
    const ozzBlockEditor = document.querySelectorAll('.ozz-block-editor');
    ozzBlockEditor.forEach(blockEditor => {
      // Add wrapper class
      blockEditor.closest('.ozz-form__field').classList.add('ozz-block-editor-wrapper');
      addCommonEvents();

      const
        blocksArr = JSON.parse(blockEditor.getAttribute('data-blocks')),
        blockPicker = blockEditor.querySelector('.ozz-block-editor__block-picker'),
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
      blockEditor.closest('form.ozz-form').addEventListener('submit', ozzFormHandler);

      // Add index to field names
      function indexFieldNames() {
        const usedBlocks = blockFormLoader.querySelectorAll('.ozz-used-block');
        usedBlocks.forEach((block, ind) => {
          const thisBlockFields = block.querySelectorAll('input, textarea, button, progress, meter, select, datalist');
          thisBlockFields.forEach((field) => {
            const newName = `i-${ind}___${ field.name.replace(/^i-\d+___/, '') }`;
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
        }
      }
    });
  }

})();
