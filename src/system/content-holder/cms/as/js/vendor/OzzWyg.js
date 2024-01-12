/**
 * Ozz Wysiwyg Editor JS
 * The wysiwyg editor plugin for vanilla JS
 * 
 * Author: A.W.M. Shakir
 * Email: shakeerwahid@gmail.com
 */

class OzzWyg {
  constructor(options) {
    this.options = { ...OzzWyg.defaults, ...options };

    if (typeof this.options.selector === 'object') {
      this.editors = [this.options.selector];
    } else {
      this.editors = document.querySelectorAll(this.options.selector).length > 0 ? document.querySelectorAll(this.options.selector) : false;
    }

    // Tools
    this.tools = {
      'headings': {
        name: 'Headings',
        dom: '<div class="ozz-wyg__tool-headings-trigger"><button type="button">Headings</button><div class="ozz-wyg__tool-headings-setting"></div></div>',
        child: {
          'h1': {
            name: 'Heading 1',
            dom: '<button type="button" data-action="formatBlock" data-value="h1">Heading 1</button>'
          },
          'h2': {
            name: 'Heading 2',
            dom: '<button type="button" data-action="formatBlock" data-value="h2">Heading 2</button>'
          },
          'h3': {
            name: 'Heading 3',
            dom: '<button type="button" data-action="formatBlock" data-value="h3">Heading 3</button>'
          },
          'h4': {
            name: 'Heading 4',
            dom: '<button type="button" data-action="formatBlock" data-value="h4">Heading 4</button>'
          },
          'h5': {
            name: 'Heading 5',
            dom: '<button type="button" data-action="formatBlock" data-value="h5">Heading 5</button>'
          },
          'h6': {
            name: 'Heading 6',
            dom: '<button type="button" data-action="formatBlock" data-value="h6">Heading 6</button>'
          },
          'paragraph': {
            name: 'Paragraph',
            dom: '<button type="button" data-action="formatBlock" data-value="P">Normal Text</button>'
          },
          'quote': {
            name: 'Quote',
            dom: '<button type="button" data-action="quote">Quote</button>'
          },
          'code': {
            name: 'Code',
            dom: '<button type="button" data-action="code">Code</button>'
          },
        }
      },
      'bold': {
        name: 'Bold',
        dom: '<button type="button" data-action="bold">Bold</button>'
      },
      'italic': {
        name: 'Italic',
        dom: '<button type="button" data-action="italic">Italic</button>'
      },
      'underline': {
        name: 'Underline',
        dom: '<button type="button" data-action="underline">Underline</button>',
        child: {
          'strikethrough': {
            name: 'Strikethrough',
            dom: '<button type="button" data-action="strikethrough">Strikethrough</button>',
          },
          'subscript': {
            name: 'Subscript',
            dom: '<button type="button" data-action="subscript">Subscript</button>',
          },
          'superscript': {
            name: 'Superscript',
            dom: '<button type="button" data-action="superscript">Superscript</button>',
          },
        }
      },
      'link': {
        name: 'Link',
        dom: '<div class="ozz-wyg__tool-link-trigger"><button type="button" data-action="link">Link</button><div class="ozz-wyg__tool-link-setting"></div></div>'
      },
      'table': {
        name: 'Table',
        dom: '<div class="ozz-wyg__tool-table-trigger"><button type="button" data-action="table">Table</button><div class="ozz-wyg__tool-table-setting"></div></div>'
      },
      'ol': {
        name: 'Ordered List',
        dom: '<button type="button" data-action="insertOrderedList">Ordered List</button>'
      },
      'ul': {
        name: 'Un-ordered List',
        dom: '<button type="button" data-action="insertUnorderedList">Un-ordered List</button>'
      },
      'alignLeft': {
        name: 'Align Left',
        dom: '<button type="button" data-action="justifyLeft">Align Left</button>',
        child: {
          'alignRight': {
            name: 'Align Right',
            dom: '<button type="button" data-action="justifyRight">Align Right</button>'
          },
          'alignCenter': {
            name: 'Align Center',
            dom: '<button type="button" data-action="justifyCenter">Align Center</button>'
          },
          'justify': {
            name: 'Justify',
            dom: '<button type="button" data-action="justifyFull">Justify</button>'
          },
          'indentIncrease': {
            name: 'Indent Increase',
            dom: '<button type="button" data-action="indent">Indent Increase</button>'
          },
          'indentDecrease': {
            name: 'Indent Decrease',
            dom: '<button type="button" data-action="outdent">Indent Decrease</button>'
          },
        }
      },
      'media': {
        name: 'Media',
        dom: '<div class="ozz-wyg__tool-media-trigger"><button type="button" data-action="media">Media</button><div class="ozz-wyg__tool-media-setting"></div></div>'
      },
      'codeView': {
        name: 'Code View',
        dom: '<button type="button" data-action="codeView">Code View</button>'
      }
    };

    // Initiate Each Editors
    if (this.editors) {
      this.editors.forEach(editor => {
        this.editorID = `i-${this.randomId()}`;
        editor.setAttribute('data-editor', this.editorID);
        this.editor = editor;
        this.initEditor();
      });
    }
  }

