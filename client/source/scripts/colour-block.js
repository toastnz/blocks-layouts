const colourBlocks = [];
const parentElements = [];
const hexColorCache = new Map();

const blockPaddingVar = '--block-padding';

let bodyComputedStyle = null;
let bodyPrimaryColorFallback = null;
let transparentColorHex = null;

// Delay for transition in iframes
const transitionDelay = (window.self !== window.top) ? 150 : 0;

const colourBlockMutationObserver = new MutationObserver(debounceFn(() => {
  colourBlocks.forEach(block => block.resize());
}, transitionDelay));

// Utility: Debounce function
function debounceFn(fn, wait) {
  let timeout;

  return function (...args) {
    clearTimeout(timeout);
    timeout = setTimeout(() => fn.apply(this, args), wait);
  };
}

// Utility: Convert color string to hex
function toHexColor(colorStr) {
  if (!colorStr) return;
  if (hexColorCache.has(colorStr)) return hexColorCache.get(colorStr);

  // Helper: RGB to hex
  function rgbToHex(r, g, b) {
    return `#${[r, g, b].map(n => n.toString(16).padStart(2, '0')).join('')}`;
  }

  colorStr = colorStr.trim().toLowerCase();

  // Hex format
  if (/^#([0-9a-f]{3}){1,2}$/i.test(colorStr)) {
    if (colorStr.length === 4) {
      colorStr = `#${colorStr[1]}${colorStr[1]}${colorStr[2]}${colorStr[2]}${colorStr[3]}${colorStr[3]}`;
    }

    hexColorCache.set(colorStr, colorStr);

    return colorStr;
  }

  // RGB/RGBA format
  const rgbaMatch = /^rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*(?:,\s*([\d.]+)\s*)?\)$/.exec(colorStr);
  if (rgbaMatch) {
    const [_, r, g, b, a] = rgbaMatch.map(Number);

    if (a === 0) {
      hexColorCache.set(colorStr, transparentColorHex);
      return transparentColorHex;
    }

    const hexColor = rgbToHex(r, g, b);
    hexColorCache.set(colorStr, hexColor);

    return hexColor;
  }

  console.warn("Unsupported color format", colorStr);
  return null;
}

// Custom Element: ColourBlock
class ColourBlock extends HTMLElement {
  constructor() {
    super();
    this.ensureStyleTag();
    colourBlocks.push(this);

    this.debouncedUpdate = debounceFn(this.resize.bind(this), 100);
    requestAnimationFrame(() => this.debouncedUpdate());

    colourBlockMutationObserver.observe(this, { attributes: true, attributeFilter: ['style'] });
  }

  // Ensure style tag exists for this block
  ensureStyleTag() {
    this.styleTag = document.querySelector(`[data-styles-for="${this.id}"]`);
    if (!this.styleTag) {
      this.styleTag = document.createElement('style');
      this.styleTag.setAttribute('data-styles-for', this.id);
      document.head.appendChild(this.styleTag);
    }
    this.applyBlockStyles();
  }

  // Generate and apply styles for this block
  applyBlockStyles({ paddingTop = `var(${blockPaddingVar})`, paddingBottom = `var(${blockPaddingVar})` } = {}) {
    if (!this.id) return console.warn('ColourBlock must have an ID');

    const transitions = (window.self !== window.top)
      ? `
        transition:
          color 0.1s,
          margin-top 0.2s,
          padding-top 0.2s,
          margin-bottom 0.2s,
          padding-bottom 0.2s,
          background-color 0.1s;
      `
      : '';

    this.styleTag.textContent = `
      #${this.id} {
        padding-top: ${paddingTop};
        padding-bottom: ${paddingBottom};
        ${transitions}
      }
    `;
  }

  // Efficiently update padding and collapsed classes
  resize() {
    // Batch all reads before writes
    const previousBlock = this.previousElementSibling;
    const nextBlock = this.nextElementSibling;

    // Read computed styles for siblings first
    let previousBlockColor = null;
    let nextBlockColor = null;
    let isPreviousSame = false;
    let isNextSame = false;

    if (previousBlock && previousBlock instanceof ColourBlock) {
      const prevBgColor = getComputedStyle(previousBlock).backgroundColor;
      previousBlockColor = prevBgColor ? toHexColor(prevBgColor.trim()) : null;
      isPreviousSame = this.isSameHexColor(previousBlockColor);
    }

    if (nextBlock && nextBlock instanceof ColourBlock) {
      const nextBgColor = getComputedStyle(nextBlock).backgroundColor;
      nextBlockColor = nextBgColor ? toHexColor(nextBgColor.trim()) : null;
      isNextSame = this.isSameHexColor(nextBlockColor);
    }

    // Now batch all DOM/class/style writes
    window.requestAnimationFrame(() => {
      let paddingTop = `var(${blockPaddingVar})`;
      let paddingBottom = `var(${blockPaddingVar})`;

      this.classList.remove('collapsed--top', 'collapsed--bottom');

      if (isPreviousSame) {
        paddingTop = `calc(var(${blockPaddingVar}) / 2)`;
        this.classList.add('collapsed--top');
      }

      if (isNextSame) {
        paddingBottom = `calc(var(${blockPaddingVar}) / 2)`;
        this.classList.add('collapsed--bottom');
      }

      // Only update styles if changed
      if (
        this._lastPaddingTop !== paddingTop ||
        this._lastPaddingBottom !== paddingBottom
      ) {
        this.applyBlockStyles({ paddingTop, paddingBottom });
        this._lastPaddingTop = paddingTop;
        this._lastPaddingBottom = paddingBottom;
      }
    });
  }

  // Compare this block's colour to another
  isSameHexColor(hexColor) {
    const blockBgColor = getComputedStyle(this).backgroundColor;

    if (transparentColorHex !== undefined && !hexColor) hexColor = transparentColorHex;

    this.classList.toggle('transparent', (blockBgColor === 'rgba(0, 0, 0, 0)' || blockBgColor === 'transparent'));

    if (blockBgColor) {
      return toHexColor(blockBgColor) == hexColor;
    }

    if (transparentColorHex !== undefined) return transparentColorHex === hexColor;

    return !hexColor;
  }

  // Get sibling's colour in hex
  getSiblingHexColor(siblingBlock) {
    if (!siblingBlock) return null;
    if (!(siblingBlock instanceof ColourBlock)) return 'null';

    const siblingBgColor = getComputedStyle(siblingBlock).backgroundColor;

    if (!siblingBgColor) return null;

    let hexColor = siblingBgColor.trim();

    if (hexColor.startsWith('rgb')) {
      hexColor = toHexColor(hexColor);
    }

    return hexColor;
  }
}

// Register custom element
window.customElements.define('colour-block', ColourBlock);

// Observe body for changes and set up null fallback
function setupBodyObserver() {
  bodyComputedStyle = getComputedStyle(document.body);
  bodyPrimaryColorFallback = bodyComputedStyle.getPropertyValue('--body-primary-colour') || bodyComputedStyle.getPropertyValue('--colour-white');
  transparentColorHex = toHexColor(bodyPrimaryColorFallback);
  colourBlockMutationObserver.observe(document.body, { childList: true, subtree: true });
}

// DOMContentLoaded handler
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', setupBodyObserver);
} else {
  setupBodyObserver();
}
