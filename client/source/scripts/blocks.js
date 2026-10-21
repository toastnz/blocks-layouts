/*------------------------------------------------------------------
Blocks: CMS scripts

- Inlines SVG layout icons in the layout picker so they can be styled
- Moves the "sibling blocks" strip to the top of the edit form
- Talks to the preview iframe (block-preview-receiver.js) to scroll to
  the hovered block and live-update palette colours
------------------------------------------------------------------*/

import 'styles/blocks.scss';
import DomObserverController from 'domobserverjs';

const CMSObserver = new DomObserverController();

/*------------------------------------------------------------------
Layout picker: inline SVG icons
------------------------------------------------------------------*/

/** SVG markup by URL, shared across pickers */
const svgCache = new Map();

function fetchSVG(url) {
  if (!svgCache.has(url)) {
    svgCache.set(url, fetch(url).then((response) => {
      if (!response.ok) throw new Error(`HTTP ${response.status}`);
      return response.text();
    }));
  }

  return svgCache.get(url);
}

async function inlineSVG(img) {
  try {
    const markup = await fetchSVG(img.src);
    const svg = new DOMParser().parseFromString(markup, 'image/svg+xml').documentElement;

    if (svg && svg.nodeName.toLowerCase() === 'svg' && img.isConnected) {
      img.replaceWith(document.importNode(svg, true));
    }
  } catch (error) {
    console.error(`Failed to fetch SVG: ${img.src}`, error);
  }
}

CMSObserver.observe('.toast-block-layouts', (fieldsets) => {
  fieldsets.forEach((fieldset) => {
    fieldset.querySelectorAll('img').forEach((img) => {
      if (new URL(img.src, window.location.href).pathname.endsWith('.svg')) inlineSVG(img);
    });
  });
});

/*------------------------------------------------------------------
Sibling blocks strip
------------------------------------------------------------------*/

CMSObserver.observe('.content-block-siblings', (elements) => {
  const element = elements[0];
  const tab = element && element.closest('.tab-content');

  if (!tab) return;

  tab.insertBefore(element, tab.firstChild);

  // Scroll the strip to the current block, otherwise back to the start
  const activeLink = element.querySelector('a.active');

  if (activeLink) {
    activeLink.scrollIntoView({ block: 'center', container: 'nearest' });
  } else {
    element.scrollTop = 0;
  }
});

/*------------------------------------------------------------------
Preview messaging
------------------------------------------------------------------*/

class BlockPreviewMessenger {
  constructor() {
    this.initialised = false;
    this.previewIframe = null;
    this.blockID = null;
    this.blockIDElement = null;
    this.colourTimeout = null;

    window.addEventListener('message', (event) => {
      if (event.origin === window.location.origin && event.data && event.data.action === 'block-previews-ready') {
        this.init();
      }
    });
  }

  init() {
    if (this.initialised) return;

    this.initialised = true;

    window.addEventListener('ColourPaletteChanged', () => this.updateColours());

    this.updateColours();
    this.prepareToScroll();
  }

  /** The CMS preview iframe, if it is open */
  getPreviewWindow() {
    if (!this.previewIframe || !this.previewIframe.isConnected) {
      this.previewIframe = document.querySelector('.cms-preview iframe')
        || [...document.querySelectorAll('iframe')].find((iframe) => iframe.src.includes('CMSPreview'))
        || null;
    }

    return this.previewIframe ? this.previewIframe.contentWindow : null;
  }

  /** Send a message to the preview iframe, if it is open */
  post(message) {
    const previewWindow = this.getPreviewWindow();

    if (previewWindow) previewWindow.postMessage(message, window.location.origin);
  }

  /** The anchor ID of the block being edited, from the "Block ID" field */
  getBlockID() {
    if (this.blockID && this.blockIDElement && this.blockIDElement.isConnected) return this.blockID;

    this.blockIDElement = document.querySelector('#Form_ItemEditForm_BlockID');
    this.blockID = this.blockIDElement ? this.blockIDElement.innerText.trim() : null;

    return this.blockID;
  }

  /** Push the CMS palette colours (from the colour palettes module) into the preview */
  updateColours() {
    clearTimeout(this.colourTimeout);

    this.colourTimeout = setTimeout(() => {
      if (!window.ColourPalettes) return;

      Object.entries(window.ColourPalettes).forEach(([name, data]) => {
        this.post({
          action: 'updateColours',
          name,
          value: data.value,
          brightness: data.brightness,
          blockID: this.getBlockID(),
        });
      });
    }, 200);
  }

  /** Hovering a block row or sibling icon scrolls the preview to that block */
  prepareToScroll() {
    let scrollTimeout = null;
    let clicked = false;

    const scrollTo = (blockID, delay) => {
      clearTimeout(scrollTimeout);
      scrollTimeout = setTimeout(() => this.post({ action: 'scrollTo', blockID }), delay);
    };

    CMSObserver.observe('.ss-gridfield-item, .content-block-siblings > a', (elements) => {
      elements.forEach((element) => {
        const blockIDElement = element.querySelector('[data-block-id]');
        const blockID = blockIDElement ? blockIDElement.getAttribute('data-block-id') : null;

        if (!blockID) return;

        element.addEventListener('mouseenter', () => scrollTo(blockID, 300));

        element.addEventListener('click', () => {
          clicked = true;
          clearTimeout(scrollTimeout);
          scrollTimeout = setTimeout(() => { clicked = false; }, 500);
        });

        // Scroll back to the current block when the pointer leaves (unless it was a click)
        element.addEventListener('mouseleave', () => {
          if (!clicked) scrollTo(undefined, 500);
        });
      });
    });
  }
}

new BlockPreviewMessenger();
