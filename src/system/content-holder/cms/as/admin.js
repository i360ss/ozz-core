"use strict";

function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { _defineProperty2(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _defineProperty2(e, r, t) { return (r = _toPropertyKey(r)) in e ? Object.defineProperty(e, r, { value: t, enumerable: !0, configurable: !0, writable: !0 }) : e[r] = t, e; }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof2(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof2(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof2(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function _typeof2(o) { "@babel/helpers - typeof"; return _typeof2 = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof2(o); }
function _regeneratorRuntime() { "use strict"; /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/facebook/regenerator/blob/main/LICENSE */ _regeneratorRuntime = function _regeneratorRuntime() { return e; }; var t, e = {}, r = Object.prototype, n = r.hasOwnProperty, o = Object.defineProperty || function (t, e, r) { t[e] = r.value; }, i = "function" == typeof Symbol ? Symbol : {}, a = i.iterator || "@@iterator", c = i.asyncIterator || "@@asyncIterator", u = i.toStringTag || "@@toStringTag"; function define(t, e, r) { return Object.defineProperty(t, e, { value: r, enumerable: !0, configurable: !0, writable: !0 }), t[e]; } try { define({}, ""); } catch (t) { define = function define(t, e, r) { return t[e] = r; }; } function wrap(t, e, r, n) { var i = e && e.prototype instanceof Generator ? e : Generator, a = Object.create(i.prototype), c = new Context(n || []); return o(a, "_invoke", { value: makeInvokeMethod(t, r, c) }), a; } function tryCatch(t, e, r) { try { return { type: "normal", arg: t.call(e, r) }; } catch (t) { return { type: "throw", arg: t }; } } e.wrap = wrap; var h = "suspendedStart", l = "suspendedYield", f = "executing", s = "completed", y = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} var p = {}; define(p, a, function () { return this; }); var d = Object.getPrototypeOf, v = d && d(d(values([]))); v && v !== r && n.call(v, a) && (p = v); var g = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(p); function defineIteratorMethods(t) { ["next", "throw", "return"].forEach(function (e) { define(t, e, function (t) { return this._invoke(e, t); }); }); } function AsyncIterator(t, e) { function invoke(r, o, i, a) { var c = tryCatch(t[r], t, o); if ("throw" !== c.type) { var u = c.arg, h = u.value; return h && "object" == _typeof2(h) && n.call(h, "__await") ? e.resolve(h.__await).then(function (t) { invoke("next", t, i, a); }, function (t) { invoke("throw", t, i, a); }) : e.resolve(h).then(function (t) { u.value = t, i(u); }, function (t) { return invoke("throw", t, i, a); }); } a(c.arg); } var r; o(this, "_invoke", { value: function value(t, n) { function callInvokeWithMethodAndArg() { return new e(function (e, r) { invoke(t, n, e, r); }); } return r = r ? r.then(callInvokeWithMethodAndArg, callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg(); } }); } function makeInvokeMethod(e, r, n) { var o = h; return function (i, a) { if (o === f) throw Error("Generator is already running"); if (o === s) { if ("throw" === i) throw a; return { value: t, done: !0 }; } for (n.method = i, n.arg = a;;) { var c = n.delegate; if (c) { var u = maybeInvokeDelegate(c, n); if (u) { if (u === y) continue; return u; } } if ("next" === n.method) n.sent = n._sent = n.arg;else if ("throw" === n.method) { if (o === h) throw o = s, n.arg; n.dispatchException(n.arg); } else "return" === n.method && n.abrupt("return", n.arg); o = f; var p = tryCatch(e, r, n); if ("normal" === p.type) { if (o = n.done ? s : l, p.arg === y) continue; return { value: p.arg, done: n.done }; } "throw" === p.type && (o = s, n.method = "throw", n.arg = p.arg); } }; } function maybeInvokeDelegate(e, r) { var n = r.method, o = e.iterator[n]; if (o === t) return r.delegate = null, "throw" === n && e.iterator["return"] && (r.method = "return", r.arg = t, maybeInvokeDelegate(e, r), "throw" === r.method) || "return" !== n && (r.method = "throw", r.arg = new TypeError("The iterator does not provide a '" + n + "' method")), y; var i = tryCatch(o, e.iterator, r.arg); if ("throw" === i.type) return r.method = "throw", r.arg = i.arg, r.delegate = null, y; var a = i.arg; return a ? a.done ? (r[e.resultName] = a.value, r.next = e.nextLoc, "return" !== r.method && (r.method = "next", r.arg = t), r.delegate = null, y) : a : (r.method = "throw", r.arg = new TypeError("iterator result is not an object"), r.delegate = null, y); } function pushTryEntry(t) { var e = { tryLoc: t[0] }; 1 in t && (e.catchLoc = t[1]), 2 in t && (e.finallyLoc = t[2], e.afterLoc = t[3]), this.tryEntries.push(e); } function resetTryEntry(t) { var e = t.completion || {}; e.type = "normal", delete e.arg, t.completion = e; } function Context(t) { this.tryEntries = [{ tryLoc: "root" }], t.forEach(pushTryEntry, this), this.reset(!0); } function values(e) { if (e || "" === e) { var r = e[a]; if (r) return r.call(e); if ("function" == typeof e.next) return e; if (!isNaN(e.length)) { var o = -1, i = function next() { for (; ++o < e.length;) if (n.call(e, o)) return next.value = e[o], next.done = !1, next; return next.value = t, next.done = !0, next; }; return i.next = i; } } throw new TypeError(_typeof2(e) + " is not iterable"); } return GeneratorFunction.prototype = GeneratorFunctionPrototype, o(g, "constructor", { value: GeneratorFunctionPrototype, configurable: !0 }), o(GeneratorFunctionPrototype, "constructor", { value: GeneratorFunction, configurable: !0 }), GeneratorFunction.displayName = define(GeneratorFunctionPrototype, u, "GeneratorFunction"), e.isGeneratorFunction = function (t) { var e = "function" == typeof t && t.constructor; return !!e && (e === GeneratorFunction || "GeneratorFunction" === (e.displayName || e.name)); }, e.mark = function (t) { return Object.setPrototypeOf ? Object.setPrototypeOf(t, GeneratorFunctionPrototype) : (t.__proto__ = GeneratorFunctionPrototype, define(t, u, "GeneratorFunction")), t.prototype = Object.create(g), t; }, e.awrap = function (t) { return { __await: t }; }, defineIteratorMethods(AsyncIterator.prototype), define(AsyncIterator.prototype, c, function () { return this; }), e.AsyncIterator = AsyncIterator, e.async = function (t, r, n, o, i) { void 0 === i && (i = Promise); var a = new AsyncIterator(wrap(t, r, n, o), i); return e.isGeneratorFunction(r) ? a : a.next().then(function (t) { return t.done ? t.value : a.next(); }); }, defineIteratorMethods(g), define(g, u, "Generator"), define(g, a, function () { return this; }), define(g, "toString", function () { return "[object Generator]"; }), e.keys = function (t) { var e = Object(t), r = []; for (var n in e) r.push(n); return r.reverse(), function next() { for (; r.length;) { var t = r.pop(); if (t in e) return next.value = t, next.done = !1, next; } return next.done = !0, next; }; }, e.values = values, Context.prototype = { constructor: Context, reset: function reset(e) { if (this.prev = 0, this.next = 0, this.sent = this._sent = t, this.done = !1, this.delegate = null, this.method = "next", this.arg = t, this.tryEntries.forEach(resetTryEntry), !e) for (var r in this) "t" === r.charAt(0) && n.call(this, r) && !isNaN(+r.slice(1)) && (this[r] = t); }, stop: function stop() { this.done = !0; var t = this.tryEntries[0].completion; if ("throw" === t.type) throw t.arg; return this.rval; }, dispatchException: function dispatchException(e) { if (this.done) throw e; var r = this; function handle(n, o) { return a.type = "throw", a.arg = e, r.next = n, o && (r.method = "next", r.arg = t), !!o; } for (var o = this.tryEntries.length - 1; o >= 0; --o) { var i = this.tryEntries[o], a = i.completion; if ("root" === i.tryLoc) return handle("end"); if (i.tryLoc <= this.prev) { var c = n.call(i, "catchLoc"), u = n.call(i, "finallyLoc"); if (c && u) { if (this.prev < i.catchLoc) return handle(i.catchLoc, !0); if (this.prev < i.finallyLoc) return handle(i.finallyLoc); } else if (c) { if (this.prev < i.catchLoc) return handle(i.catchLoc, !0); } else { if (!u) throw Error("try statement without catch or finally"); if (this.prev < i.finallyLoc) return handle(i.finallyLoc); } } } }, abrupt: function abrupt(t, e) { for (var r = this.tryEntries.length - 1; r >= 0; --r) { var o = this.tryEntries[r]; if (o.tryLoc <= this.prev && n.call(o, "finallyLoc") && this.prev < o.finallyLoc) { var i = o; break; } } i && ("break" === t || "continue" === t) && i.tryLoc <= e && e <= i.finallyLoc && (i = null); var a = i ? i.completion : {}; return a.type = t, a.arg = e, i ? (this.method = "next", this.next = i.finallyLoc, y) : this.complete(a); }, complete: function complete(t, e) { if ("throw" === t.type) throw t.arg; return "break" === t.type || "continue" === t.type ? this.next = t.arg : "return" === t.type ? (this.rval = this.arg = t.arg, this.method = "return", this.next = "end") : "normal" === t.type && e && (this.next = e), y; }, finish: function finish(t) { for (var e = this.tryEntries.length - 1; e >= 0; --e) { var r = this.tryEntries[e]; if (r.finallyLoc === t) return this.complete(r.completion, r.afterLoc), resetTryEntry(r), y; } }, "catch": function _catch(t) { for (var e = this.tryEntries.length - 1; e >= 0; --e) { var r = this.tryEntries[e]; if (r.tryLoc === t) { var n = r.completion; if ("throw" === n.type) { var o = n.arg; resetTryEntry(r); } return o; } } throw Error("illegal catch attempt"); }, delegateYield: function delegateYield(e, r, n) { return this.delegate = { iterator: values(e), resultName: r, nextLoc: n }, "next" === this.method && (this.arg = t), y; } }, e; }
function asyncGeneratorStep(n, t, e, r, o, a, c) { try { var i = n[a](c), u = i.value; } catch (n) { return void e(n); } i.done ? t(u) : Promise.resolve(u).then(r, o); }
function _asyncToGenerator(n) { return function () { var t = this, e = arguments; return new Promise(function (r, o) { var a = n.apply(t, e); function _next(n) { asyncGeneratorStep(a, r, o, _next, _throw, "next", n); } function _throw(n) { asyncGeneratorStep(a, r, o, _next, _throw, "throw", n); } _next(void 0); }); }; }
function _toConsumableArray2(r) { return _arrayWithoutHoles2(r) || _iterableToArray2(r) || _unsupportedIterableToArray2(r) || _nonIterableSpread2(); }
function _nonIterableSpread2() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray2(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray2(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray2(r, a) : void 0; } }
function _iterableToArray2(r) { if ("undefined" != typeof Symbol && null != r[Symbol.iterator] || null != r["@@iterator"]) return Array.from(r); }
function _arrayWithoutHoles2(r) { if (Array.isArray(r)) return _arrayLikeToArray2(r); }
function _arrayLikeToArray2(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
/******/(function () {
  // webpackBootstrap
  /******/
  "use strict";

  /******/
  var __webpack_modules__ = {
    /***/"./js/modules/AlertBar.js": (
    /*!********************************!*\
      !*** ./js/modules/AlertBar.js ***!
      \********************************/
    /***/
    function _js_modules_AlertBarJs(__unused_webpack_module, __webpack_exports__, __webpack_require__) {
      __webpack_require__.r(__webpack_exports__);
      /* harmony export */
      __webpack_require__.d(__webpack_exports__, {
        /* harmony export */"default": function _default() {
          return __WEBPACK_DEFAULT_EXPORT__;
        }
        /* harmony export */
      });
      /* harmony default export */
      var __WEBPACK_DEFAULT_EXPORT__ = function __WEBPACK_DEFAULT_EXPORT__() {
        var alertBar = document.querySelector('.common-alert-bar');
        if (alertBar && alertBar.querySelectorAll('.message').length > 0) {
          setTimeout(function () {
            alertBar.classList.add('active');
            setTimeout(function () {
              alertBar.classList.remove('active');
              setTimeout(function () {
                alertBar.innerHTML = '';
              }, 300);
            }, 8000);
          }, 300);
        }
      };

      /***/
    }),
    /***/"./js/modules/BlockEditor.js": (
    /*!***********************************!*\
      !*** ./js/modules/BlockEditor.js ***!
      \***********************************/
    /***/
    function _js_modules_BlockEditorJs(__unused_webpack_module, __webpack_exports__, __webpack_require__) {
      __webpack_require__.r(__webpack_exports__);
      /* harmony export */
      __webpack_require__.d(__webpack_exports__, {
        /* harmony export */"default": function _default() {
          return __WEBPACK_DEFAULT_EXPORT__;
        }
        /* harmony export */
      });
      /* harmony import */
      var _RepeaterField__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./RepeaterField */"./js/modules/RepeaterField.js");
      /* harmony import */
      var _vendor_ozz_wyg__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../vendor/ozz-wyg */"./js/vendor/ozz-wyg.js");
      /* harmony import */
      var _vendor_Sortable__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../vendor/Sortable */"./js/vendor/Sortable.js");
      /* harmony import */
      var _utils_State__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../utils/State */"./js/utils/State.js");
      /* harmony import */
      var _FormHandler__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./FormHandler */"./js/modules/FormHandler.js");
      /* harmony import */
      var _MediaManagerPopup__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./MediaManagerPopup */"./js/modules/MediaManagerPopup.js");
      /* harmony import */
      var _LinkField__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./LinkField */"./js/modules/LinkField.js");

      /* harmony default export */
      var __WEBPACK_DEFAULT_EXPORT__ = function __WEBPACK_DEFAULT_EXPORT__() {
        if (document.querySelectorAll('.ozz-block-editor').length === 0) return;
        var repeaterField = new _RepeaterField__WEBPACK_IMPORTED_MODULE_0__["default"]();
        var toggleBlockEditorResize = function toggleBlockEditorResize(blockEditorWrapper) {
          if ((0, _utils_State__WEBPACK_IMPORTED_MODULE_3__.GetState)('nav_collapsed')) {
            blockEditorWrapper.classList.add('collapsed');
            (0, _utils_State__WEBPACK_IMPORTED_MODULE_3__.SetState)('block_editor_collapsed', true);
          } else {
            blockEditorWrapper.classList.remove('collapsed');
            (0, _utils_State__WEBPACK_IMPORTED_MODULE_3__.SetState)('block_editor_collapsed', false);
          }
        };
        var toggleBlockEditorExpand = function toggleBlockEditorExpand(blockEditorWrapper) {
          if (blockEditorWrapper.classList.contains('expanded')) {
            blockEditorWrapper.classList.remove('expanded');
            (0, _utils_State__WEBPACK_IMPORTED_MODULE_3__.SetState)('block_editor_expanded', false);
          } else {
            blockEditorWrapper.classList.add('expanded');
            (0, _utils_State__WEBPACK_IMPORTED_MODULE_3__.SetState)('block_editor_expanded', true);
          }
        };

        // Toggle Block Accordion
        var ozzToggleBlock = function ozzToggleBlock(block) {
          var state = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;
          block.setAttribute('data-expand', state);
          block.querySelector(':scope > .ozz-block-accordion-bar').classList.toggle('active', state);
          block.querySelector(':scope > .ozz-accordion-body').classList.toggle('active', state);
        };

        // Expand block if error field inside
        var ozzExpandBlockIfError = function ozzExpandBlockIfError() {
          var usedBlocks = document.querySelectorAll('.ozz-block-editor .ozz-used-block');
          usedBlocks.forEach(function (block) {
            var errors = block.querySelectorAll('.field-error', 'input.error', 'textarea.error', 'select.error');
            if (errors.length > 0) {
              ozzToggleBlock(block);
            }
          });
        };
        var ozzBlockEditor = document.querySelectorAll('.ozz-block-editor');
        ozzBlockEditor.forEach(function (blockEditor) {
          // Add wrapper class
          var blockEditorWrapper = blockEditor.closest('.ozz-fm__field');
          var blockEditorStockHead = blockEditor.querySelector('.ozz-block-editor__block-picker-head');
          blockEditorWrapper.classList.add('ozz-block-editor-wrapper', 'lay2');
          addCommonEvents();
          var blocksArr = JSON.parse(blockEditor.getAttribute('data-blocks')),
            blockPicker = blockEditor.querySelector('.ozz-block-editor__block-picker-content'),
            blockFormLoader = blockEditor.querySelector('.ozz-block-editor__form-loader');
          var blockListDOM = "";
          var blocksObj = {};
          blocksArr.forEach(function (block) {
            blocksObj[block.name] = block;
            blockListDOM += "<li class=\"pick-block ".concat(block.name, "\" data-blockName=\"").concat(block.name, "\">").concat(block.label, "</li>");
          });

          // Make the Block store DOM
          blockPicker.innerHTML = blockListDOM;

          // Make editor sortable and draggable
          new _vendor_Sortable__WEBPACK_IMPORTED_MODULE_2__["default"](blockPicker, {
            group: {
              name: 'ozz-block-editor',
              pull: 'clone',
              put: false
            },
            animation: 150,
            sort: false
          });
          new _vendor_Sortable__WEBPACK_IMPORTED_MODULE_2__["default"](blockFormLoader, {
            group: 'ozz-block-editor',
            animation: 150,
            handle: '.ozz-block-accordion-bar',
            onSort: function onSort() {
              indexFieldNames();
            },
            onAdd: function onAdd(evt) {
              var draggedItem = evt.item;
              var blockName = evt.clone.getAttribute('data-blockName'),
                thisBlockFormDOM = document.querySelector(".ozz-block-editor-hidden-form-dom #".concat(blockName)),
                initialDOM = "<div class=\"ozz-block-accordion-bar\">\n            <span class=\"ozz-handle\"></span>\n            <div>\n              <h4>".concat(blocksObj[blockName].label, "</h4>\n              <p class=\"light-text\">").concat(blocksObj[blockName].note ? blocksObj[blockName].note : '', "</p>\n            </div>\n            <div class=\"ozz-block-actions\">\n              <span class=\"ozz-block-duplicate-trigger\"></span>\n              <span class=\"ozz-block-delete-trigger\"></span>\n            </div>\n            <span class=\"ozz-accordion-arrow\"></span>\n          </div>");
              indexFieldNames();
              draggedItem.classList.add('ozz-used-block');
              draggedItem.innerHTML = "".concat(initialDOM, " <div class=\"ozz-accordion-body\">").concat(thisBlockFormDOM.innerHTML, "</div>");
              addCommonEvents(draggedItem);
              repeaterField.initRepeater(draggedItem, function () {
                (0, _MediaManagerPopup__WEBPACK_IMPORTED_MODULE_5__["default"])();
                (0, _LinkField__WEBPACK_IMPORTED_MODULE_6__["default"])();
              });
              (0, _MediaManagerPopup__WEBPACK_IMPORTED_MODULE_5__["default"])(draggedItem);
              (0, _LinkField__WEBPACK_IMPORTED_MODULE_6__["default"])();
              if (typeof _vendor_ozz_wyg__WEBPACK_IMPORTED_MODULE_1__["default"] === 'function') {
                new _vendor_ozz_wyg__WEBPACK_IMPORTED_MODULE_1__["default"]({
                  selector: '[data-ozz-wyg]'
                });
              }
            }
          });

          // On Submit with block editor
          blockEditor.closest('form.ozz-fm').addEventListener('submit', _FormHandler__WEBPACK_IMPORTED_MODULE_4__["default"]);

          // Add index to field names
          function indexFieldNames() {
            var usedBlocks = blockFormLoader.querySelectorAll('.ozz-used-block');
            usedBlocks.forEach(function (block, ind) {
              var thisBlockFields = block.querySelectorAll('input, textarea, button, progress, meter, select, datalist, [data-ozz-wyg]');
              thisBlockFields.forEach(function (field) {
                var newName;
                if (field.name) {
                  newName = "i-".concat(ind, "__").concat(field.name.replace(/^i-\d+__/, ''));
                  field.name = newName;
                } else if (field.getAttribute('data-field-name')) {
                  newName = "i-".concat(ind, "__").concat(field.getAttribute('data-field-name').replace(/^i-\d+__/, ''));
                  field.setAttribute('data-field-name', newName);
                }

                // Update Field ID
                field.id = newName;

                // Update Label for
                if (field.closest('.block-editor-field').querySelector('label')) {
                  field.closest('.block-editor-field').querySelector('label').setAttribute('for', newName);
                }

                // Update media selector field ID and data attr
                var mediaSelector = field.closest('.ozz-fm__media-selector');
                if (mediaSelector) {
                  var trigger = mediaSelector.querySelector('[data-field-name]');
                  trigger.setAttribute('data-field-name', newName);
                }
              });
            });
          }

          // Block editor state
          if ((0, _utils_State__WEBPACK_IMPORTED_MODULE_3__.GetState)('block_editor_expanded')) {
            blockEditorWrapper.classList.add('expanded');
          }
          if ((0, _utils_State__WEBPACK_IMPORTED_MODULE_3__.GetState)('nav_collapsed')) {
            blockEditorWrapper.classList.add('collapsed');
          }

          // Add common events to each block
          function addCommonEvents() {
            var block = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
            function blockEvents(block) {
              // Remove Block
              block.querySelector('.ozz-block-delete-trigger').addEventListener('click', function () {
                block.remove();
                indexFieldNames();
              });

              // Duplicate Block
              block.querySelector('.ozz-block-duplicate-trigger').addEventListener('click', function (e) {
                e.preventDefault();
                var blockClone = block.cloneNode(true);
                addCommonEvents(blockClone);
                blockFormLoader.appendChild(blockClone);
                indexFieldNames();
                repeaterField.initRepeater(blockClone, function () {
                  (0, _MediaManagerPopup__WEBPACK_IMPORTED_MODULE_5__["default"])(blockClone);
                  (0, _LinkField__WEBPACK_IMPORTED_MODULE_6__["default"])();
                });
                (0, _MediaManagerPopup__WEBPACK_IMPORTED_MODULE_5__["default"])(blockClone);
                (0, _LinkField__WEBPACK_IMPORTED_MODULE_6__["default"])();
              });

              // Block accordion event
              block.querySelector('.ozz-block-accordion-bar').addEventListener('click', function (e) {
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
            if (block) {
              blockEvents(block);
            } else {
              var _setStockLayout = function setStockLayout() {
                var layout = (0, _utils_State__WEBPACK_IMPORTED_MODULE_3__.GetState)('block_editor_stock_layout');
                layouts.forEach(function (lay, i) {
                  lay.classList.remove('active');
                  blockEditorWrapper.classList.remove("lay".concat(i + 1));
                });
                blockEditorStockHead.querySelector("span.button.".concat(layout)).classList.add('active');
                blockEditorWrapper.classList.add(layout);
                layouts.forEach(function (layout) {
                  layout.addEventListener('click', function (el) {
                    var layoutName = el.target.getAttribute('data-lay');
                    (0, _utils_State__WEBPACK_IMPORTED_MODULE_3__.SetState)('block_editor_stock_layout', layoutName);
                    _setStockLayout();
                  });
                });
              };
              blockEditor.querySelectorAll('li.pick-block').forEach(function (block) {
                blockEvents(block);
              });

              // Expand editor
              blockEditorWrapper.querySelector('.ozz-block-editor-expand-button').addEventListener('click', function () {
                toggleBlockEditorResize(blockEditorWrapper);
                toggleBlockEditorExpand(blockEditorWrapper);
              });

              // Set Block stock layout
              var layouts = blockEditorStockHead.querySelectorAll('.lay');
              _setStockLayout();
            }
          }
        });

        // Expand Block if there any field error inside the block
        ozzExpandBlockIfError();
      };

      /***/
    }),
    /***/"./js/modules/ChangeTheme.js": (
    /*!***********************************!*\
      !*** ./js/modules/ChangeTheme.js ***!
      \***********************************/
    /***/
    function _js_modules_ChangeThemeJs(__unused_webpack_module, __webpack_exports__, __webpack_require__) {
      __webpack_require__.r(__webpack_exports__);
      /* harmony export */
      __webpack_require__.d(__webpack_exports__, {
        /* harmony export */"default": function _default() {
          return __WEBPACK_DEFAULT_EXPORT__;
        }
        /* harmony export */
      });
      /* harmony import */
      var _utils_State__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../utils/State */"./js/utils/State.js");

      /* harmony default export */
      var __WEBPACK_DEFAULT_EXPORT__ = function __WEBPACK_DEFAULT_EXPORT__() {
        var appBody = document.querySelector('body.ozz-cms'),
          changeTrigger = document.getElementById('ozz-color-theme-switcher');
        changeTrigger.addEventListener('change', function (e) {
          (0, _utils_State__WEBPACK_IMPORTED_MODULE_0__.SetState)('theme', e.target.checked ? 'dark' : 'light');
          appBody.setAttribute('data-theme', (0, _utils_State__WEBPACK_IMPORTED_MODULE_0__.GetState)('theme'));
        });
        appBody.setAttribute('data-theme', (0, _utils_State__WEBPACK_IMPORTED_MODULE_0__.GetState)('theme'));
        if ((0, _utils_State__WEBPACK_IMPORTED_MODULE_0__.GetState)('theme') == 'dark') {
          changeTrigger.checked = true;
        }
      };

      /***/
    }),
    /***/"./js/modules/EmbedMedia.js": (
    /*!**********************************!*\
      !*** ./js/modules/EmbedMedia.js ***!
      \**********************************/
    /***/
    function _js_modules_EmbedMediaJs(__unused_webpack_module, __webpack_exports__, __webpack_require__) {
      __webpack_require__.r(__webpack_exports__);
      /* harmony export */
      __webpack_require__.d(__webpack_exports__, {
        /* harmony export */"default": function _default() {
          return __WEBPACK_DEFAULT_EXPORT__;
        }
        /* harmony export */
      });
      /* harmony default export */
      var __WEBPACK_DEFAULT_EXPORT__ = function __WEBPACK_DEFAULT_EXPORT__(fileInfo) {
        var fileInfoThumbnail = '';
        if (fileInfo.format == 'image') {
          fileInfoThumbnail = "<img src=\"".concat(fileInfo.absolute_url, "\">");
        } else if (fileInfo.format == 'svg') {
          fileInfoThumbnail = "<object type=\"image/svg+xml\" data=\"".concat(fileInfo.absolute_url, "\">\n      <img src=\"").concat(fileInfo.absolute_url, "\" />\n    </object>");
        } else if (fileInfo.format == 'video') {
          fileInfoThumbnail = "<video width=\"100%\" controls>\n      <source src=\"".concat(fileInfo.absolute_url, "\" type=\"video/mp4\">\n      <source src=\"").concat(fileInfo.absolute_url, "\" type=\"video/ogg\">\n    </video>");
        } else if (fileInfo.format == 'audio') {
          fileInfoThumbnail = "<audio controls>\n      <source src=\"".concat(fileInfo.absolute_url, "\" type=\"audio/ogg\">\n      <source src=\"").concat(fileInfo.absolute_url, "\" type=\"audio/mpeg\">\n    </audio>");
        } else if (fileInfo.format == 'vimeo') {
          fileInfoThumbnail = "<iframe src=\"".concat(fileInfo.absolute_url, "\" width=\"100%\" frameborder=\"0\" picture-in-picture allowfullscreen></iframe>");
        } else if (fileInfo.format == 'youtube') {
          fileInfoThumbnail = "<iframe width=\"100%\" src=\"".concat(fileInfo.absolute_url, "\"></iframe>");
        } else if (fileInfo.format == 'pdf') {
          fileInfoThumbnail = "<object data=\"".concat(fileInfo.absolute_url, "\" type=\"application/pdf\" width=\"100%\" height=\"400px\">\n      <p>Unable to display PDF file. <a href=\"").concat(fileInfo.absolute_url, "\">Download</a> instead.</p>\n    </object>");
        } else if (['word', 'powerpoint', 'pptx', 'excel', 'odt', 'ods'].includes(fileInfo.format)) {
          fileInfoThumbnail = "<object data=\"".concat(fileInfo.absolute_url, "\"\n      type=\"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet\" width=\"100%\" height=\"400px\">\n      <p>Unable to display the file.<br>Click <a href=\"").concat(fileInfo.absolute_url, "\" target=\"_blank\">here</a> to open it</p>\n    </object>");
        } else if (['text', 'swf', 'json'].includes(fileInfo.format)) {
          fileInfoThumbnail = "<iframe width=\"100%\" height=\"400px\" src=\"".concat(fileInfo.absolute_url, "\" frameborder=\"0\"></iframe>");
        } else {
          fileInfoThumbnail = "<span class=\"icon ".concat(fileInfo.format, "\"></span>");
        }
        return fileInfoThumbnail;
      };

      /***/
    }),
    /***/"./js/modules/FormHandler.js": (
    /*!***********************************!*\
      !*** ./js/modules/FormHandler.js ***!
      \***********************************/
    /***/
    function _js_modules_FormHandlerJs(__unused_webpack_module, __webpack_exports__, __webpack_require__) {
      __webpack_require__.r(__webpack_exports__);
      /* harmony export */
      __webpack_require__.d(__webpack_exports__, {
        /* harmony export */"default": function _default() {
          return __WEBPACK_DEFAULT_EXPORT__;
        }
        /* harmony export */
      });
      /* harmony default export */
      var __WEBPACK_DEFAULT_EXPORT__ = function __WEBPACK_DEFAULT_EXPORT__(e) {
        e.preventDefault();
        var thisForm = e.target;

        // Store OzzWyg data in a hidden field
        var ozzWygEditors = thisForm.querySelectorAll('[data-ozz-wyg]');
        ozzWygEditors.forEach(function (wygEditor) {
          var name = wygEditor.getAttribute('data-field-name');
          var value = wygEditor.querySelector('[data-editor-area]').innerHTML;
          var existingHiddenField = thisForm.querySelector("input[name=\"".concat(name, "\"]"));
          if (!existingHiddenField) {
            var hdnField = document.createElement('input');
            hdnField.type = 'hidden';
            hdnField.name = name;
            hdnField.value = value;
            thisForm.appendChild(hdnField);
          } else {
            existingHiddenField.value = value;
          }
        });
        thisForm.submit();
      };

      /***/
    }),
    /***/"./js/modules/Forms.js": (
    /*!*****************************!*\
      !*** ./js/modules/Forms.js ***!
      \*****************************/
    /***/
    function _js_modules_FormsJs(__unused_webpack_module, __webpack_exports__, __webpack_require__) {
      __webpack_require__.r(__webpack_exports__);
      /* harmony export */
      __webpack_require__.d(__webpack_exports__, {
        /* harmony export */"default": function _default() {
          return __WEBPACK_DEFAULT_EXPORT__;
        }
        /* harmony export */
      });
      /* harmony default export */
      var __WEBPACK_DEFAULT_EXPORT__ = function __WEBPACK_DEFAULT_EXPORT__() {
        // Delete entry
        var formEntryDeleteTriggers = document.querySelector('form.delete-entry');
        if (!formEntryDeleteTriggers) return;
        formEntryDeleteTriggers.addEventListener("submit", function (event) {
          if (!confirm('The Entry will be deleted permanently. Are you sure?')) {
            event.preventDefault();
          }
        });
      };

      /***/
    }),
    /***/"./js/modules/GlobalSearch.js": (
    /*!************************************!*\
      !*** ./js/modules/GlobalSearch.js ***!
      \************************************/
    /***/
    function _js_modules_GlobalSearchJs(__unused_webpack_module, __webpack_exports__, __webpack_require__) {
      __webpack_require__.r(__webpack_exports__);
      /* harmony export */
      __webpack_require__.d(__webpack_exports__, {
        /* harmony export */"default": function _default() {
          return __WEBPACK_DEFAULT_EXPORT__;
        }
        /* harmony export */
      });
      /* harmony import */
      var _utils_Fetch__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../utils/Fetch */"./js/utils/Fetch.js");

      /* harmony default export */
      var __WEBPACK_DEFAULT_EXPORT__ = function __WEBPACK_DEFAULT_EXPORT__() {
        var DOM = document.querySelector('.cms-global-search'),
          searchField = DOM.querySelector('input[name="cms-global-search-field"]'),
          resultWrapper = DOM.querySelector('.cms-global-search__result-wrapper'),
          resultDOM = DOM.querySelector('.cms-global-search__result');
        searchField.addEventListener('focus', function () {
          resultWrapper.classList.add('active');
        });
        searchField.addEventListener('blur', function () {
          if (searchField.value == '') {
            resultWrapper.classList.remove('active');
          }
        });
        var processing = false;
        searchField.addEventListener('input', function (e) {
          e.preventDefault();
          resultWrapper.classList.add('active');
          if (searchField.value !== '') {
            if (processing === false) {
              var getResult = (0, _utils_Fetch__WEBPACK_IMPORTED_MODULE_0__.send)(DATA.CMS_URL + 'global-search', 'POST', JSON.stringify({
                keyword: searchField.value
              }));
              getResult.then(function (response) {
                var result = '';
                response.forEach(function (item) {
                  result += "<li>\n              <a href=\"".concat(item.url, "\">\n                <span>").concat(item.title, "</span>\n                <span class=\"button micro light\">").concat(item.type, "</span>\n              </a>\n            </li>");
                });
                resultDOM.style.opacity = 1;
                resultDOM.innerHTML = result;
              });
              processing = true;
              setTimeout(function () {
                processing = false;
              }, 500);
            }
          } else {
            processing = false;
            resultDOM.innerHTML = '';
            resultDOM.style.opacity = 0;
          }
        });
      };

      /***/
    }),
    /***/"./js/modules/InitOzzWyg.js": (
    /*!**********************************!*\
      !*** ./js/modules/InitOzzWyg.js ***!
      \**********************************/
    /***/
    function _js_modules_InitOzzWygJs(__unused_webpack_module, __webpack_exports__, __webpack_require__) {
      __webpack_require__.r(__webpack_exports__);
      /* harmony export */
      __webpack_require__.d(__webpack_exports__, {
        /* harmony export */"default": function _default() {
          return __WEBPACK_DEFAULT_EXPORT__;
        }
        /* harmony export */
      });
      /* harmony import */
      var _vendor_ozz_wyg__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../vendor/ozz-wyg */"./js/vendor/ozz-wyg.js");

      /* harmony default export */
      var __WEBPACK_DEFAULT_EXPORT__ = function __WEBPACK_DEFAULT_EXPORT__() {
        if (typeof _vendor_ozz_wyg__WEBPACK_IMPORTED_MODULE_0__["default"] === 'function') {
          new _vendor_ozz_wyg__WEBPACK_IMPORTED_MODULE_0__["default"]({
            selector: '[data-ozz-wyg]'
          });
        }
      };

      /***/
    }),
    /***/"./js/modules/LinkField.js": (
    /*!*********************************!*\
      !*** ./js/modules/LinkField.js ***!
      \*********************************/
    /***/
    function _js_modules_LinkFieldJs(__unused_webpack_module, __webpack_exports__, __webpack_require__) {
      __webpack_require__.r(__webpack_exports__);
      /* harmony export */
      __webpack_require__.d(__webpack_exports__, {
        /* harmony export */"default": function _default() {
          return __WEBPACK_DEFAULT_EXPORT__;
        }
        /* harmony export */
      });
      /* harmony default export */
      var __WEBPACK_DEFAULT_EXPORT__ = function __WEBPACK_DEFAULT_EXPORT__() {
        var linkFields = document.querySelectorAll('[data-ozz-link-field]');
        if (!linkFields) return;
        var setLinkFieldTypes = function setLinkFieldTypes(field) {
          var linkType = field.querySelector('[data-link-type]'),
            linkPost = field.querySelector('[data-link-post]'),
            linkTitle = field.querySelector('[data-link-title]'),
            linkUrl = field.querySelector('[data-link-url]'),
            linkTarget = field.querySelector('[data-link-target]'),
            linkRel = field.querySelector('[data-link-rel]'),
            linkAria = field.querySelector('[data-link-aria]'),
            linkClass = field.querySelector('[data-link-class]'),
            actualJSONField = field.querySelector('[data-link-actual-value-json]');
          var realValues = {
            'type': linkType.value,
            'post_id': linkPost.value,
            'title': linkTitle.value,
            'url': linkUrl.value,
            'target': linkTarget.value,
            'rel': linkRel.value,
            'aria_label': linkAria.value,
            'class': linkClass.value
          };
          if (linkType.value === 'internal') {
            linkPost.closest('.ozz-fm__field').classList.remove('hide');
            linkPost.closest('.ozz-fm__field').classList.add('show');
            linkTitle.closest('.ozz-fm__field').classList.remove('show');
            linkTitle.closest('.ozz-fm__field').classList.add('hide');
            linkUrl.closest('.ozz-fm__field').classList.remove('show');
            linkUrl.closest('.ozz-fm__field').classList.add('hide');
          } else {
            linkPost.closest('.ozz-fm__field').classList.remove('show');
            linkPost.closest('.ozz-fm__field').classList.add('hide');
            linkTitle.closest('.ozz-fm__field').classList.remove('hide');
            linkTitle.closest('.ozz-fm__field').classList.add('show');
            linkUrl.closest('.ozz-fm__field').classList.remove('hide');
            linkUrl.closest('.ozz-fm__field').classList.add('show');
          }
          actualJSONField.value = JSON.stringify(realValues);
        };
        linkFields.forEach(function (field) {
          field.addEventListener('load', setLinkFieldTypes(field));
          field.addEventListener('input', function (e) {
            e.preventDefault();
            setLinkFieldTypes(field);
          });
        });
      };

      /***/
    }),
    /***/"./js/modules/MediaManager.js": (
    /*!************************************!*\
      !*** ./js/modules/MediaManager.js ***!
      \************************************/
    /***/
    function _js_modules_MediaManagerJs(__unused_webpack_module, __webpack_exports__, __webpack_require__) {
      __webpack_require__.r(__webpack_exports__);
      /* harmony export */
      __webpack_require__.d(__webpack_exports__, {
        /* harmony export */"default": function _default() {
          return __WEBPACK_DEFAULT_EXPORT__;
        }
        /* harmony export */
      });
      /* harmony import */
      var _EmbedMedia__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./EmbedMedia */"./js/modules/EmbedMedia.js");
      /* harmony import */
      var _utils_Popup__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/Popup */"./js/utils/Popup.js");

      /* harmony default export */
      var __WEBPACK_DEFAULT_EXPORT__ = function __WEBPACK_DEFAULT_EXPORT__() {
        if (document.querySelectorAll('.ozz-media-manager').length === 0) return;
        var MediaManager = document.querySelector('.ozz-media-manager'),
          mediaFileItems = MediaManager.querySelectorAll('.ozz-media-manager__item.media-file'),
          mediaViewer = MediaManager.querySelector('.ozz-media-manager__viewer'),
          actionButtons = MediaManager.querySelectorAll('.ozz-media-manager .popup-trigger');
        mediaFileItems.forEach(function (mediaFile) {
          mediaFile.addEventListener('click', function (e) {
            e.preventDefault();
            mediaFileItems.forEach(function (el) {
              el.classList.remove('active');
            });
            mediaFile.classList.add('active');
            var fileInfo = JSON.parse(mediaFile.getAttribute('data-fileInfo'));
            // Media item Embed DOM
            var fileInfoThumbnail = (0, _EmbedMedia__WEBPACK_IMPORTED_MODULE_0__["default"])(fileInfo);
            // File Info DOM
            var fileInfoDOM = "<div class=\"ozz-media-manager__info\">\n      <div class=\"ozz-media-manager__info-thumbnail\">".concat(fileInfoThumbnail, "</div>\n        <ul class=\"ozz-media-manager__info-info\">\n          <li><strong>Name:</strong> ").concat(fileInfo.name, "</li>\n          <li><strong>Size:</strong> ").concat(fileInfo.size, "</li>\n          <li><strong>URL:</strong> <a href=\"").concat(fileInfo.absolute_url, "\" class=\"link\" target=\"_blank\">").concat(fileInfo.absolute_url, "</a></li>\n          <li><strong>Created:</strong> ").concat(fileInfo.created, "</li>\n          <li><strong>Modified:</strong> ").concat(fileInfo.modified, "</li>\n          <li><strong>Access:</strong> ").concat(fileInfo.access, "</li>\n          <li>\n            <form action=\"").concat(DATA.CMS_URL, "media/action?q=delete_file\" method=\"post\" class=\"media-delete-form\">\n              <input type=\"hidden\" value=\"").concat(fileInfo.dir + fileInfo.name, "\" name=\"ozz_media_file_name_delete\">\n              <input type=\"submit\" value=\"Delete File\" class=\"button mini danger\">\n            </form>\n          </li>\n        </ul>\n      </div>");
            mediaViewer.innerHTML = fileInfoDOM;
            MediaManager.classList.add('viewer-active');
            mediaViewer.classList.add('active');
            mediaViewer.querySelector('form.media-delete-form').addEventListener('submit', function (e) {
              if (!confirm('The File will be deleted permanently. Are you sure?')) {
                e.preventDefault();
              }
            });
          });
        });
        // Media Actions
        actionButtons.forEach(function (action) {
          var actionFormDOM = action.querySelector('.hidden-action-form');
          if (actionFormDOM) {
            action.addEventListener('click', function () {
              (0, _utils_Popup__WEBPACK_IMPORTED_MODULE_1__.openPopup)(actionFormDOM.outerHTML);
            });
          }
        });
      };

      /***/
    }),
    /***/"./js/modules/MediaManagerPopup.js": (
    /*!*****************************************!*\
      !*** ./js/modules/MediaManagerPopup.js ***!
      \*****************************************/
    /***/
    function _js_modules_MediaManagerPopupJs(__unused_webpack_module, __webpack_exports__, __webpack_require__) {
      __webpack_require__.r(__webpack_exports__);
      /* harmony export */
      __webpack_require__.d(__webpack_exports__, {
        /* harmony export */"default": function _default() {
          return __WEBPACK_DEFAULT_EXPORT__;
        }
        /* harmony export */
      });
      /* harmony import */
      var _utils_Popup__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../utils/Popup */"./js/utils/Popup.js");

      /* harmony default export */
      var __WEBPACK_DEFAULT_EXPORT__ = function __WEBPACK_DEFAULT_EXPORT__() {
        var DOM = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
        if (document.querySelectorAll('.ozz-fm__media-selector').length == 0) {
          return;
        }
        var initialTrigger = false; // Popup opened by this

        /**
         * Build Media manager and popup
         * @param {object} media Media elements
         * @param {object} trigger Selector Trigger clicked event
         * @param {string} breadCrumb Directory URL
         */
        var BuildMediaManager = function BuildMediaManager(media) {
          var _initialTrigger$targe;
          var trigger = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
          var breadCrumb = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
          var itemsDOM = '',
            currentValues = false;
          if (trigger) {
            initialTrigger = trigger;
          }
          var actualField = (_initialTrigger$targe = initialTrigger.target.closest('.ozz-fm__media-selector')) === null || _initialTrigger$targe === void 0 ? void 0 : _initialTrigger$targe.querySelector('input[type=hidden]');
          var actualFieldValue = actualField === null || actualField === void 0 ? void 0 : actualField.value;
          currentValues = actualFieldValue !== '' ? JSON.parse(actualFieldValue) : '';

          // Setup breadcrumb
          var breadCrumbDOM = document.createElement('div');
          breadCrumbDOM.classList.add('ozz-media-popup__breadcrumb');
          breadCrumbDOM.innerHTML = "<span class=\"ozz-media-popup__breadcrumb-item home\" data-dir=\"/\"></span>";
          var buildBreadcrumb = function buildBreadcrumb(url) {
            var parts = url.split('/');
            var breadcrumb = '';
            var cumulativePath = '';
            parts.forEach(function (part, index) {
              cumulativePath += part + '/';
              if (index === parts.length - 1) {
                breadcrumb += "<span class=\"ozz-media-popup__breadcrumb-current\">".concat(part, "</span>");
              } else {
                breadcrumb += "<span class=\"ozz-media-popup__breadcrumb-item\" data-dir=\"".concat(part, "\">").concat(part, "</span> / ");
              }
            });
            return "<span class=\"ozz-media-popup__breadcrumb-item home\" data-dir=\"/\"></span> ".concat(breadcrumb);
          };
          if (breadCrumb) {
            breadCrumbDOM.innerHTML = buildBreadcrumb(breadCrumb);
          }
          var _loop = function _loop() {
            if (media.items.data.hasOwnProperty(key)) {
              var _checked, _active;
              var val = media.items.data[key];
              var toolTip = val.type !== 'folder' ? "title=\"Size: ".concat(val.size, "&#013;Created: ").concat(val.created, "&#013;Modified: ").concat(val.modified, "&#013;\"") : '';
              var mediaElement;
              if (val.format == 'image') {
                mediaElement = "<img src=\"".concat(val.absolute_url, "\" class=\"ozz-media-popup__thumbnail-image\" alt=\"").concat(val.name, "\">");
              } else if (val.format == 'svg') {
                mediaElement = "<object data=\"".concat(val.absolute_url, "\"></object>");
              } else {
                var _val$format;
                mediaElement = "<span title=\"".concat(val.name, "\" class=\"icon ").concat((_val$format = val.format) !== null && _val$format !== void 0 ? _val$format : val.type, "\"></span>");
              }

              // Set item value
              var value = encodeURIComponent(JSON.stringify({
                url: val.url,
                name: val.name
              }));
              var _ref13 = '',
                checked = _ref13.checked,
                active = _ref13.active;
              if (currentValues) {
                currentValues.forEach(function (item) {
                  if (item.url == val.url) {
                    checked = 'checked="true"';
                    active = 'active';
                  }
                });
              }
              var showCheckbox = "<input type=\"checkbox\" ".concat((_checked = checked) !== null && _checked !== void 0 ? _checked : '', " name=\"ozz-fm-media-selected-item[]\" value=\"").concat(value, "\" />").concat(mediaElement);
              var folderURL = val.type == 'folder' ? "data-dir=".concat(val.url) : '';
              itemsDOM += "\n        <div class=\"ozz-media-popup__thumbnail ".concat(val.type, " ").concat((_active = active) !== null && _active !== void 0 ? _active : '', "\" ").concat(toolTip, " ").concat(folderURL, ">\n          ").concat(val.type !== 'folder' ? showCheckbox : mediaElement, "\n          <div class=\"ozz-media-popup__thumbnail-name\">").concat(val.name, "</div>\n        </div>");
            }
          };
          for (var key in media.items.data) {
            _loop();
          }
          var wrapperDOM = "\n    <div class=\"ozz-media-popup\">\n      ".concat(breadCrumbDOM.outerHTML, "\n      <div class=\"ozz-media-popup__grid-wrap\"><div class=\"ozz-media-popup__grid\">").concat(itemsDOM, "</div></div>\n      <div class=\"ozz-media-popup__submit\"><span class=\"button small\">Select</span></div>\n    </div>");

          // Open Media Selector popup
          (0, _utils_Popup__WEBPACK_IMPORTED_MODULE_0__.openPopup)(wrapperDOM, function (DOM) {
            var thumbs = DOM.querySelectorAll('.ozz-media-popup__thumbnail');
            var submitBtn = DOM.querySelector('.ozz-media-popup__submit');
            var links = DOM.querySelectorAll('.ozz-media-popup__breadcrumb-item');

            // Select Items
            thumbs.forEach(function (tmb) {
              tmb.addEventListener('click', function (e) {
                if (tmb.classList.contains('folder')) {
                  var _tmb$getAttribute;
                  // open sub folder
                  loadMedia(e, (_tmb$getAttribute = tmb.getAttribute('data-dir')) !== null && _tmb$getAttribute !== void 0 ? _tmb$getAttribute : false);
                } else {
                  tmb.classList.toggle('active');
                  var selected = tmb.querySelector('input[name="ozz-fm-media-selected-item[]"]');
                  selected.checked = !selected.checked;

                  // Remove un selected item
                  if (!selected.checked) {
                    var urlToRemove = JSON.parse(decodeURIComponent(selected.value)).url;
                    var updatedValues = currentValues.filter(function (item) {
                      return item.url !== urlToRemove;
                    });
                    actualField.value = JSON.stringify(updatedValues);
                  }
                }
              });
            });

            // Insert Selected items into hidden field
            submitBtn.addEventListener('click', function () {
              var checked = DOM.querySelectorAll('input[name="ozz-fm-media-selected-item[]"]:checked');
              var values = [];
              checked.forEach(function (checkbox) {
                var val = JSON.parse(decodeURIComponent(checkbox.value));
                values.push(val);
              });
              var currentValues = actualField.value == '' ? [] : JSON.parse(actualField.value);
              var finalVals = [].concat(_toConsumableArray2(currentValues), values).filter(function (obj, index, self) {
                return index === self.findIndex(function (o) {
                  return o.url === obj.url;
                });
              });
              var finalValues = JSON.stringify(finalVals);
              actualField.value = finalValues;
              (0, _utils_Popup__WEBPACK_IMPORTED_MODULE_0__.closePopup)();

              // Update selected media
              listSelectedMedia(actualField);
            }, {
              once: true
            });

            // Breadcrumb trigger
            links.forEach(function (link) {
              link.addEventListener('click', function (e) {
                return loadMedia(e, e.target.getAttribute('data-dir'));
              });
            });
          });
        };

        /**
        * Update Selector with selected media items
        * @param {DOM} actualField 
        */
        var listSelectedMedia = function listSelectedMedia(actualField) {
          if ('' === actualField.value) {
            return;
          }
          var selectedItems = JSON.parse(actualField.value);
          var thisWrapper = actualField.parentNode.querySelector('.ozz-fm__media-embed-wrapper');
          var listingDOM = '';
          selectedItems.forEach(function (item) {
            listingDOM += "<div class=\"embed-wrapper-item\">\n        <span class=\"embed-wrapper-item__remove-btn\" data-imgurl=\"".concat(item.url, "\"></span>\n        <img src=\"/").concat(item.url, "\" alt=\"").concat(item.name, "\" title=\"").concat(item.name, "\">\n      </div>");
          });
          thisWrapper.innerHTML = listingDOM;

          // Bind remove trigger
          thisWrapper.addEventListener('click', function (e) {
            if (e.target.classList.contains('embed-wrapper-item__remove-btn')) {
              var url = e.target.getAttribute('data-imgurl');
              var updatedValues = JSON.parse(actualField.value).filter(function (item) {
                return item.url !== url;
              });
              actualField.value = JSON.stringify(updatedValues);
              e.target.closest('.embed-wrapper-item').remove();
            }
          });
        };

        // Initiate a trigger
        DOM = DOM !== false ? DOM : document;
        var selectors = DOM.querySelectorAll('.ozz-fm__media-selector .media-selector-trigger');
        var loadMedia = /*#__PURE__*/function () {
          var _ref14 = _asyncToGenerator(/*#__PURE__*/_regeneratorRuntime().mark(function _callee(e) {
            var dir,
              response,
              media,
              _response,
              _media,
              _args = arguments;
            return _regeneratorRuntime().wrap(function _callee$(_context) {
              while (1) switch (_context.prev = _context.next) {
                case 0:
                  dir = _args.length > 1 && _args[1] !== undefined ? _args[1] : false;
                  if (!dir) {
                    _context.next = 11;
                    break;
                  }
                  _context.next = 4;
                  return fetch("".concat(DATA.CMS_URL, "media/items?dir=").concat(dir));
                case 4:
                  response = _context.sent;
                  _context.next = 7;
                  return response.json();
                case 7:
                  media = _context.sent;
                  BuildMediaManager(media, false, dir);
                  _context.next = 18;
                  break;
                case 11:
                  _context.next = 13;
                  return fetch("".concat(DATA.CMS_URL, "media/items"));
                case 13:
                  _response = _context.sent;
                  _context.next = 16;
                  return _response.json();
                case 16:
                  _media = _context.sent;
                  BuildMediaManager(_media, e);
                case 18:
                case "end":
                  return _context.stop();
              }
            }, _callee);
          }));
          return function loadMedia(_x) {
            return _ref14.apply(this, arguments);
          };
        }();
        selectors.forEach(function (mediaSelector) {
          mediaSelector.addEventListener('click', loadMedia);

          // List down selected media items
          var actualField = mediaSelector.closest('.ozz-fm__media-selector').querySelector('input[type=hidden]');
          ;
          listSelectedMedia(actualField);
        });
      };

      /***/
    }),
    /***/"./js/modules/MultiSelector.js": (
    /*!*************************************!*\
      !*** ./js/modules/MultiSelector.js ***!
      \*************************************/
    /***/
    function _js_modules_MultiSelectorJs(__unused_webpack_module, __webpack_exports__, __webpack_require__) {
      __webpack_require__.r(__webpack_exports__);
      /* harmony export */
      __webpack_require__.d(__webpack_exports__, {
        /* harmony export */"default": function _default() {
          return __WEBPACK_DEFAULT_EXPORT__;
        }
        /* harmony export */
      });
      /* harmony default export */
      var __WEBPACK_DEFAULT_EXPORT__ = function __WEBPACK_DEFAULT_EXPORT__() {
        var selectors = document.querySelectorAll('.ozz-fm__multiselect');
        if (selectors.length === 0) return;
        selectors.forEach(function (selector) {
          var selectedItemsDiv = selector.querySelector('.ozz-fm__multiselect--selected');
          var options = selector.querySelectorAll('ul li');
          var realField = selector.querySelector('input[type="hidden"]');
          var dataOptions = Array.from(options, function (option) {
            return {
              label: option.textContent,
              value: option.dataset.value
            };
          });
          var dataAvailable = dataOptions.slice();

          // Get current values
          var dataSelected = [];
          var defaultValuesObj = JSON.parse(realField.value);
          if (_typeof2(defaultValuesObj.terms) === 'object' && defaultValuesObj.terms.length > 0) {
            defaultValuesObj['terms'].forEach(function (term) {
              var label = dataAvailable.find(function (obj) {
                return obj.value == term;
              });
              dataSelected.push(label);
            });
          }
          selector.setAttribute('data-name', realField.name);

          // Add search field
          var searchField = document.createElement('input');
          searchField.type = 'search';
          selector.insertBefore(searchField, selector.querySelector('ul'));

          // Add dropdown element
          var ddDOM = document.createElement('ul');
          ddDOM.classList.add('ozz-fm__multiselect-dropdown');
          selector.insertBefore(ddDOM, selector.querySelector('ul'));

          // Bind search field events
          searchField.addEventListener('focus', function () {
            openDropdown();
            var _closeEvent = function closeEvent(e) {
              if (selector.contains(e.target) === false) {
                closeDropdown();
                document.removeEventListener('click', _closeEvent);
              }
            };
            document.addEventListener('click', _closeEvent);
          });

          // Setup search input event
          searchField.addEventListener('input', function (e) {
            var li = document.querySelectorAll('.ozz-fm__multiselect-dropdown li');
            li.forEach(function (item) {
              var val = e.target.value.toLowerCase();
              var itemTxt = item.textContent.toLowerCase();
              if (itemTxt.includes(val)) {
                item.style.display = 'block';
              } else {
                item.style.display = 'none';
              }
            });
          });

          // Setup dropdown items
          var setupDDItems = function setupDDItems() {
            var DOM = document.createElement('ul');
            dataAvailable.forEach(function (option) {
              var selected = dataSelected.some(function (it) {
                return it.value === option.value;
              });
              var cls = selected ? 'selected' : 'not';
              var li = document.createElement('li');
              li.dataset.value = option.value;
              li.dataset.selected = selected;
              li.classList.add(cls);
              li.textContent = option.label;
              DOM.appendChild(li);
            });
            var dd = selector.querySelector('.ozz-fm__multiselect-dropdown');
            dd.innerHTML = DOM.innerHTML;
            dd.querySelectorAll('li').forEach(function (li) {
              li.addEventListener('click', function () {
                toggleTermSelectState(li, li.classList.contains('selected'));
                _modifySelectedItems();
              });
            });
            _modifySelectedItems();
          };

          // Set and modify selected items DOM
          var _modifySelectedItems = function modifySelectedItems() {
            var DOM = '';
            dataSelected.forEach(function (item) {
              DOM += "<span class=\"button light mini\" data-value=\"".concat(item.value, "\">").concat(item.label, "<span class=\"remove-btn\"></span></span>");
            });
            selectedItemsDiv.innerHTML = DOM;

            // Bind remove event
            selectedItemsDiv.querySelectorAll('span.remove-btn').forEach(function (removeBtn) {
              removeBtn.addEventListener('click', function (e) {
                e.preventDefault();
                var removed = removeBtn.closest('.button');
                var value = removed.getAttribute('data-value');
                dataSelected = dataSelected.filter(function (item) {
                  return item.value != value;
                });
                storeValues(dataSelected);
                removeBtn.closest('.button').remove();
                _modifySelectedItems();
              });
            });
          };

          // Toggle term select state
          var toggleTermSelectState = function toggleTermSelectState(li) {
            var state = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;
            var value = li.getAttribute('data-value');
            var label = li.textContent;
            if (state) {
              li.classList.remove('selected');
              li.setAttribute('selected', false);
              dataSelected = dataSelected.filter(function (item) {
                return item.value !== value;
              });
            } else {
              li.classList.add('selected');
              li.setAttribute('selected', true);
              dataSelected.push({
                label: label,
                value: value
              });
            }
            storeValues(dataSelected);
          };

          // Store values in hidden field
          var storeValues = function storeValues(values) {
            var _realField$getAttribu;
            var taxonomyID = (_realField$getAttribu = realField.getAttribute('data-taxonomy-id')) !== null && _realField$getAttribu !== void 0 ? _realField$getAttribu : null;
            var valueData = [];
            if (taxonomyID !== null) {
              valueData.push({
                'taxonomy': taxonomyID,
                'terms': values.map(function (it) {
                  return it.value;
                })
              });
            } else {
              valueData.push(values.map(function (it) {
                return it.value;
              }));
            }
            realField.value = JSON.stringify(valueData);
          };

          // Open dropdown
          var openDropdown = function openDropdown() {
            setupDDItems();
            selector.querySelector('.ozz-fm__multiselect-dropdown').classList.add('active');
          };

          // Close multiselect dropdown
          var closeDropdown = function closeDropdown() {
            selector.querySelector('.ozz-fm__multiselect-dropdown').classList.remove('active');
          };

          // Set initial values as selected
          if (dataSelected.length > 0) {
            _modifySelectedItems();
          }
        });
      };

      /***/
    }),
    /***/"./js/modules/NavBar.js": (
    /*!******************************!*\
      !*** ./js/modules/NavBar.js ***!
      \******************************/
    /***/
    function _js_modules_NavBarJs(__unused_webpack_module, __webpack_exports__, __webpack_require__) {
      __webpack_require__.r(__webpack_exports__);
      /* harmony export */
      __webpack_require__.d(__webpack_exports__, {
        /* harmony export */"default": function _default() {
          return __WEBPACK_DEFAULT_EXPORT__;
        }
        /* harmony export */
      });
      /* harmony import */
      var _utils_State__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../utils/State */"./js/utils/State.js");

      /* harmony default export */
      var __WEBPACK_DEFAULT_EXPORT__ = function __WEBPACK_DEFAULT_EXPORT__() {
        var body = document.querySelector('body');
        var collapseNav = function collapseNav(navbar) {
          navbar.classList.add('collapsed');
          body.classList.add('nav-collapsed');
          (0, _utils_State__WEBPACK_IMPORTED_MODULE_0__.SetState)('nav_collapsed', true);
        };
        var revealNav = function revealNav(navbar) {
          navbar.classList.remove('collapsed');
          body.classList.remove('nav-collapsed');
          (0, _utils_State__WEBPACK_IMPORTED_MODULE_0__.SetState)('nav_collapsed', false);
        };
        var navbar = document.querySelector('.cms-nav'),
          navbarTrigger = navbar.querySelector('.cms-nav__nav-collapse-trigger'),
          navFirstUl = navbar.querySelector('ul'),
          currentLink = navFirstUl.getAttribute('data-active-link');
        if ((0, _utils_State__WEBPACK_IMPORTED_MODULE_0__.GetState)('nav_collapsed')) {
          collapseNav(navbar);
        }
        navbarTrigger.addEventListener('click', function () {
          navbar.classList.contains('collapsed') ? revealNav(navbar) : collapseNav(navbar);
          if (document.querySelector('.ozz-block-editor')) {
            var ozzBlockEditorWrapper = document.querySelector('.ozz-block-editor').closest('.ozz-fm__field');
            if ((0, _utils_State__WEBPACK_IMPORTED_MODULE_0__.GetState)('nav_collapsed')) {
              ozzBlockEditorWrapper.classList.add('collapsed');
              (0, _utils_State__WEBPACK_IMPORTED_MODULE_0__.SetState)('block_editor_collapsed', true);
            } else {
              ozzBlockEditorWrapper.classList.remove('collapsed');
              (0, _utils_State__WEBPACK_IMPORTED_MODULE_0__.SetState)('block_editor_collapsed', false);
            }
          }
        });

        // Activate Current nav item
        navFirstUl.querySelectorAll('li').forEach(function (li) {
          if (li.classList.contains(currentLink)) {
            li.classList.add('active');
          }
        });

        // Activate Tab
        var tabContainer = document.querySelectorAll('[data-active-tab]');
        tabContainer.forEach(function (element) {
          var activateClass = element.getAttribute('data-active-tab');
          if (activateClass == '') {
            element.querySelector('.all').classList.add('active');
          } else if (element.querySelector(".".concat(activateClass))) {
            element.querySelector(".".concat(activateClass)).classList.add('active');
          }
        });
      };

      /***/
    }),
    /***/"./js/modules/PostTabs.js": (
    /*!********************************!*\
      !*** ./js/modules/PostTabs.js ***!
      \********************************/
    /***/
    function _js_modules_PostTabsJs(__unused_webpack_module, __webpack_exports__, __webpack_require__) {
      __webpack_require__.r(__webpack_exports__);
      /* harmony export */
      __webpack_require__.d(__webpack_exports__, {
        /* harmony export */"default": function _default() {
          return __WEBPACK_DEFAULT_EXPORT__;
        }
        /* harmony export */
      });
      /* harmony default export */
      var __WEBPACK_DEFAULT_EXPORT__ = function __WEBPACK_DEFAULT_EXPORT__() {
        if (document.querySelectorAll('.ozz-cms .post-edit-view').length === 0) return;

        // Ozz Activate Tab (On post create/edit)
        var activateTab = function activateTab() {
          var _window$location$hash, _window$location$hash2, _document$querySelect, _document$getElementB;
          var tab = ((_window$location$hash = window.location.hash) === null || _window$location$hash === void 0 ? void 0 : _window$location$hash.substring(1)) !== '' ? (_window$location$hash2 = window.location.hash) === null || _window$location$hash2 === void 0 ? void 0 : _window$location$hash2.substring(1) : 'default',
            allTabs = document.querySelectorAll('.post-edit-view__tab'),
            tabMenuItemsBtn = document.querySelectorAll('.ozz-cms .post-edit-view__tab-menu > a > .button');

          // Remove active classes on tab menu
          tabMenuItemsBtn.forEach(function (button) {
            button.classList.remove('active');
          });

          // Remove active classes on tabs
          allTabs.forEach(function (thisTab) {
            thisTab.classList.remove('active');
          });
          (_document$querySelect = document.querySelector(".ozz-cms .post-edit-view__tab-menu > a > .button.".concat(tab))) === null || _document$querySelect === void 0 || _document$querySelect.classList.add('active');
          (_document$getElementB = document.getElementById("tab_id-".concat(tab))) === null || _document$getElementB === void 0 || _document$getElementB.classList.add('active');
        };
        var ozzFocusErrorTab = function ozzFocusErrorTab() {
          var errors = document.querySelectorAll('.field-error', 'input.error', 'textarea.error', 'select.error');
          if (errors.length > 0) {
            var focusTab = errors[0].closest('.post-edit-view__tab');
            if (!focusTab) {
              return;
            }
            var tabName = focusTab.getAttribute('data-tab-name');
            window.history.replaceState(null, null, "#".concat(tabName));
            activateTab();
          }
        };

        // Change Tab
        var tabMenuItems = document.querySelectorAll('.ozz-cms .post-edit-view__tab-menu > a');
        tabMenuItems.forEach(function (tabMenuItem) {
          tabMenuItem.addEventListener('click', function (e) {
            e.target.classList.add('active');
            setTimeout(function () {
              activateTab();
            }, 10);
          });
        });
        activateTab();
        ozzFocusErrorTab();
      };

      /***/
    }),
    /***/"./js/modules/RelocatePostInfoComp.js": (
    /*!********************************************!*\
      !*** ./js/modules/RelocatePostInfoComp.js ***!
      \********************************************/
    /***/
    function _js_modules_RelocatePostInfoCompJs(__unused_webpack_module, __webpack_exports__, __webpack_require__) {
      __webpack_require__.r(__webpack_exports__);
      /* harmony export */
      __webpack_require__.d(__webpack_exports__, {
        /* harmony export */"default": function _default() {
          return __WEBPACK_DEFAULT_EXPORT__;
        }
        /* harmony export */
      });
      /* harmony default export */
      var __WEBPACK_DEFAULT_EXPORT__ = function __WEBPACK_DEFAULT_EXPORT__() {
        var postInfoComp = document.getElementById('post-info-comp');
        if (postInfoComp === null) return;
        var postSidebar = document.querySelector('.post-edit-view__sidebar');
        postSidebar.insertBefore(postInfoComp, postSidebar.firstChild);
      };

      /***/
    }),
    /***/"./js/modules/RepeaterField.js": (
    /*!*************************************!*\
      !*** ./js/modules/RepeaterField.js ***!
      \*************************************/
    /***/
    function _js_modules_RepeaterFieldJs(__unused_webpack_module, __webpack_exports__, __webpack_require__) {
      __webpack_require__.r(__webpack_exports__);
      /* harmony export */
      __webpack_require__.d(__webpack_exports__, {
        /* harmony export */"default": function _default() {
          return __WEBPACK_DEFAULT_EXPORT__;
        }
        /* harmony export */
      });
      var RepeaterField = /*#__PURE__*/function () {
        function RepeaterField() {
          _classCallCheck(this, RepeaterField);
        }
        return _createClass(RepeaterField, [{
          key: "toggleClass",
          value:
          /**
           * Toggle Class
           * @param object DOM
           * @param string className
           */
          function toggleClass(DOM, className) {
            DOM.classList.contains(className) ? DOM.classList.remove(className) : DOM.classList.add(className);
          }

          /**
           * Random string
           * @param int length
           */
        }, {
          key: "randomString",
          value: function randomString() {
            var length = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 5;
            return Array.from({
              length: length
            }, function () {
              return Math.random().toString(36)[2];
            }).join('');
          }

          /**
          * Ozz Form Repeater initialization
          * @param DOM Used Block
          * @param bindEvent Event for bind with newly added item
          */
        }, {
          key: "initRepeater",
          value: function initRepeater() {
            var DOM = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
            var bindEvent = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
            this.repeater__Accordion(DOM);
            this.repeater__addItem(DOM, bindEvent);
            this.repeater__deleteItem(DOM);
          }

          /**
           * Repeater field Accordion
           */
        }, {
          key: "repeater__Accordion",
          value: function repeater__Accordion() {
            var _this4 = this;
            var repeater = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
            var accordionHeads = repeater ? repeater.querySelectorAll('.ozz-fm__repeat-head') : document.querySelectorAll('.ozz-fm__repeat-head');
            accordionHeads.forEach(function (thisHead) {
              var thisHeadParent = thisHead.closest('.ozz-fm__repeat-fields'),
                accordionBody = thisHeadParent.querySelector('.ozz-fm__repeat-body');
              if (thisHead) {
                thisHead.addEventListener('click', function (e) {
                  if (e.target.classList.contains('ozz-fm__repeat-remove')) {
                    return;
                  }
                  _this4.toggleClass(accordionBody, 'close');
                  _this4.toggleClass(thisHead, 'close');
                });
              }
            });
          }

          /**
           * Add Repeater Item
           */
        }, {
          key: "repeater__addItem",
          value: function repeater__addItem(DOM) {
            var _this5 = this;
            var bindEvents = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
            var addItemTrigger = DOM ? DOM.querySelectorAll('.ozz-fm__repeat-add') : document.querySelectorAll('.ozz-fm__repeat-add');
            addItemTrigger.forEach(function (addTrigger) {
              addTrigger.addEventListener('click', function (e) {
                var thisRepeater = e.target.closest('.ozz-fm__repeat'),
                  thisWrapper = thisRepeater.querySelector(':scope > .ozz-fm__repeat-wrapper'),
                  thisItemCount = thisWrapper.querySelectorAll(':scope > .ozz-fm__repeat-fields'),
                  thisItem = thisWrapper.querySelector(':scope > .ozz-fm__repeat-fields'),
                  newItem = thisItem.cloneNode(true);

                // Add ID to new item
                newItem.setAttribute('id', "rptf-".concat(_this5.randomString(18)));

                // Clear values and modify repeater item
                var itemFields = newItem.querySelectorAll('input, textarea, button, progress, meter, select, datalist');
                itemFields.forEach(function (elm) {
                  if (elm.tagName === 'INPUT' || elm.tagName === 'TEXTAREA') {
                    elm.value = '';
                  } else if (elm.tagName === 'SELECT') {
                    elm.selectedIndex = 0;
                  }
                  if (thisRepeater.classList.contains('single') === false) {
                    itemFields.forEach(function (elm) {
                      var newName = elm.name.replace(/__\d+__(?=[^__]*$)/, "__".concat(thisItemCount.length, "__"));
                      elm.name = newName;
                    });
                  }

                  // Enable delete button if disabled
                  [thisItem, newItem].forEach(function (element) {
                    element.querySelector('.ozz-fm__repeat-remove').removeAttribute('disabled');
                  });
                });

                // Clear selected media files
                newItem.querySelectorAll('.ozz-fm__media-embed-wrapper').forEach(function (mediaWrapper) {
                  mediaWrapper.innerHTML = '';
                });

                // Clear and re-init Wysiwyg editors
                _this5.repeater__clearAndInitOzzWyg(newItem);
                newItem.querySelector('.ozz-fm__repeat-number').innerHTML = thisItemCount.length + 1;

                // Init for child repeaters of clone
                _this5.repeater__addItem(newItem, bindEvents);
                _this5.repeater__deleteItem(newItem);

                // Init accordion
                _this5.repeater__Accordion(newItem);

                // Append new item
                thisWrapper.appendChild(newItem);

                // Update Names
                _this5.repeater__renameFields();

                // bind events for new item
                if (typeof bindEvents === 'function') {
                  bindEvents();
                }
              });
            });
          }

          /**
           * Delete repeater Item
           */
        }, {
          key: "repeater__deleteItem",
          value: function repeater__deleteItem(DOM) {
            var _this6 = this;
            var deleteItemTrigger = DOM ? DOM.querySelectorAll('.ozz-fm__repeat-remove') : document.querySelectorAll('.ozz-fm__repeat-remove');
            deleteItemTrigger.forEach(function (deleteItem) {
              deleteItem.addEventListener('click', function (e) {
                e.preventDefault();
                // Confirmation popup should be here

                // Delete Item
                var thisFieldsetWrapper = deleteItem.closest('.ozz-fm__repeat-wrapper'),
                  thisFields = thisFieldsetWrapper === null || thisFieldsetWrapper === void 0 ? void 0 : thisFieldsetWrapper.querySelectorAll(':scope > .ozz-fm__repeat-fields');
                if (thisFields && thisFields.length > 1) {
                  deleteItem.closest('.ozz-fm__repeat-fields').remove();
                  _this6.repeater__renameFields();
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
        }, {
          key: "repeater__renameFields",
          value: function repeater__renameFields() {
            var specificFieldSet = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
            var allWrappers = specificFieldSet !== false ? specificFieldSet : document.querySelectorAll('.ozz-fm__repeat-wrapper');
            allWrappers.forEach(function (wrapper) {
              var thisRepeater = wrapper.closest('.ozz-fm__repeat'),
                thisFieldSet = wrapper.querySelectorAll(':scope > .ozz-fm__repeat-fields'),
                isSingle = thisRepeater.classList.contains('single'),
                rptName = thisRepeater.getAttribute('data-rpt'),
                rptNameParts = rptName.split('__'),
                rptNameOnly = rptNameParts[rptNameParts.length - 1];
              thisFieldSet.forEach(function (fieldSet, i) {
                if (fieldSet) {
                  var itemIndex = !isSingle ? fieldSet.querySelector(':scope > .ozz-fm__repeat-head .ozz-fm__repeat-number') : fieldSet.querySelector(':scope > .ozz-fm__repeat-number');
                  if (itemIndex) {
                    itemIndex.innerHTML = i + 1;
                  }
                  if (isSingle === false) {
                    // Rename field names
                    var itemFields = fieldSet.querySelectorAll('input, textarea, button, progress, meter, select, datalist');
                    itemFields.forEach(function (elm) {
                      var newName = elm.name.replace(new RegExp("".concat(rptNameOnly, "__\\d+__")), "".concat(rptNameOnly, "__").concat(i, "__"));
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
        }, {
          key: "repeater__clearAndInitOzzWyg",
          value: function repeater__clearAndInitOzzWyg() {
            var DOM = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
            if (typeof OzzWyg === 'function') {
              var editors = DOM === false ? document.querySelectorAll('[data-ozz-wyg]') : DOM.querySelectorAll('[data-ozz-wyg]');
              editors.forEach(function (editor) {
                var id = "i-".concat(Math.random().toString(36).substring(2, 6 + 2));
                editor.innerHTML = '';
                editor.setAttribute('data-editor', id);
                new OzzWyg({
                  selector: editor
                });
              });
            }
          }
        }]);
      }();
      /* harmony default export */
      var __WEBPACK_DEFAULT_EXPORT__ = RepeaterField;

      /***/
    }),
    /***/"./js/modules/SlugUpdate.js": (
    /*!**********************************!*\
      !*** ./js/modules/SlugUpdate.js ***!
      \**********************************/
    /***/
    function _js_modules_SlugUpdateJs(__unused_webpack_module, __webpack_exports__, __webpack_require__) {
      __webpack_require__.r(__webpack_exports__);
      /* harmony export */
      __webpack_require__.d(__webpack_exports__, {
        /* harmony export */"default": function _default() {
          return __WEBPACK_DEFAULT_EXPORT__;
        }
        /* harmony export */
      });
      /* harmony default export */
      var __WEBPACK_DEFAULT_EXPORT__ = function __WEBPACK_DEFAULT_EXPORT__() {
        var updateThis = document.querySelector('.j-title-onchange'),
          getFrom = document.querySelector('.ozz-fm .default-post-title');
        if (getFrom) {
          getFrom.addEventListener('keyup', function () {
            var slug = getFrom.value.replace(/\s+/g, '-').toLowerCase().replace(/[^\w\-]+/g, '');
            document.querySelector('.ozz-fm .default-post-slug').value = slug;
            if (updateThis) {
              updateThis.innerHTML = getFrom.value;
            }
          });
        }
      };

      /***/
    }),
    /***/"./js/modules/Taxonomy.js": (
    /*!********************************!*\
      !*** ./js/modules/Taxonomy.js ***!
      \********************************/
    /***/
    function _js_modules_TaxonomyJs(__unused_webpack_module, __webpack_exports__, __webpack_require__) {
      __webpack_require__.r(__webpack_exports__);
      /* harmony export */
      __webpack_require__.d(__webpack_exports__, {
        /* harmony export */"default": function _default() {
          return __WEBPACK_DEFAULT_EXPORT__;
        }
        /* harmony export */
      });
      /* harmony import */
      var _utils_Fetch__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../utils/Fetch */"./js/utils/Fetch.js");
      /* harmony import */
      var _utils_Alert__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/Alert */"./js/utils/Alert.js");
      /* harmony import */
      var _utils_CommonDelete__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../utils/CommonDelete */"./js/utils/CommonDelete.js");

      /* harmony default export */
      var __WEBPACK_DEFAULT_EXPORT__ = function __WEBPACK_DEFAULT_EXPORT__() {
        var $base_url = "".concat(DATA.CMS_URL, "taxonomy/");
        var initTaxonomy = function initTaxonomy() {
          // Delete Taxonomy
          var taxonomyDeleteTriggers = document.querySelectorAll('.taxonomy-listing__delete');
          if (!taxonomyDeleteTriggers || taxonomyDeleteTriggers.length === 0) return;
          taxonomyDeleteTriggers.forEach(function (trigger) {
            trigger.addEventListener('click', function (e) {
              e.preventDefault();
              var taxonomyID = trigger.getAttribute('data-taxonomy-id');
              (0, _utils_CommonDelete__WEBPACK_IMPORTED_MODULE_2__.CommonDelete)($base_url + 'delete', 'POST', {
                taxonomyID: taxonomyID
              }, 'The Taxonomy will be deleted permanently. Are you sure?');
            });
          });
        };
        var initTerm = function initTerm() {
          // Edit terms
          var termEditTriggers = document.querySelectorAll('.terms-table__edit-term');
          if (!termEditTriggers || termEditTriggers.length === 0) return;
          termEditTriggers.forEach(function (trigger) {
            trigger.addEventListener('click', function (e) {
              e.preventDefault();
              var tr = trigger.closest('tr'),
                termID = tr.querySelector('input[name="term-id"]').value,
                name = tr.querySelector('input[name="term-name"]').value,
                slug = tr.querySelector('input[name="term-slug"]').value;
              var data = {
                termID: termID,
                name: name,
                slug: slug
              };
              var save = (0, _utils_Fetch__WEBPACK_IMPORTED_MODULE_0__.send)($base_url + 'update-term', 'POST', JSON.stringify(data));
              save.then(function (response) {
                (0, _utils_Alert__WEBPACK_IMPORTED_MODULE_1__.showAlert)(response.message, response.status);
              });
            });
          });

          // Delete term
          var termDeleteTriggers = document.querySelectorAll('.terms-table__delete-term');
          termDeleteTriggers.forEach(function (trigger) {
            trigger.addEventListener('click', function (e) {
              e.preventDefault();
              var termID = trigger.closest('tr').querySelector('input[name="term-id"]').value;
              (0, _utils_CommonDelete__WEBPACK_IMPORTED_MODULE_2__.CommonDelete)($base_url + 'delete-term', 'POST', {
                termID: termID
              }, 'The Term will be deleted permanently. Are you sure?');
            });
          });
        };
        initTaxonomy();
        initTerm();
      };

      /***/
    }),
    /***/"./js/utils/Alert.js": (
    /*!***************************!*\
      !*** ./js/utils/Alert.js ***!
      \***************************/
    /***/
    function _js_utils_AlertJs(__unused_webpack_module, __webpack_exports__, __webpack_require__) {
      __webpack_require__.r(__webpack_exports__);
      /* harmony export */
      __webpack_require__.d(__webpack_exports__, {
        /* harmony export */showAlert: function showAlert() {
          return /* binding */_showAlert;
        }
        /* harmony export */
      });
      // Show alert
      var _showAlert = function _showAlert(message) {
        var style = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'success';
        var alertBar = document.querySelector('.common-alert-bar');
        alertBar.innerHTML = "<span class=\"message ".concat(style, "\">").concat(message, "</span>");
        setTimeout(function () {
          alertBar.classList.add('active');
          setTimeout(function () {
            alertBar.classList.remove('active');
            setTimeout(function () {
              alertBar.innerHTML = '';
            }, 300);
          }, 6000);
        }, 300);
      };

      /***/
    }),
    /***/"./js/utils/CommonDelete.js": (
    /*!**********************************!*\
      !*** ./js/utils/CommonDelete.js ***!
      \**********************************/
    /***/
    function _js_utils_CommonDeleteJs(__unused_webpack_module, __webpack_exports__, __webpack_require__) {
      __webpack_require__.r(__webpack_exports__);
      /* harmony export */
      __webpack_require__.d(__webpack_exports__, {
        /* harmony export */CommonDelete: function CommonDelete() {
          return /* binding */_CommonDelete;
        }
        /* harmony export */
      });
      /* harmony import */
      var _Fetch__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Fetch */"./js/utils/Fetch.js");
      /* harmony import */
      var _Alert__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Alert */"./js/utils/Alert.js");

      /**
       * Common fetch API delete request
       * @param {String} $url 
       * @param {String} $method 
       * @param {Object} $data 
       * @param {String} $confirmation 
       */
      var _CommonDelete = function _CommonDelete($url, $method, $data, $confirmation) {
        if (confirm($confirmation)) {
          var deleteTerm = (0, _Fetch__WEBPACK_IMPORTED_MODULE_0__.send)($url, $method, JSON.stringify($data));
          deleteTerm.then(function (response) {
            (0, _Alert__WEBPACK_IMPORTED_MODULE_1__.showAlert)(response.message, response.status);
            if (response.status == 'success') {
              setTimeout(function () {
                location.reload();
              }, 800);
            }
          });
        }
      };

      /***/
    }),
    /***/"./js/utils/Fetch.js": (
    /*!***************************!*\
      !*** ./js/utils/Fetch.js ***!
      \***************************/
    /***/
    function _js_utils_FetchJs(__unused_webpack_module, __webpack_exports__, __webpack_require__) {
      __webpack_require__.r(__webpack_exports__);
      /* harmony export */
      __webpack_require__.d(__webpack_exports__, {
        /* harmony export */send: function send() {
          return /* binding */_send;
        }
        /* harmony export */
      });
      // Send fetch request
      var _send = function _send(url) {
        var method = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'GET';
        var body = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
        var headers = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : {};
        return new Promise(function (resolve, reject) {
          fetch(url, {
            method: method,
            headers: headers,
            body: body
          }).then(function (response) {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
          }).then(function (data) {
            resolve(data);
          })["catch"](function (error) {
            console.error('Error:', error);
            reject(error);
          });
        });
      };

      /***/
    }),
    /***/"./js/utils/Popup.js": (
    /*!***************************!*\
      !*** ./js/utils/Popup.js ***!
      \***************************/
    /***/
    function _js_utils_PopupJs(__unused_webpack_module, __webpack_exports__, __webpack_require__) {
      __webpack_require__.r(__webpack_exports__);
      /* harmony export */
      __webpack_require__.d(__webpack_exports__, {
        /* harmony export */closePopup: function closePopup() {
          return /* binding */_closePopup;
        },
        /* harmony export */openPopup: function openPopup() {
          return /* binding */_openPopup;
        }
        /* harmony export */
      });
      /* harmony import */
      var _State__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./State */"./js/utils/State.js");

      /**
       * Bind events after close
       * @param {Function} bindAfterClose 
       */
      var _closePopup = function _closePopup() {
        var bindAfterClose = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
        var popup = document.querySelector('.ozz-cms-popup'),
          popupContent = document.querySelector('#cms-popup-content');
        popupContent.innerHTML = '';
        popup.classList.remove('active');
        (0, _State__WEBPACK_IMPORTED_MODULE_0__.SetState)('popup_opened', false);
        if (typeof bindAfterClose == 'function') {
          bindAfterClose();
        }
      };

      /**
       * Open default popup
       * @param {Element} popupDOM 
       * @param {Function} bindEvent 
       * @param {Function} afterCloseCallback 
       */
      var _openPopup = function _openPopup(popupDOM) {
        var bindEvent = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
        var afterCloseCallback = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
        var popup = document.querySelector('.ozz-cms-popup'),
          popupContent = popup.querySelector('#cms-popup-content'),
          closeTrigger = popup.querySelector('#cms-popup-close');
        popupContent.innerHTML = '';
        popupContent.insertAdjacentHTML('beforeend', _typeof2(popupDOM) === 'object' ? popupDOM.outerHTML : popupDOM);
        popup.classList.add('active');
        (0, _State__WEBPACK_IMPORTED_MODULE_0__.SetState)('popup_opened', true);

        // Binding Events
        if (typeof bindEvent == 'function') {
          bindEvent(popupContent);
        }

        // Focus field
        var inputsOnPopup = popupContent.querySelectorAll('input[type="text"]');
        if (inputsOnPopup.length > 0) {
          inputsOnPopup[0].focus();
        }

        // Close popup by trigger
        closeTrigger.addEventListener('click', function () {
          _closePopup(afterCloseCallback);
        }, {
          once: true
        });

        // Close popup by click on Esc
        document.addEventListener('keydown', function (event) {
          if (event.key === 'Escape') {
            _closePopup(afterCloseCallback);
          }
        }, {
          once: true
        });
      };

      /***/
    }),
    /***/"./js/utils/State.js": (
    /*!***************************!*\
      !*** ./js/utils/State.js ***!
      \***************************/
    /***/
    function _js_utils_StateJs(__unused_webpack_module, __webpack_exports__, __webpack_require__) {
      __webpack_require__.r(__webpack_exports__);
      /* harmony export */
      __webpack_require__.d(__webpack_exports__, {
        /* harmony export */GetState: function GetState() {
          return /* binding */_GetState;
        },
        /* harmony export */SetState: function SetState() {
          return /* binding */_SetState;
        }
        /* harmony export */
      });
      var DOMAIN_KEY = window.location.host.replace(/[^a-zA-Z0-9]/g, '_');
      var STORAGE_KEY = "ozz_app_state_".concat(DOMAIN_KEY);

      // Default state
      var ozz_app_state = {
        nav_collapsed: false,
        block_editor_expanded: false,
        block_editor_collapsed: false,
        popup_opened: false,
        block_editor_stock_layout: 'lay2',
        theme: 'light'
      };

      /**
       * Get State
       */
      function _GetState() {
        var key = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
        var hasItem = typeof localStorage !== 'undefined' && localStorage.getItem(STORAGE_KEY) !== null;
        var state = hasItem ? JSON.parse(localStorage.getItem(STORAGE_KEY)) : ozz_app_state;
        return key ? state[key] : state;
      }

      /**
       * Set State
       */
      function _SetState(key, value) {
        var hasItem = typeof localStorage !== 'undefined' && localStorage.getItem(STORAGE_KEY) !== null;
        var state = hasItem ? JSON.parse(localStorage.getItem(STORAGE_KEY)) : ozz_app_state;
        state[key] = value;
        ozz_app_state = state;
        if (typeof localStorage !== 'undefined') {
          localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
        }
      }

      /***/
    }),
    /***/"./js/vendor/Sortable.js": (
    /*!*******************************!*\
      !*** ./js/vendor/Sortable.js ***!
      \*******************************/
    /***/
    function _js_vendor_SortableJs(module, __webpack_exports__, __webpack_require__) {
      __webpack_require__.r(__webpack_exports__);
      /* harmony export */
      __webpack_require__.d(__webpack_exports__, {
        /* harmony export */"default": function _default() {
          return __WEBPACK_DEFAULT_EXPORT__;
        }
        /* harmony export */
      });
      /* module decorator */
      module = __webpack_require__.hmd(module);
      /**!
      * Sortable 1.15.2
      * @author	RubaXa   <trash@rubaxa.org>
      * @author	owenm    <owen23355@gmail.com>
      * @license MIT
      */
      (function (global, factory) {
        (typeof exports === "undefined" ? "undefined" : _typeof2(exports)) === 'object' && "object" !== 'undefined' ? module.exports = factory() : typeof define === 'function' && __webpack_require__.amdO ? define(factory) : (global = global || self, global.Sortable = factory());
      })(undefined, function () {
        'use strict';

        function ownKeys(object, enumerableOnly) {
          var keys = Object.keys(object);
          if (Object.getOwnPropertySymbols) {
            var symbols = Object.getOwnPropertySymbols(object);
            if (enumerableOnly) {
              symbols = symbols.filter(function (sym) {
                return Object.getOwnPropertyDescriptor(object, sym).enumerable;
              });
            }
            keys.push.apply(keys, symbols);
          }
          return keys;
        }
        function _objectSpread2(target) {
          for (var i = 1; i < arguments.length; i++) {
            var source = arguments[i] != null ? arguments[i] : {};
            if (i % 2) {
              ownKeys(Object(source), true).forEach(function (key) {
                _defineProperty(target, key, source[key]);
              });
            } else if (Object.getOwnPropertyDescriptors) {
              Object.defineProperties(target, Object.getOwnPropertyDescriptors(source));
            } else {
              ownKeys(Object(source)).forEach(function (key) {
                Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key));
              });
            }
          }
          return target;
        }
        function _typeof(obj) {
          "@babel/helpers - typeof";

          if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
            _typeof = function _typeof(obj) {
              return typeof obj;
            };
          } else {
            _typeof = function _typeof(obj) {
              return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
            };
          }
          return _typeof(obj);
        }
        function _defineProperty(obj, key, value) {
          if (key in obj) {
            Object.defineProperty(obj, key, {
              value: value,
              enumerable: true,
              configurable: true,
              writable: true
            });
          } else {
            obj[key] = value;
          }
          return obj;
        }
        function _extends() {
          _extends = Object.assign || function (target) {
            for (var i = 1; i < arguments.length; i++) {
              var source = arguments[i];
              for (var key in source) {
                if (Object.prototype.hasOwnProperty.call(source, key)) {
                  target[key] = source[key];
                }
              }
            }
            return target;
          };
          return _extends.apply(this, arguments);
        }
        function _objectWithoutPropertiesLoose(source, excluded) {
          if (source == null) return {};
          var target = {};
          var sourceKeys = Object.keys(source);
          var key, i;
          for (i = 0; i < sourceKeys.length; i++) {
            key = sourceKeys[i];
            if (excluded.indexOf(key) >= 0) continue;
            target[key] = source[key];
          }
          return target;
        }
        function _objectWithoutProperties(source, excluded) {
          if (source == null) return {};
          var target = _objectWithoutPropertiesLoose(source, excluded);
          var key, i;
          if (Object.getOwnPropertySymbols) {
            var sourceSymbolKeys = Object.getOwnPropertySymbols(source);
            for (i = 0; i < sourceSymbolKeys.length; i++) {
              key = sourceSymbolKeys[i];
              if (excluded.indexOf(key) >= 0) continue;
              if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue;
              target[key] = source[key];
            }
          }
          return target;
        }
        function _toConsumableArray(arr) {
          return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread();
        }
        function _arrayWithoutHoles(arr) {
          if (Array.isArray(arr)) return _arrayLikeToArray(arr);
        }
        function _iterableToArray(iter) {
          if (typeof Symbol !== "undefined" && iter[Symbol.iterator] != null || iter["@@iterator"] != null) return Array.from(iter);
        }
        function _unsupportedIterableToArray(o, minLen) {
          if (!o) return;
          if (typeof o === "string") return _arrayLikeToArray(o, minLen);
          var n = Object.prototype.toString.call(o).slice(8, -1);
          if (n === "Object" && o.constructor) n = o.constructor.name;
          if (n === "Map" || n === "Set") return Array.from(o);
          if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen);
        }
        function _arrayLikeToArray(arr, len) {
          if (len == null || len > arr.length) len = arr.length;
          for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i];
          return arr2;
        }
        function _nonIterableSpread() {
          throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
        }
        var version = "1.15.2";
        function userAgent(pattern) {
          if (typeof window !== 'undefined' && window.navigator) {
            return !! /*@__PURE__*/navigator.userAgent.match(pattern);
          }
        }
        var IE11OrLess = userAgent(/(?:Trident.*rv[ :]?11\.|msie|iemobile|Windows Phone)/i);
        var Edge = userAgent(/Edge/i);
        var FireFox = userAgent(/firefox/i);
        var Safari = userAgent(/safari/i) && !userAgent(/chrome/i) && !userAgent(/android/i);
        var IOS = userAgent(/iP(ad|od|hone)/i);
        var ChromeForAndroid = userAgent(/chrome/i) && userAgent(/android/i);
        var captureMode = {
          capture: false,
          passive: false
        };
        function on(el, event, fn) {
          el.addEventListener(event, fn, !IE11OrLess && captureMode);
        }
        function off(el, event, fn) {
          el.removeEventListener(event, fn, !IE11OrLess && captureMode);
        }
        function matches(/**HTMLElement*/el, /**String*/selector) {
          if (!selector) return;
          selector[0] === '>' && (selector = selector.substring(1));
          if (el) {
            try {
              if (el.matches) {
                return el.matches(selector);
              } else if (el.msMatchesSelector) {
                return el.msMatchesSelector(selector);
              } else if (el.webkitMatchesSelector) {
                return el.webkitMatchesSelector(selector);
              }
            } catch (_) {
              return false;
            }
          }
          return false;
        }
        function getParentOrHost(el) {
          return el.host && el !== document && el.host.nodeType ? el.host : el.parentNode;
        }
        function closest(/**HTMLElement*/el, /**String*/selector, /**HTMLElement*/ctx, includeCTX) {
          if (el) {
            ctx = ctx || document;
            do {
              if (selector != null && (selector[0] === '>' ? el.parentNode === ctx && matches(el, selector) : matches(el, selector)) || includeCTX && el === ctx) {
                return el;
              }
              if (el === ctx) break;
              /* jshint boss:true */
            } while (el = getParentOrHost(el));
          }
          return null;
        }
        var R_SPACE = /\s+/g;
        function toggleClass(el, name, state) {
          if (el && name) {
            if (el.classList) {
              el.classList[state ? 'add' : 'remove'](name);
            } else {
              var className = (' ' + el.className + ' ').replace(R_SPACE, ' ').replace(' ' + name + ' ', ' ');
              el.className = (className + (state ? ' ' + name : '')).replace(R_SPACE, ' ');
            }
          }
        }
        function css(el, prop, val) {
          var style = el && el.style;
          if (style) {
            if (val === void 0) {
              if (document.defaultView && document.defaultView.getComputedStyle) {
                val = document.defaultView.getComputedStyle(el, '');
              } else if (el.currentStyle) {
                val = el.currentStyle;
              }
              return prop === void 0 ? val : val[prop];
            } else {
              if (!(prop in style) && prop.indexOf('webkit') === -1) {
                prop = '-webkit-' + prop;
              }
              style[prop] = val + (typeof val === 'string' ? '' : 'px');
            }
          }
        }
        function matrix(el, selfOnly) {
          var appliedTransforms = '';
          if (typeof el === 'string') {
            appliedTransforms = el;
          } else {
            do {
              var transform = css(el, 'transform');
              if (transform && transform !== 'none') {
                appliedTransforms = transform + ' ' + appliedTransforms;
              }
              /* jshint boss:true */
            } while (!selfOnly && (el = el.parentNode));
          }
          var matrixFn = window.DOMMatrix || window.WebKitCSSMatrix || window.CSSMatrix || window.MSCSSMatrix;
          /*jshint -W056 */
          return matrixFn && new matrixFn(appliedTransforms);
        }
        function find(ctx, tagName, iterator) {
          if (ctx) {
            var list = ctx.getElementsByTagName(tagName),
              i = 0,
              n = list.length;
            if (iterator) {
              for (; i < n; i++) {
                iterator(list[i], i);
              }
            }
            return list;
          }
          return [];
        }
        function getWindowScrollingElement() {
          var scrollingElement = document.scrollingElement;
          if (scrollingElement) {
            return scrollingElement;
          } else {
            return document.documentElement;
          }
        }

        /**
        * Returns the "bounding client rect" of given element
        * @param  {HTMLElement} el                       The element whose boundingClientRect is wanted
        * @param  {[Boolean]} relativeToContainingBlock  Whether the rect should be relative to the containing block of (including) the container
        * @param  {[Boolean]} relativeToNonStaticParent  Whether the rect should be relative to the relative parent of (including) the contaienr
        * @param  {[Boolean]} undoScale                  Whether the container's scale() should be undone
        * @param  {[HTMLElement]} container              The parent the element will be placed in
        * @return {Object}                               The boundingClientRect of el, with specified adjustments
        */
        function getRect(el, relativeToContainingBlock, relativeToNonStaticParent, undoScale, container) {
          if (!el.getBoundingClientRect && el !== window) return;
          var elRect, top, left, bottom, right, height, width;
          if (el !== window && el.parentNode && el !== getWindowScrollingElement()) {
            elRect = el.getBoundingClientRect();
            top = elRect.top;
            left = elRect.left;
            bottom = elRect.bottom;
            right = elRect.right;
            height = elRect.height;
            width = elRect.width;
          } else {
            top = 0;
            left = 0;
            bottom = window.innerHeight;
            right = window.innerWidth;
            height = window.innerHeight;
            width = window.innerWidth;
          }
          if ((relativeToContainingBlock || relativeToNonStaticParent) && el !== window) {
            // Adjust for translate()
            container = container || el.parentNode;

            // solves #1123 (see: https://stackoverflow.com/a/37953806/6088312)
            // Not needed on <= IE11
            if (!IE11OrLess) {
              do {
                if (container && container.getBoundingClientRect && (css(container, 'transform') !== 'none' || relativeToNonStaticParent && css(container, 'position') !== 'static')) {
                  var containerRect = container.getBoundingClientRect();

                  // Set relative to edges of padding box of container
                  top -= containerRect.top + parseInt(css(container, 'border-top-width'));
                  left -= containerRect.left + parseInt(css(container, 'border-left-width'));
                  bottom = top + elRect.height;
                  right = left + elRect.width;
                  break;
                }
                /* jshint boss:true */
              } while (container = container.parentNode);
            }
          }
          if (undoScale && el !== window) {
            // Adjust for scale()
            var elMatrix = matrix(container || el),
              scaleX = elMatrix && elMatrix.a,
              scaleY = elMatrix && elMatrix.d;
            if (elMatrix) {
              top /= scaleY;
              left /= scaleX;
              width /= scaleX;
              height /= scaleY;
              bottom = top + height;
              right = left + width;
            }
          }
          return {
            top: top,
            left: left,
            bottom: bottom,
            right: right,
            width: width,
            height: height
          };
        }

        /**
        * Checks if a side of an element is scrolled past a side of its parents
        * @param  {HTMLElement}  el           The element who's side being scrolled out of view is in question
        * @param  {String}       elSide       Side of the element in question ('top', 'left', 'right', 'bottom')
        * @param  {String}       parentSide   Side of the parent in question ('top', 'left', 'right', 'bottom')
        * @return {HTMLElement}               The parent scroll element that the el's side is scrolled past, or null if there is no such element
        */
        function isScrolledPast(el, elSide, parentSide) {
          var parent = getParentAutoScrollElement(el, true),
            elSideVal = getRect(el)[elSide];

          /* jshint boss:true */
          while (parent) {
            var parentSideVal = getRect(parent)[parentSide],
              visible = void 0;
            if (parentSide === 'top' || parentSide === 'left') {
              visible = elSideVal >= parentSideVal;
            } else {
              visible = elSideVal <= parentSideVal;
            }
            if (!visible) return parent;
            if (parent === getWindowScrollingElement()) break;
            parent = getParentAutoScrollElement(parent, false);
          }
          return false;
        }

        /**
        * Gets nth child of el, ignoring hidden children, sortable's elements (does not ignore clone if it's visible)
        * and non-draggable elements
        * @param  {HTMLElement} el       The parent element
        * @param  {Number} childNum      The index of the child
        * @param  {Object} options       Parent Sortable's options
        * @return {HTMLElement}          The child at index childNum, or null if not found
        */
        function getChild(el, childNum, options, includeDragEl) {
          var currentChild = 0,
            i = 0,
            children = el.children;
          while (i < children.length) {
            if (children[i].style.display !== 'none' && children[i] !== Sortable.ghost && (includeDragEl || children[i] !== Sortable.dragged) && closest(children[i], options.draggable, el, false)) {
              if (currentChild === childNum) {
                return children[i];
              }
              currentChild++;
            }
            i++;
          }
          return null;
        }

        /**
        * Gets the last child in the el, ignoring ghostEl or invisible elements (clones)
        * @param  {HTMLElement} el       Parent element
        * @param  {selector} selector    Any other elements that should be ignored
        * @return {HTMLElement}          The last child, ignoring ghostEl
        */
        function lastChild(el, selector) {
          var last = el.lastElementChild;
          while (last && (last === Sortable.ghost || css(last, 'display') === 'none' || selector && !matches(last, selector))) {
            last = last.previousElementSibling;
          }
          return last || null;
        }

        /**
        * Returns the index of an element within its parent for a selected set of
        * elements
        * @param  {HTMLElement} el
        * @param  {selector} selector
        * @return {number}
        */
        function index(el, selector) {
          var index = 0;
          if (!el || !el.parentNode) {
            return -1;
          }

          /* jshint boss:true */
          while (el = el.previousElementSibling) {
            if (el.nodeName.toUpperCase() !== 'TEMPLATE' && el !== Sortable.clone && (!selector || matches(el, selector))) {
              index++;
            }
          }
          return index;
        }

        /**
        * Returns the scroll offset of the given element, added with all the scroll offsets of parent elements.
        * The value is returned in real pixels.
        * @param  {HTMLElement} el
        * @return {Array}             Offsets in the format of [left, top]
        */
        function getRelativeScrollOffset(el) {
          var offsetLeft = 0,
            offsetTop = 0,
            winScroller = getWindowScrollingElement();
          if (el) {
            do {
              var elMatrix = matrix(el),
                scaleX = elMatrix.a,
                scaleY = elMatrix.d;
              offsetLeft += el.scrollLeft * scaleX;
              offsetTop += el.scrollTop * scaleY;
            } while (el !== winScroller && (el = el.parentNode));
          }
          return [offsetLeft, offsetTop];
        }

        /**
        * Returns the index of the object within the given array
        * @param  {Array} arr   Array that may or may not hold the object
        * @param  {Object} obj  An object that has a key-value pair unique to and identical to a key-value pair in the object you want to find
        * @return {Number}      The index of the object in the array, or -1
        */
        function indexOfObject(arr, obj) {
          for (var i in arr) {
            if (!arr.hasOwnProperty(i)) continue;
            for (var key in obj) {
              if (obj.hasOwnProperty(key) && obj[key] === arr[i][key]) return Number(i);
            }
          }
          return -1;
        }
        function getParentAutoScrollElement(el, includeSelf) {
          // skip to window
          if (!el || !el.getBoundingClientRect) return getWindowScrollingElement();
          var elem = el;
          var gotSelf = false;
          do {
            // we don't need to get elem css if it isn't even overflowing in the first place (performance)
            if (elem.clientWidth < elem.scrollWidth || elem.clientHeight < elem.scrollHeight) {
              var elemCSS = css(elem);
              if (elem.clientWidth < elem.scrollWidth && (elemCSS.overflowX == 'auto' || elemCSS.overflowX == 'scroll') || elem.clientHeight < elem.scrollHeight && (elemCSS.overflowY == 'auto' || elemCSS.overflowY == 'scroll')) {
                if (!elem.getBoundingClientRect || elem === document.body) return getWindowScrollingElement();
                if (gotSelf || includeSelf) return elem;
                gotSelf = true;
              }
            }
            /* jshint boss:true */
          } while (elem = elem.parentNode);
          return getWindowScrollingElement();
        }
        function extend(dst, src) {
          if (dst && src) {
            for (var key in src) {
              if (src.hasOwnProperty(key)) {
                dst[key] = src[key];
              }
            }
          }
          return dst;
        }
        function isRectEqual(rect1, rect2) {
          return Math.round(rect1.top) === Math.round(rect2.top) && Math.round(rect1.left) === Math.round(rect2.left) && Math.round(rect1.height) === Math.round(rect2.height) && Math.round(rect1.width) === Math.round(rect2.width);
        }
        var _throttleTimeout;
        function throttle(callback, ms) {
          return function () {
            if (!_throttleTimeout) {
              var args = arguments,
                _this = this;
              if (args.length === 1) {
                callback.call(_this, args[0]);
              } else {
                callback.apply(_this, args);
              }
              _throttleTimeout = setTimeout(function () {
                _throttleTimeout = void 0;
              }, ms);
            }
          };
        }
        function cancelThrottle() {
          clearTimeout(_throttleTimeout);
          _throttleTimeout = void 0;
        }
        function scrollBy(el, x, y) {
          el.scrollLeft += x;
          el.scrollTop += y;
        }
        function clone(el) {
          var Polymer = window.Polymer;
          var $ = window.jQuery || window.Zepto;
          if (Polymer && Polymer.dom) {
            return Polymer.dom(el).cloneNode(true);
          } else if ($) {
            return $(el).clone(true)[0];
          } else {
            return el.cloneNode(true);
          }
        }
        function setRect(el, rect) {
          css(el, 'position', 'absolute');
          css(el, 'top', rect.top);
          css(el, 'left', rect.left);
          css(el, 'width', rect.width);
          css(el, 'height', rect.height);
        }
        function unsetRect(el) {
          css(el, 'position', '');
          css(el, 'top', '');
          css(el, 'left', '');
          css(el, 'width', '');
          css(el, 'height', '');
        }
        function getChildContainingRectFromElement(container, options, ghostEl) {
          var rect = {};
          Array.from(container.children).forEach(function (child) {
            var _rect$left, _rect$top, _rect$right, _rect$bottom;
            if (!closest(child, options.draggable, container, false) || child.animated || child === ghostEl) return;
            var childRect = getRect(child);
            rect.left = Math.min((_rect$left = rect.left) !== null && _rect$left !== void 0 ? _rect$left : Infinity, childRect.left);
            rect.top = Math.min((_rect$top = rect.top) !== null && _rect$top !== void 0 ? _rect$top : Infinity, childRect.top);
            rect.right = Math.max((_rect$right = rect.right) !== null && _rect$right !== void 0 ? _rect$right : -Infinity, childRect.right);
            rect.bottom = Math.max((_rect$bottom = rect.bottom) !== null && _rect$bottom !== void 0 ? _rect$bottom : -Infinity, childRect.bottom);
          });
          rect.width = rect.right - rect.left;
          rect.height = rect.bottom - rect.top;
          rect.x = rect.left;
          rect.y = rect.top;
          return rect;
        }
        var expando = 'Sortable' + new Date().getTime();
        function AnimationStateManager() {
          var animationStates = [],
            animationCallbackId;
          return {
            captureAnimationState: function captureAnimationState() {
              animationStates = [];
              if (!this.options.animation) return;
              var children = [].slice.call(this.el.children);
              children.forEach(function (child) {
                if (css(child, 'display') === 'none' || child === Sortable.ghost) return;
                animationStates.push({
                  target: child,
                  rect: getRect(child)
                });
                var fromRect = _objectSpread2({}, animationStates[animationStates.length - 1].rect);

                // If animating: compensate for current animation
                if (child.thisAnimationDuration) {
                  var childMatrix = matrix(child, true);
                  if (childMatrix) {
                    fromRect.top -= childMatrix.f;
                    fromRect.left -= childMatrix.e;
                  }
                }
                child.fromRect = fromRect;
              });
            },
            addAnimationState: function addAnimationState(state) {
              animationStates.push(state);
            },
            removeAnimationState: function removeAnimationState(target) {
              animationStates.splice(indexOfObject(animationStates, {
                target: target
              }), 1);
            },
            animateAll: function animateAll(callback) {
              var _this = this;
              if (!this.options.animation) {
                clearTimeout(animationCallbackId);
                if (typeof callback === 'function') callback();
                return;
              }
              var animating = false,
                animationTime = 0;
              animationStates.forEach(function (state) {
                var time = 0,
                  target = state.target,
                  fromRect = target.fromRect,
                  toRect = getRect(target),
                  prevFromRect = target.prevFromRect,
                  prevToRect = target.prevToRect,
                  animatingRect = state.rect,
                  targetMatrix = matrix(target, true);
                if (targetMatrix) {
                  // Compensate for current animation
                  toRect.top -= targetMatrix.f;
                  toRect.left -= targetMatrix.e;
                }
                target.toRect = toRect;
                if (target.thisAnimationDuration) {
                  // Could also check if animatingRect is between fromRect and toRect
                  if (isRectEqual(prevFromRect, toRect) && !isRectEqual(fromRect, toRect) &&
                  // Make sure animatingRect is on line between toRect & fromRect
                  (animatingRect.top - toRect.top) / (animatingRect.left - toRect.left) === (fromRect.top - toRect.top) / (fromRect.left - toRect.left)) {
                    // If returning to same place as started from animation and on same axis
                    time = calculateRealTime(animatingRect, prevFromRect, prevToRect, _this.options);
                  }
                }

                // if fromRect != toRect: animate
                if (!isRectEqual(toRect, fromRect)) {
                  target.prevFromRect = fromRect;
                  target.prevToRect = toRect;
                  if (!time) {
                    time = _this.options.animation;
                  }
                  _this.animate(target, animatingRect, toRect, time);
                }
                if (time) {
                  animating = true;
                  animationTime = Math.max(animationTime, time);
                  clearTimeout(target.animationResetTimer);
                  target.animationResetTimer = setTimeout(function () {
                    target.animationTime = 0;
                    target.prevFromRect = null;
                    target.fromRect = null;
                    target.prevToRect = null;
                    target.thisAnimationDuration = null;
                  }, time);
                  target.thisAnimationDuration = time;
                }
              });
              clearTimeout(animationCallbackId);
              if (!animating) {
                if (typeof callback === 'function') callback();
              } else {
                animationCallbackId = setTimeout(function () {
                  if (typeof callback === 'function') callback();
                }, animationTime);
              }
              animationStates = [];
            },
            animate: function animate(target, currentRect, toRect, duration) {
              if (duration) {
                css(target, 'transition', '');
                css(target, 'transform', '');
                var elMatrix = matrix(this.el),
                  scaleX = elMatrix && elMatrix.a,
                  scaleY = elMatrix && elMatrix.d,
                  translateX = (currentRect.left - toRect.left) / (scaleX || 1),
                  translateY = (currentRect.top - toRect.top) / (scaleY || 1);
                target.animatingX = !!translateX;
                target.animatingY = !!translateY;
                css(target, 'transform', 'translate3d(' + translateX + 'px,' + translateY + 'px,0)');
                this.forRepaintDummy = repaint(target); // repaint

                css(target, 'transition', 'transform ' + duration + 'ms' + (this.options.easing ? ' ' + this.options.easing : ''));
                css(target, 'transform', 'translate3d(0,0,0)');
                typeof target.animated === 'number' && clearTimeout(target.animated);
                target.animated = setTimeout(function () {
                  css(target, 'transition', '');
                  css(target, 'transform', '');
                  target.animated = false;
                  target.animatingX = false;
                  target.animatingY = false;
                }, duration);
              }
            }
          };
        }
        function repaint(target) {
          return target.offsetWidth;
        }
        function calculateRealTime(animatingRect, fromRect, toRect, options) {
          return Math.sqrt(Math.pow(fromRect.top - animatingRect.top, 2) + Math.pow(fromRect.left - animatingRect.left, 2)) / Math.sqrt(Math.pow(fromRect.top - toRect.top, 2) + Math.pow(fromRect.left - toRect.left, 2)) * options.animation;
        }
        var plugins = [];
        var defaults = {
          initializeByDefault: true
        };
        var PluginManager = {
          mount: function mount(plugin) {
            // Set default static properties
            for (var option in defaults) {
              if (defaults.hasOwnProperty(option) && !(option in plugin)) {
                plugin[option] = defaults[option];
              }
            }
            plugins.forEach(function (p) {
              if (p.pluginName === plugin.pluginName) {
                throw "Sortable: Cannot mount plugin ".concat(plugin.pluginName, " more than once");
              }
            });
            plugins.push(plugin);
          },
          pluginEvent: function pluginEvent(eventName, sortable, evt) {
            var _this = this;
            this.eventCanceled = false;
            evt.cancel = function () {
              _this.eventCanceled = true;
            };
            var eventNameGlobal = eventName + 'Global';
            plugins.forEach(function (plugin) {
              if (!sortable[plugin.pluginName]) return;
              // Fire global events if it exists in this sortable
              if (sortable[plugin.pluginName][eventNameGlobal]) {
                sortable[plugin.pluginName][eventNameGlobal](_objectSpread2({
                  sortable: sortable
                }, evt));
              }

              // Only fire plugin event if plugin is enabled in this sortable,
              // and plugin has event defined
              if (sortable.options[plugin.pluginName] && sortable[plugin.pluginName][eventName]) {
                sortable[plugin.pluginName][eventName](_objectSpread2({
                  sortable: sortable
                }, evt));
              }
            });
          },
          initializePlugins: function initializePlugins(sortable, el, defaults, options) {
            plugins.forEach(function (plugin) {
              var pluginName = plugin.pluginName;
              if (!sortable.options[pluginName] && !plugin.initializeByDefault) return;
              var initialized = new plugin(sortable, el, sortable.options);
              initialized.sortable = sortable;
              initialized.options = sortable.options;
              sortable[pluginName] = initialized;

              // Add default options from plugin
              _extends(defaults, initialized.defaults);
            });
            for (var option in sortable.options) {
              if (!sortable.options.hasOwnProperty(option)) continue;
              var modified = this.modifyOption(sortable, option, sortable.options[option]);
              if (typeof modified !== 'undefined') {
                sortable.options[option] = modified;
              }
            }
          },
          getEventProperties: function getEventProperties(name, sortable) {
            var eventProperties = {};
            plugins.forEach(function (plugin) {
              if (typeof plugin.eventProperties !== 'function') return;
              _extends(eventProperties, plugin.eventProperties.call(sortable[plugin.pluginName], name));
            });
            return eventProperties;
          },
          modifyOption: function modifyOption(sortable, name, value) {
            var modifiedValue;
            plugins.forEach(function (plugin) {
              // Plugin must exist on the Sortable
              if (!sortable[plugin.pluginName]) return;

              // If static option listener exists for this option, call in the context of the Sortable's instance of this plugin
              if (plugin.optionListeners && typeof plugin.optionListeners[name] === 'function') {
                modifiedValue = plugin.optionListeners[name].call(sortable[plugin.pluginName], value);
              }
            });
            return modifiedValue;
          }
        };
        function dispatchEvent(_ref) {
          var sortable = _ref.sortable,
            rootEl = _ref.rootEl,
            name = _ref.name,
            targetEl = _ref.targetEl,
            cloneEl = _ref.cloneEl,
            toEl = _ref.toEl,
            fromEl = _ref.fromEl,
            oldIndex = _ref.oldIndex,
            newIndex = _ref.newIndex,
            oldDraggableIndex = _ref.oldDraggableIndex,
            newDraggableIndex = _ref.newDraggableIndex,
            originalEvent = _ref.originalEvent,
            putSortable = _ref.putSortable,
            extraEventProperties = _ref.extraEventProperties;
          sortable = sortable || rootEl && rootEl[expando];
          if (!sortable) return;
          var evt,
            options = sortable.options,
            onName = 'on' + name.charAt(0).toUpperCase() + name.substr(1);
          // Support for new CustomEvent feature
          if (window.CustomEvent && !IE11OrLess && !Edge) {
            evt = new CustomEvent(name, {
              bubbles: true,
              cancelable: true
            });
          } else {
            evt = document.createEvent('Event');
            evt.initEvent(name, true, true);
          }
          evt.to = toEl || rootEl;
          evt.from = fromEl || rootEl;
          evt.item = targetEl || rootEl;
          evt.clone = cloneEl;
          evt.oldIndex = oldIndex;
          evt.newIndex = newIndex;
          evt.oldDraggableIndex = oldDraggableIndex;
          evt.newDraggableIndex = newDraggableIndex;
          evt.originalEvent = originalEvent;
          evt.pullMode = putSortable ? putSortable.lastPutMode : undefined;
          var allEventProperties = _objectSpread2(_objectSpread2({}, extraEventProperties), PluginManager.getEventProperties(name, sortable));
          for (var option in allEventProperties) {
            evt[option] = allEventProperties[option];
          }
          if (rootEl) {
            rootEl.dispatchEvent(evt);
          }
          if (options[onName]) {
            options[onName].call(sortable, evt);
          }
        }
        var _excluded = ["evt"];
        var pluginEvent = function pluginEvent(eventName, sortable) {
          var _ref = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {},
            originalEvent = _ref.evt,
            data = _objectWithoutProperties(_ref, _excluded);
          PluginManager.pluginEvent.bind(Sortable)(eventName, sortable, _objectSpread2({
            dragEl: dragEl,
            parentEl: parentEl,
            ghostEl: ghostEl,
            rootEl: rootEl,
            nextEl: nextEl,
            lastDownEl: lastDownEl,
            cloneEl: cloneEl,
            cloneHidden: cloneHidden,
            dragStarted: moved,
            putSortable: putSortable,
            activeSortable: Sortable.active,
            originalEvent: originalEvent,
            oldIndex: oldIndex,
            oldDraggableIndex: oldDraggableIndex,
            newIndex: newIndex,
            newDraggableIndex: newDraggableIndex,
            hideGhostForTarget: _hideGhostForTarget,
            unhideGhostForTarget: _unhideGhostForTarget,
            cloneNowHidden: function cloneNowHidden() {
              cloneHidden = true;
            },
            cloneNowShown: function cloneNowShown() {
              cloneHidden = false;
            },
            dispatchSortableEvent: function dispatchSortableEvent(name) {
              _dispatchEvent({
                sortable: sortable,
                name: name,
                originalEvent: originalEvent
              });
            }
          }, data));
        };
        function _dispatchEvent(info) {
          dispatchEvent(_objectSpread2({
            putSortable: putSortable,
            cloneEl: cloneEl,
            targetEl: dragEl,
            rootEl: rootEl,
            oldIndex: oldIndex,
            oldDraggableIndex: oldDraggableIndex,
            newIndex: newIndex,
            newDraggableIndex: newDraggableIndex
          }, info));
        }
        var dragEl,
          parentEl,
          ghostEl,
          rootEl,
          nextEl,
          lastDownEl,
          cloneEl,
          cloneHidden,
          oldIndex,
          newIndex,
          oldDraggableIndex,
          newDraggableIndex,
          activeGroup,
          putSortable,
          awaitingDragStarted = false,
          ignoreNextClick = false,
          sortables = [],
          tapEvt,
          touchEvt,
          lastDx,
          lastDy,
          tapDistanceLeft,
          tapDistanceTop,
          moved,
          lastTarget,
          lastDirection,
          pastFirstInvertThresh = false,
          isCircumstantialInvert = false,
          targetMoveDistance,
          // For positioning ghost absolutely
          ghostRelativeParent,
          ghostRelativeParentInitialScroll = [],
          // (left, top)

          _silent = false,
          savedInputChecked = [];

        /** @const */
        var documentExists = typeof document !== 'undefined',
          PositionGhostAbsolutely = IOS,
          CSSFloatProperty = Edge || IE11OrLess ? 'cssFloat' : 'float',
          // This will not pass for IE9, because IE9 DnD only works on anchors
          supportDraggable = documentExists && !ChromeForAndroid && !IOS && 'draggable' in document.createElement('div'),
          supportCssPointerEvents = function () {
            if (!documentExists) return;
            // false when <= IE11
            if (IE11OrLess) {
              return false;
            }
            var el = document.createElement('x');
            el.style.cssText = 'pointer-events:auto';
            return el.style.pointerEvents === 'auto';
          }(),
          _detectDirection = function _detectDirection(el, options) {
            var elCSS = css(el),
              elWidth = parseInt(elCSS.width) - parseInt(elCSS.paddingLeft) - parseInt(elCSS.paddingRight) - parseInt(elCSS.borderLeftWidth) - parseInt(elCSS.borderRightWidth),
              child1 = getChild(el, 0, options),
              child2 = getChild(el, 1, options),
              firstChildCSS = child1 && css(child1),
              secondChildCSS = child2 && css(child2),
              firstChildWidth = firstChildCSS && parseInt(firstChildCSS.marginLeft) + parseInt(firstChildCSS.marginRight) + getRect(child1).width,
              secondChildWidth = secondChildCSS && parseInt(secondChildCSS.marginLeft) + parseInt(secondChildCSS.marginRight) + getRect(child2).width;
            if (elCSS.display === 'flex') {
              return elCSS.flexDirection === 'column' || elCSS.flexDirection === 'column-reverse' ? 'vertical' : 'horizontal';
            }
            if (elCSS.display === 'grid') {
              return elCSS.gridTemplateColumns.split(' ').length <= 1 ? 'vertical' : 'horizontal';
            }
            if (child1 && firstChildCSS["float"] && firstChildCSS["float"] !== 'none') {
              var touchingSideChild2 = firstChildCSS["float"] === 'left' ? 'left' : 'right';
              return child2 && (secondChildCSS.clear === 'both' || secondChildCSS.clear === touchingSideChild2) ? 'vertical' : 'horizontal';
            }
            return child1 && (firstChildCSS.display === 'block' || firstChildCSS.display === 'flex' || firstChildCSS.display === 'table' || firstChildCSS.display === 'grid' || firstChildWidth >= elWidth && elCSS[CSSFloatProperty] === 'none' || child2 && elCSS[CSSFloatProperty] === 'none' && firstChildWidth + secondChildWidth > elWidth) ? 'vertical' : 'horizontal';
          },
          _dragElInRowColumn = function _dragElInRowColumn(dragRect, targetRect, vertical) {
            var dragElS1Opp = vertical ? dragRect.left : dragRect.top,
              dragElS2Opp = vertical ? dragRect.right : dragRect.bottom,
              dragElOppLength = vertical ? dragRect.width : dragRect.height,
              targetS1Opp = vertical ? targetRect.left : targetRect.top,
              targetS2Opp = vertical ? targetRect.right : targetRect.bottom,
              targetOppLength = vertical ? targetRect.width : targetRect.height;
            return dragElS1Opp === targetS1Opp || dragElS2Opp === targetS2Opp || dragElS1Opp + dragElOppLength / 2 === targetS1Opp + targetOppLength / 2;
          },
          /**
          * Detects first nearest empty sortable to X and Y position using emptyInsertThreshold.
          * @param  {Number} x      X position
          * @param  {Number} y      Y position
          * @return {HTMLElement}   Element of the first found nearest Sortable
          */
          _detectNearestEmptySortable = function _detectNearestEmptySortable(x, y) {
            var ret;
            sortables.some(function (sortable) {
              var threshold = sortable[expando].options.emptyInsertThreshold;
              if (!threshold || lastChild(sortable)) return;
              var rect = getRect(sortable),
                insideHorizontally = x >= rect.left - threshold && x <= rect.right + threshold,
                insideVertically = y >= rect.top - threshold && y <= rect.bottom + threshold;
              if (insideHorizontally && insideVertically) {
                return ret = sortable;
              }
            });
            return ret;
          },
          _prepareGroup = function _prepareGroup(options) {
            function toFn(value, pull) {
              return function (to, from, dragEl, evt) {
                var sameGroup = to.options.group.name && from.options.group.name && to.options.group.name === from.options.group.name;
                if (value == null && (pull || sameGroup)) {
                  // Default pull value
                  // Default pull and put value if same group
                  return true;
                } else if (value == null || value === false) {
                  return false;
                } else if (pull && value === 'clone') {
                  return value;
                } else if (typeof value === 'function') {
                  return toFn(value(to, from, dragEl, evt), pull)(to, from, dragEl, evt);
                } else {
                  var otherGroup = (pull ? to : from).options.group.name;
                  return value === true || typeof value === 'string' && value === otherGroup || value.join && value.indexOf(otherGroup) > -1;
                }
              };
            }
            var group = {};
            var originalGroup = options.group;
            if (!originalGroup || _typeof(originalGroup) != 'object') {
              originalGroup = {
                name: originalGroup
              };
            }
            group.name = originalGroup.name;
            group.checkPull = toFn(originalGroup.pull, true);
            group.checkPut = toFn(originalGroup.put);
            group.revertClone = originalGroup.revertClone;
            options.group = group;
          },
          _hideGhostForTarget = function _hideGhostForTarget() {
            if (!supportCssPointerEvents && ghostEl) {
              css(ghostEl, 'display', 'none');
            }
          },
          _unhideGhostForTarget = function _unhideGhostForTarget() {
            if (!supportCssPointerEvents && ghostEl) {
              css(ghostEl, 'display', '');
            }
          };

        // #1184 fix - Prevent click event on fallback if dragged but item not changed position
        if (documentExists && !ChromeForAndroid) {
          document.addEventListener('click', function (evt) {
            if (ignoreNextClick) {
              evt.preventDefault();
              evt.stopPropagation && evt.stopPropagation();
              evt.stopImmediatePropagation && evt.stopImmediatePropagation();
              ignoreNextClick = false;
              return false;
            }
          }, true);
        }
        var nearestEmptyInsertDetectEvent = function nearestEmptyInsertDetectEvent(evt) {
          if (dragEl) {
            evt = evt.touches ? evt.touches[0] : evt;
            var nearest = _detectNearestEmptySortable(evt.clientX, evt.clientY);
            if (nearest) {
              // Create imitation event
              var event = {};
              for (var i in evt) {
                if (evt.hasOwnProperty(i)) {
                  event[i] = evt[i];
                }
              }
              event.target = event.rootEl = nearest;
              event.preventDefault = void 0;
              event.stopPropagation = void 0;
              nearest[expando]._onDragOver(event);
            }
          }
        };
        var _checkOutsideTargetEl = function _checkOutsideTargetEl(evt) {
          if (dragEl) {
            dragEl.parentNode[expando]._isOutsideThisEl(evt.target);
          }
        };

        /**
        * @class  Sortable
        * @param  {HTMLElement}  el
        * @param  {Object}       [options]
        */
        function Sortable(el, options) {
          if (!(el && el.nodeType && el.nodeType === 1)) {
            throw "Sortable: `el` must be an HTMLElement, not ".concat({}.toString.call(el));
          }
          this.el = el; // root element
          this.options = options = _extends({}, options);

          // Export instance
          el[expando] = this;
          var defaults = {
            group: null,
            sort: true,
            disabled: false,
            store: null,
            handle: null,
            draggable: /^[uo]l$/i.test(el.nodeName) ? '>li' : '>*',
            swapThreshold: 1,
            // percentage; 0 <= x <= 1
            invertSwap: false,
            // invert always
            invertedSwapThreshold: null,
            // will be set to same as swapThreshold if default
            removeCloneOnHide: true,
            direction: function direction() {
              return _detectDirection(el, this.options);
            },
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            ignore: 'a, img',
            filter: null,
            preventOnFilter: true,
            animation: 0,
            easing: null,
            setData: function setData(dataTransfer, dragEl) {
              dataTransfer.setData('Text', dragEl.textContent);
            },
            dropBubble: false,
            dragoverBubble: false,
            dataIdAttr: 'data-id',
            delay: 0,
            delayOnTouchOnly: false,
            touchStartThreshold: (Number.parseInt ? Number : window).parseInt(window.devicePixelRatio, 10) || 1,
            forceFallback: false,
            fallbackClass: 'sortable-fallback',
            fallbackOnBody: false,
            fallbackTolerance: 0,
            fallbackOffset: {
              x: 0,
              y: 0
            },
            supportPointer: Sortable.supportPointer !== false && 'PointerEvent' in window && !Safari,
            emptyInsertThreshold: 5
          };
          PluginManager.initializePlugins(this, el, defaults);

          // Set default options
          for (var name in defaults) {
            !(name in options) && (options[name] = defaults[name]);
          }
          _prepareGroup(options);

          // Bind all private methods
          for (var fn in this) {
            if (fn.charAt(0) === '_' && typeof this[fn] === 'function') {
              this[fn] = this[fn].bind(this);
            }
          }

          // Setup drag mode
          this.nativeDraggable = options.forceFallback ? false : supportDraggable;
          if (this.nativeDraggable) {
            // Touch start threshold cannot be greater than the native dragstart threshold
            this.options.touchStartThreshold = 1;
          }

          // Bind events
          if (options.supportPointer) {
            on(el, 'pointerdown', this._onTapStart);
          } else {
            on(el, 'mousedown', this._onTapStart);
            on(el, 'touchstart', this._onTapStart);
          }
          if (this.nativeDraggable) {
            on(el, 'dragover', this);
            on(el, 'dragenter', this);
          }
          sortables.push(this.el);

          // Restore sorting
          options.store && options.store.get && this.sort(options.store.get(this) || []);

          // Add animation state manager
          _extends(this, AnimationStateManager());
        }
        Sortable.prototype = /** @lends Sortable.prototype */{
          constructor: Sortable,
          _isOutsideThisEl: function _isOutsideThisEl(target) {
            if (!this.el.contains(target) && target !== this.el) {
              lastTarget = null;
            }
          },
          _getDirection: function _getDirection(evt, target) {
            return typeof this.options.direction === 'function' ? this.options.direction.call(this, evt, target, dragEl) : this.options.direction;
          },
          _onTapStart: function _onTapStart(/** Event|TouchEvent */evt) {
            if (!evt.cancelable) return;
            var _this = this,
              el = this.el,
              options = this.options,
              preventOnFilter = options.preventOnFilter,
              type = evt.type,
              touch = evt.touches && evt.touches[0] || evt.pointerType && evt.pointerType === 'touch' && evt,
              target = (touch || evt).target,
              originalTarget = evt.target.shadowRoot && (evt.path && evt.path[0] || evt.composedPath && evt.composedPath()[0]) || target,
              filter = options.filter;
            _saveInputCheckedState(el);

            // Don't trigger start event when an element is been dragged, otherwise the evt.oldindex always wrong when set option.group.
            if (dragEl) {
              return;
            }
            if (/mousedown|pointerdown/.test(type) && evt.button !== 0 || options.disabled) {
              return; // only left button and enabled
            }

            // cancel dnd if original target is content editable
            if (originalTarget.isContentEditable) {
              return;
            }

            // Safari ignores further event handling after mousedown
            if (!this.nativeDraggable && Safari && target && target.tagName.toUpperCase() === 'SELECT') {
              return;
            }
            target = closest(target, options.draggable, el, false);
            if (target && target.animated) {
              return;
            }
            if (lastDownEl === target) {
              // Ignoring duplicate `down`
              return;
            }

            // Get the index of the dragged element within its parent
            oldIndex = index(target);
            oldDraggableIndex = index(target, options.draggable);

            // Check filter
            if (typeof filter === 'function') {
              if (filter.call(this, evt, target, this)) {
                _dispatchEvent({
                  sortable: _this,
                  rootEl: originalTarget,
                  name: 'filter',
                  targetEl: target,
                  toEl: el,
                  fromEl: el
                });
                pluginEvent('filter', _this, {
                  evt: evt
                });
                preventOnFilter && evt.cancelable && evt.preventDefault();
                return; // cancel dnd
              }
            } else if (filter) {
              filter = filter.split(',').some(function (criteria) {
                criteria = closest(originalTarget, criteria.trim(), el, false);
                if (criteria) {
                  _dispatchEvent({
                    sortable: _this,
                    rootEl: criteria,
                    name: 'filter',
                    targetEl: target,
                    fromEl: el,
                    toEl: el
                  });
                  pluginEvent('filter', _this, {
                    evt: evt
                  });
                  return true;
                }
              });
              if (filter) {
                preventOnFilter && evt.cancelable && evt.preventDefault();
                return; // cancel dnd
              }
            }
            if (options.handle && !closest(originalTarget, options.handle, el, false)) {
              return;
            }

            // Prepare `dragstart`
            this._prepareDragStart(evt, touch, target);
          },
          _prepareDragStart: function _prepareDragStart(/** Event */evt, /** Touch */touch, /** HTMLElement */target) {
            var _this = this,
              el = _this.el,
              options = _this.options,
              ownerDocument = el.ownerDocument,
              dragStartFn;
            if (target && !dragEl && target.parentNode === el) {
              var dragRect = getRect(target);
              rootEl = el;
              dragEl = target;
              parentEl = dragEl.parentNode;
              nextEl = dragEl.nextSibling;
              lastDownEl = target;
              activeGroup = options.group;
              Sortable.dragged = dragEl;
              tapEvt = {
                target: dragEl,
                clientX: (touch || evt).clientX,
                clientY: (touch || evt).clientY
              };
              tapDistanceLeft = tapEvt.clientX - dragRect.left;
              tapDistanceTop = tapEvt.clientY - dragRect.top;
              this._lastX = (touch || evt).clientX;
              this._lastY = (touch || evt).clientY;
              dragEl.style['will-change'] = 'all';
              dragStartFn = function dragStartFn() {
                pluginEvent('delayEnded', _this, {
                  evt: evt
                });
                if (Sortable.eventCanceled) {
                  _this._onDrop();
                  return;
                }
                // Delayed drag has been triggered
                // we can re-enable the events: touchmove/mousemove
                _this._disableDelayedDragEvents();
                if (!FireFox && _this.nativeDraggable) {
                  dragEl.draggable = true;
                }

                // Bind the events: dragstart/dragend
                _this._triggerDragStart(evt, touch);

                // Drag start event
                _dispatchEvent({
                  sortable: _this,
                  name: 'choose',
                  originalEvent: evt
                });

                // Chosen item
                toggleClass(dragEl, options.chosenClass, true);
              };

              // Disable "draggable"
              options.ignore.split(',').forEach(function (criteria) {
                find(dragEl, criteria.trim(), _disableDraggable);
              });
              on(ownerDocument, 'dragover', nearestEmptyInsertDetectEvent);
              on(ownerDocument, 'mousemove', nearestEmptyInsertDetectEvent);
              on(ownerDocument, 'touchmove', nearestEmptyInsertDetectEvent);
              on(ownerDocument, 'mouseup', _this._onDrop);
              on(ownerDocument, 'touchend', _this._onDrop);
              on(ownerDocument, 'touchcancel', _this._onDrop);

              // Make dragEl draggable (must be before delay for FireFox)
              if (FireFox && this.nativeDraggable) {
                this.options.touchStartThreshold = 4;
                dragEl.draggable = true;
              }
              pluginEvent('delayStart', this, {
                evt: evt
              });

              // Delay is impossible for native DnD in Edge or IE
              if (options.delay && (!options.delayOnTouchOnly || touch) && (!this.nativeDraggable || !(Edge || IE11OrLess))) {
                if (Sortable.eventCanceled) {
                  this._onDrop();
                  return;
                }
                // If the user moves the pointer or let go the click or touch
                // before the delay has been reached:
                // disable the delayed drag
                on(ownerDocument, 'mouseup', _this._disableDelayedDrag);
                on(ownerDocument, 'touchend', _this._disableDelayedDrag);
                on(ownerDocument, 'touchcancel', _this._disableDelayedDrag);
                on(ownerDocument, 'mousemove', _this._delayedDragTouchMoveHandler);
                on(ownerDocument, 'touchmove', _this._delayedDragTouchMoveHandler);
                options.supportPointer && on(ownerDocument, 'pointermove', _this._delayedDragTouchMoveHandler);
                _this._dragStartTimer = setTimeout(dragStartFn, options.delay);
              } else {
                dragStartFn();
              }
            }
          },
          _delayedDragTouchMoveHandler: function _delayedDragTouchMoveHandler(/** TouchEvent|PointerEvent **/e) {
            var touch = e.touches ? e.touches[0] : e;
            if (Math.max(Math.abs(touch.clientX - this._lastX), Math.abs(touch.clientY - this._lastY)) >= Math.floor(this.options.touchStartThreshold / (this.nativeDraggable && window.devicePixelRatio || 1))) {
              this._disableDelayedDrag();
            }
          },
          _disableDelayedDrag: function _disableDelayedDrag() {
            dragEl && _disableDraggable(dragEl);
            clearTimeout(this._dragStartTimer);
            this._disableDelayedDragEvents();
          },
          _disableDelayedDragEvents: function _disableDelayedDragEvents() {
            var ownerDocument = this.el.ownerDocument;
            off(ownerDocument, 'mouseup', this._disableDelayedDrag);
            off(ownerDocument, 'touchend', this._disableDelayedDrag);
            off(ownerDocument, 'touchcancel', this._disableDelayedDrag);
            off(ownerDocument, 'mousemove', this._delayedDragTouchMoveHandler);
            off(ownerDocument, 'touchmove', this._delayedDragTouchMoveHandler);
            off(ownerDocument, 'pointermove', this._delayedDragTouchMoveHandler);
          },
          _triggerDragStart: function _triggerDragStart(/** Event */evt, /** Touch */touch) {
            touch = touch || evt.pointerType == 'touch' && evt;
            if (!this.nativeDraggable || touch) {
              if (this.options.supportPointer) {
                on(document, 'pointermove', this._onTouchMove);
              } else if (touch) {
                on(document, 'touchmove', this._onTouchMove);
              } else {
                on(document, 'mousemove', this._onTouchMove);
              }
            } else {
              on(dragEl, 'dragend', this);
              on(rootEl, 'dragstart', this._onDragStart);
            }
            try {
              if (document.selection) {
                // Timeout neccessary for IE9
                _nextTick(function () {
                  document.selection.empty();
                });
              } else {
                window.getSelection().removeAllRanges();
              }
            } catch (err) {}
          },
          _dragStarted: function _dragStarted(fallback, evt) {
            awaitingDragStarted = false;
            if (rootEl && dragEl) {
              pluginEvent('dragStarted', this, {
                evt: evt
              });
              if (this.nativeDraggable) {
                on(document, 'dragover', _checkOutsideTargetEl);
              }
              var options = this.options;

              // Apply effect
              !fallback && toggleClass(dragEl, options.dragClass, false);
              toggleClass(dragEl, options.ghostClass, true);
              Sortable.active = this;
              fallback && this._appendGhost();

              // Drag start event
              _dispatchEvent({
                sortable: this,
                name: 'start',
                originalEvent: evt
              });
            } else {
              this._nulling();
            }
          },
          _emulateDragOver: function _emulateDragOver() {
            if (touchEvt) {
              this._lastX = touchEvt.clientX;
              this._lastY = touchEvt.clientY;
              _hideGhostForTarget();
              var target = document.elementFromPoint(touchEvt.clientX, touchEvt.clientY);
              var parent = target;
              while (target && target.shadowRoot) {
                target = target.shadowRoot.elementFromPoint(touchEvt.clientX, touchEvt.clientY);
                if (target === parent) break;
                parent = target;
              }
              dragEl.parentNode[expando]._isOutsideThisEl(target);
              if (parent) {
                do {
                  if (parent[expando]) {
                    var inserted = void 0;
                    inserted = parent[expando]._onDragOver({
                      clientX: touchEvt.clientX,
                      clientY: touchEvt.clientY,
                      target: target,
                      rootEl: parent
                    });
                    if (inserted && !this.options.dragoverBubble) {
                      break;
                    }
                  }
                  target = parent; // store last element
                }
                /* jshint boss:true */ while (parent = parent.parentNode);
              }
              _unhideGhostForTarget();
            }
          },
          _onTouchMove: function _onTouchMove(/**TouchEvent*/evt) {
            if (tapEvt) {
              var options = this.options,
                fallbackTolerance = options.fallbackTolerance,
                fallbackOffset = options.fallbackOffset,
                touch = evt.touches ? evt.touches[0] : evt,
                ghostMatrix = ghostEl && matrix(ghostEl, true),
                scaleX = ghostEl && ghostMatrix && ghostMatrix.a,
                scaleY = ghostEl && ghostMatrix && ghostMatrix.d,
                relativeScrollOffset = PositionGhostAbsolutely && ghostRelativeParent && getRelativeScrollOffset(ghostRelativeParent),
                dx = (touch.clientX - tapEvt.clientX + fallbackOffset.x) / (scaleX || 1) + (relativeScrollOffset ? relativeScrollOffset[0] - ghostRelativeParentInitialScroll[0] : 0) / (scaleX || 1),
                dy = (touch.clientY - tapEvt.clientY + fallbackOffset.y) / (scaleY || 1) + (relativeScrollOffset ? relativeScrollOffset[1] - ghostRelativeParentInitialScroll[1] : 0) / (scaleY || 1);

              // only set the status to dragging, when we are actually dragging
              if (!Sortable.active && !awaitingDragStarted) {
                if (fallbackTolerance && Math.max(Math.abs(touch.clientX - this._lastX), Math.abs(touch.clientY - this._lastY)) < fallbackTolerance) {
                  return;
                }
                this._onDragStart(evt, true);
              }
              if (ghostEl) {
                if (ghostMatrix) {
                  ghostMatrix.e += dx - (lastDx || 0);
                  ghostMatrix.f += dy - (lastDy || 0);
                } else {
                  ghostMatrix = {
                    a: 1,
                    b: 0,
                    c: 0,
                    d: 1,
                    e: dx,
                    f: dy
                  };
                }
                var cssMatrix = "matrix(".concat(ghostMatrix.a, ",").concat(ghostMatrix.b, ",").concat(ghostMatrix.c, ",").concat(ghostMatrix.d, ",").concat(ghostMatrix.e, ",").concat(ghostMatrix.f, ")");
                css(ghostEl, 'webkitTransform', cssMatrix);
                css(ghostEl, 'mozTransform', cssMatrix);
                css(ghostEl, 'msTransform', cssMatrix);
                css(ghostEl, 'transform', cssMatrix);
                lastDx = dx;
                lastDy = dy;
                touchEvt = touch;
              }
              evt.cancelable && evt.preventDefault();
            }
          },
          _appendGhost: function _appendGhost() {
            // Bug if using scale(): https://stackoverflow.com/questions/2637058
            // Not being adjusted for
            if (!ghostEl) {
              var container = this.options.fallbackOnBody ? document.body : rootEl,
                rect = getRect(dragEl, true, PositionGhostAbsolutely, true, container),
                options = this.options;

              // Position absolutely
              if (PositionGhostAbsolutely) {
                // Get relatively positioned parent
                ghostRelativeParent = container;
                while (css(ghostRelativeParent, 'position') === 'static' && css(ghostRelativeParent, 'transform') === 'none' && ghostRelativeParent !== document) {
                  ghostRelativeParent = ghostRelativeParent.parentNode;
                }
                if (ghostRelativeParent !== document.body && ghostRelativeParent !== document.documentElement) {
                  if (ghostRelativeParent === document) ghostRelativeParent = getWindowScrollingElement();
                  rect.top += ghostRelativeParent.scrollTop;
                  rect.left += ghostRelativeParent.scrollLeft;
                } else {
                  ghostRelativeParent = getWindowScrollingElement();
                }
                ghostRelativeParentInitialScroll = getRelativeScrollOffset(ghostRelativeParent);
              }
              ghostEl = dragEl.cloneNode(true);
              toggleClass(ghostEl, options.ghostClass, false);
              toggleClass(ghostEl, options.fallbackClass, true);
              toggleClass(ghostEl, options.dragClass, true);
              css(ghostEl, 'transition', '');
              css(ghostEl, 'transform', '');
              css(ghostEl, 'box-sizing', 'border-box');
              css(ghostEl, 'margin', 0);
              css(ghostEl, 'top', rect.top);
              css(ghostEl, 'left', rect.left);
              css(ghostEl, 'width', rect.width);
              css(ghostEl, 'height', rect.height);
              css(ghostEl, 'opacity', '0.8');
              css(ghostEl, 'position', PositionGhostAbsolutely ? 'absolute' : 'fixed');
              css(ghostEl, 'zIndex', '100000');
              css(ghostEl, 'pointerEvents', 'none');
              Sortable.ghost = ghostEl;
              container.appendChild(ghostEl);

              // Set transform-origin
              css(ghostEl, 'transform-origin', tapDistanceLeft / parseInt(ghostEl.style.width) * 100 + '% ' + tapDistanceTop / parseInt(ghostEl.style.height) * 100 + '%');
            }
          },
          _onDragStart: function _onDragStart(/**Event*/evt, /**boolean*/fallback) {
            var _this = this;
            var dataTransfer = evt.dataTransfer;
            var options = _this.options;
            pluginEvent('dragStart', this, {
              evt: evt
            });
            if (Sortable.eventCanceled) {
              this._onDrop();
              return;
            }
            pluginEvent('setupClone', this);
            if (!Sortable.eventCanceled) {
              cloneEl = clone(dragEl);
              cloneEl.removeAttribute("id");
              cloneEl.draggable = false;
              cloneEl.style['will-change'] = '';
              this._hideClone();
              toggleClass(cloneEl, this.options.chosenClass, false);
              Sortable.clone = cloneEl;
            }

            // #1143: IFrame support workaround
            _this.cloneId = _nextTick(function () {
              pluginEvent('clone', _this);
              if (Sortable.eventCanceled) return;
              if (!_this.options.removeCloneOnHide) {
                rootEl.insertBefore(cloneEl, dragEl);
              }
              _this._hideClone();
              _dispatchEvent({
                sortable: _this,
                name: 'clone'
              });
            });
            !fallback && toggleClass(dragEl, options.dragClass, true);

            // Set proper drop events
            if (fallback) {
              ignoreNextClick = true;
              _this._loopId = setInterval(_this._emulateDragOver, 50);
            } else {
              // Undo what was set in _prepareDragStart before drag started
              off(document, 'mouseup', _this._onDrop);
              off(document, 'touchend', _this._onDrop);
              off(document, 'touchcancel', _this._onDrop);
              if (dataTransfer) {
                dataTransfer.effectAllowed = 'move';
                options.setData && options.setData.call(_this, dataTransfer, dragEl);
              }
              on(document, 'drop', _this);

              // #1276 fix:
              css(dragEl, 'transform', 'translateZ(0)');
            }
            awaitingDragStarted = true;
            _this._dragStartId = _nextTick(_this._dragStarted.bind(_this, fallback, evt));
            on(document, 'selectstart', _this);
            moved = true;
            if (Safari) {
              css(document.body, 'user-select', 'none');
            }
          },
          // Returns true - if no further action is needed (either inserted or another condition)
          _onDragOver: function _onDragOver(/**Event*/evt) {
            var el = this.el,
              target = evt.target,
              dragRect,
              targetRect,
              revert,
              options = this.options,
              group = options.group,
              activeSortable = Sortable.active,
              isOwner = activeGroup === group,
              canSort = options.sort,
              fromSortable = putSortable || activeSortable,
              vertical,
              _this = this,
              completedFired = false;
            if (_silent) return;
            function dragOverEvent(name, extra) {
              pluginEvent(name, _this, _objectSpread2({
                evt: evt,
                isOwner: isOwner,
                axis: vertical ? 'vertical' : 'horizontal',
                revert: revert,
                dragRect: dragRect,
                targetRect: targetRect,
                canSort: canSort,
                fromSortable: fromSortable,
                target: target,
                completed: completed,
                onMove: function onMove(target, after) {
                  return _onMove(rootEl, el, dragEl, dragRect, target, getRect(target), evt, after);
                },
                changed: changed
              }, extra));
            }

            // Capture animation state
            function capture() {
              dragOverEvent('dragOverAnimationCapture');
              _this.captureAnimationState();
              if (_this !== fromSortable) {
                fromSortable.captureAnimationState();
              }
            }

            // Return invocation when dragEl is inserted (or completed)
            function completed(insertion) {
              dragOverEvent('dragOverCompleted', {
                insertion: insertion
              });
              if (insertion) {
                // Clones must be hidden before folding animation to capture dragRectAbsolute properly
                if (isOwner) {
                  activeSortable._hideClone();
                } else {
                  activeSortable._showClone(_this);
                }
                if (_this !== fromSortable) {
                  // Set ghost class to new sortable's ghost class
                  toggleClass(dragEl, putSortable ? putSortable.options.ghostClass : activeSortable.options.ghostClass, false);
                  toggleClass(dragEl, options.ghostClass, true);
                }
                if (putSortable !== _this && _this !== Sortable.active) {
                  putSortable = _this;
                } else if (_this === Sortable.active && putSortable) {
                  putSortable = null;
                }

                // Animation
                if (fromSortable === _this) {
                  _this._ignoreWhileAnimating = target;
                }
                _this.animateAll(function () {
                  dragOverEvent('dragOverAnimationComplete');
                  _this._ignoreWhileAnimating = null;
                });
                if (_this !== fromSortable) {
                  fromSortable.animateAll();
                  fromSortable._ignoreWhileAnimating = null;
                }
              }

              // Null lastTarget if it is not inside a previously swapped element
              if (target === dragEl && !dragEl.animated || target === el && !target.animated) {
                lastTarget = null;
              }

              // no bubbling and not fallback
              if (!options.dragoverBubble && !evt.rootEl && target !== document) {
                dragEl.parentNode[expando]._isOutsideThisEl(evt.target);

                // Do not detect for empty insert if already inserted
                !insertion && nearestEmptyInsertDetectEvent(evt);
              }
              !options.dragoverBubble && evt.stopPropagation && evt.stopPropagation();
              return completedFired = true;
            }

            // Call when dragEl has been inserted
            function changed() {
              newIndex = index(dragEl);
              newDraggableIndex = index(dragEl, options.draggable);
              _dispatchEvent({
                sortable: _this,
                name: 'change',
                toEl: el,
                newIndex: newIndex,
                newDraggableIndex: newDraggableIndex,
                originalEvent: evt
              });
            }
            if (evt.preventDefault !== void 0) {
              evt.cancelable && evt.preventDefault();
            }
            target = closest(target, options.draggable, el, true);
            dragOverEvent('dragOver');
            if (Sortable.eventCanceled) return completedFired;
            if (dragEl.contains(evt.target) || target.animated && target.animatingX && target.animatingY || _this._ignoreWhileAnimating === target) {
              return completed(false);
            }
            ignoreNextClick = false;
            if (activeSortable && !options.disabled && (isOwner ? canSort || (revert = parentEl !== rootEl) // Reverting item into the original list
            : putSortable === this || (this.lastPutMode = activeGroup.checkPull(this, activeSortable, dragEl, evt)) && group.checkPut(this, activeSortable, dragEl, evt))) {
              vertical = this._getDirection(evt, target) === 'vertical';
              dragRect = getRect(dragEl);
              dragOverEvent('dragOverValid');
              if (Sortable.eventCanceled) return completedFired;
              if (revert) {
                parentEl = rootEl; // actualization
                capture();
                this._hideClone();
                dragOverEvent('revert');
                if (!Sortable.eventCanceled) {
                  if (nextEl) {
                    rootEl.insertBefore(dragEl, nextEl);
                  } else {
                    rootEl.appendChild(dragEl);
                  }
                }
                return completed(true);
              }
              var elLastChild = lastChild(el, options.draggable);
              if (!elLastChild || _ghostIsLast(evt, vertical, this) && !elLastChild.animated) {
                // Insert to end of list

                // If already at end of list: Do not insert
                if (elLastChild === dragEl) {
                  return completed(false);
                }

                // if there is a last element, it is the target
                if (elLastChild && el === evt.target) {
                  target = elLastChild;
                }
                if (target) {
                  targetRect = getRect(target);
                }
                if (_onMove(rootEl, el, dragEl, dragRect, target, targetRect, evt, !!target) !== false) {
                  capture();
                  if (elLastChild && elLastChild.nextSibling) {
                    // the last draggable element is not the last node
                    el.insertBefore(dragEl, elLastChild.nextSibling);
                  } else {
                    el.appendChild(dragEl);
                  }
                  parentEl = el; // actualization

                  changed();
                  return completed(true);
                }
              } else if (elLastChild && _ghostIsFirst(evt, vertical, this)) {
                // Insert to start of list
                var firstChild = getChild(el, 0, options, true);
                if (firstChild === dragEl) {
                  return completed(false);
                }
                target = firstChild;
                targetRect = getRect(target);
                if (_onMove(rootEl, el, dragEl, dragRect, target, targetRect, evt, false) !== false) {
                  capture();
                  el.insertBefore(dragEl, firstChild);
                  parentEl = el; // actualization

                  changed();
                  return completed(true);
                }
              } else if (target.parentNode === el) {
                targetRect = getRect(target);
                var direction = 0,
                  targetBeforeFirstSwap,
                  differentLevel = dragEl.parentNode !== el,
                  differentRowCol = !_dragElInRowColumn(dragEl.animated && dragEl.toRect || dragRect, target.animated && target.toRect || targetRect, vertical),
                  side1 = vertical ? 'top' : 'left',
                  scrolledPastTop = isScrolledPast(target, 'top', 'top') || isScrolledPast(dragEl, 'top', 'top'),
                  scrollBefore = scrolledPastTop ? scrolledPastTop.scrollTop : void 0;
                if (lastTarget !== target) {
                  targetBeforeFirstSwap = targetRect[side1];
                  pastFirstInvertThresh = false;
                  isCircumstantialInvert = !differentRowCol && options.invertSwap || differentLevel;
                }
                direction = _getSwapDirection(evt, target, targetRect, vertical, differentRowCol ? 1 : options.swapThreshold, options.invertedSwapThreshold == null ? options.swapThreshold : options.invertedSwapThreshold, isCircumstantialInvert, lastTarget === target);
                var sibling;
                if (direction !== 0) {
                  // Check if target is beside dragEl in respective direction (ignoring hidden elements)
                  var dragIndex = index(dragEl);
                  do {
                    dragIndex -= direction;
                    sibling = parentEl.children[dragIndex];
                  } while (sibling && (css(sibling, 'display') === 'none' || sibling === ghostEl));
                }
                // If dragEl is already beside target: Do not insert
                if (direction === 0 || sibling === target) {
                  return completed(false);
                }
                lastTarget = target;
                lastDirection = direction;
                var nextSibling = target.nextElementSibling,
                  after = false;
                after = direction === 1;
                var moveVector = _onMove(rootEl, el, dragEl, dragRect, target, targetRect, evt, after);
                if (moveVector !== false) {
                  if (moveVector === 1 || moveVector === -1) {
                    after = moveVector === 1;
                  }
                  _silent = true;
                  setTimeout(_unsilent, 30);
                  capture();
                  if (after && !nextSibling) {
                    el.appendChild(dragEl);
                  } else {
                    target.parentNode.insertBefore(dragEl, after ? nextSibling : target);
                  }

                  // Undo chrome's scroll adjustment (has no effect on other browsers)
                  if (scrolledPastTop) {
                    scrollBy(scrolledPastTop, 0, scrollBefore - scrolledPastTop.scrollTop);
                  }
                  parentEl = dragEl.parentNode; // actualization

                  // must be done before animation
                  if (targetBeforeFirstSwap !== undefined && !isCircumstantialInvert) {
                    targetMoveDistance = Math.abs(targetBeforeFirstSwap - getRect(target)[side1]);
                  }
                  changed();
                  return completed(true);
                }
              }
              if (el.contains(dragEl)) {
                return completed(false);
              }
            }
            return false;
          },
          _ignoreWhileAnimating: null,
          _offMoveEvents: function _offMoveEvents() {
            off(document, 'mousemove', this._onTouchMove);
            off(document, 'touchmove', this._onTouchMove);
            off(document, 'pointermove', this._onTouchMove);
            off(document, 'dragover', nearestEmptyInsertDetectEvent);
            off(document, 'mousemove', nearestEmptyInsertDetectEvent);
            off(document, 'touchmove', nearestEmptyInsertDetectEvent);
          },
          _offUpEvents: function _offUpEvents() {
            var ownerDocument = this.el.ownerDocument;
            off(ownerDocument, 'mouseup', this._onDrop);
            off(ownerDocument, 'touchend', this._onDrop);
            off(ownerDocument, 'pointerup', this._onDrop);
            off(ownerDocument, 'touchcancel', this._onDrop);
            off(document, 'selectstart', this);
          },
          _onDrop: function _onDrop(/**Event*/evt) {
            var el = this.el,
              options = this.options;

            // Get the index of the dragged element within its parent
            newIndex = index(dragEl);
            newDraggableIndex = index(dragEl, options.draggable);
            pluginEvent('drop', this, {
              evt: evt
            });
            parentEl = dragEl && dragEl.parentNode;

            // Get again after plugin event
            newIndex = index(dragEl);
            newDraggableIndex = index(dragEl, options.draggable);
            if (Sortable.eventCanceled) {
              this._nulling();
              return;
            }
            awaitingDragStarted = false;
            isCircumstantialInvert = false;
            pastFirstInvertThresh = false;
            clearInterval(this._loopId);
            clearTimeout(this._dragStartTimer);
            _cancelNextTick(this.cloneId);
            _cancelNextTick(this._dragStartId);

            // Unbind events
            if (this.nativeDraggable) {
              off(document, 'drop', this);
              off(el, 'dragstart', this._onDragStart);
            }
            this._offMoveEvents();
            this._offUpEvents();
            if (Safari) {
              css(document.body, 'user-select', '');
            }
            css(dragEl, 'transform', '');
            if (evt) {
              if (moved) {
                evt.cancelable && evt.preventDefault();
                !options.dropBubble && evt.stopPropagation();
              }
              ghostEl && ghostEl.parentNode && ghostEl.parentNode.removeChild(ghostEl);
              if (rootEl === parentEl || putSortable && putSortable.lastPutMode !== 'clone') {
                // Remove clone(s)
                cloneEl && cloneEl.parentNode && cloneEl.parentNode.removeChild(cloneEl);
              }
              if (dragEl) {
                if (this.nativeDraggable) {
                  off(dragEl, 'dragend', this);
                }
                _disableDraggable(dragEl);
                dragEl.style['will-change'] = '';

                // Remove classes
                // ghostClass is added in dragStarted
                if (moved && !awaitingDragStarted) {
                  toggleClass(dragEl, putSortable ? putSortable.options.ghostClass : this.options.ghostClass, false);
                }
                toggleClass(dragEl, this.options.chosenClass, false);

                // Drag stop event
                _dispatchEvent({
                  sortable: this,
                  name: 'unchoose',
                  toEl: parentEl,
                  newIndex: null,
                  newDraggableIndex: null,
                  originalEvent: evt
                });
                if (rootEl !== parentEl) {
                  if (newIndex >= 0) {
                    // Add event
                    _dispatchEvent({
                      rootEl: parentEl,
                      name: 'add',
                      toEl: parentEl,
                      fromEl: rootEl,
                      originalEvent: evt
                    });

                    // Remove event
                    _dispatchEvent({
                      sortable: this,
                      name: 'remove',
                      toEl: parentEl,
                      originalEvent: evt
                    });

                    // drag from one list and drop into another
                    _dispatchEvent({
                      rootEl: parentEl,
                      name: 'sort',
                      toEl: parentEl,
                      fromEl: rootEl,
                      originalEvent: evt
                    });
                    _dispatchEvent({
                      sortable: this,
                      name: 'sort',
                      toEl: parentEl,
                      originalEvent: evt
                    });
                  }
                  putSortable && putSortable.save();
                } else {
                  if (newIndex !== oldIndex) {
                    if (newIndex >= 0) {
                      // drag & drop within the same list
                      _dispatchEvent({
                        sortable: this,
                        name: 'update',
                        toEl: parentEl,
                        originalEvent: evt
                      });
                      _dispatchEvent({
                        sortable: this,
                        name: 'sort',
                        toEl: parentEl,
                        originalEvent: evt
                      });
                    }
                  }
                }
                if (Sortable.active) {
                  /* jshint eqnull:true */
                  if (newIndex == null || newIndex === -1) {
                    newIndex = oldIndex;
                    newDraggableIndex = oldDraggableIndex;
                  }
                  _dispatchEvent({
                    sortable: this,
                    name: 'end',
                    toEl: parentEl,
                    originalEvent: evt
                  });

                  // Save sorting
                  this.save();
                }
              }
            }
            this._nulling();
          },
          _nulling: function _nulling() {
            pluginEvent('nulling', this);
            rootEl = dragEl = parentEl = ghostEl = nextEl = cloneEl = lastDownEl = cloneHidden = tapEvt = touchEvt = moved = newIndex = newDraggableIndex = oldIndex = oldDraggableIndex = lastTarget = lastDirection = putSortable = activeGroup = Sortable.dragged = Sortable.ghost = Sortable.clone = Sortable.active = null;
            savedInputChecked.forEach(function (el) {
              el.checked = true;
            });
            savedInputChecked.length = lastDx = lastDy = 0;
          },
          handleEvent: function handleEvent(/**Event*/evt) {
            switch (evt.type) {
              case 'drop':
              case 'dragend':
                this._onDrop(evt);
                break;
              case 'dragenter':
              case 'dragover':
                if (dragEl) {
                  this._onDragOver(evt);
                  _globalDragOver(evt);
                }
                break;
              case 'selectstart':
                evt.preventDefault();
                break;
            }
          },
          /**
          * Serializes the item into an array of string.
          * @returns {String[]}
          */
          toArray: function toArray() {
            var order = [],
              el,
              children = this.el.children,
              i = 0,
              n = children.length,
              options = this.options;
            for (; i < n; i++) {
              el = children[i];
              if (closest(el, options.draggable, this.el, false)) {
                order.push(el.getAttribute(options.dataIdAttr) || _generateId(el));
              }
            }
            return order;
          },
          /**
          * Sorts the elements according to the array.
          * @param  {String[]}  order  order of the items
          */
          sort: function sort(order, useAnimation) {
            var items = {},
              rootEl = this.el;
            this.toArray().forEach(function (id, i) {
              var el = rootEl.children[i];
              if (closest(el, this.options.draggable, rootEl, false)) {
                items[id] = el;
              }
            }, this);
            useAnimation && this.captureAnimationState();
            order.forEach(function (id) {
              if (items[id]) {
                rootEl.removeChild(items[id]);
                rootEl.appendChild(items[id]);
              }
            });
            useAnimation && this.animateAll();
          },
          /**
          * Save the current sorting
          */
          save: function save() {
            var store = this.options.store;
            store && store.set && store.set(this);
          },
          /**
          * For each element in the set, get the first element that matches the selector by testing the element itself and traversing up through its ancestors in the DOM tree.
          * @param   {HTMLElement}  el
          * @param   {String}       [selector]  default: `options.draggable`
          * @returns {HTMLElement|null}
          */
          closest: function closest$1(el, selector) {
            return closest(el, selector || this.options.draggable, this.el, false);
          },
          /**
          * Set/get option
          * @param   {string} name
          * @param   {*}      [value]
          * @returns {*}
          */
          option: function option(name, value) {
            var options = this.options;
            if (value === void 0) {
              return options[name];
            } else {
              var modifiedValue = PluginManager.modifyOption(this, name, value);
              if (typeof modifiedValue !== 'undefined') {
                options[name] = modifiedValue;
              } else {
                options[name] = value;
              }
              if (name === 'group') {
                _prepareGroup(options);
              }
            }
          },
          /**
          * Destroy
          */
          destroy: function destroy() {
            pluginEvent('destroy', this);
            var el = this.el;
            el[expando] = null;
            off(el, 'mousedown', this._onTapStart);
            off(el, 'touchstart', this._onTapStart);
            off(el, 'pointerdown', this._onTapStart);
            if (this.nativeDraggable) {
              off(el, 'dragover', this);
              off(el, 'dragenter', this);
            }
            // Remove draggable attributes
            Array.prototype.forEach.call(el.querySelectorAll('[draggable]'), function (el) {
              el.removeAttribute('draggable');
            });
            this._onDrop();
            this._disableDelayedDragEvents();
            sortables.splice(sortables.indexOf(this.el), 1);
            this.el = el = null;
          },
          _hideClone: function _hideClone() {
            if (!cloneHidden) {
              pluginEvent('hideClone', this);
              if (Sortable.eventCanceled) return;
              css(cloneEl, 'display', 'none');
              if (this.options.removeCloneOnHide && cloneEl.parentNode) {
                cloneEl.parentNode.removeChild(cloneEl);
              }
              cloneHidden = true;
            }
          },
          _showClone: function _showClone(putSortable) {
            if (putSortable.lastPutMode !== 'clone') {
              this._hideClone();
              return;
            }
            if (cloneHidden) {
              pluginEvent('showClone', this);
              if (Sortable.eventCanceled) return;

              // show clone at dragEl or original position
              if (dragEl.parentNode == rootEl && !this.options.group.revertClone) {
                rootEl.insertBefore(cloneEl, dragEl);
              } else if (nextEl) {
                rootEl.insertBefore(cloneEl, nextEl);
              } else {
                rootEl.appendChild(cloneEl);
              }
              if (this.options.group.revertClone) {
                this.animate(dragEl, cloneEl);
              }
              css(cloneEl, 'display', '');
              cloneHidden = false;
            }
          }
        };
        function _globalDragOver(/**Event*/evt) {
          if (evt.dataTransfer) {
            evt.dataTransfer.dropEffect = 'move';
          }
          evt.cancelable && evt.preventDefault();
        }
        function _onMove(fromEl, toEl, dragEl, dragRect, targetEl, targetRect, originalEvent, willInsertAfter) {
          var evt,
            sortable = fromEl[expando],
            onMoveFn = sortable.options.onMove,
            retVal;
          // Support for new CustomEvent feature
          if (window.CustomEvent && !IE11OrLess && !Edge) {
            evt = new CustomEvent('move', {
              bubbles: true,
              cancelable: true
            });
          } else {
            evt = document.createEvent('Event');
            evt.initEvent('move', true, true);
          }
          evt.to = toEl;
          evt.from = fromEl;
          evt.dragged = dragEl;
          evt.draggedRect = dragRect;
          evt.related = targetEl || toEl;
          evt.relatedRect = targetRect || getRect(toEl);
          evt.willInsertAfter = willInsertAfter;
          evt.originalEvent = originalEvent;
          fromEl.dispatchEvent(evt);
          if (onMoveFn) {
            retVal = onMoveFn.call(sortable, evt, originalEvent);
          }
          return retVal;
        }
        function _disableDraggable(el) {
          el.draggable = false;
        }
        function _unsilent() {
          _silent = false;
        }
        function _ghostIsFirst(evt, vertical, sortable) {
          var firstElRect = getRect(getChild(sortable.el, 0, sortable.options, true));
          var childContainingRect = getChildContainingRectFromElement(sortable.el, sortable.options, ghostEl);
          var spacer = 10;
          return vertical ? evt.clientX < childContainingRect.left - spacer || evt.clientY < firstElRect.top && evt.clientX < firstElRect.right : evt.clientY < childContainingRect.top - spacer || evt.clientY < firstElRect.bottom && evt.clientX < firstElRect.left;
        }
        function _ghostIsLast(evt, vertical, sortable) {
          var lastElRect = getRect(lastChild(sortable.el, sortable.options.draggable));
          var childContainingRect = getChildContainingRectFromElement(sortable.el, sortable.options, ghostEl);
          var spacer = 10;
          return vertical ? evt.clientX > childContainingRect.right + spacer || evt.clientY > lastElRect.bottom && evt.clientX > lastElRect.left : evt.clientY > childContainingRect.bottom + spacer || evt.clientX > lastElRect.right && evt.clientY > lastElRect.top;
        }
        function _getSwapDirection(evt, target, targetRect, vertical, swapThreshold, invertedSwapThreshold, invertSwap, isLastTarget) {
          var mouseOnAxis = vertical ? evt.clientY : evt.clientX,
            targetLength = vertical ? targetRect.height : targetRect.width,
            targetS1 = vertical ? targetRect.top : targetRect.left,
            targetS2 = vertical ? targetRect.bottom : targetRect.right,
            invert = false;
          if (!invertSwap) {
            // Never invert or create dragEl shadow when target movemenet causes mouse to move past the end of regular swapThreshold
            if (isLastTarget && targetMoveDistance < targetLength * swapThreshold) {
              // multiplied only by swapThreshold because mouse will already be inside target by (1 - threshold) * targetLength / 2
              // check if past first invert threshold on side opposite of lastDirection
              if (!pastFirstInvertThresh && (lastDirection === 1 ? mouseOnAxis > targetS1 + targetLength * invertedSwapThreshold / 2 : mouseOnAxis < targetS2 - targetLength * invertedSwapThreshold / 2)) {
                // past first invert threshold, do not restrict inverted threshold to dragEl shadow
                pastFirstInvertThresh = true;
              }
              if (!pastFirstInvertThresh) {
                // dragEl shadow (target move distance shadow)
                if (lastDirection === 1 ? mouseOnAxis < targetS1 + targetMoveDistance // over dragEl shadow
                : mouseOnAxis > targetS2 - targetMoveDistance) {
                  return -lastDirection;
                }
              } else {
                invert = true;
              }
            } else {
              // Regular
              if (mouseOnAxis > targetS1 + targetLength * (1 - swapThreshold) / 2 && mouseOnAxis < targetS2 - targetLength * (1 - swapThreshold) / 2) {
                return _getInsertDirection(target);
              }
            }
          }
          invert = invert || invertSwap;
          if (invert) {
            // Invert of regular
            if (mouseOnAxis < targetS1 + targetLength * invertedSwapThreshold / 2 || mouseOnAxis > targetS2 - targetLength * invertedSwapThreshold / 2) {
              return mouseOnAxis > targetS1 + targetLength / 2 ? 1 : -1;
            }
          }
          return 0;
        }

        /**
        * Gets the direction dragEl must be swapped relative to target in order to make it
        * seem that dragEl has been "inserted" into that element's position
        * @param  {HTMLElement} target       The target whose position dragEl is being inserted at
        * @return {Number}                   Direction dragEl must be swapped
        */
        function _getInsertDirection(target) {
          if (index(dragEl) < index(target)) {
            return 1;
          } else {
            return -1;
          }
        }

        /**
        * Generate id
        * @param   {HTMLElement} el
        * @returns {String}
        * @private
        */
        function _generateId(el) {
          var str = el.tagName + el.className + el.src + el.href + el.textContent,
            i = str.length,
            sum = 0;
          while (i--) {
            sum += str.charCodeAt(i);
          }
          return sum.toString(36);
        }
        function _saveInputCheckedState(root) {
          savedInputChecked.length = 0;
          var inputs = root.getElementsByTagName('input');
          var idx = inputs.length;
          while (idx--) {
            var el = inputs[idx];
            el.checked && savedInputChecked.push(el);
          }
        }
        function _nextTick(fn) {
          return setTimeout(fn, 0);
        }
        function _cancelNextTick(id) {
          return clearTimeout(id);
        }

        // Fixed #973:
        if (documentExists) {
          on(document, 'touchmove', function (evt) {
            if ((Sortable.active || awaitingDragStarted) && evt.cancelable) {
              evt.preventDefault();
            }
          });
        }

        // Export utils
        Sortable.utils = {
          on: on,
          off: off,
          css: css,
          find: find,
          is: function is(el, selector) {
            return !!closest(el, selector, el, false);
          },
          extend: extend,
          throttle: throttle,
          closest: closest,
          toggleClass: toggleClass,
          clone: clone,
          index: index,
          nextTick: _nextTick,
          cancelNextTick: _cancelNextTick,
          detectDirection: _detectDirection,
          getChild: getChild
        };

        /**
        * Get the Sortable instance of an element
        * @param  {HTMLElement} element The element
        * @return {Sortable|undefined}         The instance of Sortable
        */
        Sortable.get = function (element) {
          return element[expando];
        };

        /**
        * Mount a plugin to Sortable
        * @param  {...SortablePlugin|SortablePlugin[]} plugins       Plugins being mounted
        */
        Sortable.mount = function () {
          for (var _len = arguments.length, plugins = new Array(_len), _key = 0; _key < _len; _key++) {
            plugins[_key] = arguments[_key];
          }
          if (plugins[0].constructor === Array) plugins = plugins[0];
          plugins.forEach(function (plugin) {
            if (!plugin.prototype || !plugin.prototype.constructor) {
              throw "Sortable: Mounted plugin must be a constructor function, not ".concat({}.toString.call(plugin));
            }
            if (plugin.utils) Sortable.utils = _objectSpread2(_objectSpread2({}, Sortable.utils), plugin.utils);
            PluginManager.mount(plugin);
          });
        };

        /**
        * Create sortable instance
        * @param {HTMLElement}  el
        * @param {Object}      [options]
        */
        Sortable.create = function (el, options) {
          return new Sortable(el, options);
        };

        // Export
        Sortable.version = version;
        var autoScrolls = [],
          scrollEl,
          scrollRootEl,
          scrolling = false,
          lastAutoScrollX,
          lastAutoScrollY,
          touchEvt$1,
          pointerElemChangedInterval;
        function AutoScrollPlugin() {
          function AutoScroll() {
            this.defaults = {
              scroll: true,
              forceAutoScrollFallback: false,
              scrollSensitivity: 30,
              scrollSpeed: 10,
              bubbleScroll: true
            };

            // Bind all private methods
            for (var fn in this) {
              if (fn.charAt(0) === '_' && typeof this[fn] === 'function') {
                this[fn] = this[fn].bind(this);
              }
            }
          }
          AutoScroll.prototype = {
            dragStarted: function dragStarted(_ref) {
              var originalEvent = _ref.originalEvent;
              if (this.sortable.nativeDraggable) {
                on(document, 'dragover', this._handleAutoScroll);
              } else {
                if (this.options.supportPointer) {
                  on(document, 'pointermove', this._handleFallbackAutoScroll);
                } else if (originalEvent.touches) {
                  on(document, 'touchmove', this._handleFallbackAutoScroll);
                } else {
                  on(document, 'mousemove', this._handleFallbackAutoScroll);
                }
              }
            },
            dragOverCompleted: function dragOverCompleted(_ref2) {
              var originalEvent = _ref2.originalEvent;
              // For when bubbling is canceled and using fallback (fallback 'touchmove' always reached)
              if (!this.options.dragOverBubble && !originalEvent.rootEl) {
                this._handleAutoScroll(originalEvent);
              }
            },
            drop: function drop() {
              if (this.sortable.nativeDraggable) {
                off(document, 'dragover', this._handleAutoScroll);
              } else {
                off(document, 'pointermove', this._handleFallbackAutoScroll);
                off(document, 'touchmove', this._handleFallbackAutoScroll);
                off(document, 'mousemove', this._handleFallbackAutoScroll);
              }
              clearPointerElemChangedInterval();
              clearAutoScrolls();
              cancelThrottle();
            },
            nulling: function nulling() {
              touchEvt$1 = scrollRootEl = scrollEl = scrolling = pointerElemChangedInterval = lastAutoScrollX = lastAutoScrollY = null;
              autoScrolls.length = 0;
            },
            _handleFallbackAutoScroll: function _handleFallbackAutoScroll(evt) {
              this._handleAutoScroll(evt, true);
            },
            _handleAutoScroll: function _handleAutoScroll(evt, fallback) {
              var _this = this;
              var x = (evt.touches ? evt.touches[0] : evt).clientX,
                y = (evt.touches ? evt.touches[0] : evt).clientY,
                elem = document.elementFromPoint(x, y);
              touchEvt$1 = evt;

              // IE does not seem to have native autoscroll,
              // Edge's autoscroll seems too conditional,
              // MACOS Safari does not have autoscroll,
              // Firefox and Chrome are good
              if (fallback || this.options.forceAutoScrollFallback || Edge || IE11OrLess || Safari) {
                autoScroll(evt, this.options, elem, fallback);

                // Listener for pointer element change
                var ogElemScroller = getParentAutoScrollElement(elem, true);
                if (scrolling && (!pointerElemChangedInterval || x !== lastAutoScrollX || y !== lastAutoScrollY)) {
                  pointerElemChangedInterval && clearPointerElemChangedInterval();
                  // Detect for pointer elem change, emulating native DnD behaviour
                  pointerElemChangedInterval = setInterval(function () {
                    var newElem = getParentAutoScrollElement(document.elementFromPoint(x, y), true);
                    if (newElem !== ogElemScroller) {
                      ogElemScroller = newElem;
                      clearAutoScrolls();
                    }
                    autoScroll(evt, _this.options, newElem, fallback);
                  }, 10);
                  lastAutoScrollX = x;
                  lastAutoScrollY = y;
                }
              } else {
                // if DnD is enabled (and browser has good autoscrolling), first autoscroll will already scroll, so get parent autoscroll of first autoscroll
                if (!this.options.bubbleScroll || getParentAutoScrollElement(elem, true) === getWindowScrollingElement()) {
                  clearAutoScrolls();
                  return;
                }
                autoScroll(evt, this.options, getParentAutoScrollElement(elem, false), false);
              }
            }
          };
          return _extends(AutoScroll, {
            pluginName: 'scroll',
            initializeByDefault: true
          });
        }
        function clearAutoScrolls() {
          autoScrolls.forEach(function (autoScroll) {
            clearInterval(autoScroll.pid);
          });
          autoScrolls = [];
        }
        function clearPointerElemChangedInterval() {
          clearInterval(pointerElemChangedInterval);
        }
        var autoScroll = throttle(function (evt, options, rootEl, isFallback) {
          // Bug: https://bugzilla.mozilla.org/show_bug.cgi?id=505521
          if (!options.scroll) return;
          var x = (evt.touches ? evt.touches[0] : evt).clientX,
            y = (evt.touches ? evt.touches[0] : evt).clientY,
            sens = options.scrollSensitivity,
            speed = options.scrollSpeed,
            winScroller = getWindowScrollingElement();
          var scrollThisInstance = false,
            scrollCustomFn;

          // New scroll root, set scrollEl
          if (scrollRootEl !== rootEl) {
            scrollRootEl = rootEl;
            clearAutoScrolls();
            scrollEl = options.scroll;
            scrollCustomFn = options.scrollFn;
            if (scrollEl === true) {
              scrollEl = getParentAutoScrollElement(rootEl, true);
            }
          }
          var layersOut = 0;
          var currentParent = scrollEl;
          do {
            var el = currentParent,
              rect = getRect(el),
              top = rect.top,
              bottom = rect.bottom,
              left = rect.left,
              right = rect.right,
              width = rect.width,
              height = rect.height,
              canScrollX = void 0,
              canScrollY = void 0,
              scrollWidth = el.scrollWidth,
              scrollHeight = el.scrollHeight,
              elCSS = css(el),
              scrollPosX = el.scrollLeft,
              scrollPosY = el.scrollTop;
            if (el === winScroller) {
              canScrollX = width < scrollWidth && (elCSS.overflowX === 'auto' || elCSS.overflowX === 'scroll' || elCSS.overflowX === 'visible');
              canScrollY = height < scrollHeight && (elCSS.overflowY === 'auto' || elCSS.overflowY === 'scroll' || elCSS.overflowY === 'visible');
            } else {
              canScrollX = width < scrollWidth && (elCSS.overflowX === 'auto' || elCSS.overflowX === 'scroll');
              canScrollY = height < scrollHeight && (elCSS.overflowY === 'auto' || elCSS.overflowY === 'scroll');
            }
            var vx = canScrollX && (Math.abs(right - x) <= sens && scrollPosX + width < scrollWidth) - (Math.abs(left - x) <= sens && !!scrollPosX);
            var vy = canScrollY && (Math.abs(bottom - y) <= sens && scrollPosY + height < scrollHeight) - (Math.abs(top - y) <= sens && !!scrollPosY);
            if (!autoScrolls[layersOut]) {
              for (var i = 0; i <= layersOut; i++) {
                if (!autoScrolls[i]) {
                  autoScrolls[i] = {};
                }
              }
            }
            if (autoScrolls[layersOut].vx != vx || autoScrolls[layersOut].vy != vy || autoScrolls[layersOut].el !== el) {
              autoScrolls[layersOut].el = el;
              autoScrolls[layersOut].vx = vx;
              autoScrolls[layersOut].vy = vy;
              clearInterval(autoScrolls[layersOut].pid);
              if (vx != 0 || vy != 0) {
                scrollThisInstance = true;
                /* jshint loopfunc:true */
                autoScrolls[layersOut].pid = setInterval(function () {
                  // emulate drag over during autoscroll (fallback), emulating native DnD behaviour
                  if (isFallback && this.layer === 0) {
                    Sortable.active._onTouchMove(touchEvt$1); // To move ghost if it is positioned absolutely
                  }
                  var scrollOffsetY = autoScrolls[this.layer].vy ? autoScrolls[this.layer].vy * speed : 0;
                  var scrollOffsetX = autoScrolls[this.layer].vx ? autoScrolls[this.layer].vx * speed : 0;
                  if (typeof scrollCustomFn === 'function') {
                    if (scrollCustomFn.call(Sortable.dragged.parentNode[expando], scrollOffsetX, scrollOffsetY, evt, touchEvt$1, autoScrolls[this.layer].el) !== 'continue') {
                      return;
                    }
                  }
                  scrollBy(autoScrolls[this.layer].el, scrollOffsetX, scrollOffsetY);
                }.bind({
                  layer: layersOut
                }), 24);
              }
            }
            layersOut++;
          } while (options.bubbleScroll && currentParent !== winScroller && (currentParent = getParentAutoScrollElement(currentParent, false)));
          scrolling = scrollThisInstance; // in case another function catches scrolling as false in between when it is not
        }, 30);
        var drop = function drop(_ref) {
          var originalEvent = _ref.originalEvent,
            putSortable = _ref.putSortable,
            dragEl = _ref.dragEl,
            activeSortable = _ref.activeSortable,
            dispatchSortableEvent = _ref.dispatchSortableEvent,
            hideGhostForTarget = _ref.hideGhostForTarget,
            unhideGhostForTarget = _ref.unhideGhostForTarget;
          if (!originalEvent) return;
          var toSortable = putSortable || activeSortable;
          hideGhostForTarget();
          var touch = originalEvent.changedTouches && originalEvent.changedTouches.length ? originalEvent.changedTouches[0] : originalEvent;
          var target = document.elementFromPoint(touch.clientX, touch.clientY);
          unhideGhostForTarget();
          if (toSortable && !toSortable.el.contains(target)) {
            dispatchSortableEvent('spill');
            this.onSpill({
              dragEl: dragEl,
              putSortable: putSortable
            });
          }
        };
        function Revert() {}
        Revert.prototype = {
          startIndex: null,
          dragStart: function dragStart(_ref2) {
            var oldDraggableIndex = _ref2.oldDraggableIndex;
            this.startIndex = oldDraggableIndex;
          },
          onSpill: function onSpill(_ref3) {
            var dragEl = _ref3.dragEl,
              putSortable = _ref3.putSortable;
            this.sortable.captureAnimationState();
            if (putSortable) {
              putSortable.captureAnimationState();
            }
            var nextSibling = getChild(this.sortable.el, this.startIndex, this.options);
            if (nextSibling) {
              this.sortable.el.insertBefore(dragEl, nextSibling);
            } else {
              this.sortable.el.appendChild(dragEl);
            }
            this.sortable.animateAll();
            if (putSortable) {
              putSortable.animateAll();
            }
          },
          drop: drop
        };
        _extends(Revert, {
          pluginName: 'revertOnSpill'
        });
        function Remove() {}
        Remove.prototype = {
          onSpill: function onSpill(_ref4) {
            var dragEl = _ref4.dragEl,
              putSortable = _ref4.putSortable;
            var parentSortable = putSortable || this.sortable;
            parentSortable.captureAnimationState();
            dragEl.parentNode && dragEl.parentNode.removeChild(dragEl);
            parentSortable.animateAll();
          },
          drop: drop
        };
        _extends(Remove, {
          pluginName: 'removeOnSpill'
        });
        var lastSwapEl;
        function SwapPlugin() {
          function Swap() {
            this.defaults = {
              swapClass: 'sortable-swap-highlight'
            };
          }
          Swap.prototype = {
            dragStart: function dragStart(_ref) {
              var dragEl = _ref.dragEl;
              lastSwapEl = dragEl;
            },
            dragOverValid: function dragOverValid(_ref2) {
              var completed = _ref2.completed,
                target = _ref2.target,
                onMove = _ref2.onMove,
                activeSortable = _ref2.activeSortable,
                changed = _ref2.changed,
                cancel = _ref2.cancel;
              if (!activeSortable.options.swap) return;
              var el = this.sortable.el,
                options = this.options;
              if (target && target !== el) {
                var prevSwapEl = lastSwapEl;
                if (onMove(target) !== false) {
                  toggleClass(target, options.swapClass, true);
                  lastSwapEl = target;
                } else {
                  lastSwapEl = null;
                }
                if (prevSwapEl && prevSwapEl !== lastSwapEl) {
                  toggleClass(prevSwapEl, options.swapClass, false);
                }
              }
              changed();
              completed(true);
              cancel();
            },
            drop: function drop(_ref3) {
              var activeSortable = _ref3.activeSortable,
                putSortable = _ref3.putSortable,
                dragEl = _ref3.dragEl;
              var toSortable = putSortable || this.sortable;
              var options = this.options;
              lastSwapEl && toggleClass(lastSwapEl, options.swapClass, false);
              if (lastSwapEl && (options.swap || putSortable && putSortable.options.swap)) {
                if (dragEl !== lastSwapEl) {
                  toSortable.captureAnimationState();
                  if (toSortable !== activeSortable) activeSortable.captureAnimationState();
                  swapNodes(dragEl, lastSwapEl);
                  toSortable.animateAll();
                  if (toSortable !== activeSortable) activeSortable.animateAll();
                }
              }
            },
            nulling: function nulling() {
              lastSwapEl = null;
            }
          };
          return _extends(Swap, {
            pluginName: 'swap',
            eventProperties: function eventProperties() {
              return {
                swapItem: lastSwapEl
              };
            }
          });
        }
        function swapNodes(n1, n2) {
          var p1 = n1.parentNode,
            p2 = n2.parentNode,
            i1,
            i2;
          if (!p1 || !p2 || p1.isEqualNode(n2) || p2.isEqualNode(n1)) return;
          i1 = index(n1);
          i2 = index(n2);
          if (p1.isEqualNode(p2) && i1 < i2) {
            i2++;
          }
          p1.insertBefore(n2, p1.children[i1]);
          p2.insertBefore(n1, p2.children[i2]);
        }
        var multiDragElements = [],
          multiDragClones = [],
          lastMultiDragSelect,
          // for selection with modifier key down (SHIFT)
          multiDragSortable,
          initialFolding = false,
          // Initial multi-drag fold when drag started
          folding = false,
          // Folding any other time
          dragStarted = false,
          dragEl$1,
          clonesFromRect,
          clonesHidden;
        function MultiDragPlugin() {
          function MultiDrag(sortable) {
            // Bind all private methods
            for (var fn in this) {
              if (fn.charAt(0) === '_' && typeof this[fn] === 'function') {
                this[fn] = this[fn].bind(this);
              }
            }
            if (!sortable.options.avoidImplicitDeselect) {
              if (sortable.options.supportPointer) {
                on(document, 'pointerup', this._deselectMultiDrag);
              } else {
                on(document, 'mouseup', this._deselectMultiDrag);
                on(document, 'touchend', this._deselectMultiDrag);
              }
            }
            on(document, 'keydown', this._checkKeyDown);
            on(document, 'keyup', this._checkKeyUp);
            this.defaults = {
              selectedClass: 'sortable-selected',
              multiDragKey: null,
              avoidImplicitDeselect: false,
              setData: function setData(dataTransfer, dragEl) {
                var data = '';
                if (multiDragElements.length && multiDragSortable === sortable) {
                  multiDragElements.forEach(function (multiDragElement, i) {
                    data += (!i ? '' : ', ') + multiDragElement.textContent;
                  });
                } else {
                  data = dragEl.textContent;
                }
                dataTransfer.setData('Text', data);
              }
            };
          }
          MultiDrag.prototype = {
            multiDragKeyDown: false,
            isMultiDrag: false,
            delayStartGlobal: function delayStartGlobal(_ref) {
              var dragged = _ref.dragEl;
              dragEl$1 = dragged;
            },
            delayEnded: function delayEnded() {
              this.isMultiDrag = ~multiDragElements.indexOf(dragEl$1);
            },
            setupClone: function setupClone(_ref2) {
              var sortable = _ref2.sortable,
                cancel = _ref2.cancel;
              if (!this.isMultiDrag) return;
              for (var i = 0; i < multiDragElements.length; i++) {
                multiDragClones.push(clone(multiDragElements[i]));
                multiDragClones[i].sortableIndex = multiDragElements[i].sortableIndex;
                multiDragClones[i].draggable = false;
                multiDragClones[i].style['will-change'] = '';
                toggleClass(multiDragClones[i], this.options.selectedClass, false);
                multiDragElements[i] === dragEl$1 && toggleClass(multiDragClones[i], this.options.chosenClass, false);
              }
              sortable._hideClone();
              cancel();
            },
            clone: function clone(_ref3) {
              var sortable = _ref3.sortable,
                rootEl = _ref3.rootEl,
                dispatchSortableEvent = _ref3.dispatchSortableEvent,
                cancel = _ref3.cancel;
              if (!this.isMultiDrag) return;
              if (!this.options.removeCloneOnHide) {
                if (multiDragElements.length && multiDragSortable === sortable) {
                  insertMultiDragClones(true, rootEl);
                  dispatchSortableEvent('clone');
                  cancel();
                }
              }
            },
            showClone: function showClone(_ref4) {
              var cloneNowShown = _ref4.cloneNowShown,
                rootEl = _ref4.rootEl,
                cancel = _ref4.cancel;
              if (!this.isMultiDrag) return;
              insertMultiDragClones(false, rootEl);
              multiDragClones.forEach(function (clone) {
                css(clone, 'display', '');
              });
              cloneNowShown();
              clonesHidden = false;
              cancel();
            },
            hideClone: function hideClone(_ref5) {
              var _this = this;
              var sortable = _ref5.sortable,
                cloneNowHidden = _ref5.cloneNowHidden,
                cancel = _ref5.cancel;
              if (!this.isMultiDrag) return;
              multiDragClones.forEach(function (clone) {
                css(clone, 'display', 'none');
                if (_this.options.removeCloneOnHide && clone.parentNode) {
                  clone.parentNode.removeChild(clone);
                }
              });
              cloneNowHidden();
              clonesHidden = true;
              cancel();
            },
            dragStartGlobal: function dragStartGlobal(_ref6) {
              var sortable = _ref6.sortable;
              if (!this.isMultiDrag && multiDragSortable) {
                multiDragSortable.multiDrag._deselectMultiDrag();
              }
              multiDragElements.forEach(function (multiDragElement) {
                multiDragElement.sortableIndex = index(multiDragElement);
              });

              // Sort multi-drag elements
              multiDragElements = multiDragElements.sort(function (a, b) {
                return a.sortableIndex - b.sortableIndex;
              });
              dragStarted = true;
            },
            dragStarted: function dragStarted(_ref7) {
              var _this2 = this;
              var sortable = _ref7.sortable;
              if (!this.isMultiDrag) return;
              if (this.options.sort) {
                // Capture rects,
                // hide multi drag elements (by positioning them absolute),
                // set multi drag elements rects to dragRect,
                // show multi drag elements,
                // animate to rects,
                // unset rects & remove from DOM

                sortable.captureAnimationState();
                if (this.options.animation) {
                  multiDragElements.forEach(function (multiDragElement) {
                    if (multiDragElement === dragEl$1) return;
                    css(multiDragElement, 'position', 'absolute');
                  });
                  var dragRect = getRect(dragEl$1, false, true, true);
                  multiDragElements.forEach(function (multiDragElement) {
                    if (multiDragElement === dragEl$1) return;
                    setRect(multiDragElement, dragRect);
                  });
                  folding = true;
                  initialFolding = true;
                }
              }
              sortable.animateAll(function () {
                folding = false;
                initialFolding = false;
                if (_this2.options.animation) {
                  multiDragElements.forEach(function (multiDragElement) {
                    unsetRect(multiDragElement);
                  });
                }

                // Remove all auxiliary multidrag items from el, if sorting enabled
                if (_this2.options.sort) {
                  removeMultiDragElements();
                }
              });
            },
            dragOver: function dragOver(_ref8) {
              var target = _ref8.target,
                completed = _ref8.completed,
                cancel = _ref8.cancel;
              if (folding && ~multiDragElements.indexOf(target)) {
                completed(false);
                cancel();
              }
            },
            revert: function revert(_ref9) {
              var fromSortable = _ref9.fromSortable,
                rootEl = _ref9.rootEl,
                sortable = _ref9.sortable,
                dragRect = _ref9.dragRect;
              if (multiDragElements.length > 1) {
                // Setup unfold animation
                multiDragElements.forEach(function (multiDragElement) {
                  sortable.addAnimationState({
                    target: multiDragElement,
                    rect: folding ? getRect(multiDragElement) : dragRect
                  });
                  unsetRect(multiDragElement);
                  multiDragElement.fromRect = dragRect;
                  fromSortable.removeAnimationState(multiDragElement);
                });
                folding = false;
                insertMultiDragElements(!this.options.removeCloneOnHide, rootEl);
              }
            },
            dragOverCompleted: function dragOverCompleted(_ref10) {
              var sortable = _ref10.sortable,
                isOwner = _ref10.isOwner,
                insertion = _ref10.insertion,
                activeSortable = _ref10.activeSortable,
                parentEl = _ref10.parentEl,
                putSortable = _ref10.putSortable;
              var options = this.options;
              if (insertion) {
                // Clones must be hidden before folding animation to capture dragRectAbsolute properly
                if (isOwner) {
                  activeSortable._hideClone();
                }
                initialFolding = false;
                // If leaving sort:false root, or already folding - Fold to new location
                if (options.animation && multiDragElements.length > 1 && (folding || !isOwner && !activeSortable.options.sort && !putSortable)) {
                  // Fold: Set all multi drag elements's rects to dragEl's rect when multi-drag elements are invisible
                  var dragRectAbsolute = getRect(dragEl$1, false, true, true);
                  multiDragElements.forEach(function (multiDragElement) {
                    if (multiDragElement === dragEl$1) return;
                    setRect(multiDragElement, dragRectAbsolute);

                    // Move element(s) to end of parentEl so that it does not interfere with multi-drag clones insertion if they are inserted
                    // while folding, and so that we can capture them again because old sortable will no longer be fromSortable
                    parentEl.appendChild(multiDragElement);
                  });
                  folding = true;
                }

                // Clones must be shown (and check to remove multi drags) after folding when interfering multiDragElements are moved out
                if (!isOwner) {
                  // Only remove if not folding (folding will remove them anyways)
                  if (!folding) {
                    removeMultiDragElements();
                  }
                  if (multiDragElements.length > 1) {
                    var clonesHiddenBefore = clonesHidden;
                    activeSortable._showClone(sortable);

                    // Unfold animation for clones if showing from hidden
                    if (activeSortable.options.animation && !clonesHidden && clonesHiddenBefore) {
                      multiDragClones.forEach(function (clone) {
                        activeSortable.addAnimationState({
                          target: clone,
                          rect: clonesFromRect
                        });
                        clone.fromRect = clonesFromRect;
                        clone.thisAnimationDuration = null;
                      });
                    }
                  } else {
                    activeSortable._showClone(sortable);
                  }
                }
              }
            },
            dragOverAnimationCapture: function dragOverAnimationCapture(_ref11) {
              var dragRect = _ref11.dragRect,
                isOwner = _ref11.isOwner,
                activeSortable = _ref11.activeSortable;
              multiDragElements.forEach(function (multiDragElement) {
                multiDragElement.thisAnimationDuration = null;
              });
              if (activeSortable.options.animation && !isOwner && activeSortable.multiDrag.isMultiDrag) {
                clonesFromRect = _extends({}, dragRect);
                var dragMatrix = matrix(dragEl$1, true);
                clonesFromRect.top -= dragMatrix.f;
                clonesFromRect.left -= dragMatrix.e;
              }
            },
            dragOverAnimationComplete: function dragOverAnimationComplete() {
              if (folding) {
                folding = false;
                removeMultiDragElements();
              }
            },
            drop: function drop(_ref12) {
              var evt = _ref12.originalEvent,
                rootEl = _ref12.rootEl,
                parentEl = _ref12.parentEl,
                sortable = _ref12.sortable,
                dispatchSortableEvent = _ref12.dispatchSortableEvent,
                oldIndex = _ref12.oldIndex,
                putSortable = _ref12.putSortable;
              var toSortable = putSortable || this.sortable;
              if (!evt) return;
              var options = this.options,
                children = parentEl.children;

              // Multi-drag selection
              if (!dragStarted) {
                if (options.multiDragKey && !this.multiDragKeyDown) {
                  this._deselectMultiDrag();
                }
                toggleClass(dragEl$1, options.selectedClass, !~multiDragElements.indexOf(dragEl$1));
                if (!~multiDragElements.indexOf(dragEl$1)) {
                  multiDragElements.push(dragEl$1);
                  dispatchEvent({
                    sortable: sortable,
                    rootEl: rootEl,
                    name: 'select',
                    targetEl: dragEl$1,
                    originalEvent: evt
                  });

                  // Modifier activated, select from last to dragEl
                  if (evt.shiftKey && lastMultiDragSelect && sortable.el.contains(lastMultiDragSelect)) {
                    var lastIndex = index(lastMultiDragSelect),
                      currentIndex = index(dragEl$1);
                    if (~lastIndex && ~currentIndex && lastIndex !== currentIndex) {
                      // Must include lastMultiDragSelect (select it), in case modified selection from no selection
                      // (but previous selection existed)
                      var n, i;
                      if (currentIndex > lastIndex) {
                        i = lastIndex;
                        n = currentIndex;
                      } else {
                        i = currentIndex;
                        n = lastIndex + 1;
                      }
                      for (; i < n; i++) {
                        if (~multiDragElements.indexOf(children[i])) continue;
                        toggleClass(children[i], options.selectedClass, true);
                        multiDragElements.push(children[i]);
                        dispatchEvent({
                          sortable: sortable,
                          rootEl: rootEl,
                          name: 'select',
                          targetEl: children[i],
                          originalEvent: evt
                        });
                      }
                    }
                  } else {
                    lastMultiDragSelect = dragEl$1;
                  }
                  multiDragSortable = toSortable;
                } else {
                  multiDragElements.splice(multiDragElements.indexOf(dragEl$1), 1);
                  lastMultiDragSelect = null;
                  dispatchEvent({
                    sortable: sortable,
                    rootEl: rootEl,
                    name: 'deselect',
                    targetEl: dragEl$1,
                    originalEvent: evt
                  });
                }
              }

              // Multi-drag drop
              if (dragStarted && this.isMultiDrag) {
                folding = false;
                // Do not "unfold" after around dragEl if reverted
                if ((parentEl[expando].options.sort || parentEl !== rootEl) && multiDragElements.length > 1) {
                  var dragRect = getRect(dragEl$1),
                    multiDragIndex = index(dragEl$1, ':not(.' + this.options.selectedClass + ')');
                  if (!initialFolding && options.animation) dragEl$1.thisAnimationDuration = null;
                  toSortable.captureAnimationState();
                  if (!initialFolding) {
                    if (options.animation) {
                      dragEl$1.fromRect = dragRect;
                      multiDragElements.forEach(function (multiDragElement) {
                        multiDragElement.thisAnimationDuration = null;
                        if (multiDragElement !== dragEl$1) {
                          var rect = folding ? getRect(multiDragElement) : dragRect;
                          multiDragElement.fromRect = rect;

                          // Prepare unfold animation
                          toSortable.addAnimationState({
                            target: multiDragElement,
                            rect: rect
                          });
                        }
                      });
                    }

                    // Multi drag elements are not necessarily removed from the DOM on drop, so to reinsert
                    // properly they must all be removed
                    removeMultiDragElements();
                    multiDragElements.forEach(function (multiDragElement) {
                      if (children[multiDragIndex]) {
                        parentEl.insertBefore(multiDragElement, children[multiDragIndex]);
                      } else {
                        parentEl.appendChild(multiDragElement);
                      }
                      multiDragIndex++;
                    });

                    // If initial folding is done, the elements may have changed position because they are now
                    // unfolding around dragEl, even though dragEl may not have his index changed, so update event
                    // must be fired here as Sortable will not.
                    if (oldIndex === index(dragEl$1)) {
                      var update = false;
                      multiDragElements.forEach(function (multiDragElement) {
                        if (multiDragElement.sortableIndex !== index(multiDragElement)) {
                          update = true;
                          return;
                        }
                      });
                      if (update) {
                        dispatchSortableEvent('update');
                        dispatchSortableEvent('sort');
                      }
                    }
                  }

                  // Must be done after capturing individual rects (scroll bar)
                  multiDragElements.forEach(function (multiDragElement) {
                    unsetRect(multiDragElement);
                  });
                  toSortable.animateAll();
                }
                multiDragSortable = toSortable;
              }

              // Remove clones if necessary
              if (rootEl === parentEl || putSortable && putSortable.lastPutMode !== 'clone') {
                multiDragClones.forEach(function (clone) {
                  clone.parentNode && clone.parentNode.removeChild(clone);
                });
              }
            },
            nullingGlobal: function nullingGlobal() {
              this.isMultiDrag = dragStarted = false;
              multiDragClones.length = 0;
            },
            destroyGlobal: function destroyGlobal() {
              this._deselectMultiDrag();
              off(document, 'pointerup', this._deselectMultiDrag);
              off(document, 'mouseup', this._deselectMultiDrag);
              off(document, 'touchend', this._deselectMultiDrag);
              off(document, 'keydown', this._checkKeyDown);
              off(document, 'keyup', this._checkKeyUp);
            },
            _deselectMultiDrag: function _deselectMultiDrag(evt) {
              if (typeof dragStarted !== "undefined" && dragStarted) return;

              // Only deselect if selection is in this sortable
              if (multiDragSortable !== this.sortable) return;

              // Only deselect if target is not item in this sortable
              if (evt && closest(evt.target, this.options.draggable, this.sortable.el, false)) return;

              // Only deselect if left click
              if (evt && evt.button !== 0) return;
              while (multiDragElements.length) {
                var el = multiDragElements[0];
                toggleClass(el, this.options.selectedClass, false);
                multiDragElements.shift();
                dispatchEvent({
                  sortable: this.sortable,
                  rootEl: this.sortable.el,
                  name: 'deselect',
                  targetEl: el,
                  originalEvent: evt
                });
              }
            },
            _checkKeyDown: function _checkKeyDown(evt) {
              if (evt.key === this.options.multiDragKey) {
                this.multiDragKeyDown = true;
              }
            },
            _checkKeyUp: function _checkKeyUp(evt) {
              if (evt.key === this.options.multiDragKey) {
                this.multiDragKeyDown = false;
              }
            }
          };
          return _extends(MultiDrag, {
            // Static methods & properties
            pluginName: 'multiDrag',
            utils: {
              /**
              * Selects the provided multi-drag item
              * @param  {HTMLElement} el    The element to be selected
              */
              select: function select(el) {
                var sortable = el.parentNode[expando];
                if (!sortable || !sortable.options.multiDrag || ~multiDragElements.indexOf(el)) return;
                if (multiDragSortable && multiDragSortable !== sortable) {
                  multiDragSortable.multiDrag._deselectMultiDrag();
                  multiDragSortable = sortable;
                }
                toggleClass(el, sortable.options.selectedClass, true);
                multiDragElements.push(el);
              },
              /**
              * Deselects the provided multi-drag item
              * @param  {HTMLElement} el    The element to be deselected
              */
              deselect: function deselect(el) {
                var sortable = el.parentNode[expando],
                  index = multiDragElements.indexOf(el);
                if (!sortable || !sortable.options.multiDrag || !~index) return;
                toggleClass(el, sortable.options.selectedClass, false);
                multiDragElements.splice(index, 1);
              }
            },
            eventProperties: function eventProperties() {
              var _this3 = this;
              var oldIndicies = [],
                newIndicies = [];
              multiDragElements.forEach(function (multiDragElement) {
                oldIndicies.push({
                  multiDragElement: multiDragElement,
                  index: multiDragElement.sortableIndex
                });

                // multiDragElements will already be sorted if folding
                var newIndex;
                if (folding && multiDragElement !== dragEl$1) {
                  newIndex = -1;
                } else if (folding) {
                  newIndex = index(multiDragElement, ':not(.' + _this3.options.selectedClass + ')');
                } else {
                  newIndex = index(multiDragElement);
                }
                newIndicies.push({
                  multiDragElement: multiDragElement,
                  index: newIndex
                });
              });
              return {
                items: _toConsumableArray(multiDragElements),
                clones: [].concat(multiDragClones),
                oldIndicies: oldIndicies,
                newIndicies: newIndicies
              };
            },
            optionListeners: {
              multiDragKey: function multiDragKey(key) {
                key = key.toLowerCase();
                if (key === 'ctrl') {
                  key = 'Control';
                } else if (key.length > 1) {
                  key = key.charAt(0).toUpperCase() + key.substr(1);
                }
                return key;
              }
            }
          });
        }
        function insertMultiDragElements(clonesInserted, rootEl) {
          multiDragElements.forEach(function (multiDragElement, i) {
            var target = rootEl.children[multiDragElement.sortableIndex + (clonesInserted ? Number(i) : 0)];
            if (target) {
              rootEl.insertBefore(multiDragElement, target);
            } else {
              rootEl.appendChild(multiDragElement);
            }
          });
        }

        /**
        * Insert multi-drag clones
        * @param  {[Boolean]} elementsInserted  Whether the multi-drag elements are inserted
        * @param  {HTMLElement} rootEl
        */
        function insertMultiDragClones(elementsInserted, rootEl) {
          multiDragClones.forEach(function (clone, i) {
            var target = rootEl.children[clone.sortableIndex + (elementsInserted ? Number(i) : 0)];
            if (target) {
              rootEl.insertBefore(clone, target);
            } else {
              rootEl.appendChild(clone);
            }
          });
        }
        function removeMultiDragElements() {
          multiDragElements.forEach(function (multiDragElement) {
            if (multiDragElement === dragEl$1) return;
            multiDragElement.parentNode && multiDragElement.parentNode.removeChild(multiDragElement);
          });
        }
        Sortable.mount(new AutoScrollPlugin());
        Sortable.mount(Remove, Revert);
        Sortable.mount(new SwapPlugin());
        Sortable.mount(new MultiDragPlugin());
        return Sortable;
      });
      /* harmony default export */
      var __WEBPACK_DEFAULT_EXPORT__ = Sortable;

      /***/
    }),
    /***/"./js/vendor/ozz-wyg.js": (
    /*!******************************!*\
      !*** ./js/vendor/ozz-wyg.js ***!
      \******************************/
    /***/
    function _js_vendor_ozzWygJs(__unused_webpack_module, __webpack_exports__, __webpack_require__) {
      __webpack_require__.r(__webpack_exports__);
      /* harmony export */
      __webpack_require__.d(__webpack_exports__, {
        /* harmony export */"default": function _default() {
          return __WEBPACK_DEFAULT_EXPORT__;
        }
        /* harmony export */
      });
      /**
       * Ozz Wysiwyg Editor JS
       * The wysiwyg editor plugin for vanilla JS
       * 
       * Author: A.W.M. Shakir
       * Email: shakeerwahid@gmail.com
       */
      var OzzWyg = /*#__PURE__*/function () {
        function OzzWyg(options) {
          var _this7 = this;
          _classCallCheck(this, OzzWyg);
          this.options = _objectSpread(_objectSpread({}, OzzWyg.defaults), options);

          // Normalize selector: accept string, single DOM element, NodeList, or array of elements
          var editorElements = [];
          var selector = this.options.selector;
          if (typeof selector === 'string') {
            editorElements = document.querySelectorAll(selector);
          } else if (selector instanceof Element) {
            editorElements = [selector];
          } else if (selector instanceof NodeList || Array.isArray(selector)) {
            editorElements = selector;
          }
          this.editors = editorElements.length > 0 ? editorElements : false;

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
                }
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
                  dom: '<button type="button" data-action="strikethrough">Strikethrough</button>'
                },
                'subscript': {
                  name: 'Subscript',
                  dom: '<button type="button" data-action="subscript">Subscript</button>'
                },
                'superscript': {
                  name: 'Superscript',
                  dom: '<button type="button" data-action="superscript">Superscript</button>'
                }
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
                }
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
          this.editorInstances = new Map();
          if (this.editors) {
            this.editors.forEach(function (editor) {
              var _ref15, _ref16;
              var editorID = "i-".concat(_this7.randomId());
              editor.setAttribute('data-editor', editorID);

              // Capture initial content before we overwrite the DOM
              var initialContent = (_ref15 = (_ref16 = typeof _this7.options.value === 'string' ? _this7.options.value : null) !== null && _ref16 !== void 0 ? _ref16 : editor.getAttribute('data-value')) !== null && _ref15 !== void 0 ? _ref15 : editor.innerHTML;
              var instance = {
                id: editorID,
                element: editor,
                playGround: null,
                ozzWygInstance: _this7,
                initialContent: initialContent
              };
              _this7.editorInstances.set(editorID, instance);
              _this7.currentEditorID = editorID;
              _this7.editor = editor;
              _this7.editorID = editorID;
              _this7.initEditor();
              instance.playGround = _this7.playGround;

              // Apply initial content if provided
              if (initialContent !== null && initialContent !== undefined && initialContent.trim() !== '') {
                _this7.setValue(initialContent, editorID);
              } else {
                // Initialize features for any existing content in playground
                setTimeout(function () {
                  // Ensure we're using the correct instance
                  var instancePlayGround = instance.playGround;
                  var instanceEditor = instance.element;
                  if (instancePlayGround) {
                    // Set context for initialization
                    var originalEditor = _this7.editor;
                    var originalPlayGround = _this7.playGround;
                    var originalEditorID = _this7.editorID;
                    _this7.editor = instanceEditor;
                    _this7.playGround = instancePlayGround;
                    _this7.editorID = instance.id;
                    _this7.currentEditorID = instance.id;
                    _this7.initializeContentFeatures();

                    // Restore original context
                    _this7.editor = originalEditor;
                    _this7.playGround = originalPlayGround;
                    _this7.editorID = originalEditorID;
                  }
                }, 0);
              }

              // Register instance in static registry
              OzzWyg.instances.set(editor, _this7);
              OzzWyg.instances.set(editorID, _this7);
            });
          }
        }

        /**
         * Set active editor context based on a child element (toolbar button / playground)
         * @param {HTMLElement} element
         * @returns {boolean} true if context updated
         */
        return _createClass(OzzWyg, [{
          key: "setActiveContextFromElement",
          value: function setActiveContextFromElement(element) {
            if (!element) return false;
            var editorEl = element.closest('.ozz-wyg');
            if (!editorEl) return false;
            var editorID = editorEl.getAttribute('data-editor');
            var instance = this.editorInstances.get(editorID);
            if (!instance) return false;
            this.editor = instance.element;
            this.playGround = instance.playGround;
            this.editorID = editorID;
            this.currentEditorID = editorID;
            return true;
          }

          /**
           * Initialize Wysiwyg Editor
           */
        }, {
          key: "initEditor",
          value: function initEditor() {
            var _this8 = this;
            this.editor.classList.add('ozz-wyg');
            this.editor.innerHTML = this.editorDOM();
            this.editor.querySelectorAll('button[data-action]').forEach(function (trigger) {
              trigger.addEventListener('click', function (e) {
                _this8.setActiveContextFromElement(e.target);
                _this8.fireAction(e);
              });
            });

            // Initialize child tool dropdowns
            this.editor.querySelectorAll('.ozz-wyg__tool-has-child').forEach(function (parent) {
              var trigger = parent.querySelector('.more-tools-trigger');
              var childMenu = parent.parentElement.querySelector('.ozz-wyg__tool-child');
              if (trigger && childMenu) {
                trigger.addEventListener('click', function (e) {
                  e.stopPropagation();
                  childMenu.classList.toggle('active');
                });
              }
            });

            // Close dropdowns when clicking outside
            document.addEventListener('click', function (e) {
              if (!e.target.closest('.ozz-wyg__tool-has-child')) {
                _this8.editor.querySelectorAll('.ozz-wyg__tool-child').forEach(function (menu) {
                  menu.classList.remove('active');
                });
              }
            });
            this.playGround = this.editor.querySelector('[data-editor-area]');

            // Initialize event listeners
            this.initEventListeners();
          }

          /**
           * Initialize all event listeners for the editor
           */
        }, {
          key: "initEventListeners",
          value: function initEventListeners() {
            var _this9 = this;
            // Input event - fires on content change
            this.playGround.addEventListener('input', function (e) {
              _this9.setActiveContextFromElement(_this9.playGround);
              _this9.handleInput(e);
            });

            // Focus event - when editor gains focus
            this.playGround.addEventListener('focus', function (e) {
              _this9.setActiveContextFromElement(_this9.playGround);
              _this9.emitEvent('focus', {
                editorID: _this9.editorID,
                event: e
              });
              _this9.editor.classList.add('ozz-wyg--focused');
            });

            // Blur event - when editor loses focus
            this.playGround.addEventListener('blur', function (e) {
              _this9.setActiveContextFromElement(_this9.playGround);
              _this9.emitEvent('blur', {
                editorID: _this9.editorID,
                event: e
              });
              _this9.editor.classList.remove('ozz-wyg--focused');
              _this9.emitEvent('change', {
                editorID: _this9.editorID,
                content: _this9.playGround.innerHTML,
                event: e
              });
            });

            // Paste event - clean pasted content
            this.playGround.addEventListener('paste', function (e) {
              _this9.setActiveContextFromElement(_this9.playGround);
              _this9.handlePaste(e);
            });

            // Keyboard shortcuts
            this.playGround.addEventListener('keydown', function (e) {
              _this9.setActiveContextFromElement(_this9.playGround);
              _this9.handleKeydown(e);
            });

            // Selection change - update toolbar states
            document.addEventListener('selectionchange', function () {
              var selection = window.getSelection();
              if (selection.rangeCount > 0) {
                var range = selection.getRangeAt(0);
                var commonAncestor = range.commonAncestorContainer;
                var node = commonAncestor.nodeType === 3 ? commonAncestor.parentElement : commonAncestor;
                if (node) {
                  if (_this9.setActiveContextFromElement(node) && _this9.playGround.contains(node)) {
                    _this9.updateToolbarStates();
                  }
                }
              }
            });

            // Click event - update toolbar states when clicking in editor
            this.playGround.addEventListener('click', function () {
              _this9.setActiveContextFromElement(_this9.playGround);
              setTimeout(function () {
                return _this9.updateToolbarStates();
              }, 10);
            });

            // Mouseup event - update toolbar states after selection
            this.playGround.addEventListener('mouseup', function () {
              _this9.setActiveContextFromElement(_this9.playGround);
              setTimeout(function () {
                return _this9.updateToolbarStates();
              }, 10);
            });

            // Keyup event - update toolbar states after keyboard actions
            this.playGround.addEventListener('keyup', function () {
              _this9.setActiveContextFromElement(_this9.playGround);
              setTimeout(function () {
                return _this9.updateToolbarStates();
              }, 10);
            });

            // Keydown event - update toolbar states for arrow keys and other navigation
            this.playGround.addEventListener('keydown', function (e) {
              if (['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(e.key)) {
                _this9.setActiveContextFromElement(_this9.playGround);
                setTimeout(function () {
                  return _this9.updateToolbarStates();
                }, 10);
              }
            });
          }

          /**
           * Handle input event
           */
        }, {
          key: "handleInput",
          value: function handleInput(e) {
            // Modify tables
            var tables = this.playGround.querySelectorAll('table');
            tables.forEach(function (table) {
              // Wrap table
              if (!table.closest('.ozz-wyg-table-wrapper')) {
                var tableWrapped = document.createElement('div');
                tableWrapped.classList.add('ozz-wyg-table-wrapper');
                tableWrapped.innerHTML = table.outerHTML;
                table.outerHTML = tableWrapped.outerHTML;
              }

              // Clear inline styles
              table.removeAttribute('style');
              var tItems = table.querySelectorAll('tbody, thead, tfoot, tr, td, th');
              tItems.forEach(function (item) {
                item.removeAttribute('style');
              });
            });

            // Emit custom input event
            this.emitEvent('input', {
              editorID: this.editorID,
              content: this.playGround.innerHTML,
              event: e
            });
          }

          /**
           * Handle paste event - clean pasted content
           */
        }, {
          key: "handlePaste",
          value: function handlePaste(e) {
            e.preventDefault();

            // Get pasted content
            var clipboardData = e.clipboardData || window.clipboardData;
            var htmlData = clipboardData.getData('text/html');
            var textData = clipboardData.getData('text/plain');
            var pastedData = htmlData || textData;

            // Clean the pasted content
            var cleanedContent = this.cleanPastedContent(pastedData);
            try {
              // Insert cleaned content
              var selection = window.getSelection();
              if (selection.rangeCount > 0) {
                var range = selection.getRangeAt(0);

                // Ensure range is within this editor; otherwise collapse to end of playground
                var containerNode = range.commonAncestorContainer.nodeType === 3 ? range.commonAncestorContainer.parentElement : range.commonAncestorContainer;
                if (!containerNode || !this.playGround.contains(containerNode)) {
                  range.selectNodeContents(this.playGround);
                  range.collapse(false);
                }

                // Delete selected content
                range.deleteContents();

                // Insert cleaned content
                if (cleanedContent) {
                  if (htmlData && cleanedContent.includes('<')) {
                    // Treat as HTML: parse into a temp container and move children into a fragment
                    var tempDiv = document.createElement('div');
                    tempDiv.innerHTML = cleanedContent;
                    var fragment = document.createDocumentFragment();
                    while (tempDiv.firstChild) {
                      fragment.appendChild(tempDiv.firstChild);
                    }
                    range.insertNode(fragment);

                    // Move cursor to end of inserted content
                    var lastChild = this.playGround.lastChild;
                    if (lastChild) {
                      range.setStartAfter(lastChild);
                    }
                  } else {
                    // Plain text
                    var textNode = document.createTextNode(cleanedContent);
                    range.insertNode(textNode);
                    range.setStartAfter(textNode);
                  }
                  range.collapse(true);

                  // Update selection
                  selection.removeAllRanges();
                  selection.addRange(range);
                }
              } else {
                // No selection, insert at end of playground
                var _range = document.createRange();
                _range.selectNodeContents(this.playGround);
                _range.collapse(false);
                if (cleanedContent) {
                  if (htmlData && cleanedContent.includes('<')) {
                    var _tempDiv = document.createElement('div');
                    _tempDiv.innerHTML = cleanedContent;
                    var _fragment = document.createDocumentFragment();
                    while (_tempDiv.firstChild) {
                      _fragment.appendChild(_tempDiv.firstChild);
                    }
                    _range.insertNode(_fragment);
                  } else {
                    var _textNode = document.createTextNode(cleanedContent);
                    _range.insertNode(_textNode);
                  }
                }
              }
            } catch (err) {
              // Fallback: let the browser handle it if our manual insertion fails
              if (cleanedContent) {
                document.execCommand('insertHTML', false, cleanedContent);
              }
            }

            // Emit paste event
            this.emitEvent('paste', {
              editorID: this.editorID,
              originalContent: pastedData,
              cleanedContent: cleanedContent,
              event: e
            });

            // Trigger input event manually
            this.playGround.dispatchEvent(new Event('input', {
              bubbles: true
            }));
          }

          /**
           * Clean pasted content - remove unwanted styles and tags
           */
        }, {
          key: "cleanPastedContent",
          value: function cleanPastedContent(content) {
            // Create temporary div to parse HTML
            var tempDiv = document.createElement('div');
            tempDiv.innerHTML = content;

            // Remove unwanted attributes
            var unwantedAttributes = ['style', 'class', 'id', 'width', 'height', 'border'];
            var allElements = tempDiv.querySelectorAll('*');
            allElements.forEach(function (el) {
              unwantedAttributes.forEach(function (attr) {
                el.removeAttribute(attr);
              });
            });

            // Remove script and style tags
            var scripts = tempDiv.querySelectorAll('script, style');
            scripts.forEach(function (el) {
              return el.remove();
            });

            // Clean up empty elements
            var emptyElements = tempDiv.querySelectorAll('p:empty, div:empty, span:empty');
            emptyElements.forEach(function (el) {
              if (el.textContent.trim() === '') {
                el.remove();
              }
            });
            return tempDiv.innerHTML || tempDiv.textContent;
          }

          /**
           * Handle keyboard shortcuts
           */
        }, {
          key: "handleKeydown",
          value: function handleKeydown(e) {
            var _this10 = this;
            // Ctrl/Cmd + B - Bold
            if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
              e.preventDefault();
              this.exeCMD('bold');
              this.updateToolbarStates();
              return;
            }

            // Ctrl/Cmd + I - Italic
            if ((e.ctrlKey || e.metaKey) && e.key === 'i') {
              e.preventDefault();
              this.exeCMD('italic');
              this.updateToolbarStates();
              return;
            }

            // Ctrl/Cmd + U - Underline
            if ((e.ctrlKey || e.metaKey) && e.key === 'u') {
              e.preventDefault();
              this.exeCMD('underline');
              this.updateToolbarStates();
              return;
            }

            // Ctrl/Cmd + K - Link
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
              e.preventDefault();
              var linkBtn = this.editor.querySelector('button[data-action="link"]');
              if (linkBtn) linkBtn.click();
              return;
            }

            // Ctrl/Cmd + Z - Undo
            if ((e.ctrlKey || e.metaKey) && e.key === 'z' && !e.shiftKey) {
              // Allow default undo behavior
              setTimeout(function () {
                return _this10.updateToolbarStates();
              }, 10);
              return;
            }

            // Ctrl/Cmd + Shift + Z or Ctrl/Cmd + Y - Redo
            if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'z' || (e.ctrlKey || e.metaKey) && e.key === 'y') {
              // Allow default redo behavior
              setTimeout(function () {
                return _this10.updateToolbarStates();
              }, 10);
              return;
            }

            // Emit keydown event
            this.emitEvent('keydown', {
              editorID: this.editorID,
              key: e.key,
              ctrlKey: e.ctrlKey,
              metaKey: e.metaKey,
              shiftKey: e.shiftKey,
              event: e
            });
          }

          /**
           * Update toolbar button states based on current selection
           */
        }, {
          key: "updateToolbarStates",
          value: function updateToolbarStates() {
            var selection = window.getSelection();
            if (!selection.rangeCount) {
              // Clear all active states if no selection
              this.clearAllToolbarStates();
              return;
            }
            var range = selection.getRangeAt(0);
            var commonAncestor = range.commonAncestorContainer;

            // Check if selection is within this editor
            var node = commonAncestor.nodeType === 3 ? commonAncestor.parentElement : commonAncestor;
            if (!node || !this.playGround.contains(node)) {
              this.clearAllToolbarStates();
              return;
            }

            // Store current focus to restore later
            var wasFocused = document.activeElement === this.playGround;

            // Temporarily focus playground for queryCommandState to work correctly
            if (!wasFocused) {
              this.playGround.focus();
            }

            // Update bold button
            var boldBtn = this.editor.querySelector('button[data-action="bold"]');
            if (boldBtn) {
              try {
                var isBold = document.queryCommandState('bold');
                boldBtn.classList.toggle('active', isBold);
              } catch (e) {
                // Fallback: check if parent is strong or b tag
                var parent = this.getParentTag(range, ['strong', 'b']);
                boldBtn.classList.toggle('active', !!parent);
              }
            }

            // Update italic button
            var italicBtn = this.editor.querySelector('button[data-action="italic"]');
            if (italicBtn) {
              try {
                var isItalic = document.queryCommandState('italic');
                italicBtn.classList.toggle('active', isItalic);
              } catch (e) {
                var _parent = this.getParentTag(range, ['em', 'i']);
                italicBtn.classList.toggle('active', !!_parent);
              }
            }

            // Update underline button
            var underlineBtn = this.editor.querySelector('button[data-action="underline"]');
            if (underlineBtn) {
              try {
                var isUnderline = document.queryCommandState('underline');
                underlineBtn.classList.toggle('active', isUnderline);
              } catch (e) {
                var _parent2 = this.getParentTag(range, ['u']);
                underlineBtn.classList.toggle('active', !!_parent2);
              }
            }

            // Update strikethrough button
            var strikeBtn = this.editor.querySelector('button[data-action="strikethrough"]');
            if (strikeBtn) {
              try {
                var isStrike = document.queryCommandState('strikethrough');
                strikeBtn.classList.toggle('active', isStrike);
              } catch (e) {
                var _parent3 = this.getParentTag(range, ['s', 'strike', 'del']);
                strikeBtn.classList.toggle('active', !!_parent3);
              }
            }

            // Update subscript button
            var subBtn = this.editor.querySelector('button[data-action="subscript"]');
            if (subBtn) {
              try {
                var isSub = document.queryCommandState('subscript');
                subBtn.classList.toggle('active', isSub);
              } catch (e) {
                var _parent4 = this.getParentTag(range, ['sub']);
                subBtn.classList.toggle('active', !!_parent4);
              }
            }

            // Update superscript button
            var supBtn = this.editor.querySelector('button[data-action="superscript"]');
            if (supBtn) {
              try {
                var isSup = document.queryCommandState('superscript');
                supBtn.classList.toggle('active', isSup);
              } catch (e) {
                var _parent5 = this.getParentTag(range, ['sup']);
                supBtn.classList.toggle('active', !!_parent5);
              }
            }

            // Update format block (headings, paragraph)
            this.updateFormatBlockState(range);

            // Update list buttons
            var olBtn = this.editor.querySelector('button[data-action="insertOrderedList"]');
            if (olBtn) {
              try {
                var isOL = document.queryCommandState('insertOrderedList');
                olBtn.classList.toggle('active', isOL);
              } catch (e) {
                var _parent6 = this.getParentTag(range, ['ol']);
                olBtn.classList.toggle('active', !!_parent6);
              }
            }
            var ulBtn = this.editor.querySelector('button[data-action="insertUnorderedList"]');
            if (ulBtn) {
              try {
                var isUL = document.queryCommandState('insertUnorderedList');
                ulBtn.classList.toggle('active', isUL);
              } catch (e) {
                var _parent7 = this.getParentTag(range, ['ul']);
                ulBtn.classList.toggle('active', !!_parent7);
              }
            }

            // Update alignment buttons
            var alignLeftBtn = this.editor.querySelector('button[data-action="justifyLeft"]');
            if (alignLeftBtn) {
              try {
                var isLeft = document.queryCommandState('justifyLeft');
                alignLeftBtn.classList.toggle('active', isLeft);
              } catch (e) {
                alignLeftBtn.classList.remove('active');
              }
            }
            var alignCenterBtn = this.editor.querySelector('button[data-action="justifyCenter"]');
            if (alignCenterBtn) {
              try {
                var isCenter = document.queryCommandState('justifyCenter');
                alignCenterBtn.classList.toggle('active', isCenter);
              } catch (e) {
                alignCenterBtn.classList.remove('active');
              }
            }
            var alignRightBtn = this.editor.querySelector('button[data-action="justifyRight"]');
            if (alignRightBtn) {
              try {
                var isRight = document.queryCommandState('justifyRight');
                alignRightBtn.classList.toggle('active', isRight);
              } catch (e) {
                alignRightBtn.classList.remove('active');
              }
            }
            var justifyBtn = this.editor.querySelector('button[data-action="justifyFull"]');
            if (justifyBtn) {
              try {
                var isJustify = document.queryCommandState('justifyFull');
                justifyBtn.classList.toggle('active', isJustify);
              } catch (e) {
                justifyBtn.classList.remove('active');
              }
            }
          }

          /**
           * Get parent element with specific tag names
           */
        }, {
          key: "getParentTag",
          value: function getParentTag(range, tagNames) {
            var node = range.commonAncestorContainer;
            if (node.nodeType === 3) {
              node = node.parentElement;
            }
            while (node && node !== this.playGround) {
              if (node.nodeType === 1 && tagNames.includes(node.tagName.toLowerCase())) {
                return node;
              }
              node = node.parentElement;
            }
            return null;
          }

          /**
           * Update format block state (headings, paragraph)
           */
        }, {
          key: "updateFormatBlockState",
          value: function updateFormatBlockState(range) {
            var node = range.commonAncestorContainer;
            if (node.nodeType === 3) {
              node = node.parentElement;
            }

            // Find the block-level element
            var blockTag = null;
            while (node && node !== this.playGround) {
              if (node.nodeType === 1) {
                var tagName = node.tagName.toLowerCase();
                if (['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'blockquote', 'pre', 'code', 'div'].includes(tagName)) {
                  blockTag = tagName;
                  break;
                }
              }
              node = node.parentElement;
            }

            // Update heading/format buttons
            var headingBtns = this.editor.querySelectorAll('button[data-action="formatBlock"]');
            headingBtns.forEach(function (btn) {
              var value = btn.getAttribute('data-value');
              if (value) {
                var valueLower = value.toLowerCase();
                // Handle paragraph - can be 'p' or 'P'
                if (valueLower === 'p' && (blockTag === 'p' || blockTag === 'div' || !blockTag)) {
                  btn.classList.toggle('active', true);
                } else if (valueLower === blockTag) {
                  btn.classList.toggle('active', true);
                } else {
                  btn.classList.remove('active');
                }
              }
            });
          }

          /**
           * Clear all toolbar button active states
           */
        }, {
          key: "clearAllToolbarStates",
          value: function clearAllToolbarStates() {
            var allButtons = this.editor.querySelectorAll('button[data-action]');
            allButtons.forEach(function (btn) {
              return btn.classList.remove('active');
            });
          }

          /**
           * Emit custom event
           */
        }, {
          key: "emitEvent",
          value: function emitEvent(eventName) {
            var data = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
            var event = new CustomEvent("ozzwyg:".concat(eventName), {
              detail: _objectSpread({
                editorID: this.editorID,
                editor: this.editor,
                playGround: this.playGround
              }, data),
              bubbles: true,
              cancelable: true
            });

            // Dispatch on both the editor element and the playground
            this.editor.dispatchEvent(event);
            this.playGround.dispatchEvent(event);
          }

          /**
           * Setup Editor DOM
           * @returns {*} EditorDOM
           */
        }, {
          key: "editorDOM",
          value: function editorDOM() {
            var _this11 = this;
            var toolsDOM = '';
            this.options.tools.forEach(function (tool) {
              if (_this11.tools[tool]) {
                var parentTool = document.createElement('div');
                parentTool.classList.add('ozz-wyg__tool', "ozz-wyg__tool--".concat(tool));
                if (_this11.tools[tool].child) {
                  parentTool.innerHTML = "<div class=\"ozz-wyg__tool-has-child\">".concat(_this11.tools[tool].dom, "<span class=\"more-tools-trigger\"></span></div>");
                  // Get Child tools
                  var childElementWrapper = document.createElement('div');
                  childElementWrapper.classList.add('ozz-wyg__tool-child');
                  _this11.options.tools.forEach(function (child2) {
                    if (_this11.tools[tool].child[child2]) {
                      var childItem = _this11.tools[tool].child[child2];
                      childElementWrapper.innerHTML += childItem.dom;
                    }
                  });
                  parentTool.appendChild(childElementWrapper);
                } else {
                  parentTool.innerHTML = _this11.tools[tool].dom;
                }
                toolsDOM += parentTool.outerHTML;
              }
            });
            this.toolbar = "<div class=\"ozz-wyg__toolbar\">".concat(toolsDOM, "</div>");
            this.editableArea = "<div class=\"ozz-wyg__editor-area\" data-editor-area contenteditable=\"true\"></div>";
            return this.toolbar + this.editableArea;
          }

          /**
           * Check if HTML view enabled
           * @returns {boolean}
           */
        }, {
          key: "isHTML",
          value: function isHTML() {
            return this.playGround.classList.contains('ozz-wyg-html-view');
          }

          /**
           * Utility - Find nested object item
           * @param {*} obj 
           * @param {*} key 
           * @returns 
           */
        }, {
          key: "findObject",
          value: function findObject(obj, key) {
            if (obj.hasOwnProperty(key)) {
              return obj[key];
            } else {
              for (var prop in obj) {
                if (obj[prop] !== null && _typeof2(obj[prop]) === 'object') {
                  var result = this.findObject(obj[prop], key);
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
        }, {
          key: "randomId",
          value: function randomId() {
            var length = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 6;
            return Math.random().toString(36).substring(2, length + 2);
          }

          /**
           * Fire Action
           * @param {*} event
           */
        }, {
          key: "fireAction",
          value: function fireAction(event) {
            var _this12 = this;
            var action = event.target.getAttribute('data-action');

            // Format block
            var value = action == 'formatBlock' ? event.target.getAttribute('data-value') : null;
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

            // Update toolbar states after action
            setTimeout(function () {
              return _this12.updateToolbarStates();
            }, 10);
          }

          /**
           * Link Tool Popup
           */
        }, {
          key: "linkPopUp",
          value: function linkPopUp(ev) {
            var _this13 = this;
            var linkCls = 'ozz-wyg__tool-link-',
              parent = ev.target.closest(".".concat(linkCls, "trigger"));
            if (!parent) return;
            var settingsDOM = parent.querySelector(".".concat(linkCls, "setting"));
            if (!settingsDOM) return;
            var selection = window.getSelection();

            // Store selection
            var sRange = false;
            var selectionStr = selection.toString();
            if (selection.getRangeAt && selection.rangeCount) {
              sRange = selection.getRangeAt(0).cloneRange();
            }

            // Check if there's an existing anchor link
            var existingAnchor = null;
            if (selection.anchorNode) {
              var _parent8 = selection.anchorNode.parentElement;
              if (_parent8 && _parent8.tagName === 'A') {
                existingAnchor = _parent8;
              } else if (_parent8 && _parent8.closest('a')) {
                existingAnchor = _parent8.closest('a');
              }
            }
            var existingURL = existingAnchor ? existingAnchor.href : '';
            var existingTarget = existingAnchor ? existingAnchor.target : '';
            settingsDOM.innerHTML = "\n      <label>URL:</label> <input type=\"text\" id=\"url-".concat(this.editorID, "\" value=\"").concat(existingURL, "\"><br>\n      <label>Target:</label> <input type=\"text\" id=\"target-").concat(this.editorID, "\" value=\"").concat(existingTarget ? existingTarget : '_blank', "\"><br>\n      <button type=\"button\" class=\"ozz-wyg-regular-btn\" id=\"insertLinkTrigger-").concat(this.editorID, "\">").concat(existingAnchor ? 'Update' : 'Insert', "</button>");
            settingsDOM.classList.toggle('active');

            // Insert or update link
            var insertLinkTrigger = settingsDOM.querySelector('#insertLinkTrigger-' + this.editorID);
            insertLinkTrigger.addEventListener('click', function () {
              var urlInput = settingsDOM.querySelector('#url-' + _this13.editorID).value;
              var targetInput = settingsDOM.querySelector('#target-' + _this13.editorID).value;
              if (urlInput && targetInput) {
                if (existingAnchor) {
                  // Update existing anchor
                  existingAnchor.href = urlInput;
                  existingAnchor.target = targetInput;
                } else {
                  // Create new anchor
                  var anchor = document.createElement('a');
                  anchor.href = urlInput;
                  anchor.target = targetInput;
                  anchor.textContent = selectionStr || urlInput;
                  if (sRange) {
                    selection.removeAllRanges();
                    selection.addRange(sRange);
                  }
                  document.execCommand('insertHTML', false, anchor.outerHTML);
                }
                settingsDOM.classList.remove('active'); // Close Popup
                _this13.linkPopOver(); // Init Link Popover
              }
            });

            // Close popup
            document.addEventListener('click', function (e) {
              if (!parent.contains(e.target)) {
                settingsDOM.classList.remove('active');
                _this13.linkPopOver(); // Init Link Popover
              }
            });
          }

          /**
           * Link popover
           */
        }, {
          key: "linkPopOver",
          value: function linkPopOver() {
            var _this14 = this;
            this.playGround.querySelectorAll('a').forEach(function (anchor) {
              // Only add listeners if not already added
              if (anchor.hasAttribute('data-link-handled')) {
                return;
              }
              anchor.setAttribute('data-link-handled', 'true');
              anchor.addEventListener('click', function (e) {
                if (anchor.getAttribute('role') !== 'popover') {
                  e.preventDefault();
                  e.stopPropagation();
                  var popoverDOM = document.createElement('span');
                  popoverDOM.setAttribute('contenteditable', false);
                  popoverDOM.classList.add('ozz-wyg-popover');
                  popoverDOM.innerHTML = "\n          <a href=\"".concat(anchor.href, "\" role=\"popover\" target=\"_blank\">").concat(anchor.href, "</a>\n          <button type=\"button\" class=\"ozz-wyg-editlink\"></button>\n          <button type=\"button\" class=\"ozz-wyg-unlink\"></button>");
                  if (_this14.editor.querySelectorAll('.ozz-wyg-popover').length === 0) {
                    anchor.insertAdjacentElement('afterend', popoverDOM);

                    // Position popover element
                    var _2 = 0,
                      rx = _2.rx,
                      ry = _2.ry;
                    if (popoverDOM.closest('.ozz-wyg-table-wrapper')) {
                      var cTbl = popoverDOM.closest('.ozz-wyg-table-wrapper');
                      ry = cTbl.getBoundingClientRect().y;
                      rx = cTbl.getBoundingClientRect().x;
                    }
                    popoverDOM.style.top = "".concat(e.clientY - ry, "px");
                    popoverDOM.style.left = "".concat(e.clientX - rx, "px");
                  }
                  var _removePopoverEv = function removePopoverEv(ev) {
                    if (!anchor.contains(ev.target) && !popoverDOM.contains(ev.target)) {
                      popoverDOM.remove();
                      document.removeEventListener('click', _removePopoverEv);
                    }
                  };
                  document.addEventListener('click', _removePopoverEv);

                  // Unlink
                  popoverDOM.querySelector('.ozz-wyg-unlink').addEventListener('click', function (ev) {
                    ev.stopPropagation();
                    var textNode = document.createTextNode(anchor.textContent);
                    anchor.parentNode.replaceChild(textNode, anchor);
                    popoverDOM.remove();
                    document.removeEventListener('click', _removePopoverEv);
                  });

                  // Edit this link
                  popoverDOM.querySelector('.ozz-wyg-editlink').addEventListener('click', function () {
                    var linkBtn = _this14.editor.querySelector('button[data-action="link"]');
                    setTimeout(function () {
                      linkBtn.click();
                    }, 1);
                  });
                }
              });
            });
          }

          /**
           * Table Popup
           */
        }, {
          key: "tablePopUp",
          value: function tablePopUp(ev) {
            var _this15 = this;
            var tableCls = 'ozz-wyg__tool-table-',
              parent = ev.target.closest(".".concat(tableCls, "trigger"));
            if (!parent) return;
            var settingsDOM = parent.querySelector(".".concat(tableCls, "setting"));
            if (!settingsDOM) return;
            var selection = window.getSelection();

            // Store selection
            var sRange = false;
            if (selection.getRangeAt && selection.rangeCount) {
              sRange = selection.getRangeAt(0).cloneRange();
            }
            settingsDOM.innerHTML = "\n      <input type=\"number\" min=\"1\" max=\"100\" value=\"2\" placeholder=\"X\" id=\"row-".concat(this.editorID, "\">\n      <input type=\"number\" min=\"1\" max=\"100\" value=\"2\" placeholder=\"Y\" id=\"column-").concat(this.editorID, "\">\n      <span class=\"sub-options\">\n        <span>\n          <input type=\"checkbox\" id=\"has-th-").concat(this.editorID, "\">\n          <label for=\"has-th-").concat(this.editorID, "\">No Header</label>\n        </span>\n        <span>\n          <input type=\"checkbox\" id=\"has-footer-").concat(this.editorID, "\">\n          <label for=\"has-footer-").concat(this.editorID, "\">No Footer</label>\n        </span>\n      </span>\n      <button type=\"button\" class=\"ozz-wyg-regular-btn\" id=\"insertTableTrigger-").concat(this.editorID, "\">Insert</button>");
            settingsDOM.classList.toggle('active');

            // Insert or update Table
            var insertTableTrigger = settingsDOM.querySelector('#insertTableTrigger-' + this.editorID);
            insertTableTrigger.addEventListener('click', function () {
              var rows = settingsDOM.querySelector('#row-' + _this15.editorID).value,
                columns = settingsDOM.querySelector('#column-' + _this15.editorID).value,
                noHead = settingsDOM.querySelector('#has-th-' + _this15.editorID).checked,
                noFooter = settingsDOM.querySelector('#has-footer-' + _this15.editorID).checked;
              rows = rows > 100 ? 100 : rows;
              columns = columns > 100 ? 100 : columns;

              // Create Table
              var table = document.createElement('table');
              // Table Head
              if (noHead === false) {
                var tHead = table.createTHead();
                var headerRow = tHead.insertRow();
                for (var i = 0; i < columns; i++) {
                  var th = document.createElement('th');
                  headerRow.appendChild(th);
                }
              }

              // Table Body
              var tBody = table.createTBody();
              for (var _i = 0; _i < rows; _i++) {
                var tRows = tBody.insertRow(_i);
                for (var j = 0; j < columns; j++) {
                  tRows.insertCell(j);
                }
              }

              // Table Footer
              if (noFooter === false) {
                var tFoot = table.createTFoot();
                var footRow = tFoot.insertRow();
                for (var _i2 = 0; _i2 < columns; _i2++) {
                  footRow.insertCell(_i2);
                }
              }

              // Set Range Again
              if (sRange) {
                selection.removeAllRanges();
                selection.addRange(sRange);
              }

              // Create Table Wrapper
              var $table = document.createElement('div');
              $table.classList.add('ozz-wyg-table-wrapper');
              $table.innerHTML = table.outerHTML;
              document.execCommand('insertHTML', false, "<br>".concat($table.outerHTML, "<br>"));
              _this15.tableActions(); // Table Actions
            });

            // Close popup
            document.addEventListener('click', function (e) {
              if (!parent.contains(e.target)) {
                settingsDOM.classList.remove('active');
              }
            });
          }

          /**
           * Table actions
           */
        }, {
          key: "tableActions",
          value: function tableActions() {
            var _this16 = this;
            var $table = this.playGround.querySelectorAll('.ozz-wyg-table-wrapper');
            $table.forEach(function (tbl) {
              // Only add listeners if not already added
              if (tbl.hasAttribute('data-table-handled')) {
                return;
              }
              tbl.setAttribute('data-table-handled', 'true');
              tbl.addEventListener('mouseover', function () {
                var tblTools = tbl.querySelectorAll('.ozz-wyg-table-actions');
                if (tblTools.length === 0) {
                  var actions = document.createElement('div');
                  actions.setAttribute('contenteditable', false);
                  actions.classList.add('ozz-wyg-table-actions');
                  actions.innerHTML = "\n          <span class=\"ozz-wyg-table-actions__row\">\n            <button type=\"button\" title=\"Add Row\" data-tbl-action=\"addrow\">Add Row</button>\n            <button type=\"button\" title=\"Remove Row\" data-tbl-action=\"deleterow\">Delete Row</button>\n          </span>\n          <span class=\"ozz-wyg-table-actions__column\">\n            <button type=\"button\" title=\"Add Column\" data-tbl-action=\"addcol\">Add Column</button>\n            <button type=\"button\" title=\"Remove Column\" data-tbl-action=\"deletecol\">Delete Column</button>\n          </span>";

                  // Perform Table Actions
                  actions.querySelectorAll('button').forEach(function (btn) {
                    btn.addEventListener('click', function (e) {
                      e.preventDefault();
                      e.stopPropagation();
                      var action = e.target.getAttribute('data-tbl-action');
                      switch (action) {
                        case 'addrow':
                          _this16.addTableRow(tbl);
                          break;
                        case 'deleterow':
                          _this16.deleteTableRow(tbl);
                          break;
                        case 'addcol':
                          _this16.addTableCol(tbl);
                          break;
                        case 'deletecol':
                          _this16.deleteTableCol(tbl);
                          break;
                        default:
                          break;
                      }
                    });
                  });
                  tbl.appendChild(actions);
                }
              });
              tbl.addEventListener('mouseleave', function () {
                var tblTools = tbl.querySelectorAll('.ozz-wyg-table-actions');
                if (tblTools.length > 0) {
                  tblTools[0].remove();
                }
              });
            });
          }

          /**
           * Add Table Row
           * @param table_wrap
           */
        }, {
          key: "addTableRow",
          value: function addTableRow(table_wrap) {
            if (!table_wrap) return;
            var tbody = table_wrap.querySelector('tbody');
            if (tbody) {
              var firstRow = tbody.querySelector('tr');
              var cellsCount = firstRow ? firstRow.querySelectorAll('td, th').length : 1;
              var newRow = tbody.insertRow(-1);
              for (var i = 0; i < cellsCount; i++) {
                var td = document.createElement('td');
                td.innerHTML = '<br>';
                newRow.appendChild(td);
              }
            }
          }

          /**
           * Delete Table Row
           * @param table_wrap
           */
        }, {
          key: "deleteTableRow",
          value: function deleteTableRow(table_wrap) {
            if (!table_wrap) return;
            var table = table_wrap.querySelector('table');
            if (!table) return;
            var tbody = table.querySelector('tbody');
            if (tbody && tbody.rows.length > 1) {
              tbody.deleteRow(-1);
            }
          }

          /**
           * Add Table Column
           * @param table_wrap
           */
        }, {
          key: "addTableCol",
          value: function addTableCol(table_wrap) {
            if (!table_wrap) return;
            var thead = table_wrap.querySelector('thead');
            var tbody = table_wrap.querySelector('tbody');
            var tfoot = table_wrap.querySelector('tfoot');
            var addCellToRows = function addCellToRows(rows, newCellIndex) {
              var type = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'td';
              if (!rows || rows.length === 0) return;
              rows.forEach(function (row) {
                var td = document.createElement(type);
                td.innerHTML = '<br>';
                if (newCellIndex < row.cells.length) {
                  row.insertBefore(td, row.cells[newCellIndex]);
                } else {
                  row.appendChild(td);
                }
              });
            };
            if (thead) {
              var headerRows = thead.querySelectorAll('tr');
              if (headerRows.length > 0) {
                var firstRow = headerRows[0];
                var newCellIndex = firstRow ? firstRow.querySelectorAll('th, td').length : 0;
                addCellToRows(headerRows, newCellIndex, 'th');
              }
            }
            if (tbody) {
              var bodyRows = tbody.querySelectorAll('tr');
              if (bodyRows.length > 0) {
                var _firstRow = bodyRows[0];
                var _newCellIndex = _firstRow ? _firstRow.querySelectorAll('td').length : 0;
                addCellToRows(bodyRows, _newCellIndex);
              }
            }
            if (tfoot) {
              var footerRows = tfoot.querySelectorAll('tr');
              if (footerRows.length > 0) {
                var _firstRow2 = footerRows[0];
                var _newCellIndex2 = _firstRow2 ? _firstRow2.querySelectorAll('td').length : 0;
                addCellToRows(footerRows, _newCellIndex2);
              }
            }
          }

          /**
           * Delete Table Column
           * @param table_wrap
           */
        }, {
          key: "deleteTableCol",
          value: function deleteTableCol(table_wrap) {
            if (!table_wrap) return;
            var thead = table_wrap.querySelector('thead');
            var tbody = table_wrap.querySelector('tbody');
            var tfoot = table_wrap.querySelector('tfoot');
            var deleteLastCell = function deleteLastCell(rows) {
              if (!rows || rows.length === 0) return;
              var firstRow = rows[0];
              if (!firstRow) return;
              var lastCellIndex = firstRow.cells.length - 1;
              if (lastCellIndex >= 0) {
                rows.forEach(function (row) {
                  if (row.cells.length > 1) {
                    row.deleteCell(lastCellIndex);
                  }
                });
              }
            };
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
        }, {
          key: "mediaPopUp",
          value: function mediaPopUp(ev) {
            var _this17 = this;
            var linkCls = 'ozz-wyg__tool-media-',
              parent = ev.target.closest(".".concat(linkCls, "trigger"));
            if (!parent) return;
            var settingsDOM = parent.querySelector(".".concat(linkCls, "setting"));
            if (!settingsDOM) return;
            var selection = window.getSelection();

            // Store selection
            var sRange = false;
            if (selection.getRangeAt && selection.rangeCount) {
              sRange = selection.getRangeAt(0).cloneRange();
            }

            // Get Selected Media element tp update - To Do
            var existingURL = '';
            var existingALT = '';
            settingsDOM.innerHTML = "\n      <label>Upload:</label> <input type=\"file\" accept=\"image/*,video/*\" id=\"file-".concat(this.editorID, "\"><br>\n      <label>Media URL:</label> <input type=\"text\" id=\"url-").concat(this.editorID, "\" value=\"").concat(existingURL, "\"><br>\n      <label>Alt:</label> <input type=\"text\" id=\"alt-").concat(this.editorID, "\" value=\"").concat(existingALT, "\"><br>\n      <button type=\"button\" class=\"ozz-wyg-regular-btn\" id=\"insertMediaTrigger-").concat(this.editorID, "\">").concat(existingURL ? 'Update' : 'Insert', "</button>");
            settingsDOM.classList.toggle('active');

            // Insert Media
            var insertMediaTrigger = settingsDOM.querySelector('#insertMediaTrigger-' + this.editorID);
            insertMediaTrigger.addEventListener('click', function () {
              var files = settingsDOM.querySelector('#file-' + _this17.editorID).files;
              var url = settingsDOM.querySelector('#url-' + _this17.editorID).value;
              var alt = settingsDOM.querySelector('#alt-' + _this17.editorID).value;
              var altText = alt;
              var fileType = 'unknown';

              // Set Range Again
              if (sRange) {
                selection.removeAllRanges();
                selection.addRange(sRange);
              }
              if (files.length > 0) {
                // File (DataURI)
                var file = files[0];
                fileType = file.type.split('/')[0];
                if (fileType === 'image' || fileType === 'video') {
                  var reader = new FileReader();
                  reader.onload = function (event) {
                    altText = altText == '' ? file.name : altText;
                    processAndInsertMediaElement(fileType, event.target.result, altText);
                  };
                  reader.readAsDataURL(file);
                }
              } else if (url !== '') {
                // URL attachment
                var urlParts = url.split('/');
                var filename = urlParts[urlParts.length - 1];
                var fileExt = filename.includes('.') ? filename.split('.').pop().toLowerCase() : '';

                // Determine file type based on the file extension or URL
                if (['jpg', 'jpeg', 'png', 'svg', 'webp', 'gif'].includes(fileExt)) {
                  fileType = 'image';
                } else if (['mp4', 'webm', 'ogg', 'avi', 'mov'].includes(fileExt)) {
                  fileType = 'video';
                } else if (_this17.isYouTubeURL(url)) {
                  fileType = 'youtube';
                } else if (_this17.isVimeoURL(url)) {
                  fileType = 'vimeo';
                }
                altText = altText !== '' ? altText : filename;
                processAndInsertMediaElement(fileType, url, altText);
              }
            });

            // Process and Insert Media Element
            var processAndInsertMediaElement = function processAndInsertMediaElement(fileType, mediaItem, altText) {
              var mediaElement;
              if (fileType === 'image') {
                mediaElement = "<img src=\"".concat(mediaItem, "\" class=\"align-center\" alt=\"").concat(altText, "\">");
              } else if (fileType === 'video') {
                mediaElement = "<br><div class=\"media-wrapper\"><video src=\"".concat(mediaItem, "\" controls></video></div><br>");
              } else if (fileType === 'youtube') {
                mediaElement = "<br>".concat(_this17.getYouTubeEmbedCode(mediaItem), "<br>");
              } else if (fileType === 'vimeo') {
                mediaElement = "<br>".concat(_this17.getVimeoEmbedCode(mediaItem), "<br>");
              } else {
                mediaElement = false;
              }

              // Insert Media
              if (mediaElement !== false) {
                document.execCommand('insertHTML', false, "<br>".concat(mediaElement, "<br>"));
                settingsDOM.classList.remove('active'); // Close PopUp

                _this17.mediaPopover(); // Config Media Popover
              }
            };

            // Close popup
            document.addEventListener('click', function (e) {
              if (!parent.contains(e.target)) {
                settingsDOM.classList.remove('active');
              }
            });
          }

          /**
           * Media Popover
           */
        }, {
          key: "mediaPopover",
          value: function mediaPopover() {
            var _this18 = this;
            // Use event delegation to prevent duplicate listeners
            var mediaItems = this.playGround.querySelectorAll('img, .media-wrapper');
            mediaItems.forEach(function (mediaItem) {
              // Remove existing click handlers by cloning
              if (mediaItem.hasAttribute('data-media-handled')) {
                return;
              }
              mediaItem.setAttribute('data-media-handled', 'true');
              mediaItem.addEventListener('click', function (e) {
                var popoverDOM = document.createElement('div');
                popoverDOM.setAttribute('contenteditable', false);
                popoverDOM.classList.add('ozz-wyg-media-actions');

                // Set Selected Width
                var regex = /^w-\d+/;
                var widthClass = Array.from(mediaItem.classList).find(function (className) {
                  return regex.test(className);
                });
                var options = '<option value="auto">Auto</option>';
                for (var i = 1; i < 21; i++) {
                  var clsName = 'w-' + i * 5;
                  if (clsName == widthClass) {
                    options += "<option selected value=\"".concat(clsName, "\">").concat(i * 5, "%</option>");
                  } else {
                    options += "<option value=\"".concat(clsName, "\">").concat(i * 5, "%</option>");
                  }
                }
                popoverDOM.innerHTML = "\n          <button type=\"button\" title=\"Align Left\" data-media-action=\"align-left\">Align Left</button>\n          <button type=\"button\" title=\"Align Center\" data-media-action=\"align-center\">Align Center</button>\n          <button type=\"button\" title=\"Align Right\" data-media-action=\"align-right\">Align Right</button>\n          <button type=\"button\" title=\"Inline\" data-media-action=\"inline\">Inline</button>\n          <select data-media-action=\"width\">".concat(options, "</select>\n          <button type=\"button\" title=\"Delete\" data-media-action=\"delete\">Delete</button>\n        ");

                // Remove any existing popover first
                var existingPopover = _this18.editor.querySelector('.ozz-wyg-media-actions');
                if (existingPopover) {
                  existingPopover.remove();
                }
                mediaItem.insertAdjacentElement('afterend', popoverDOM);

                // Position popover element at the click point
                // Get the editor's position relative to viewport
                var editorRect = _this18.editor.getBoundingClientRect();

                // Calculate position relative to editor container
                var relativeX = e.clientX - editorRect.left;
                var relativeY = e.clientY - editorRect.top;

                // Ensure editor has relative positioning for absolute children
                var editorPosition = window.getComputedStyle(_this18.editor).position;
                if (editorPosition === 'static') {
                  _this18.editor.style.position = 'relative';
                }

                // Set position relative to editor (not absolute screen coordinates)
                popoverDOM.style.position = 'absolute';
                popoverDOM.style.top = "".concat(relativeY, "px");
                popoverDOM.style.left = "".concat(relativeX, "px");
                popoverDOM.style.zIndex = '1000';

                // Media Actions
                popoverDOM.querySelectorAll('button, select').forEach(function (actionTrigger) {
                  if (actionTrigger.tagName === 'SELECT') {
                    actionTrigger.addEventListener('change', function () {
                      var _mediaItem$classList;
                      var action = actionTrigger.value;
                      (_mediaItem$classList = mediaItem.classList).remove.apply(_mediaItem$classList, _toConsumableArray2(Array.from(mediaItem.classList).filter(function (className) {
                        return className.startsWith('w-');
                      })));
                      if (action !== 'auto') {
                        mediaItem.classList.add(action);
                      }
                    });
                  } else {
                    actionTrigger.addEventListener('click', function () {
                      var action = actionTrigger.getAttribute('data-media-action');
                      if (action == 'align-left' || action == 'align-right' || action == 'align-center') {
                        var _mediaItem$classList2;
                        (_mediaItem$classList2 = mediaItem.classList).remove.apply(_mediaItem$classList2, _toConsumableArray2(Array.from(mediaItem.classList).filter(function (className) {
                          return className.startsWith('align-');
                        })));
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

                // Close popover
                var _tempCloseEvent = function tempCloseEvent(ev) {
                  if (!popoverDOM.contains(ev.target) && ev.target !== popoverDOM && ev.target !== mediaItem) {
                    var _this18$editor$queryS;
                    (_this18$editor$queryS = _this18.editor.querySelector('.ozz-wyg-media-actions')) === null || _this18$editor$queryS === void 0 || _this18$editor$queryS.remove();
                    document.removeEventListener('click', _tempCloseEvent);
                  }
                };
                document.addEventListener('click', _tempCloseEvent);
              });
            });
          }

          /**
           * Is Youtube URL
           * @param {string} url
           * @return
           */
        }, {
          key: "isYouTubeURL",
          value: function isYouTubeURL(url) {
            return url.includes('youtube.com/watch') || url.includes('youtu.be') || url.includes('youtube.com/embed');
          }

          /**
           * Is Vimeo URL
           * @param {string} url
           * @returns
           */
        }, {
          key: "isVimeoURL",
          value: function isVimeoURL(url) {
            return url.includes('vimeo.com') || url.includes('player.vimeo.com/video');
          }

          /**
           * Get Embed code (Youtube)
           * @param {string} url
           * @returns 
           */
        }, {
          key: "getYouTubeEmbedCode",
          value: function getYouTubeEmbedCode(url) {
            var videoID = this.extractYouTubeVideoID(url);
            return "<div class=\"media-wrapper\"><span class=\"height-holder\"></span><iframe src=\"https://www.youtube.com/embed/".concat(videoID, "\" frameborder=\"0\" allowfullscreen></iframe></div>");
          }

          /**
           * Extract Youtube video ID
           * @param {string} url 
           * @returns 
           */
        }, {
          key: "extractYouTubeVideoID",
          value: function extractYouTubeVideoID(url) {
            var regex = /(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/;
            var match = url.match(regex);
            return match ? match[1] : '';
          }

          /**
           * Get Embed code (Vimeo)
           * @param {string} url 
           * @returns 
           */
        }, {
          key: "getVimeoEmbedCode",
          value: function getVimeoEmbedCode(url) {
            var videoID = this.extractVimeoVideoID(url);
            return "<div class=\"media-wrapper\"><span class=\"height-holder\"></span><iframe src=\"https://player.vimeo.com/video/".concat(videoID, "\" frameborder=\"0\" allowfullscreen></iframe></div>");
          }

          /**
           * Extract Vimeo video ID
           * @param {string} url 
           * @returns 
           */
        }, {
          key: "extractVimeoVideoID",
          value: function extractVimeoVideoID(url) {
            var regex = /vimeo\.com\/(?:video\/)?(\d+)/;
            var match = url.match(regex);
            return match ? match[1] : '';
          }

          /**
           * Execute Command
           * @param {string} command
           * @param value
           */
        }, {
          key: "exeCMD",
          value: function exeCMD(command) {
            var value = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
            document.execCommand(command, false, value);
          }

          /**
           * Quote Text
           */
        }, {
          key: "quoteText",
          value: function quoteText() {
            var selection = window.getSelection();
            var selectedText = selection.toString().trim();
            var quote = '<blockquote><p>' + (selectedText === '' ? '<br>' : selectedText) + '</p><footer class="blockquote-footer">--Footer, <cite>cite</cite></footer></blockquote><br>';
            document.execCommand('insertHTML', false, quote);
          }

          /**
           * Add Code
           */
        }, {
          key: "codeText",
          value: function codeText() {
            var selection = window.getSelection();
            var selectedText = selection.toString();
            if (selectedText === '') {
              // If no selection, insert empty code tag
              document.execCommand('insertHTML', false, '<code></code>');
            } else {
              var code = '<code>' + selectedText + '</code>';
              document.execCommand('insertHTML', false, code);
            }
          }

          /**
           * Toggle Code and visual view
           */
        }, {
          key: "toggleCodeView",
          value: function toggleCodeView() {
            var _this19 = this;
            if (this.isHTML()) {
              this.playGround.classList.remove('ozz-wyg-html-view');
              // Parse HTML from text content
              var htmlContent = this.playGround.textContent;
              this.playGround.innerHTML = htmlContent;
              // Re-initialize all interactive features after switching back to visual view
              setTimeout(function () {
                _this19.initializeContentFeatures();
              }, 0);
            } else {
              this.playGround.querySelectorAll('[contenteditable="false"]').forEach(function (element) {
                element.remove();
              });
              this.playGround.classList.add('ozz-wyg-html-view');
              this.playGround.textContent = this.playGround.innerHTML;
            }
          }

          /**
           * Get value from editor
           * @param {string} editorID - Optional editor ID, if not provided returns first editor's value
           */
        }, {
          key: "getValue",
          value: function getValue() {
            var editorID = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
            if (editorID && this.editorInstances.has(editorID)) {
              return this.editorInstances.get(editorID).playGround.innerHTML;
            }
            // Return first editor's value for backward compatibility
            if (this.editorInstances.size > 0) {
              var firstInstance = this.editorInstances.values().next().value;
              return firstInstance.playGround.innerHTML;
            }
            return this.playGround ? this.playGround.innerHTML : '';
          }

          /**
           * Initialize interactive features for loaded content
           * This should be called after setting content via setValue or when content is loaded
           */
        }, {
          key: "initializeContentFeatures",
          value: function initializeContentFeatures() {
            if (!this.playGround) return;

            // Wrap tables that aren't wrapped yet
            var tables = this.playGround.querySelectorAll('table');
            tables.forEach(function (table) {
              if (!table.closest('.ozz-wyg-table-wrapper')) {
                var tableWrapped = document.createElement('div');
                tableWrapped.classList.add('ozz-wyg-table-wrapper');
                var tableParent = table.parentNode;
                tableParent.insertBefore(tableWrapped, table);
                tableWrapped.appendChild(table);

                // Clear inline styles
                table.removeAttribute('style');
                var tItems = table.querySelectorAll('tbody, thead, tfoot, tr, td, th');
                tItems.forEach(function (item) {
                  item.removeAttribute('style');
                });
              }
            });

            // Remove existing handlers to re-initialize
            this.playGround.querySelectorAll('[data-link-handled]').forEach(function (el) {
              el.removeAttribute('data-link-handled');
            });
            this.playGround.querySelectorAll('[data-media-handled]').forEach(function (el) {
              el.removeAttribute('data-media-handled');
            });
            this.playGround.querySelectorAll('[data-table-handled]').forEach(function (el) {
              el.removeAttribute('data-table-handled');
            });

            // Initialize all interactive features
            this.mediaPopover();
            this.tableActions();
            this.linkPopOver();
          }

          /**
           * Set value for editor
           * @param {string} value - HTML string to set
           * @param {string|null} editorID - target editor ID; if null and single editor exists, applies to first
           */
        }, {
          key: "setValue",
          value: function setValue(value) {
            var _this20 = this;
            var editorID = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
            if (value === undefined || value === null) return;
            var targetInstance = null;
            if (editorID && this.editorInstances.has(editorID)) {
              targetInstance = this.editorInstances.get(editorID);
            } else if (this.editorInstances.size === 1) {
              targetInstance = this.editorInstances.values().next().value;
            }
            var playGroundToSet = null;
            var editorToSet = null;
            var editorIDToSet = null;
            if (targetInstance && targetInstance.playGround) {
              playGroundToSet = targetInstance.playGround;
              editorToSet = targetInstance.element;
              editorIDToSet = targetInstance.id;
            } else if (this.playGround) {
              playGroundToSet = this.playGround;
              editorToSet = this.editor;
              editorIDToSet = this.editorID;
            }
            if (playGroundToSet) {
              // Set the content
              playGroundToSet.innerHTML = value;

              // Initialize features after DOM update
              // Use captured values in closure to ensure correct editor instance
              setTimeout(function () {
                // Set context for initialization using captured values
                var originalEditor = _this20.editor;
                var originalPlayGround = _this20.playGround;
                var originalEditorID = _this20.editorID;
                _this20.editor = editorToSet;
                _this20.playGround = playGroundToSet;
                _this20.editorID = editorIDToSet;
                if (editorIDToSet) {
                  _this20.currentEditorID = editorIDToSet;
                }
                _this20.initializeContentFeatures();

                // Restore original context after initialization
                _this20.editor = originalEditor;
                _this20.playGround = originalPlayGround;
                _this20.editorID = originalEditorID;
              }, 0);
            }
          }

          /**
           * Get editor instance by editor ID
           * @param {string} editorID - Editor ID
           * @returns {Object|null} Editor instance or null
           */
        }, {
          key: "getEditorInstance",
          value: function getEditorInstance(editorID) {
            return this.editorInstances.get(editorID) || null;
          }

          /**
           * Get all editor instances
           * @returns {Map} Map of all editor instances
           */
        }, {
          key: "getAllEditorInstances",
          value: function getAllEditorInstances() {
            return this.editorInstances;
          }

          /**
           * Bind event to specific editor instance
           * @param {string} editorID - Editor ID
           * @param {string} eventName - Event name (without 'ozzwyg:' prefix)
           * @param {Function} callback - Event callback function
           */
        }, {
          key: "on",
          value: function on(editorID, eventName, callback) {
            var instance = this.getEditorInstance(editorID);
            if (instance && instance.element) {
              instance.element.addEventListener("ozzwyg:".concat(eventName), callback);
            }
          }

          /**
           * Unbind event from specific editor instance
           * @param {string} editorID - Editor ID
           * @param {string} eventName - Event name (without 'ozzwyg:' prefix)
           * @param {Function} callback - Event callback function
           */
        }, {
          key: "off",
          value: function off(editorID, eventName, callback) {
            var instance = this.getEditorInstance(editorID);
            if (instance && instance.element) {
              instance.element.removeEventListener("ozzwyg:".concat(eventName), callback);
            }
          }
        }]);
      }();
      /**
       * Static method to get OzzWyg instance from DOM element
       * @param {string|HTMLElement} selector - CSS selector or DOM element
       * @returns {OzzWyg|null} OzzWyg instance or null
       */
      // Static registry to track all editor instances
      _defineProperty2(OzzWyg, "instances", new Map());
      OzzWyg.getInstance = function (selector) {
        var element = null;
        if (typeof selector === 'string') {
          element = document.querySelector(selector);
        } else if (selector instanceof HTMLElement) {
          element = selector;
        }
        if (element) {
          // Check if element has data-editor attribute
          var editorID = element.getAttribute('data-editor');
          if (editorID && OzzWyg.instances.has(editorID)) {
            return OzzWyg.instances.get(editorID);
          }
          // Check if element itself is registered
          if (OzzWyg.instances.has(element)) {
            return OzzWyg.instances.get(element);
          }
        }
        return null;
      };

      /**
       * Static method to get value from editor by selector
       * @param {string|HTMLElement} selector - CSS selector or DOM element
       * @returns {string} Editor content or empty string
       */
      OzzWyg.getValue = function (selector) {
        var instance = OzzWyg.getInstance(selector);
        if (instance) {
          var element = typeof selector === 'string' ? document.querySelector(selector) : selector;
          var editorID = element ? element.getAttribute('data-editor') : null;
          return instance.getValue(editorID);
        }
        return '';
      };

      /**
       * Static method to set value for editor by selector or element
       * @param {string|HTMLElement} selector - CSS selector or DOM element
       * @param {string} value - HTML string
       */
      OzzWyg.setValue = function (selector, value) {
        var instance = OzzWyg.getInstance(selector);
        if (instance) {
          var element = typeof selector === 'string' ? document.querySelector(selector) : selector;
          var editorID = element ? element.getAttribute('data-editor') : null;
          instance.setValue(value, editorID);
        }
      };

      /**
       * Static method to bind event to editor by selector
       * @param {string|HTMLElement} selector - CSS selector or DOM element
       * @param {string} eventName - Event name (without 'ozzwyg:' prefix)
       * @param {Function} callback - Event callback function
       */
      OzzWyg.on = function (selector, eventName, callback) {
        var instance = OzzWyg.getInstance(selector);
        if (instance) {
          var element = typeof selector === 'string' ? document.querySelector(selector) : selector;
          var editorID = element ? element.getAttribute('data-editor') : null;
          if (editorID) {
            instance.on(editorID, eventName, callback);
          } else if (element) {
            // Fallback: bind directly to element
            element.addEventListener("ozzwyg:".concat(eventName), callback);
          }
        }
      };

      /**
       * Static method to unbind event from editor by selector
       * @param {string|HTMLElement} selector - CSS selector or DOM element
       * @param {string} eventName - Event name (without 'ozzwyg:' prefix)
       * @param {Function} callback - Event callback function
       */
      OzzWyg.off = function (selector, eventName, callback) {
        var instance = OzzWyg.getInstance(selector);
        if (instance) {
          var element = typeof selector === 'string' ? document.querySelector(selector) : selector;
          var editorID = element ? element.getAttribute('data-editor') : null;
          if (editorID) {
            instance.off(editorID, eventName, callback);
          } else if (element) {
            // Fallback: unbind directly from element
            element.removeEventListener("ozzwyg:".concat(eventName), callback);
          }
        }
      };

      // Default options for the plugin
      OzzWyg.defaults = {
        pluginName: 'Ozz Wysiwyg Editor',
        selector: '[data-ozz-wyg]',
        tools: ['headings', 'paragraph', 'code', 'quote', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'bold', 'italic', 'underline', 'strikethrough', 'subscript', 'superscript', 'alignLeft', 'alignRight', 'alignCenter', 'justify', 'indent', 'outdent', 'ol', 'ul', 'link', 'table', 'media', 'codeView']
      };
      /* harmony default export */
      var __WEBPACK_DEFAULT_EXPORT__ = OzzWyg;

      /***/
    })

    /******/
  };
  /************************************************************************/
  /******/ // The module cache
  /******/
  var __webpack_module_cache__ = {};
  /******/
  /******/ // The require function
  /******/
  function __webpack_require__(moduleId) {
    /******/ // Check if module is in cache
    /******/var cachedModule = __webpack_module_cache__[moduleId];
    /******/
    if (cachedModule !== undefined) {
      /******/return cachedModule.exports;
      /******/
    }
    /******/ // Create a new module (and put it into the cache)
    /******/
    var module = __webpack_module_cache__[moduleId] = {
      /******/id: moduleId,
      /******/loaded: false,
      /******/exports: {}
      /******/
    };
    /******/
    /******/ // Execute the module function
    /******/
    __webpack_modules__[moduleId](module, module.exports, __webpack_require__);
    /******/
    /******/ // Flag the module as loaded
    /******/
    module.loaded = true;
    /******/
    /******/ // Return the exports of the module
    /******/
    return module.exports;
    /******/
  }
  /******/
  /************************************************************************/
  /******/ /* webpack/runtime/amd options */
  /******/
  (function () {
    /******/__webpack_require__.amdO = {};
    /******/
  })();
  /******/
  /******/ /* webpack/runtime/define property getters */
  /******/
  (function () {
    /******/ // define getter functions for harmony exports
    /******/__webpack_require__.d = function (exports, definition) {
      /******/for (var key in definition) {
        /******/if (__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
          /******/Object.defineProperty(exports, key, {
            enumerable: true,
            get: definition[key]
          });
          /******/
        }
        /******/
      }
      /******/
    };
    /******/
  })();
  /******/
  /******/ /* webpack/runtime/harmony module decorator */
  /******/
  (function () {
    /******/__webpack_require__.hmd = function (module) {
      /******/module = Object.create(module);
      /******/
      if (!module.children) module.children = [];
      /******/
      Object.defineProperty(module, 'exports', {
        /******/enumerable: true,
        /******/set: function set() {
          /******/throw new Error('ES Modules may not assign module.exports or exports.*, Use ESM export syntax, instead: ' + module.id);
          /******/
        }
        /******/
      });
      /******/
      return module;
      /******/
    };
    /******/
  })();
  /******/
  /******/ /* webpack/runtime/hasOwnProperty shorthand */
  /******/
  (function () {
    /******/__webpack_require__.o = function (obj, prop) {
      return Object.prototype.hasOwnProperty.call(obj, prop);
    };
    /******/
  })();
  /******/
  /******/ /* webpack/runtime/make namespace object */
  /******/
  (function () {
    /******/ // define __esModule on exports
    /******/__webpack_require__.r = function (exports) {
      /******/if (typeof Symbol !== 'undefined' && Symbol.toStringTag) {
        /******/Object.defineProperty(exports, Symbol.toStringTag, {
          value: 'Module'
        });
        /******/
      }
      /******/
      Object.defineProperty(exports, '__esModule', {
        value: true
      });
      /******/
    };
    /******/
  })();
  /******/
  /************************************************************************/
  var __webpack_exports__ = {};
  // This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
  (function () {
    /*!*********************!*\
      !*** ./js/admin.js ***!
      \*********************/
    __webpack_require__.r(__webpack_exports__);
    /* harmony import */
    var _modules_NavBar__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./modules/NavBar */"./js/modules/NavBar.js");
    /* harmony import */
    var _modules_GlobalSearch__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./modules/GlobalSearch */"./js/modules/GlobalSearch.js");
    /* harmony import */
    var _modules_AlertBar__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./modules/AlertBar */"./js/modules/AlertBar.js");
    /* harmony import */
    var _modules_RepeaterField__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./modules/RepeaterField */"./js/modules/RepeaterField.js");
    /* harmony import */
    var _modules_PostTabs__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./modules/PostTabs */"./js/modules/PostTabs.js");
    /* harmony import */
    var _modules_BlockEditor__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./modules/BlockEditor */"./js/modules/BlockEditor.js");
    /* harmony import */
    var _modules_MediaManager__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./modules/MediaManager */"./js/modules/MediaManager.js");
    /* harmony import */
    var _modules_MediaManagerPopup__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./modules/MediaManagerPopup */"./js/modules/MediaManagerPopup.js");
    /* harmony import */
    var _modules_MultiSelector__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./modules/MultiSelector */"./js/modules/MultiSelector.js");
    /* harmony import */
    var _modules_ChangeTheme__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./modules/ChangeTheme */"./js/modules/ChangeTheme.js");
    /* harmony import */
    var _modules_SlugUpdate__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./modules/SlugUpdate */"./js/modules/SlugUpdate.js");
    /* harmony import */
    var _modules_RelocatePostInfoComp__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./modules/RelocatePostInfoComp */"./js/modules/RelocatePostInfoComp.js");
    /* harmony import */
    var _modules_InitOzzWyg__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./modules/InitOzzWyg */"./js/modules/InitOzzWyg.js");
    /* harmony import */
    var _modules_LinkField__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./modules/LinkField */"./js/modules/LinkField.js");
    /* harmony import */
    var _modules_Taxonomy__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./modules/Taxonomy */"./js/modules/Taxonomy.js");
    /* harmony import */
    var _modules_Forms__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./modules/Forms */"./js/modules/Forms.js");
    // Modules

    (function () {
      // Ozz CMS Modules
      (0, _modules_NavBar__WEBPACK_IMPORTED_MODULE_0__["default"])();
      (0, _modules_GlobalSearch__WEBPACK_IMPORTED_MODULE_1__["default"])();
      (0, _modules_AlertBar__WEBPACK_IMPORTED_MODULE_2__["default"])();
      (0, _modules_PostTabs__WEBPACK_IMPORTED_MODULE_4__["default"])();
      (0, _modules_BlockEditor__WEBPACK_IMPORTED_MODULE_5__["default"])();
      (0, _modules_MediaManager__WEBPACK_IMPORTED_MODULE_6__["default"])();
      (0, _modules_MediaManagerPopup__WEBPACK_IMPORTED_MODULE_7__["default"])();
      (0, _modules_MultiSelector__WEBPACK_IMPORTED_MODULE_8__["default"])();
      (0, _modules_ChangeTheme__WEBPACK_IMPORTED_MODULE_9__["default"])();
      (0, _modules_SlugUpdate__WEBPACK_IMPORTED_MODULE_10__["default"])();
      (0, _modules_RelocatePostInfoComp__WEBPACK_IMPORTED_MODULE_11__["default"])();
      (0, _modules_InitOzzWyg__WEBPACK_IMPORTED_MODULE_12__["default"])();
      (0, _modules_Taxonomy__WEBPACK_IMPORTED_MODULE_14__["default"])();
      (0, _modules_Forms__WEBPACK_IMPORTED_MODULE_15__["default"])();
      (0, _modules_LinkField__WEBPACK_IMPORTED_MODULE_13__["default"])();
      var repeaterField = new _modules_RepeaterField__WEBPACK_IMPORTED_MODULE_3__["default"]();
      repeaterField.initRepeater(false, function () {
        (0, _modules_MediaManagerPopup__WEBPACK_IMPORTED_MODULE_7__["default"])();
        (0, _modules_LinkField__WEBPACK_IMPORTED_MODULE_13__["default"])();
      });
    })();
  })();

  /******/
})();