  /**
   * Initialize Wysiwyg Editor
   */
  initEditor() {
    this.editor.classList.add('ozz-wyg');
    this.editor.innerHTML = this.editorDOM();
    this.editor.querySelectorAll('button[data-action]').forEach(trigger => {
      trigger.addEventListener('click', (e) => {
        this.fireAction(e);
      });
    });
    this.playGround = this.editor.querySelector('[data-editor-area]');

    // Modify on input
    this.playGround.addEventListener('input', () => {
      // Modify tables
      const tables = this.playGround.querySelectorAll('table');
      tables.forEach((table) => {
        // Wrap table
        if (!table.closest('.ozz-wyg-table-wrapper')) {
          const tableWrapped = document.createElement('div');
          tableWrapped.classList.add('ozz-wyg-table-wrapper');
          tableWrapped.innerHTML = table.outerHTML;
          table.outerHTML = tableWrapped.outerHTML;
        }

        // Clear inline styles
        table.removeAttribute('style');
        const tItems = table.querySelectorAll('tbody, thead, tfoot, tr, td, th');
        tItems.forEach(item => {
          item.removeAttribute('style');
        });
      });
    });
  }

  /**
   * Setup Editor DOM
   * @returns {*} EditorDOM
   */
  editorDOM() {
    let toolsDOM = '';
    this.options.tools.forEach(tool => {
      if (this.tools[tool]) {
        let parentTool = document.createElement('div');
        parentTool.classList.add('ozz-wyg__tool', `ozz-wyg__tool--${tool}`);

        if (this.tools[tool].child) {
          parentTool.innerHTML = `<div class="ozz-wyg__tool-has-child">${this.tools[tool].dom}<span class="more-tools-trigger"></span></div>`;
          // Get Child tools
          let childElementWrapper = document.createElement('div');
          childElementWrapper.classList.add('ozz-wyg__tool-child');
          this.options.tools.forEach(child2 => {
            if (this.tools[tool].child[child2]) {
              const childItem =  this.tools[tool].child[child2];
              childElementWrapper.innerHTML += childItem.dom;
            }
          });
          parentTool.appendChild(childElementWrapper);
        } else {
          parentTool.innerHTML = this.tools[tool].dom;
        }
        toolsDOM += parentTool.outerHTML;
      }
    });

    this.toolbar = `<div class="ozz-wyg__toolbar">${toolsDOM}</div>`;

    // Editor Area
    this.editableArea = document.createElement('div');
    this.editableArea.setAttribute('data-editor-area', true);
    this.editableArea.setAttribute('contenteditable', true);
    this.editableArea.classList.add('ozz-wyg__editor-area');

    const existingEditorArea = this.editor.querySelector('[data-editor-area]');
    if (existingEditorArea) {
      const innerContent = existingEditorArea.innerHTML;
      this.editableArea.innerHTML = innerContent;
    }

    return this.toolbar + this.editableArea.outerHTML;
  }

  /**
   * Check if HTML view enabled
   * @returns {boolean}
   */
  isHTML() {
    return this.playGround.classList.contains('ozz-wyg-html-view');
  }

