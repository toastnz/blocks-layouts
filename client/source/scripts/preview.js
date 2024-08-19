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

// Observe the CMS for the toast-block-layouts fieldsets
CMSObserver.observe('#BlockPreviewFrame', (items) => {
  // Grab the preview
  const preview = items[0];
  // Find the iframe
  const iframe = preview.querySelector('iframe');

  let interval = null;

  let width = -1;

  const resetInterval = () => {
    clearInterval(interval);
    interval = setInterval(() => onResize(), 1000);
  }

  const onResize = () => {
    // Do nothing if the width has not changed
    if (width === preview.clientWidth) return;
    // Do nothing if the iframe is not ready
    if (!iframe.contentDocument) return;
    // Do nothing if the iframe is not ready
    if (!iframe.contentDocument.readyState === 'complete') return;

    // Update the width
    width = preview.clientWidth;

    // The iframe is locked to 1440px wide, but we are going to transform scale it down to fit the width of the preview, then adjust the height of the preview to the height of the body of the iframe
    const scale = Math.min(width / 1440, 1);
    const height = iframe.contentDocument.body.clientHeight;

    // Set the scale and height
    iframe.style.transform = `scale(${scale})`;
    iframe.style.height = `${height}px`;
    preview.style.height = `${height * scale}px`;
  }

  // Create a resize observer
  const PreviewObserver = new ResizeObserver(() => {
    resetInterval();
    onResize();
  });

  // Observe the preview when the iframe is loaded
  PreviewObserver.observe(preview);
  onResize();

  resetInterval();
});
