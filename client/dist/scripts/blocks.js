/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./client/source/styles/blocks.scss":
/*!******************************************!*\
  !*** ./client/source/styles/blocks.scss ***!
  \******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./node_modules/domobserverjs/dist/index.js":
/*!**************************************************!*\
  !*** ./node_modules/domobserverjs/dist/index.js ***!
  \**************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ c)
/* harmony export */ });
var e={d:(t,s)=>{for(var r in s)e.o(s,r)&&!e.o(t,r)&&Object.defineProperty(t,r,{enumerable:!0,get:s[r]})},o:(e,t)=>Object.prototype.hasOwnProperty.call(e,t)},t={};e.d(t,{A:()=>o});const s={childList:!0,subtree:!0},r=e=>console.log("DomObserver:",e);class o{constructor(){this.agents=[],this.observers=new Map}findOrCreateObserver(e){let t=this.observers.get(e);return t||(t=new MutationObserver((()=>{this.agents.filter((t=>t.settings===e)).forEach((e=>e.update()))})),t.observe(document.body,e),this.observers.set(e,t),t)}observe(){let e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:"",t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:r,o=arguments.length>2&&void 0!==arguments[2]?arguments[2]:s;if(!e)return console.error(e+" DomObserver not created: No selector provided");if(!t||"function"!=typeof t)return console.error(e+" DomObserver not created: No callback provided");o=Object.assign(o,s),this.findOrCreateObserver(o);const c=new i(e,t,o);this.agents.push(c)}}class i{constructor(e,t,s){this.cache=new Set,this.timeouts={},this.selector=e,this.callback=t,this.settings=s,this.update()}update(){clearTimeout(this.timeouts.update),this.timeouts.update=setTimeout((()=>{const e=[...document.querySelectorAll(this.selector)].filter((e=>!this.cache.has(e)));e.length&&(e.forEach((e=>this.cache.add(e))),this.callback(e))}),100)}}var c=t.A;

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
(() => {
/*!*****************************************!*\
  !*** ./client/source/scripts/blocks.js ***!
  \*****************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var styles_blocks_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! styles/blocks.scss */ "./client/source/styles/blocks.scss");
/* harmony import */ var domobserverjs__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! domobserverjs */ "./node_modules/domobserverjs/dist/index.js");
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _slicedToArray(r, e) { return _arrayWithHoles(r) || _iterableToArrayLimit(r, e) || _unsupportedIterableToArray(r, e) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _iterableToArrayLimit(r, l) { var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (null != t) { var e, n, i, u, a = [], f = !0, o = !1; try { if (i = (t = t.call(r)).next, 0 === l) { if (Object(t) !== t) return; f = !1; } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0); } catch (r) { o = !0, n = r; } finally { try { if (!f && null != t["return"] && (u = t["return"](), Object(u) !== u)) return; } finally { if (o) throw n; } } return a; } }
function _arrayWithHoles(r) { if (Array.isArray(r)) return r; }
function _toConsumableArray(r) { return _arrayWithoutHoles(r) || _iterableToArray(r) || _unsupportedIterableToArray(r) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _iterableToArray(r) { if ("undefined" != typeof Symbol && null != r[Symbol.iterator] || null != r["@@iterator"]) return Array.from(r); }
function _arrayWithoutHoles(r) { if (Array.isArray(r)) return _arrayLikeToArray(r); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function _regenerator() { /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/babel/babel/blob/main/packages/babel-helpers/LICENSE */ var e, t, r = "function" == typeof Symbol ? Symbol : {}, n = r.iterator || "@@iterator", o = r.toStringTag || "@@toStringTag"; function i(r, n, o, i) { var c = n && n.prototype instanceof Generator ? n : Generator, u = Object.create(c.prototype); return _regeneratorDefine2(u, "_invoke", function (r, n, o) { var i, c, u, f = 0, p = o || [], y = !1, G = { p: 0, n: 0, v: e, a: d, f: d.bind(e, 4), d: function d(t, r) { return i = t, c = 0, u = e, G.n = r, a; } }; function d(r, n) { for (c = r, u = n, t = 0; !y && f && !o && t < p.length; t++) { var o, i = p[t], d = G.p, l = i[2]; r > 3 ? (o = l === n) && (u = i[(c = i[4]) ? 5 : (c = 3, 3)], i[4] = i[5] = e) : i[0] <= d && ((o = r < 2 && d < i[1]) ? (c = 0, G.v = n, G.n = i[1]) : d < l && (o = r < 3 || i[0] > n || n > l) && (i[4] = r, i[5] = n, G.n = l, c = 0)); } if (o || r > 1) return a; throw y = !0, n; } return function (o, p, l) { if (f > 1) throw TypeError("Generator is already running"); for (y && 1 === p && d(p, l), c = p, u = l; (t = c < 2 ? e : u) || !y;) { i || (c ? c < 3 ? (c > 1 && (G.n = -1), d(c, u)) : G.n = u : G.v = u); try { if (f = 2, i) { if (c || (o = "next"), t = i[o]) { if (!(t = t.call(i, u))) throw TypeError("iterator result is not an object"); if (!t.done) return t; u = t.value, c < 2 && (c = 0); } else 1 === c && (t = i["return"]) && t.call(i), c < 2 && (u = TypeError("The iterator does not provide a '" + o + "' method"), c = 1); i = e; } else if ((t = (y = G.n < 0) ? u : r.call(n, G)) !== a) break; } catch (t) { i = e, c = 1, u = t; } finally { f = 1; } } return { value: t, done: y }; }; }(r, o, i), !0), u; } var a = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} t = Object.getPrototypeOf; var c = [][n] ? t(t([][n]())) : (_regeneratorDefine2(t = {}, n, function () { return this; }), t), u = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(c); function f(e) { return Object.setPrototypeOf ? Object.setPrototypeOf(e, GeneratorFunctionPrototype) : (e.__proto__ = GeneratorFunctionPrototype, _regeneratorDefine2(e, o, "GeneratorFunction")), e.prototype = Object.create(u), e; } return GeneratorFunction.prototype = GeneratorFunctionPrototype, _regeneratorDefine2(u, "constructor", GeneratorFunctionPrototype), _regeneratorDefine2(GeneratorFunctionPrototype, "constructor", GeneratorFunction), GeneratorFunction.displayName = "GeneratorFunction", _regeneratorDefine2(GeneratorFunctionPrototype, o, "GeneratorFunction"), _regeneratorDefine2(u), _regeneratorDefine2(u, o, "Generator"), _regeneratorDefine2(u, n, function () { return this; }), _regeneratorDefine2(u, "toString", function () { return "[object Generator]"; }), (_regenerator = function _regenerator() { return { w: i, m: f }; })(); }
function _regeneratorDefine2(e, r, n, t) { var i = Object.defineProperty; try { i({}, "", {}); } catch (e) { i = 0; } _regeneratorDefine2 = function _regeneratorDefine(e, r, n, t) { function o(r, n) { _regeneratorDefine2(e, r, function (e) { return this._invoke(r, n, e); }); } r ? i ? i(e, r, { value: n, enumerable: !t, configurable: !t, writable: !t }) : e[r] = n : (o("next", 0), o("throw", 1), o("return", 2)); }, _regeneratorDefine2(e, r, n, t); }
function _createForOfIteratorHelper(r, e) { var t = "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (!t) { if (Array.isArray(r) || (t = _unsupportedIterableToArray(r)) || e && r && "number" == typeof r.length) { t && (r = t); var _n = 0, F = function F() {}; return { s: F, n: function n() { return _n >= r.length ? { done: !0 } : { done: !1, value: r[_n++] }; }, e: function e(r) { throw r; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var o, a = !0, u = !1; return { s: function s() { t = t.call(r); }, n: function n() { var r = t.next(); return a = r.done, r; }, e: function e(r) { u = !0, o = r; }, f: function f() { try { a || null == t["return"] || t["return"](); } finally { if (u) throw o; } } }; }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function asyncGeneratorStep(n, t, e, r, o, a, c) { try { var i = n[a](c), u = i.value; } catch (n) { return void e(n); } i.done ? t(u) : Promise.resolve(u).then(r, o); }
function _asyncToGenerator(n) { return function () { var t = this, e = arguments; return new Promise(function (r, o) { var a = n.apply(t, e); function _next(n) { asyncGeneratorStep(a, r, o, _next, _throw, "next", n); } function _throw(n) { asyncGeneratorStep(a, r, o, _next, _throw, "throw", n); } _next(void 0); }); }; }
/*------------------------------------------------------------------
Import styles
------------------------------------------------------------------*/



/*------------------------------------------------------------------
Scripts
------------------------------------------------------------------*/



/*------------------------------------------------------------------
Setup
------------------------------------------------------------------*/

// Create a new instance of the DomObserverController
var CMSObserver = new domobserverjs__WEBPACK_IMPORTED_MODULE_1__["default"]();

// Observe the CMS for the toast-block-layouts fieldsets
CMSObserver.observe('.toast-block-layouts', function (fieldsets) {
  // Loop through the fieldsets
  _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee() {
    var _iterator, _step, fieldset, images, _iterator2, _step2, img, imgSrc, response, svg, div, _t, _t2, _t3;
    return _regenerator().w(function (_context) {
      while (1) switch (_context.p = _context.n) {
        case 0:
          _iterator = _createForOfIteratorHelper(fieldsets);
          _context.p = 1;
          _iterator.s();
        case 2:
          if ((_step = _iterator.n()).done) {
            _context.n = 14;
            break;
          }
          fieldset = _step.value;
          // Find all the images in the fieldset
          images = fieldset.querySelectorAll('img'); // Loop the images
          _iterator2 = _createForOfIteratorHelper(images);
          _context.p = 3;
          _iterator2.s();
        case 4:
          if ((_step2 = _iterator2.n()).done) {
            _context.n = 10;
            break;
          }
          img = _step2.value;
          imgSrc = img.src; // If the img src does not include .svg, continue to the next image
          if (imgSrc.includes('.svg')) {
            _context.n = 5;
            break;
          }
          return _context.a(3, 9);
        case 5:
          _context.p = 5;
          _context.n = 6;
          return fetch(imgSrc);
        case 6:
          response = _context.v;
          _context.n = 7;
          return response.text();
        case 7:
          svg = _context.v;
          // Create a new div
          div = document.createElement('div'); // Set the innerHTML of the div to the svg
          div.innerHTML = svg;

          // Replace the img with the div
          img.parentNode.replaceChild(div.firstChild, img);
          _context.n = 9;
          break;
        case 8:
          _context.p = 8;
          _t = _context.v;
          console.error("Failed to fetch SVG: ".concat(imgSrc), _t);
        case 9:
          _context.n = 4;
          break;
        case 10:
          _context.n = 12;
          break;
        case 11:
          _context.p = 11;
          _t2 = _context.v;
          _iterator2.e(_t2);
        case 12:
          _context.p = 12;
          _iterator2.f();
          return _context.f(12);
        case 13:
          _context.n = 2;
          break;
        case 14:
          _context.n = 16;
          break;
        case 15:
          _context.p = 15;
          _t3 = _context.v;
          _iterator.e(_t3);
        case 16:
          _context.p = 16;
          _iterator.f();
          return _context.f(16);
        case 17:
          return _context.a(2);
      }
    }, _callee, null, [[5, 8], [3, 11, 12, 13], [1, 15, 16, 17]]);
  }))();
});