  /**
   * Utility - Find nested object item
   * @param {*} obj 
   * @param {*} key 
   * @returns 
   */
  findObject(obj, key) {
    if (obj.hasOwnProperty(key)) {
      return obj[key];
    } else {
      for (const prop in obj) {
        if (obj[prop] !== null && typeof obj[prop] === 'object') {
          const result = this.findObject(obj[prop], key);
          if (result) {
            return result;
          }
        }
      }
    }

    return null;
  }

  /**
   * Random ID
   */
  randomId(length = 6) {
    return Math.random().toString(36).substring(2, length+2);
  }

  /**
   * Fire Action
   * @param {*} event
   */
  fireAction(event) {
    const action = event.target.getAttribute('data-action');

    // Format block
    let value = (action == 'formatBlock')
      ? event.target.getAttribute('data-value')
      : null;

    if (action == 'link') {
      // Link
      this.linkPopUp(event);
    } else if (action == 'table') {
      // Create Table
      this.tablePopUp(event);
    } else if (action == 'media') {
      // insert Media
      this.mediaPopUp(event);
    } else if (action == 'quote') {
      this.quoteText();
    } else if (action == 'code') {
      this.codeText();
    } else if (action == 'codeView') {
      this.toggleCodeView();
    } else {
      this.exeCMD(action, value);
    }
  }

  /**
   * Link Tool Popup
   */
  linkPopUp(ev) {
    const linkCls = 'ozz-wyg__tool-link-',
      parent = ev.target.closest(`.${linkCls}trigger`),
      settingsDOM = parent.querySelector(`.${linkCls}setting`),
      selection = window.getSelection();

    // Store selection
    let sRange = false;
    let selectionStr = selection.toString();
    if (selection.getRangeAt && selection.rangeCount) {
      sRange = selection.getRangeAt(0).cloneRange();
    }

    // Check if there's an existing anchor link
    const existingAnchor = selection.anchorNode?.parentElement.tagName === 'A' ? selection.anchorNode.parentElement : null,
      existingURL = existingAnchor ? existingAnchor.href : '',
      existingTarget = existingAnchor ? existingAnchor.target : '';

    settingsDOM.innerHTML = `
      <label>URL:</label> <input form="" type="text" id="url-${this.editorID}" value="${existingURL}"><br>
      <label>Target:</label> <input form="" type="text" id="target-${this.editorID}" value="${existingTarget ? existingTarget : '_blank'}"><br>
      <button type="button" class="ozz-wyg-regular-btn" id="insertLinkTrigger-${this.editorID}">${existingAnchor ? 'Update' : 'Insert'}</button>`;
    settingsDOM.classList.toggle('active');

    // Insert or update link
    const insertLinkTrigger = settingsDOM.querySelector('#insertLinkTrigger-' + this.editorID);
    insertLinkTrigger.addEventListener('click', () => {
      const urlInput = settingsDOM.querySelector('#url-' + this.editorID).value;
      const targetInput = settingsDOM.querySelector('#target-' + this.editorID).value;

      if (urlInput && targetInput) {
        const anchor = existingAnchor || document.createElement('a');
        anchor.href = urlInput;
        anchor.target = targetInput;
        anchor.textContent = existingAnchor ? existingAnchor.textContent : selectionStr;

        if (!existingAnchor) {
          if (sRange) {
            selection.removeAllRanges();
            selection.addRange(sRange);
          }
          document.execCommand('insertHTML', false, anchor.outerHTML);
        }
        settingsDOM.classList.remove('active'); // Close Popup
        this.linkPopOver(); // Init Link Popover
      }
    });

    // Close popup
    document.addEventListener('click', (e) => {
      if (!parent.contains(e.target)) {
        settingsDOM.classList.remove('active');
        this.linkPopOver(); // Init Link Popover
      }
    });
  }

