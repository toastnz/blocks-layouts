/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

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

/***/ }),

/***/ "./client/source/styles/preview.scss":
/*!*******************************************!*\
  !*** ./client/source/styles/preview.scss ***!
  \*******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


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
/*!******************************************!*\
  !*** ./client/source/scripts/preview.js ***!
  \******************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var styles_preview_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! styles/preview.scss */ "./client/source/styles/preview.scss");
/* harmony import */ var domobserverjs__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! domobserverjs */ "./node_modules/domobserverjs/dist/index.js");
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
function movePreviewToNewPanel(preview) {
  // Find the root element
  var root = document.querySelector('.cms-content .panel');

  // Create a new fieldset element inside the root
  var fieldset = document.createElement('fieldset');

  // Make sure the preview and root exist
  if (!preview || !root) return;
  root.appendChild(fieldset);

  // Move the preview to the root
  fieldset.appendChild(preview);
}
function createThumb(preview) {
  // Create a new button element to drag the preview width
  var thumb = document.createElement('button');

  // We will toggle this to toggle the dragging state
  var isDragging = false;

  // Add a class
  thumb.classList.add('preview-thumb');

  // Add the thumb to the preview
  preview.appendChild(thumb);

  // Add a mouse down event listener
  thumb.addEventListener('mousedown', function () {
    // Set dragging to true
    isDragging = true;
    preview.classList.add('dragging');
  });
  window.addEventListener('mouseup', function () {
    // Set dragging to false
    isDragging = false;
    preview.classList.remove('dragging');
  });
  window.addEventListener('mousemove', function (e) {
    // If we are not dragging, return
    if (!isDragging) return;

    // Get the parent panel and its container
    var panel = preview.parentElement;
    var container = panel.parentElement;

    // Calculate the mouse X position relative to the container
    var mouseX = e.clientX - 10 - container.getBoundingClientRect().left;

    // Calculate the container width
    var containerWidth = container.clientWidth;

    // Get the mouseX as a percentage of the container width
    var percentage = mouseX / containerWidth * 100;

    // Clamp the percentage to between 25% and 75% and round to 2 decimal places
    var clampedPercentage = 100 - Math.min(Math.max(percentage, 25), 75).toFixed(2);

    // Set the width of the preview
    document.body.style.setProperty('--block-preview-width', "".concat(clampedPercentage, "%"));
  });
}

// Observe the CMS for the toast-block-layouts fieldsets
CMSObserver.observe('#BlockPreviewFrame', function (items) {
  // Grab the preview
  var preview = items[0];
  // Move the preview to the root
  movePreviewToNewPanel(preview);
  // Create the thumb
  createThumb(preview);
});

// Chuck this on the normal page preivew cus why not ;)
CMSObserver.observe('[name="cms-preview-iframe"]', function (items) {
  console.log(items);
  // Grab the preview
  var preview = items[0];
  // Create the thumb
  createThumb(preview);
});
})();

/******/ })()
;
//# sourceMappingURL=preview.js.map