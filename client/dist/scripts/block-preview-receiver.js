/******/ (() => { // webpackBootstrap
/*!*********************************************************!*\
  !*** ./client/source/scripts/block-preview-receiver.js ***!
  \*********************************************************/
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _slicedToArray(r, e) { return _arrayWithHoles(r) || _iterableToArrayLimit(r, e) || _unsupportedIterableToArray(r, e) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function _iterableToArrayLimit(r, l) { var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (null != t) { var e, n, i, u, a = [], f = !0, o = !1; try { if (i = (t = t.call(r)).next, 0 === l) { if (Object(t) !== t) return; f = !1; } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0); } catch (r) { o = !0, n = r; } finally { try { if (!f && null != t["return"] && (u = t["return"](), Object(u) !== u)) return; } finally { if (o) throw n; } } return a; } }
function _arrayWithHoles(r) { if (Array.isArray(r)) return r; }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
var BlockPreviewReceiver = /*#__PURE__*/function () {
  function BlockPreviewReceiver() {
    var _this = this;
    _classCallCheck(this, BlockPreviewReceiver);
    // Create a new XMLHttpRequest object
    this.xhr = new XMLHttpRequest();

    // A timeout to cancel ready signals
    this.timeout = null;

    // Set the block to null
    this.block = null;

    // Set up a ResizeObserver
    window.addEventListener('resize', function () {
      return _this.scrollTo();
    });

    // Add a CMS preview class to the body
    document.documentElement.classList.add('cms-preview');

    // Delegate the message to the appropriate method
    window.addEventListener('message', function (event) {
      var data = event.data,
        origin = event.origin;
      var action = data.action;
      if (_this[action] && origin === window.location.origin) _this[action](data);
    });

    // Set up some vars specific to colour updates
    this.colours = {
      timeout: null,
      styles: document.createElement('style'),
      properties: {},
      contrasts: {
        dark: 'var(--global-contrast-colour-light, #ffffff)',
        light: 'var(--global-contrast-colour-dark, #000000)',
        bodyContrast: 'var(--body-background-colour-contrast, #000000)',
        bodyOnContrast: 'var(--body-background-colour-on-contrast, #ffffff)'
      }
    };

    // Set up some vars specific to template updates
    // this.templates = {
    //   styles: document.createElement('style'),
    //   memory: {},
    //   current: null,
    // }

    // Append the styles to the head
    document.head.appendChild(this.colours.styles);
    // document.head.appendChild(this.templates.styles);

    // Let the parent window know that the block previews are ready
    this.timeout = setInterval(function () {
      if (document.readyState === 'complete') {
        window.parent.postMessage({
          action: 'block-previews-ready'
        }, window.location.origin);
        clearInterval(_this.timeout);
      }
    }, 500);
  }
  return _createClass(BlockPreviewReceiver, [{
    key: "getBlock",
    value: function getBlock(blockID) {
      if (!this.block || !document.body.contains(this.block)) this.block = document.getElementById(blockID);
      if (!this.block) return null;
      return this.block;
    }
  }, {
    key: "resizeBlocks",
    value: function resizeBlocks(block) {
      block.classList.add('cms-preview');
      block.getCurrentBackgroundColour();

      // Collect the relevant blocks that are COLOUR-BLOCK
      var blocks = [block, block.nextElementSibling, block.previousElementSibling].filter(function (element) {
        return element && element.tagName === 'COLOUR-BLOCK';
      });

      // Resize the colour blocks
      blocks.forEach(function (element) {
        try {
          element.resize();
        } catch (err) {
          console.error(err);
        }
      });
    }

    // learnTemplate(templateResponse) {
    //   const { blockID, templatePath, elements, styles } = templateResponse;

    //   let index = 0;

    //   // Look at the elements, and find the index of the block with the blockID
    //   elements.forEach((element, i) => {
    //     if (element.id === blockID) {
    //       index = i;
    //     }
    //   });

    //   // Update the block memory
    //   this.templates.memory[templatePath] = {
    //     blockID,
    //     elements,
    //     styles,
    //     index,
    //     length: elements.length,
    //   };

    //   // If there is no current template, just set it
    //   if (!this.templates.current) {
    //     this.templates.current = templatePath;
    //   }

    //   // Otherwise we will update the current template
    //   else {
    //     // Update the template
    //     this.updateTemplate(templatePath);
    //   }
    // }

    // updateTemplate(templatePath) {
    //   const { blockID, index, length } = this.templates.memory[this.templates.current];
    //   const { elements, styles, } = this.templates.memory[templatePath];
    //   // Find the block using the blockID
    //   let block = this.getBlock(blockID);

    //   // If the block is not found, return
    //   if (!block) return console.error('Block not found', blockID);

    //   // Remove the block element's siblings based on the index and length, for example, if the length is 2, and the index is 1, remove the previous sibling
    //   for (let i = 0; i < length; i++) {
    //     if (i < index) {
    //       block.previousElementSibling.remove();
    //     }

    //     if (i > index) {
    //       block.nextElementSibling.remove();
    //     }
    //   }

    //   // Convert the elements to a string containing all their HTML
    //   let template = elements.reduce((acc, element) => {
    //     return `${acc}${element.outerHTML}`;
    //   }, '');

    //   // Replace the block outerHTML with the new template
    //   block.outerHTML = template;

    //   // Find the new block
    //   block = this.getBlock(blockID)

    //   // If the block is not found, return
    //   if (!block) return console.error('Block not found', blockID);

    //   // Add the CMS preview class to the block
    //   block.classList.add('cms-preview');

    //   // Update the styles
    //   this.templates.styles.innerHTML = styles;

    //   // Update the current template
    //   this.templates.current = templatePath;

    //   // Scroll to the block
    //   this.scrollTo({ blockID });
    // }

    // getTemplate(data = {}) {
    //   const { blockID, templatePath } = data;

    //   // Abort the current request
    //   this.xhr.abort();

    //   // Set up the API URL
    //   let API = `/blocks-api/getBlock?BlockID=${blockID}&nocache=${new Date().getTime()}`;

    //   // Return if the block ID is not set
    //   if (!blockID) return;

    //   // Add the templatePath to the API URL if it is set
    //   if (templatePath) API += `&Template=${templatePath}`;

    //   // Open a new request
    //   this.xhr.open('GET', API, true);

    //   // Set the request headers
    //   this.xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

    //   // Set the request callback
    //   this.xhr.onreadystatechange = () => {
    //     if (this.xhr.readyState === 4 && this.xhr.status === 200) {
    //       const parser = new DOMParser();
    //       const response = JSON.parse(this.xhr.responseText);

    //       let template = response.template;
    //       let styles = response.styles;

    //       // convert the template to html
    //       template = parser.parseFromString(template, 'text/html');

    //       // Get the children of the template
    //       let elements = [...template.body.children];

    //       // Learn the new template data
    //       this.learnTemplate({ blockID, templatePath, elements, styles });
    //     }
    //   };

    //   // Send the request
    //   this.xhr.send();
    // }
  }, {
    key: "updateColours",
    value: function updateColours() {
      var _this2 = this;
      var data = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var blockID = data.blockID,
        name = data.name,
        value = data.value,
        brightness = data.brightness;
      var block = this.getBlock(blockID);

      // Update the colours object
      this.colours.properties[name] = {
        value: value,
        brightness: brightness
      };
      var styles = Object.entries(this.colours.properties).reduce(function (acc, _ref) {
        var _ref2 = _slicedToArray(_ref, 2),
          name = _ref2[0],
          data = _ref2[1];
        var value = data.value,
          brightness = data.brightness;
        if (value == 'rgba(0, 0, 0, 0)') return acc;
        var onContrast = brightness === 'dark' ? 'light' : 'dark';
        return "\n        ".concat(acc, "\n        .cms-preview #").concat(blockID, ".cms-preview {\n          --_").concat(name, ": ").concat(value, ";\n          --_").concat(name, "-contrast: ").concat(_this2.colours.contrasts[brightness], ";\n          --_").concat(name, "-on-contrast: ").concat(_this2.colours.contrasts[onContrast], ";\n        }\n      ");
      }, '');

      // Update the styles
      this.colours.styles.innerHTML = styles;
      if (block) {
        window.requestAnimationFrame(function () {
          return _this2.resizeBlocks(block);
        });
      }
    }
  }, {
    key: "scrollTo",
    value: function scrollTo() {
      var data = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var blockID = data.blockID;
      if (blockID && window.scrollToElementByID) {
        window.scrollToElementByID(blockID);
      } else if (window.scrollToHash) {
        window.scrollToHash(false, false);
      }
    }
  }]);
}(); // If the window is not the top window, then we are in a preview
if (window.self !== window.top) {
  // Create a new BlockPreviewController
  new BlockPreviewReceiver();
}
/******/ })()
;