  /**
   * Link popover
   */
  linkPopOver() {
    this.playGround.querySelectorAll('a').forEach(anchor => {
      anchor.addEventListener('click', (e) => {
        if (anchor.getAttribute('role') !== 'popover') {
          const popoverDOM = document.createElement('span');
          popoverDOM.setAttribute('contenteditable', false);
          popoverDOM.classList.add('ozz-wyg-popover');
          popoverDOM.innerHTML = `
          <a href="${anchor.href}" role="popover" target="_blank">${anchor.href}</a>
          <button type="button" class="ozz-wyg-editlink"></button>
          <button type="button" class="ozz-wyg-unlink"></button>`;

          if (this.editor.querySelectorAll('.ozz-wyg-popover').length === 0) {
            anchor.insertAdjacentElement('afterend', popoverDOM);

            // Position popover element
            let {rx, ry} = 0;
            if (popoverDOM.closest('.ozz-wyg-table-wrapper')) {
              const cTbl = popoverDOM.closest('.ozz-wyg-table-wrapper');
              ry = cTbl.getBoundingClientRect().y;
              rx = cTbl.getBoundingClientRect().x;
            }
            popoverDOM.style.top = `${e.clientY - ry}px`;
            popoverDOM.style.left = `${e.clientX - rx}px`;
          }

          const removePopoverEv = (ev) => {
            if (!anchor.contains(ev.target) && !popoverDOM.contains(ev.target)) {
              popoverDOM.remove();
              document.removeEventListener('click', removePopoverEv);
            }
          };
          document.addEventListener('click', removePopoverEv);

          // Unlink
          popoverDOM.querySelector('.ozz-wyg-unlink').addEventListener('click', (ev) => {
            ev.stopPropagation();
            const textNode = document.createTextNode(anchor.textContent);
            anchor.parentNode.replaceChild(textNode, anchor);
            popoverDOM.remove();
            document.removeEventListener('click', removePopoverEv);
          });

          // Edit this link
          popoverDOM.querySelector('.ozz-wyg-editlink').addEventListener('click', () => {
            const linkBtn = this.editor.querySelector('button[data-action="link"]');
            setTimeout(() => { linkBtn.click() }, 1);
          });
        }
      });
    });
  }

  /**
   * Table Popup
   */
  tablePopUp(ev) {
    const
      tableCls = 'ozz-wyg__tool-table-',
      parent = ev.target.closest(`.${tableCls}trigger`),
      settingsDOM = parent.querySelector(`.${tableCls}setting`),
      selection = window.getSelection();

    // Store selection
    let sRange = false;
    if (selection.getRangeAt && selection.rangeCount) {
      sRange = selection.getRangeAt(0).cloneRange();
    }

    settingsDOM.innerHTML = `
      <input form="" type="number" min="1" max="100" value="2" placeholder="X" id="row-${this.editorID}">
      <input form="" type="number" min="1" max="100" value="2" placeholder="Y" id="column-${this.editorID}">
      <span class="sub-options">
        <span>
          <input form="" type="checkbox" id="has-th-${this.editorID}">
          <label for="has-th-${this.editorID}"">No Header</label>
        </span>
        <span>
          <input form="" type="checkbox" id="has-footer-${this.editorID}">
          <label for="has-footer-${this.editorID}">No Footer</label>
        </span>
      </span>
      <button type="button" class="ozz-wyg-regular-btn" id="insertTableTrigger-${this.editorID}">Insert</button>`;
    settingsDOM.classList.toggle('active');

    // Insert or update Table
    const insertTableTrigger = settingsDOM.querySelector('#insertTableTrigger-' + this.editorID);
    insertTableTrigger.addEventListener('click', () => {
      let rows = settingsDOM.querySelector('#row-' + this.editorID).value,
      columns = settingsDOM.querySelector('#column-' + this.editorID).value,
      noHead = settingsDOM.querySelector('#has-th-' + this.editorID).checked,
      noFooter = settingsDOM.querySelector('#has-footer-' + this.editorID).checked;
      rows = (rows > 100) ? 100 : rows;
      columns = (columns > 100) ? 100 : columns;

      // Create Table
      const table = document.createElement('table');
      // Table Head
      if (noHead === false) {
        const tHead = table.createTHead();
        const headerRow = tHead.insertRow();
        for (let i = 0; i < columns; i++) {
          const th = document.createElement('th');
          headerRow.appendChild(th);
        }
      }

      // Table Body
      const tBody = table.createTBody();
      for (let i = 0; i < rows; i++) {
        const tRows = tBody.insertRow(i);
        for (let i = 0; i < columns; i++) {
          tRows.insertCell(i);
        }
      }

      // Table Footer
      if (noFooter === false) {
        const tFoot = table.createTFoot();
        const footRow = tFoot.insertRow();
        for (let i = 0; i < columns; i++) {
          footRow.insertCell(i);
        }
      }

      // Set Range Again
      if (sRange) {
        selection.removeAllRanges();
        selection.addRange(sRange);
      }

      // Create Table Wrapper
      const $table = document.createElement('div');
      $table.classList.add('ozz-wyg-table-wrapper');
      $table.innerHTML = table.outerHTML;
      document.execCommand('insertHTML', false, `<br>${$table.outerHTML}<br>`);
      this.tableActions(); // Table Actions
    });

    // Close popup
    document.addEventListener('click', (e) => {
      if (!parent.contains(e.target)) {
        settingsDOM.classList.remove('active');
      }
    });
  }

