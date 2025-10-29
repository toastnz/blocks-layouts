const colourBlocks = [];
const hexColourCache = new Map();

let bodyBackgroundColour = null;

const colourBlockMutationObserver = new MutationObserver(() => {
  colourBlocks.forEach((block) => block.resize());
});

// Helper: RGB(A) to hex
function rgbToHex(r, g, b, a = undefined) {
  // Clamp values
  r = Math.max(0, Math.min(255, r));
  g = Math.max(0, Math.min(255, g));
  b = Math.max(0, Math.min(255, b));
  let hex = `#${[r, g, b].map(n => n.toString(16).padStart(2, '0')).join('')}`;
  if (typeof a === 'number') {
    // Convert alpha (0-1) to 2-digit hex
    hex += (Math.round(a * 255)).toString(16).padStart(2, '0');
  }
  return hex;
}

// Utility: Convert rgba() string to hex
function toRgbaToHex(rgbaStr) {
  const match = /^rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*(?:,\s*([\d.]+)\s*)?\)$/.exec(rgbaStr.trim().toLowerCase());
  if (!match) return null;
  const [, r, g, b, a] = match;
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
  const cacheKey = colorStr;
  // Check if the cache has this key and return it if it does
  if (hexColourCache.has(cacheKey)) return hexColourCache.get(cacheKey);

  // Begin processing the color string
  let processedColorStr = colorStr.trim().toLowerCase();

  // RGB/RGBA format
  const rgbaMatch = /^rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*(?:,\s*([\d.]+)\s*)?\)$/.exec(processedColorStr);

  // Hex format (with optional alpha)
  const hexMatch = /^#([0-9a-f]{3,8})$/i.exec(processedColorStr);

  if (hexMatch) {
    let hex = hexMatch[1];

    // Expand short hex (#rgb or #rgba)
    if (hex.length === 3 || hex.length === 4) {
      hex = hex.split('').map(c => c + c).join('');
    }

    // Handle 8-digit hex (#RRGGBBAA)
    if (hex.length === 8) {
      const alpha = parseInt(hex.slice(6, 8), 16) / 255;

      if (alpha === 0) {
        hexColourCache.set(cacheKey, bodyBackgroundColour);
        return bodyBackgroundColour;
      }

      processedColorStr = `#${hex.slice(0, 6)}${hex.slice(6, 8)}`;
      hexColourCache.set(cacheKey, processedColorStr);
      return processedColorStr;
    }

    // Handle 4-digit hex (#RGBA)
    if (hex.length === 4) {
      const alpha = parseInt(hex[3] + hex[3], 16) / 255;

      if (alpha === 0) {
        hexColourCache.set(cacheKey, bodyBackgroundColour);
        return bodyBackgroundColour;
      }

      processedColorStr = `#${hex.slice(0, 3)}${hex[3] + hex[3]}`;
      hexColourCache.set(cacheKey, processedColorStr);
      return processedColorStr;
    }

    // Ensure full 6-digit hex
    if (hex.length === 6) {
      processedColorStr = `#${hex}`;
      hexColourCache.set(cacheKey, processedColorStr);
      return processedColorStr;
    }
  }

  if (rgbaMatch) {
    const [_, r, g, b, a] = rgbaMatch;
    const alpha = typeof a !== 'undefined' ? Number(a) : undefined;
    // If alpha is 0, return body background colour
    if (alpha === 0) {
      hexColourCache.set(cacheKey, bodyBackgroundColour);
      return bodyBackgroundColour;
    }
    const hexColour = rgbToHex(Number(r), Number(g), Number(b), alpha);
    hexColourCache.set(cacheKey, hexColour);
    return hexColour;
  }

  console.warn("Unsupported color format", processedColorStr);
  return null;
}

// Custom Element: ColourBlock
class ColourBlock extends HTMLElement {
  constructor() {
    super();

    // Store the background colour
    this.getCurrentBackgroundColour();

    // Observe this block for background colour changes
    const ColourObserver = new MutationObserver(() => this.getCurrentBackgroundColour());

    // Store this block element in the global array
    colourBlocks.push(this);
    // Observe this block for style changes
    colourBlockMutationObserver.observe(this, { attributes: true, attributeFilter: ['style'] });
    // Observe this block for background style changes
    ColourObserver.observe(this, { attributes: true, attributeFilter: ['style'] });
    // Initial resize
    requestAnimationFrame(() => this.resize());
  }

  getCurrentBackgroundColour() {
    this.backgroundColour = window.getComputedStyle(this).backgroundColor;
    return this.backgroundColour;
  }

  // Check if this block has the same colour as another block
  hasSameColourAsOtherBlock(block) {
    if (!block) return false;
    if (!(block instanceof ColourBlock)) return false;

    const hexColour = block.backgroundColour ? toHexColour(block.backgroundColour) : null;

    return this.hasBackgroundColourEqualTo(hexColour);
  }

  // Toggle collapsed classes based on neighbouring block colours
  resize() {
    window.requestAnimationFrame(() => {
      this.classList.toggle('collapsed-top', this.hasSameColourAsOtherBlock(this.previousElementSibling));
      this.classList.toggle('collapsed-bottom', this.hasSameColourAsOtherBlock(this.nextElementSibling));
    });
  }

  // Compare this block's colour to another
  hasBackgroundColourEqualTo(value) {
    // Grab the computed background color of this block
    const backgroundColour = this.backgroundColour;

    // If no value provided, use body background color as fallback to test against
    if (bodyBackgroundColour !== undefined && !value) value = bodyBackgroundColour;

    // Add a transparent class is this block has a transparent background colour
    this.classList.toggle('transparent', (backgroundColour === 'rgba(0, 0, 0, 0)' || backgroundColour === 'transparent'));

    // If the block has a background colour, compare it to the provided value
    if (backgroundColour) return toHexColour(backgroundColour) == value;

    // If no background colour, compare to body background colour if defined
    if (bodyBackgroundColour !== undefined) return bodyBackgroundColour === value;

    return !value;
  }
}

// Register custom element
window.customElements.define('colour-block', ColourBlock);

// Observe body for changes and set up null fallback
function init() {
  bodyBackgroundColour = toHexColour(window.getComputedStyle(document.body).backgroundColor);
  colourBlockMutationObserver.observe(document.body, { childList: true, subtree: true });
}

// Init when DOM is ready
(document.readyState === 'loading') ? document.addEventListener('DOMContentLoaded', init) : init();