// Block siblings script to move siblings to the top of the tab for better styling
CMSObserver.observe('.content-block-siblings', function (elements) {
  var element = elements[0];
  var tab = element.closest('.tab-content');
  tab.insertBefore(element, tab.firstChild);
});
var BlockPreviewMessenger = /*#__PURE__*/function () {
  function BlockPreviewMessenger() {
    var _this = this;
    _classCallCheck(this, BlockPreviewMessenger);
    this.initialised = false;
    this.iframes = [];
    this.blockID = null;
    this.blockIDElement = null;
    this.previewIframe = null;
    this.colours = {
      queue: [],
      timeout: null
    };

    // Listen for the block previews ready message
    window.addEventListener('message', function (event) {
      if (event.data.action === 'block-previews-ready') _this.init();
    });
  }
  return _createClass(BlockPreviewMessenger, [{
    key: "init",
    value: function init() {
      var _this2 = this;
      if (this.initialised) return;
      this.initialised = true;

      // When we receive a ColourPaletteChanged event, run the updateColours method
      window.addEventListener('ColourPaletteChanged', function () {
        return _this2.updateColours();
      });

      // Update the colours
      this.updateColours();
      // Prepare to scroll to the block
      this.prepareToScroll();

      // Removed because <% require %> statements in the template do not get updated live causing rendering issues
      // Prepare to update the template
      // this.prepareTemplateUpdates();
    }
  }, {
    key: "iframeExists",
    value: function iframeExists() {
      // Look for all iframes on the page
      this.iframes = _toConsumableArray(document.querySelectorAll('iframe'));
      // Find the preview iframe again if it is not found
      this.previewIframe = this.iframes.find(function (iframe) {
        return iframe.src.includes('CMSPreview');
      });
      // Resturn false if the preview iframe is not found
      if (!this.previewIframe) return false;
      // If there is not contentWindow, return false
      if (!this.previewIframe.contentWindow) return false;
      return true;
    }
  }, {
    key: "getBlockID",
    value: function getBlockID() {
      // Return the block ID if it has already been found
      if (this.blockID && document.body.contains(this.blockIDElement)) return this.blockID;
      // Find the block ID element
      this.blockIDElement = document.querySelector('#Form_ItemEditForm_BlockID');
      // Return null if the block ID element is not found
      if (!this.blockIDElement) return null;
      // Set the block ID
      this.blockID = this.blockIDElement.innerText.trim();
      return this.blockID;
    }
  }, {
    key: "updateColours",
    value: function updateColours() {
      var _this3 = this;
      // Make sure the iframe exists
      if (!this.iframeExists()) return;
      clearTimeout(this.colours.timeout);
      this.colours.timeout = setTimeout(function () {
        if (window.ColourPalettes) {
          Object.entries(window.ColourPalettes).forEach(function (_ref2) {
            var _ref3 = _slicedToArray(_ref2, 2),
              name = _ref3[0],
              data = _ref3[1];
            _this3.previewIframe.contentWindow.postMessage({
              action: 'updateColours',
              name: name,
              value: data.value,
              brightness: data.brightness,
              blockID: _this3.getBlockID()
            }, window.location.origin);
          });
        }
      }, 200);
    }
  }, {
    key: "prepareToScroll",
    value: function prepareToScroll() {
      var _this4 = this;
      var scrollTimeout = null;
      var clicked = false;

      // Look for block links that will trigger a scroll to method
      CMSObserver.observe('.ss-gridfield-item, .content-block-siblings > a', function (elements) {
        elements.forEach(function (element) {
          // Find the element with the block ID
          var blockIDElement = element.querySelector('[data-block-id]');
          var blockID = blockIDElement ? blockIDElement.getAttribute('data-block-id') : null;

          // If there is no block ID, return
          if (!blockID) return;

          // When we hover over the element, we want to scroll to the block
          element.addEventListener('mouseenter', function () {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(function () {
              if (!_this4.iframeExists()) return;

              // Post a message to the iframe
              _this4.previewIframe.contentWindow.postMessage({
                action: 'scrollTo',
                blockID: blockID
              }, window.location.origin);
            }, 300);
          });
          element.addEventListener('click', function () {
            clicked = true;
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(function () {
              clicked = false;
            }, 500);
          });
          element.addEventListener('mouseleave', function () {
            if (clicked) return;
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(function () {
              if (!_this4.iframeExists()) return;

              // Post a message to the iframe
              _this4.previewIframe.contentWindow.postMessage({
                action: 'scrollTo'
              }, window.location.origin);
            }, 500);
          });
        });
      });
    }

    // Removed because <% require %> statements in the template do not get updated live causing rendering issues
    // prepareTemplateUpdates() {
    //   const onChange = (input) => {
    //     if (!this.iframeExists()) return;

    //     // Update the colours
    //     this.updateColours();

    //     // Post a message to the iframe
    //     this.previewIframe.contentWindow.postMessage({
    //       action: 'getTemplate',
    //       templatePath: input.value,
    //       blockID: this.getBlockID(),
    //     }, window.location.origin);
    //   }

    //   // Watch for changes to the template select field
    //   CMSObserver.observe('#Form_ItemEditForm_Template_Holder [name="Template"]', (inputs) => {
    //     if (inputs.length === 0) return;

    //     // If the input is a select element, handle it differently
    //     if (inputs[0].tagName.toLowerCase() === 'select') {
    //       const select = inputs[0];

    //       try {
    //         jQuery(select).on('change', () => onChange(select));
    //       } catch (err) {
    //         select.addEventListener('change', () => onChange(select));
    //       }

    //       // Trigger the onChange event if there is a value
    //       if (select.value) onChange(select);

    //       if (OpenCMSPreviewController) {
    //         OpenCMSPreviewController.on('refresh', () => {
    //           if (select.value) onChange(select);
    //         });
    //       }

    //       return;
    //     }

    //     // Otherwise, assume they are radio inputs
    //     inputs.forEach((input) => {
    //       try {
    //         jQuery(input).on('change', () => onChange(input));
    //       } catch (err) {
    //         input.addEventListener('change', () => onChange(input));
    //       }

    //       if (input.checked) onChange(input);

    //       if (OpenCMSPreviewController) {
    //         OpenCMSPreviewController.on('refresh', () => {
    //           if (input.checked) onChange(input);
    //         });
    //       }
    //     });
    //   });
    // }
  }]);
}();
new BlockPreviewMessenger();
})();

/******/ })()
;