  /**
   * Table actions
   */
  tableActions() {
    const $table = document.querySelectorAll('.ozz-wyg-table-wrapper');
    $table.forEach(tbl => {
      tbl.addEventListener('mouseover', () => {
        let tblTools = tbl.querySelectorAll('.ozz-wyg-table-actions');
        if (tblTools.length === 0) {
          const actions = document.createElement('div');
          actions.setAttribute('contenteditable', false);
          actions.classList.add('ozz-wyg-table-actions');
          actions.innerHTML = `
          <span class="ozz-wyg-table-actions__row">
            <button type="button" title="Add Row" data-tbl-action="addrow">Add Row</button>
            <button type="button" title="Remove Row" data-tbl-action="deleterow">Delete Row</button>
          </span>
          <span class="ozz-wyg-table-actions__column">
            <button type="button" title="Add Column" data-tbl-action="addcol">Add Column</button>
            <button type="button" title="Remove Column" data-tbl-action="deletecol">Delete Column</button>
          </span>`;

          // Perform Table Actions
          actions.querySelectorAll('button').forEach(btn => {
            btn.addEventListener('click', (e) => {
              e.preventDefault();
              const action = e.target.getAttribute('data-tbl-action');
              switch (action) {
                case 'addrow':
                  this.addTableRow(tbl);
                  break;
                case 'deleterow':
                  this.deleteTableRow(tbl);
                  break;
                case 'addcol':
                  this.addTableCol(tbl);
                  break;
                case 'deletecol':
                  this.deleteTableCol(tbl);
                  break;
                default:
                  break;
              }
            });
          });
          tbl.appendChild(actions);
        }

        tbl.addEventListener('mouseleave', () => {
          tblTools[0] ? tblTools[0].remove() : false;
        });
      });
    });
  }

  /**
   * Add Table Row
   * @param table_wrap
   */
  addTableRow(table_wrap) {
    const tbody = table_wrap.querySelector('tbody');
    if (tbody) {
      const cellsCount = tbody.querySelector('tr')?.querySelectorAll('td')?.length ?? 1;
      const newRow = tbody.insertRow(-1);
      for (let i = 0; i < cellsCount; i++) {
        const td = document.createElement('td');
        td.innerHTML = '<br>';
        newRow.appendChild(td);
      }
    }
  }

  /**
   * Delete Table Row
   * @param table_wrap
   */
  deleteTableRow(table_wrap) {
    const tbody = table_wrap.querySelector('table tbody');
    if (tbody && tbody.rows.length > 1) {
      tbody.deleteRow(-1);
    }
  }

  /**
   * Add Table Column
   * @param table_wrap
   */
  addTableCol(table_wrap) {
    const thead = table_wrap.querySelector('thead');
    const tbody = table_wrap.querySelector('tbody');
    const tfoot = table_wrap.querySelector('tfoot');

    const addCellToRows = (rows, newCellIndex, type='td') => {
      rows.forEach((row) => {
        const td = document.createElement(type);
        td.innerHTML = '<br>';
        row.insertBefore(td, row.cells[newCellIndex]);
      });
    }

    if (thead) {
      const headerRows = thead.querySelectorAll('tr');
      const newCellIndex = headerRows[0]?.querySelectorAll('th, td')?.length ?? 0;
      addCellToRows(headerRows, newCellIndex, 'th');
    }

    if (tbody) {
      const bodyRows = tbody.querySelectorAll('tr');
      const newCellIndex = bodyRows[0]?.querySelectorAll('td')?.length ?? 0;
      addCellToRows(bodyRows, newCellIndex);
    }

    if (tfoot) {
      const footerRows = tfoot.querySelectorAll('tr');
      const newCellIndex = footerRows[0]?.querySelectorAll('td')?.length ?? 0;
      addCellToRows(footerRows, newCellIndex);
    }
  }

