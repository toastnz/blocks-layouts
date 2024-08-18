/*------------------------------------------------------------------
Import styles
------------------------------------------------------------------*/

import 'styles/icons.scss';

/*------------------------------------------------------------------
Import modules
------------------------------------------------------------------*/

import DomObserverController from 'domobserverjs';

/*------------------------------------------------------------------
Dom Observer
------------------------------------------------------------------*/

const CMSObserver = new DomObserverController();

/*------------------------------------------------------------------
Document setup
------------------------------------------------------------------*/

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
