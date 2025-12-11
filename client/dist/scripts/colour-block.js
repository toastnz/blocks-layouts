/******/ (() => { // webpackBootstrap
/*!***********************************************!*\
  !*** ./client/source/scripts/colour-block.js ***!
  \***********************************************/
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function _callSuper(t, o, e) { return o = _getPrototypeOf(o), _possibleConstructorReturn(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], _getPrototypeOf(t).constructor) : o.apply(t, e)); }
function _possibleConstructorReturn(t, e) { if (e && ("object" == _typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return _assertThisInitialized(t); }
function _assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function _inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && _setPrototypeOf(t, e); }
function _wrapNativeSuper(t) { var r = "function" == typeof Map ? new Map() : void 0; return _wrapNativeSuper = function _wrapNativeSuper(t) { if (null === t || !_isNativeFunction(t)) return t; if ("function" != typeof t) throw new TypeError("Super expression must either be null or a function"); if (void 0 !== r) { if (r.has(t)) return r.get(t); r.set(t, Wrapper); } function Wrapper() { return _construct(t, arguments, _getPrototypeOf(this).constructor); } return Wrapper.prototype = Object.create(t.prototype, { constructor: { value: Wrapper, enumerable: !1, writable: !0, configurable: !0 } }), _setPrototypeOf(Wrapper, t); }, _wrapNativeSuper(t); }
function _construct(t, e, r) { if (_isNativeReflectConstruct()) return Reflect.construct.apply(null, arguments); var o = [null]; o.push.apply(o, e); var p = new (t.bind.apply(t, o))(); return r && _setPrototypeOf(p, r.prototype), p; }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _isNativeFunction(t) { try { return -1 !== Function.toString.call(t).indexOf("[native code]"); } catch (n) { return "function" == typeof t; } }
function _setPrototypeOf(t, e) { return _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, _setPrototypeOf(t, e); }
function _getPrototypeOf(t) { return _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, _getPrototypeOf(t); }
function _slicedToArray(r, e) { return _arrayWithHoles(r) || _iterableToArrayLimit(r, e) || _unsupportedIterableToArray(r, e) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _iterableToArrayLimit(r, l) { var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (null != t) { var e, n, i, u, a = [], f = !0, o = !1; try { if (i = (t = t.call(r)).next, 0 === l) { if (Object(t) !== t) return; f = !1; } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0); } catch (r) { o = !0, n = r; } finally { try { if (!f && null != t["return"] && (u = t["return"](), Object(u) !== u)) return; } finally { if (o) throw n; } } return a; } }
function _arrayWithHoles(r) { if (Array.isArray(r)) return r; }
function _toConsumableArray(r) { return _arrayWithoutHoles(r) || _iterableToArray(r) || _unsupportedIterableToArray(r) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _iterableToArray(r) { if ("undefined" != typeof Symbol && null != r[Symbol.iterator] || null != r["@@iterator"]) return Array.from(r); }
function _arrayWithoutHoles(r) { if (Array.isArray(r)) return _arrayLikeToArray(r); }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
var colourBlocks = [];
var hexColourCache = new Map();
var bodyBackgroundColour = null;
var mutationTimeout = null;
var colourBlockMutationObserver = new MutationObserver(function (mutations) {
  clearTimeout(mutationTimeout);
  mutationTimeout = setTimeout(function () {
    var shouldResize = false;

    // Determine if any of the mutations affect ColourBlock elements
    for (var i = 0; i < mutations.length; i++) {
      var mutation = mutations[i];
      var addedNodes = _toConsumableArray(mutation.addedNodes);
      var removedNodes = _toConsumableArray(mutation.removedNodes);
      if (mutation.target instanceof ColourBlock) {
        shouldResize = true;
        break;
      }
      if (addedNodes.filter(function (node) {
        return node instanceof ColourBlock;
      }).length > 0 || removedNodes.filter(function (node) {
        return node instanceof ColourBlock;
      }).length > 0) {
        shouldResize = true;
        break;
      }
    }
    if (!shouldResize) return;
    colourBlocks.forEach(function (block) {
      return block.resize();
    });
  }, 100);
});

// Helper: RGB(A) to hex
function rgbToHex(r, g, b) {
  var a = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : undefined;
  // Clamp values
  r = Math.max(0, Math.min(255, r));
  g = Math.max(0, Math.min(255, g));
  b = Math.max(0, Math.min(255, b));
  var hex = "#".concat([r, g, b].map(function (n) {
    return n.toString(16).padStart(2, '0');
  }).join(''));
  if (typeof a === 'number') {
    // Convert alpha (0-1) to 2-digit hex
    hex += Math.round(a * 255).toString(16).padStart(2, '0');
  }
  return hex;
}

// Utility: Convert rgba() string to hex
function toRgbaToHex(rgbaStr) {
  var match = /^rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*(?:,\s*([\d.]+)\s*)?\)$/.exec(rgbaStr.trim().toLowerCase());
  if (!match) return null;
  var _match = _slicedToArray(match, 5),
    r = _match[1],
    g = _match[2],
    b = _match[3],
    a = _match[4];
  if (typeof a !== 'undefined') {
    return rgbToHex(Number(r), Number(g), Number(b), Number(a));
  }
  return rgbToHex(Number(r), Number(g), Number(b));
}

// Utility: Convert color string to hex
function toHexColour(colorStr) {
  // If there is no color string, return
  if (!colorStr) return;
  // Always use the original argument as the cache key
  var cacheKey = colorStr;
  // Check if the cache has this key and return it if it does
  if (hexColourCache.has(cacheKey)) return hexColourCache.get(cacheKey);

  // Begin processing the color string
  var processedColorStr = colorStr.trim().toLowerCase();

  // RGB/RGBA format
  var rgbaMatch = /^rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*(?:,\s*([\d.]+)\s*)?\)$/.exec(processedColorStr);

  // Hex format (with optional alpha)
  var hexMatch = /^#([0-9a-f]{3,8})$/i.exec(processedColorStr);
  if (hexMatch) {
    var hex = hexMatch[1];

    // Expand short hex (#rgb or #rgba)
    if (hex.length === 3 || hex.length === 4) {
      hex = hex.split('').map(function (c) {
        return c + c;
      }).join('');
    }

    // Handle 8-digit hex (#RRGGBBAA)
    if (hex.length === 8) {
      var alpha = parseInt(hex.slice(6, 8), 16) / 255;
      if (alpha === 0) {
        hexColourCache.set(cacheKey, bodyBackgroundColour);
        return bodyBackgroundColour;
      }
      processedColorStr = "#".concat(hex.slice(0, 6)).concat(hex.slice(6, 8));
      hexColourCache.set(cacheKey, processedColorStr);
      return processedColorStr;
    }

    // Handle 4-digit hex (#RGBA)
    if (hex.length === 4) {
      var _alpha = parseInt(hex[3] + hex[3], 16) / 255;
      if (_alpha === 0) {
        hexColourCache.set(cacheKey, bodyBackgroundColour);
        return bodyBackgroundColour;
      }
      processedColorStr = "#".concat(hex.slice(0, 3)).concat(hex[3] + hex[3]);
      hexColourCache.set(cacheKey, processedColorStr);
      return processedColorStr;
    }

    // Ensure full 6-digit hex
    if (hex.length === 6) {
      processedColorStr = "#".concat(hex);
      hexColourCache.set(cacheKey, processedColorStr);
      return processedColorStr;
    }
  }
  if (rgbaMatch) {
    var _rgbaMatch = _slicedToArray(rgbaMatch, 5),
      _ = _rgbaMatch[0],
      r = _rgbaMatch[1],
      g = _rgbaMatch[2],
      b = _rgbaMatch[3],
      a = _rgbaMatch[4];
    var _alpha2 = typeof a !== 'undefined' ? Number(a) : undefined;
    // If alpha is 0, return body background colour
    if (_alpha2 === 0) {
      hexColourCache.set(cacheKey, bodyBackgroundColour);
      return bodyBackgroundColour;
    }
    var hexColour = rgbToHex(Number(r), Number(g), Number(b), _alpha2);
    hexColourCache.set(cacheKey, hexColour);
    return hexColour;
  }
  console.warn("Unsupported color format", processedColorStr);
  return null;
}

