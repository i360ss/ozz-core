/**
* Ozz Core JS
* v1.0
*/

class Ozz {

  /**
   * Toggle Class
   * @param object DOM
   * @param string className
   */
  toggleClass(DOM, className) {
    DOM.classList.contains(className) ? DOM.classList.remove(className) : DOM.classList.add(className);
  }

  /**
   * Random string
   * @param int length
   */
  randomString(length) {
    return Array.from({ length }, () => Math.random().toString(36)[2]).join('');
  }

  /**
  * Ozz Form Repeater initialization
  * @param DOM Used Block
  */
  initRepeater(DOM=false) {
    this.repeater__Accordion(DOM);
    this.repeater__addItem(DOM);
    this.repeater__deleteItem(DOM);
  }

  /**
   * Repeater field Accordion
   */
  repeater__Accordion(repeater=false) {
    const accordionHeads = repeater ? repeater.querySelectorAll('.ozz-fm__repeat-head') : document.querySelectorAll('.ozz-fm__repeat-head');
    accordionHeads.forEach(thisHead => {
      const
        thisHeadParent = thisHead.closest('.ozz-fm__repeat-fields'),
        accordionBody = thisHeadParent.querySelector('.ozz-fm__repeat-body');

      if ( thisHead ) {
        thisHead.addEventListener('click', (e) => {
          if (e.target.classList.contains('ozz-fm__repeat-remove')) {
            return;
          }

          this.toggleClass(accordionBody, 'close');
          this.toggleClass(thisHead, 'close');
        })
      }
    });
  }

  /**
   * Add Repeater Item
   */
  repeater__addItem(DOM) {
    const addItemTrigger = DOM ? DOM.querySelectorAll('.ozz-fm__repeat-add') : document.querySelectorAll('.ozz-fm__repeat-add');
    addItemTrigger.forEach(addTrigger => {
      addTrigger.addEventListener('click', (e) => {
        const
          thisRepeater = e.target.closest('.ozz-fm__repeat'),
          thisWrapper = thisRepeater.querySelector(':scope > .ozz-fm__repeat-wrapper'),
          thisItemCount = thisWrapper.querySelectorAll(':scope > .ozz-fm__repeat-fields'),
          thisItem = thisWrapper.querySelector(':scope > .ozz-fm__repeat-fields'),
          newItem = thisItem.cloneNode(true);

        // Add ID to new item
        newItem.setAttribute('id', `rptf-${this.randomString(18)}`);

        // Clear values and modify repeater item
        const itemFields = newItem.querySelectorAll('input, textarea, button, progress, meter, select, datalist');
        itemFields.forEach(elm => {
          if (elm.tagName === 'INPUT' || elm.tagName === 'TEXTAREA') {
            elm.value = '';
          } else if (elm.tagName === 'SELECT') {
            elm.selectedIndex = 0;
          }

          if (thisRepeater.classList.contains('single') === false) {
            itemFields.forEach(elm => {
              const newName = elm.name.replace(/_\d+_(?=[^_]*$)/, `_${thisItemCount.length}_`);
              elm.name = newName;
            });
          }

          // Enable delete button if disabled
          [thisItem, newItem].forEach(element => {
            element.querySelector('.ozz-fm__repeat-remove').removeAttribute('disabled');
          });
        });

        // Clear and re-init Wysiwyg editors
        this.repeater__clearAndInitOzzWyg(newItem);

        newItem.querySelector('.ozz-fm__repeat-number').innerHTML = thisItemCount.length + 1;

        // Init for child repeaters of clone
        this.repeater__addItem(newItem);
        this.repeater__deleteItem(newItem);

        // Init accordion
        this.repeater__Accordion(newItem);

        // Append new item
        thisWrapper.appendChild(newItem);

        // Update Names
        this.repeater__renameFields();
      });
    });
  }

  /**
   * Delete repeater Item
   */
  repeater__deleteItem(DOM) {
    const deleteItemTrigger = DOM ? DOM.querySelectorAll('.ozz-fm__repeat-remove') : document.querySelectorAll('.ozz-fm__repeat-remove');
    deleteItemTrigger.forEach(deleteItem => {
      deleteItem.addEventListener('click', (e) => {
        e.preventDefault();
        // Confirmation popup should be here

        // Delete Item
        const
          thisFieldsetWrapper = deleteItem.closest('.ozz-fm__repeat-wrapper'),
          thisFields = thisFieldsetWrapper?.querySelectorAll(':scope > .ozz-fm__repeat-fields');

        if (thisFields && thisFields.length > 1) {
          deleteItem.closest('.ozz-fm__repeat-fields').remove();
          this.repeater__renameFields();
        } else {
          deleteItem.setAttribute('disabled', true);
          return false;
        }
      });
    });
  }

  /**
   * Reset field name indexes of repeater
   */
  repeater__renameFields(specificFieldSet=false) {
    const allWrappers = specificFieldSet !== false ? specificFieldSet : document.querySelectorAll('.ozz-fm__repeat-wrapper');
    allWrappers.forEach((wrapper) => {
      const
        thisRepeater = wrapper.closest('.ozz-fm__repeat'),
        thisFieldSet = wrapper.querySelectorAll(':scope > .ozz-fm__repeat-fields'),
        isSingle = thisRepeater.classList.contains('single'),
        rptName = thisRepeater.getAttribute('data-rpt'),
        rptNameParts = rptName.split('_'),
        rptNameOnly = rptNameParts[rptNameParts.length - 1];

      thisFieldSet.forEach((fieldSet, i) => {
        if (fieldSet) {
          const itemIndex = !isSingle
            ? fieldSet.querySelector(':scope > .ozz-fm__repeat-head .ozz-fm__repeat-number')
            : fieldSet.querySelector(':scope > .ozz-fm__repeat-number');

          if (itemIndex) {
            itemIndex.innerHTML = i + 1;
          }

          if (isSingle === false) {
            // Rename field names
            const itemFields = fieldSet.querySelectorAll('input, textarea, button, progress, meter, select, datalist');
            itemFields.forEach(elm => {
              const newName = elm.name.replace(new RegExp(`${rptNameOnly}_\\d+_`), `${rptNameOnly}_${i}_`);
              elm.name = newName;
            });
          }
        }
      });
    });
  }

  /**
   * Clear and Re-init wysiwyg editor on added by repeater
   */
  repeater__clearAndInitOzzWyg(DOM=false) {
    if (typeof OzzWyg === 'function') {
      const editors = DOM === false ? document.querySelectorAll('[data-ozz-wyg]') : DOM.querySelectorAll('[data-ozz-wyg]');
      editors.forEach(editor => {
        const id = `i-${Math.random().toString(36).substring(2, 6+2)}`;
        editor.innerHTML = '';
        editor.setAttribute('data-editor', id);
        new OzzWyg({ selector: editor });
      });
    }
  }

  /**
  * Form Field Validator
  * @param {*} field
  * @param {*} rules
  */
  validate( field, rules ) {
    // Write Validation logics here
    console.log(field);
    console.log(rules);
  }

  /**
  * Initialize validator by data attribute
  */
  initValidator() {
    const input_fields = document.querySelectorAll('[data-ozz-validate]');
    input_fields.forEach((field, i) => {
      const rules = field.getAttribute('data-ozz-validate');
      field.addEventListener('change', () => {
        this.validate(field, rules);
      });
    });
  }
}