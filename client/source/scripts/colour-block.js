const blocks = [];
const parents = [];
const colorCache = new Map();

const padding = '--block-padding';

let root = null;
let nullFallback = null;
let treatNullValueAs = null;

// Styles have a transition in iframes, so we need to delay the resize function until after they have changed colour
const delay = (window.self !== window.top) ? 150 : 0;

const debounce = (func, wait) => {
  let timeout;
  return function (...args) {
    const context = this;
    clearTimeout(timeout);
    timeout = setTimeout(() => func.apply(context, args), wait);
  };
};

const ColourBlockObserver = new MutationObserver(debounce(() => {
  blocks.forEach(block => block.resize());
}, delay));

function convertToHex(color) {
  if (!color) return;

  if (colorCache.has(color)) {
    return colorCache.get(color);
  }

  // Helper function to ensure hex code format
  function rgbToHex(r, g, b) {
    const toHex = (n) => {
      const hex = n.toString(16);
      return hex.length === 1 ? '0' + hex : hex;
    };
    return `#${toHex(r)}${toHex(g)}${toHex(b)}`;
  }

  // Remove any extra whitespace and convert to lowercase
  color = color.trim().toLowerCase();

  // Check if it's a hex color code (with or without #)
  if (/^#([0-9a-f]{3}){1,2}$/i.test(color)) {
    // Normalize 3-digit hex to 6-digit
    if (color.length === 4) {
      color = `#${color[1]}${color[1]}${color[2]}${color[2]}${color[3]}${color[3]}`;
    }
    colorCache.set(color, color);
    return color;
  }

  // Check if it's an rgb or rgba color
  const rgbaMatch = /^rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*(?:,\s*([\d.]+)\s*)?\)$/.exec(color);
  if (rgbaMatch) {
    const [_, r, g, b, a] = rgbaMatch.map(Number);
    if (a === 0) {
      colorCache.set(color, treatNullValueAs);
      return treatNullValueAs; // Special value for transparency
    }
    const hexColor = rgbToHex(r, g, b);
    colorCache.set(color, hexColor);
    return hexColor;
  }

  // If none of the formats matched, throw an error
  console.warn("Unsupported color format", color);
  return null;
}

class ColourBlock extends HTMLElement {
  constructor() {
    super();

    // Grab the style tag for the block
    this.styles = document.querySelector(`[data-styles-for="${this.id}"]`);

    // If the style tag doesn't exist, create it
    if (!this.styles) {
      this.styles = document.createElement('style');
      this.styles.setAttribute('data-styles-for', this.id);

      // Add the styles to the head
      document.head.appendChild(this.styles);
    }

    // Get the default styles
    this.getStyles();

    // Add the block to the list of blocks
    blocks.push(this);

    // Resize the block
    this.debouncedResize = debounce(this.resize.bind(this), 100);
    requestAnimationFrame(() => this.debouncedResize());

    // Observe the block for changes
    ColourBlockObserver.observe(this, { attributes: true, attributeFilter: ['style'] });
  }

  getStyles(options = {}) {
    if (!this.id) return console.warn('ColourBlock must have an ID');

    const settings = Object.assign({
      paddingTop: `var(${padding})`,
      paddingBottom: `var(${padding})`,
    }, options);

    const styles = `
      padding-top: ${settings.paddingTop};
      padding-bottom: ${settings.paddingBottom};
    `;

    // Add transitions if the block is in an iframe
    const transitions = `
      transition:
        color 0.1s,
        margin-top 0.2s,
        padding-top 0.2s,
        margin-bottom 0.2s,
        padding-bottom 0.2s,
        background-color 0.1s;
    `;

    // Update the styles
    this.styles.textContent = `
      #${this.id} {
        ${styles}
        ${(window.self !== window.top) ? transitions : ''}
      }
    `;
  }

  resize() {
    requestAnimationFrame(() => {
      const prevSibling = this.previousElementSibling;
      const nextSibling = this.nextElementSibling;

      // Grab the default padding
      let paddingTop = `var(${padding})`;
      let paddingBottom = `var(${padding})`;

      // Remove the collapsed classes
      this.classList.remove('collapsed--top', 'collapsed--bottom');

      if (prevSibling && this.isSameColour(this.getSiblingColour(prevSibling))) {
        paddingTop = `calc(var(${padding}) / 2)`;
        this.classList.add('collapsed--top');
      }

      // If the next sibling is has the same colour, adjust the padding
      if (nextSibling && this.isSameColour(this.getSiblingColour(nextSibling))) {
        paddingBottom = `calc(var(${padding}) / 2)`;
        this.classList.add('collapsed--bottom');
      }

      this.getStyles({
        paddingTop,
        paddingBottom,
      });
    });
  }

  isSameColour(colour) {
    // Get the current block's property value
    let property = getComputedStyle(this).backgroundColor;

    // If we want to override the null value, do that now!
    if (treatNullValueAs !== undefined && !colour) colour = treatNullValueAs;

    // If the currentBackgroundColour is transparent, add a transparent class
    this.classList.toggle('transparent', (property === 'rgba(0, 0, 0, 0)' || property === 'transparent'));

    if (property) {
      // Convert the value to HEX
      let currentBackgroundColour = convertToHex(property);

      // Compare the colours
      return currentBackgroundColour == colour;
    }

    // If the property is not found, return true if the colour is null
    if (treatNullValueAs !== undefined) return treatNullValueAs === colour;

    if (!colour) return true;

    return false;
  }

  getSiblingColour(sibling) {
    // Check if the sibling is a ColourBlock
    if (!sibling) return null;

    // Make sure the sibling is a ColourBlock
    if (!(sibling instanceof ColourBlock)) return 'null';

    let property = getComputedStyle(sibling).backgroundColor;

    // Check if the property is not found
    if (!property) return null;

    // Get the sibling's property value
    let siblingBackgroundColour = property.trim();

    // Convert the RGB value to HEX
    if (siblingBackgroundColour.indexOf('rgb') === 0) siblingBackgroundColour = convertToHex(siblingBackgroundColour);

    return siblingBackgroundColour;
  }
}

// Define the custom element
window.customElements.define('colour-block', ColourBlock);

// Observe the body for changes
const observe = () => {
  root = getComputedStyle(document.body);
  nullFallback = root.getPropertyValue('--body-primary-colour') || root.getPropertyValue('--colour-white');

  // Set the default value for null, leave as null if you don't want transparent colours to interact with coloured blocks
  treatNullValueAs = convertToHex(nullFallback);

  // Observe the body for changes
  ColourBlockObserver.observe(document.body, { childList: true, subtree: true });
};

// When the document is ready, observe the body for changes
(document.readyState === 'loading') ? document.addEventListener('DOMContentLoaded', () => observe()) : observe();