  /**
   * Delete Table Column
   * @param table_wrap
   */
  deleteTableCol(table_wrap) {
    const thead = table_wrap.querySelector('thead');
    const tbody = table_wrap.querySelector('tbody');
    const tfoot = table_wrap.querySelector('tfoot');

    const deleteLastCell = (rows) => {
      const lastCellIndex = rows[0]?.cells.length - 1;
      if (lastCellIndex > 0) {
        rows.forEach((row) => {
          row.deleteCell(lastCellIndex);
        });
      }
    }

    if (thead) {
      deleteLastCell(thead.querySelectorAll('tr'));
    }

    if (tbody) {
      deleteLastCell(tbody.querySelectorAll('tr'));
    }

    if (tfoot) {
      deleteLastCell(tfoot.querySelectorAll('tr'));
    }
  }

  /**
   * Insert Media
   */
  mediaPopUp(ev) {
    const linkCls = 'ozz-wyg__tool-media-',
      parent = ev.target.closest(`.${linkCls}trigger`),
      settingsDOM = parent.querySelector(`.${linkCls}setting`),
      selection = window.getSelection();

    // Store selection
    let sRange = false;
    if (selection.getRangeAt && selection.rangeCount) {
      sRange = selection.getRangeAt(0).cloneRange();
    }

    // Get Selected Media element tp update - To Do
    let existingURL = '';
    let existingALT = '';

    settingsDOM.innerHTML = `
      <label>Upload:</label> <input form="" type="file" accept="image/*,video/*" id="file-${this.editorID}" value="${existingURL}"><br>
      <label>Media URL:</label> <input form="" type="text" id="url-${this.editorID}" value="${existingURL}"><br>
      <label>Alt:</label> <input form="" type="text" id="alt-${this.editorID}" value="${existingALT}"><br>
      <button type="button" class="ozz-wyg-regular-btn" id="insertMediaTrigger-${this.editorID}">${existingURL ? 'Update' : 'Insert'}</button>`;
    settingsDOM.classList.toggle('active');

    // Insert Media
    const insertMediaTrigger = settingsDOM.querySelector('#insertMediaTrigger-' + this.editorID);
    insertMediaTrigger.addEventListener('click', () => {
      const files = settingsDOM.querySelector('#file-' + this.editorID).files;
      const url = settingsDOM.querySelector('#url-' + this.editorID).value;
      const alt = settingsDOM.querySelector('#alt-' + this.editorID).value;
      let altText = alt;
      let fileType = 'unknown';

      // Set Range Again
      if (sRange) {
        selection.removeAllRanges();
        selection.addRange(sRange);
      }

      if (files.length > 0) {
        // File (DataURI)
        const file = files[0];
        fileType = file.type.split('/')[0];
        if (fileType === 'image' || fileType === 'video') {
          const reader = new FileReader();
          reader.onload = function (event) {
            altText = altText == '' ? file.name : altText;
            processAndInsertMediaElement(fileType, event.target.result, altText);
          };
          reader.readAsDataURL(file);
        }
      } else if (url !== '') {
        // URL attachment
        const urlParts = url.split('/');
        const filename = urlParts[urlParts.length - 1];
        const fileExt = filename.includes('.') ? filename.split('.').pop().toLowerCase() : '';

        // Determine file type based on the file extension or URL
        if (['jpg', 'jpeg', 'png', 'svg', 'webp', 'gif'].includes(fileExt)) {
          fileType = 'image';
        } else if (['mp4', 'webm', 'ogg', 'avi', 'mov'].includes(fileExt)) {
          fileType = 'video';
        } else if (this.isYouTubeURL(url)) {
          fileType = 'youtube';
        } else if (this.isVimeoURL(url)) {
          fileType = 'vimeo';
        }

        altText = (altText !== '') ? altText : filename;
        processAndInsertMediaElement(fileType, url, altText);
      }
    });

    // Process and Insert Media Element
    const processAndInsertMediaElement = (fileType, mediaItem, altText) => {
      let mediaElement;
      if (fileType === 'image') {
        mediaElement = `<img src="${mediaItem}" class="align-center" alt="${altText}">`;
      } else if (fileType === 'video') {
        mediaElement = `<br><div class="media-wrapper"><video src="${mediaItem}" controls></video></div><br>`;
      } else if (fileType === 'youtube') {
        mediaElement = `<br>${this.getYouTubeEmbedCode(mediaItem)}<br>`;
      } else if (fileType === 'vimeo') {
        mediaElement = `<br>${this.getVimeoEmbedCode(mediaItem)}<br>`;
      } else {
        mediaElement = false;
      }

      // Insert Media
      if (mediaElement !== false) {
        document.execCommand('insertHTML', false, `<br>${mediaElement}<br>`);
        settingsDOM.classList.remove('active'); // Close PopUp

        this.mediaPopover(); // Config Media Popover
      }
    }

    // Close popup
    document.addEventListener('click', (e) => {
      if (!parent.contains(e.target)) {
        settingsDOM.classList.remove('active');
      }
    });
  }

