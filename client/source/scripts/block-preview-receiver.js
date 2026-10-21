/*------------------------------------------------------------------
Block preview receiver

Runs inside the CMS preview iframe. Receives messages from the CMS
(blocks.js) to live-update block colours and scroll to the block being
edited, and tells the CMS when the preview is ready.
------------------------------------------------------------------*/

class BlockPreviewReceiver {
  /** Message actions the CMS is allowed to call */
  static actions = ['updateColours', 'scrollTo'];

  constructor() {
    this.block = null;

    this.colours = {
      styles: document.createElement('style'),
      properties: {},
      contrasts: {
        dark: 'var(--global-contrast-colour-light, #ffffff)',
        light: 'var(--global-contrast-colour-dark, #000000)',
        bodyContrast: 'var(--body-background-colour-contrast, #000000)',
        bodyOnContrast: 'var(--body-background-colour-on-contrast, #ffffff)',
      },
    };

    document.documentElement.classList.add('cms-preview');
    document.head.appendChild(this.colours.styles);

    window.addEventListener('message', (event) => this.onMessage(event));
    window.addEventListener('resize', () => this.scrollTo());

    this.whenLoaded(() => {
      window.parent.postMessage({ action: 'block-previews-ready' }, window.location.origin);
    });
  }

  /** Run the callback once the page (including stylesheets and images) has loaded */
  whenLoaded(callback) {
    if (document.readyState === 'complete') {
      callback();
    } else {
      window.addEventListener('load', callback, { once: true });
    }
  }

  onMessage(event) {
    const { data, origin } = event;

    if (origin !== window.location.origin || !data || typeof data !== 'object') return;

    if (BlockPreviewReceiver.actions.includes(data.action)) this[data.action](data);
  }

  /** The block element with the given ID (cached until a different block is requested) */
  getBlock(blockID) {
    if (!blockID) return null;

    if (!this.block || this.block.id !== blockID || !document.body.contains(this.block)) {
      this.block = document.getElementById(blockID);
    }

    return this.block;
  }

  /** Re-check the colour classes of a block and its neighbours */
  resizeBlocks(block) {
    block.classList.add('cms-preview');

    [block, block.previousElementSibling, block.nextElementSibling]
      .filter((element) => element && typeof element.resize === 'function')
      .forEach((element) => {
        try {
          element.resize();
        } catch (error) {
          console.error(error);
        }
      });
  }

  /** Apply palette colours from the CMS to the block being edited */
  updateColours(data = {}) {
    const { blockID, name, value, brightness } = data;

    this.colours.properties[name] = { value, brightness };

    if (!blockID) return;

    const selector = `.cms-preview #${CSS.escape(blockID)}.cms-preview`;

    this.colours.styles.textContent = Object.entries(this.colours.properties)
      .filter(([, colour]) => colour.value !== 'rgba(0, 0, 0, 0)')
      .map(([propertyName, colour]) => {
        const onContrast = colour.brightness === 'dark' ? 'light' : 'dark';

        return `${selector} {
          --_${propertyName}: ${colour.value};
          --_${propertyName}-contrast: ${this.colours.contrasts[colour.brightness]};
          --_${propertyName}-on-contrast: ${this.colours.contrasts[onContrast]};
        }`;
      })
      .join('\n');

    const block = this.getBlock(blockID);

    if (block) window.requestAnimationFrame(() => this.resizeBlocks(block));
  }

  /** Scroll to a block, or back to the URL's hash when no block is given (uses the theme's helpers) */
  scrollTo(data = {}) {
    const { blockID } = data;

    if (blockID && window.scrollToElementByID) {
      window.scrollToElementByID(blockID);
    } else if (window.scrollToHash) {
      window.scrollToHash(false, false);
    }
  }
}

// Only run inside the CMS preview iframe
if (window.self !== window.top) {
  new BlockPreviewReceiver();
}
