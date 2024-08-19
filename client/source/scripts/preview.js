/*------------------------------------------------------------------
Import styles
------------------------------------------------------------------*/

import 'styles/preview.scss';

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

// The preview element
let preview = null;

function movePreviewToNewPanel() {
  // Find the root element
  const root = document.querySelector('.cms-content .panel');

  // Create a new fieldset element inside the root
  const fieldset = document.createElement('fieldset');

  // Make sure the preview and root exist
  if(!preview || !root) return;

  root.appendChild(fieldset);

  // Move the preview to the root
  fieldset.appendChild(preview);
}

// Observe the CMS for the toast-block-layouts fieldsets
CMSObserver.observe('#BlockPreviewFrame', (items) => {
  // Grab the preview
  preview = items[0];
  // Move the preview to the root
  movePreviewToNewPanel();

  // Find the iframe
  const iframe = preview.querySelector('iframe');

  let width = -1;

  const onResize = () => {
    // Update the width
    width = preview.clientWidth;

    // The iframe is locked to 1440px wide, but we are going to transform scale it down to fit the width of the preview, then adjust the height of the preview to the height of the body of the iframe
    const scale = Math.min(width / 1440, 1);
    // const height = iframe.contentDocument.body.clientHeight;
    const height = preview.clientHeight;

    // Set the scale and height
    iframe.style.transform = `scale(${scale})`;
    iframe.style.height = `${height / scale}px`;
    // preview.style.height = `${height * scale}px`;
  }

  // Create a resize observer
  const PreviewObserver = new ResizeObserver(() => {
    // resetInterval();
    onResize();
  });

  // Observe the preview when the iframe is loaded
  PreviewObserver.observe(preview);
  onResize();
});