  /**
   * Media Popover
   */
  mediaPopover() {
    const mediaItems = this.editor.querySelectorAll('img, .media-wrapper');
    mediaItems.forEach(mediaItem => {
      mediaItem.addEventListener('click', (e) => {
        const popoverDOM = document.createElement('div');
        popoverDOM.setAttribute('contenteditable', false);
        popoverDOM.classList.add('ozz-wyg-media-actions');

        // Set Selected Width
        const regex = /^w-\d+/;
        const widthClass = Array.from(mediaItem.classList).find(className => regex.test(className));
        let options = '<option value="auto">Auto</option>';
        for (let i = 1; i < 21; i++) {
          const clsName = 'w-'+i * 5;
          if (clsName == widthClass) {
            options += `<option selected value="${clsName}">${i * 5}%</option>`;
          } else {
            options += `<option value="${clsName}">${i * 5}%</option>`;
          }
        }
        popoverDOM.innerHTML = `
          <button type="button" title="Align Left" data-media-action="align-left">Align Left</button>
          <button type="button" title="Align Center" data-media-action="align-center">Align Center</button>
          <button type="button" title="Align Right" data-media-action="align-right">Align Right</button>
          <button type="button" title="Inline" data-media-action="inline">Inline</button>
          <select form="" data-media-action="width">${options}</select>
          <button type="button" title="Delete" data-media-action="delete">Delete</button>
        `;

        if (this.editor.querySelectorAll('.ozz-wyg-media-actions').length === 0) {
          mediaItem.insertAdjacentElement('afterend', popoverDOM);

          // Position popover element
          popoverDOM.style.top = `${e.clientY}px`;
          popoverDOM.style.left = `${e.clientX}px`;

          // Media Actions
          popoverDOM.querySelectorAll('button, select').forEach(actionTrigger => {
            if (actionTrigger.tagName === 'SELECT') {
              actionTrigger.addEventListener('change', () => {
                const action = actionTrigger.value;
                mediaItem.classList.remove(...Array.from(mediaItem.classList).filter(className => className.startsWith('w-')));
                if (action !== 'auto') {
                  mediaItem.classList.add(action);
                }
              });
            } else {
              actionTrigger.addEventListener('click', () => {
                const action = actionTrigger.getAttribute('data-media-action');
                if (action == 'align-left' || action == 'align-right' || action == 'align-center') {
                  mediaItem.classList.remove(...Array.from(mediaItem.classList).filter(className => className.startsWith('align-')));
                  mediaItem.classList.add(action);
                } else if (action == 'inline') {
                  mediaItem.classList.toggle(action);
                } else if (action == 'delete') {
                  mediaItem.remove();
                  popoverDOM.remove();
                }
              });
            }
          });
        }

        // Close popover
        const tempCloseEvent = (ev) => {
          if (!popoverDOM.contains(ev.target) && ev.target !== popoverDOM && ev.target !== mediaItem) {
            this.editor.querySelector('.ozz-wyg-media-actions')?.remove();
            document.removeEventListener('click', tempCloseEvent);
          }
        };
        document.addEventListener('click', tempCloseEvent);
      });
    });
  }