// Custom Element: ColourBlock
var ColourBlock = /*#__PURE__*/function (_HTMLElement) {
  function ColourBlock() {
    var _this;
    _classCallCheck(this, ColourBlock);
    _this = _callSuper(this, ColourBlock);

    // Store the background colour
    _this.getCurrentBackgroundColour();

    // Observe this block for background colour changes
    var ColourObserver = new MutationObserver(function () {
      return _this.getCurrentBackgroundColour();
    });

    // Store this block element in the global array
    colourBlocks.push(_this);

    // Observe this block for style changes
    colourBlockMutationObserver.observe(_this, {
      attributes: true,
      attributeFilter: ['style'],
      childList: true,
      subtree: true
    });

    // Observe this block for background style changes
    ColourObserver.observe(_this, {
      attributes: true,
      attributeFilter: ['style']
    });
    // Initial resize
    requestAnimationFrame(function () {
      return _this.resize();
    });
    return _this;
  }
  _inherits(ColourBlock, _HTMLElement);
  return _createClass(ColourBlock, [{
    key: "getCurrentBackgroundColour",
    value: function getCurrentBackgroundColour() {
      this.backgroundColour = window.getComputedStyle(this).backgroundColor;
      return this.backgroundColour;
    }

    // Check if this block has the same colour as another block
  }, {
    key: "hasSameColourAsOtherBlock",
    value: function hasSameColourAsOtherBlock(block) {
      if (!block) return false;
      if (!(block instanceof ColourBlock)) return false;
      var hexColour = block.backgroundColour ? toHexColour(block.backgroundColour) : null;
      return this.hasBackgroundColourEqualTo(hexColour);
    }

    // Toggle collapsed classes based on neighbouring block colours
  }, {
    key: "resize",
    value: function resize() {
      var _this2 = this;
      window.requestAnimationFrame(function () {
        _this2.classList.toggle('collapsed-top', _this2.hasSameColourAsOtherBlock(_this2.previousElementSibling));
        _this2.classList.toggle('collapsed-bottom', _this2.hasSameColourAsOtherBlock(_this2.nextElementSibling));
      });
    }

    // Compare this block's colour to another
  }, {
    key: "hasBackgroundColourEqualTo",
    value: function hasBackgroundColourEqualTo(value) {
      // Grab the computed background color of this block
      var backgroundColour = this.backgroundColour;

      // If no value provided, use body background color as fallback to test against
      if (bodyBackgroundColour !== undefined && !value) value = bodyBackgroundColour;

      // Add a transparent class is this block has a transparent background colour
      this.classList.toggle('transparent', backgroundColour === 'rgba(0, 0, 0, 0)' || backgroundColour === 'transparent');

      // If the block has a background colour, compare it to the provided value
      if (backgroundColour) return toHexColour(backgroundColour) == value;

      // If no background colour, compare to body background colour if defined
      if (bodyBackgroundColour !== undefined) return bodyBackgroundColour === value;
      return !value;
    }
  }]);
}(/*#__PURE__*/_wrapNativeSuper(HTMLElement)); // Register custom element
window.customElements.define('colour-block', ColourBlock);

// Observe body for changes and set up null fallback
function init() {
  bodyBackgroundColour = toHexColour(window.getComputedStyle(document.body).backgroundColor);
  colourBlockMutationObserver.observe(document.body, {
    childList: true,
    subtree: true
  });
}

// Init when DOM is ready
document.readyState === 'loading' ? document.addEventListener('DOMContentLoaded', init) : init();
/******/ })()
;