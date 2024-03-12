import RepeaterField from './RepeaterField';
import OzzWyg from '../vendor/ozz-wyg';
import Sortable from '../vendor/Sortable';
import { SetState, GetState } from '../utils/State';
import FormHandler from './FormHandler';
import MediaManagerPopup from './MediaManagerPopup';

export default () => {
  if(document.querySelectorAll('.ozz-block-editor').length === 0) return;

  const repeaterField = new RepeaterField();

  const toggleBlockEditorResize = (blockEditorWrapper) => {
    if ( GetState('nav_collapsed') ) {
      blockEditorWrapper.classList.add('collapsed');
      SetState('block_editor_collapsed', true);
    } else {
      blockEditorWrapper.classList.remove('collapsed');
      SetState('block_editor_collapsed', false);
    }
  }

  const toggleBlockEditorExpand = (blockEditorWrapper) => {
    if (blockEditorWrapper.classList.contains('expanded')) {
      blockEditorWrapper.classList.remove('expanded');
      SetState('block_editor_expanded', false);
    } else {
      blockEditorWrapper.classList.add('expanded');
      SetState('block_editor_expanded', true);
    }
  }

  // Toggle Block Accordion
  const ozzToggleBlock = (block, state=true) => {
    block.setAttribute('data-expand', state);
    block.querySelector(':scope > .ozz-block-accordion-bar').classList.toggle('active', state);
    block.querySelector(':scope > .ozz-accordion-body').classList.toggle('active', state);
  }

  // Expand block if error field inside
  const ozzExpandBlockIfError = () => {
    const usedBlocks = document.querySelectorAll('.ozz-block-editor .ozz-used-block');
    usedBlocks.forEach(block => {
      const errors = block.querySelectorAll('.field-error', 'input.error', 'textarea.error', 'select.error');
      if (errors.length > 0) {
        ozzToggleBlock(block);
      }
    });
  }

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
        repeaterField.initRepeater(draggedItem, MediaManagerPopup);
        MediaManagerPopup(draggedItem);
        if (typeof OzzWyg === 'function') {
          new OzzWyg({ selector: '[data-ozz-wyg]' });
        }
      }
    });

    // On Submit with block editor
    blockEditor.closest('form.ozz-fm').addEventListener('submit', FormHandler);

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
    if ( GetState('block_editor_expanded') ) {
      blockEditorWrapper.classList.add('expanded');
    }
    if ( GetState('nav_collapsed') ) {
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
          repeaterField.initRepeater(blockClone);
          MediaManagerPopup(blockClone);
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
          toggleBlockEditorResize(blockEditorWrapper);
          toggleBlockEditorExpand(blockEditorWrapper);
        });

        // Set Block stock layout
        const layouts = blockEditorStockHead.querySelectorAll('.lay');
        function setStockLayout() {
          const layout = GetState('block_editor_stock_layout');
          layouts.forEach((lay, i) => { 
            lay.classList.remove('active');
            blockEditorWrapper.classList.remove(`lay${i+1}`);
          });
          blockEditorStockHead.querySelector(`span.button.${layout}`).classList.add('active');
          blockEditorWrapper.classList.add(layout);

          layouts.forEach(layout => {
            layout.addEventListener('click', (el) => {
              const layoutName = el.target.getAttribute('data-lay');
              SetState('block_editor_stock_layout', layoutName);
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