  /**
   * Is Youtube URL
   * @param {string} url
   * @return
   */
  isYouTubeURL(url) {
    return (
      url.includes('youtube.com/watch') ||
      url.includes('youtu.be') ||
      url.includes('youtube.com/embed')
    );
  }

  /**
   * Is Vimeo URL
   * @param {string} url
   * @returns
   */
  isVimeoURL(url) {
    return (
      url.includes('vimeo.com') ||
      url.includes('player.vimeo.com/video')
    );
  }

  /**
   * Get Embed code (Youtube)
   * @param {string} url
   * @returns 
   */
  getYouTubeEmbedCode(url) {
    const videoID = this.extractYouTubeVideoID(url);
    return `<div class="media-wrapper"><span class="height-holder"></span><iframe src="https://www.youtube.com/embed/${videoID}" frameborder="0" allowfullscreen></iframe><div>`;
  }

  /**
   * Extract Youtube video ID
   * @param {string} url 
   * @returns 
   */
  extractYouTubeVideoID(url) {
    const regex = /(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/;
    const match = url.match(regex);
    return match ? match[1] : '';
  }

  /**
   * Get Embed code (Vimeo)
   * @param {string} url 
   * @returns 
   */
  getVimeoEmbedCode(url) {
    const videoID = this.extractVimeoVideoID(url);
    return `<div class="media-wrapper"><span class="height-holder"></span><iframe src="https://player.vimeo.com/video/${videoID}" frameborder="0" allowfullscreen></iframe></div>`;
  }

  /**
   * Extract Vimeo video ID
   * @param {string} url 
   * @returns 
   */
  extractVimeoVideoID(url) {
    const regex = /vimeo\.com\/(?:video\/)?(\d+)/;
    const match = url.match(regex);
    return match ? match[1] : '';
  }

  /**
   * Execute Command
   * @param {string} command
   * @param value
   */
  exeCMD(command, value = null) {
    document.execCommand(command, false, value);
  }

  /**
   * Quote Text
   */
  quoteText() {
    const
      selection = window.getSelection(),
      quote = '<blockquote><p>' + ((selection.toString() == '') ? '<br>' : selection.toString()) + 
      '</p><footer class="blockquote-footer">--Footer, <cite>cite</cite></footer></blockquote><br>';

    document.execCommand('insertHTML', false, quote);
  }

  /**
   * Add Code
   */
  codeText() {
    const
      selection = window.getSelection(),
      code = '<code>' + selection.toString() + '</code>';

    document.execCommand('insertHTML', false, code);
  }

  /**
   * Toggle Code and visual view
   */
  toggleCodeView() {
    if (this.isHTML()) {
      this.playGround.classList.remove('ozz-wyg-html-view');
      this.playGround.innerHTML = this.playGround.textContent;
      this.tableActions(); // Init table Actions
      this.mediaPopover(); // Config Media Popover
    } else {
      this.playGround.querySelectorAll('[contenteditable="false"]').forEach(element => {
        element.remove();
      });
      this.playGround.classList.add('ozz-wyg-html-view');
      this.playGround.textContent = this.playGround.innerHTML;
    }
  }

  /**
   * Get value from editor
   */
  getValue() {
    return this.playGround.innerHTML;
  }
}

// Default options for the plugin
OzzWyg.defaults = {
  pluginName: 'Ozz Wysiwyg Editor',
  selector: '[data-ozz-wyg]',
  tools: [
    'headings',
    'paragraph', 'code', 'quote', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
    'bold',
    'italic',
    'underline',
    'strikethrough',
    'subscript',
    'superscript',
    'alignLeft',
    'alignRight',
    'alignCenter',
    'justify',
    'indent',
    'outdent',
    'ol',
    'ul',
    'link',
    'table',
    'media',
    'codeView'
  ]
};