/*------------------------------------------------------------------
Import styles
------------------------------------------------------------------*/

import 'styles/blocks.scss';

/*------------------------------------------------------------------
Scripts
------------------------------------------------------------------*/

import DomObserverController from 'domobserverjs';

/*------------------------------------------------------------------
Setup
------------------------------------------------------------------*/

// Create a new instance of the DomObserverController
const CMSObserver = new DomObserverController();

// Observe the CMS for the toast-block-layouts fieldsets
CMSObserver.observe('.toast-block-layouts', (fieldsets) => {
  // Loop through the fieldsets
  (async () => {
    for (const fieldset of fieldsets) {
      // Find all the images in the fieldset
      const images = fieldset.querySelectorAll('img');

      // Loop the images
      for (const img of images) {
        const imgSrc = img.src;

        // If the img src does not include .svg, continue to the next image
        if (!imgSrc.includes('.svg')) continue;

        try {
          // Fetch the svg, convert it to text, and replace the img with the svg
          const response = await fetch(imgSrc);
          const svg = await response.text();

          // Create a new div
          const div = document.createElement('div');

          // Set the innerHTML of the div to the svg
          div.innerHTML = svg;

          // Replace the img with the div
          img.parentNode.replaceChild(div.firstChild, img);
        } catch (error) {
          console.error(`Failed to fetch SVG: ${imgSrc}`, error);
        }
      }
    }
  })();
});

// Block siblings script to move siblings to the top of the tab for better styling
CMSObserver.observe('.content-block-siblings', (elements) => {
  const element = elements[0];
  const tab = element.closest('.tab-content');

  tab.insertBefore(element, tab.firstChild);
});

class BlockPreviewMessenger {
  constructor() {
    this.initialised = false;
    this.iframes = [];
    this.blockID = null;
    this.blockIDElement = null;
    this.previewIframe = null;
    this.colours = {
      queue: [],
      timeout: null,
    };

    // Listen for the block previews ready message
    window.addEventListener('message', (event) => {
      if (event.data.action === 'block-previews-ready') this.init();
    });
  }

  init() {
    if (this.initialised) return;

    this.initialised = true;

    // When we receive a ColourPaletteChanged event, run the updateColours method
    window.addEventListener('ColourPaletteChanged', () => this.updateColours());

    // Update the colours
    this.updateColours();
    // Prepare to scroll to the block
    this.prepareToScroll();
    // Prepare to update the template
    this.prepareTemplateUpdates();
  }

  iframeExists() {
    // Look for all iframes on the page
    this.iframes = [...document.querySelectorAll('iframe')];
    // Find the preview iframe again if it is not found
    this.previewIframe = this.iframes.find((iframe) => iframe.src.includes('CMSPreview'));
    // Resturn false if the preview iframe is not found
    if (!this.previewIframe) return false;
    // If there is not contentWindow, return false
    if (!this.previewIframe.contentWindow) return false;

    return true;
  }

  getBlockID() {
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

  updateColours() {
    // Make sure the iframe exists
    if (!this.iframeExists()) return;

    clearTimeout(this.colours.timeout);

    this.colours.timeout = setTimeout(() => {
      if (window.ColourPalettes) {
        Object.entries(window.ColourPalettes).forEach(([name, data]) => {
          this.previewIframe.contentWindow.postMessage({
            action: 'updateColours',
            name,
            value: data.value,
            brightness: data.brightness,
            blockID: this.getBlockID(),
          }, window.location.origin);
        });
      }
    }, 200);
  }

  prepareToScroll() {
    let scrollTimeout = null;
    let clicked = false;

    // Look for block links that will trigger a scroll to method
    CMSObserver.observe('.ss-gridfield-item, .content-block-siblings > a', (elements) => {
      elements.forEach((element) => {
        // Find the element with the block ID
        const blockIDElement = element.querySelector('[data-block-id]');
        const blockID = (blockIDElement) ? blockIDElement.getAttribute('data-block-id') : null;

        // If there is no block ID, return
        if (!blockID) return;

        // When we hover over the element, we want to scroll to the block
        element.addEventListener('mouseenter', () => {

          clearTimeout(scrollTimeout);

          scrollTimeout = setTimeout(() => {
            if (!this.iframeExists()) return;

            // Post a message to the iframe
            this.previewIframe.contentWindow.postMessage({
              action: 'scrollTo',
              blockID,
            }, window.location.origin);
          }, 300);
        });

        element.addEventListener('click', () => {
          clicked = true;

          clearTimeout(scrollTimeout);

          scrollTimeout = setTimeout(() => {
            clicked = false;
          }, 500);
        });

        element.addEventListener('mouseleave', () => {
          if (clicked) return;

          clearTimeout(scrollTimeout);

          scrollTimeout = setTimeout(() => {
            if (!this.iframeExists()) return;

            // Post a message to the iframe
            this.previewIframe.contentWindow.postMessage({
              action: 'scrollTo',
            }, window.location.origin);
          }, 500);
        });
      });
    });
  }

  prepareTemplateUpdates() {
    const allInputs = [];

    const onChange = (input) => {
      if (!this.iframeExists()) return;

      // Update the colours
      this.updateColours();

      // Post a message to the iframe
      this.previewIframe.contentWindow.postMessage({
        action: 'getTemplate',
        templatePath: input.value,
        blockID: this.getBlockID(),
      }, window.location.origin);
    }

    // Watch for changes to the template select field
    CMSObserver.observe('#Form_ItemEditForm_Template_Holder [name="Template"]', (inputs) => {
      inputs.forEach((input) => {
        try {
          jQuery(input).on('change', () => onChange(input));
        } catch (err) {
          input.addEventListener('change', () => onChange(input));
        }

        if (input.checked) onChange(input);

        if (OpenCMSPreviewController) {
          OpenCMSPreviewController.on('refresh', () => {
            if (input.checked) onChange(input);
          });
        }

        // Add the input to the allInputs array
        allInputs.push(input);
      });
    });

    // If the iframe reloads we need to send the on change event again
    window.addEventListener('message', (event) => {
      allInputs.forEach((input) => {
        // If the checkbox is no longer checked, return
        if (!input.checked) return;
        // Make sure this input is still on the page
        if (document.body.contains(input) == false) return;
        // Make sure the message is from the preview iframe
        if (event.data.action === 'block-previews-ready') onChange(input);
      });
    });
  }
}

new BlockPreviewMessenger();
