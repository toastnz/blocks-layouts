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

  // Create a new button element to drag the preview width
  const thumb = document.createElement('button');

  // We will toggle this to toggle the dragging state
  let isDragging = false;

  // Add a class
  thumb.classList.add('preview-thumb');

  // Add the thumb to the preview
  preview.appendChild(thumb);

  // Add a mouse down event listener
  thumb.addEventListener('mousedown', () => {
    // Set dragging to true
    isDragging = true;
    preview.classList.add('dragging');
  });

  window.addEventListener('mouseup', () => {
    // Set dragging to false
    isDragging = false;
    preview.classList.remove('dragging');
  });

  window.addEventListener('mousemove', (e) => {
    // If we are not dragging, return
    if (!isDragging) return;

    // Get the parent panel and its container
    const panel = preview.parentElement;
    const container = panel.parentElement;

    // Calculate the mouse X position relative to the container
    const mouseX = (e.clientX - 10) - container.getBoundingClientRect().left;

    // Calculate the container width
    const containerWidth = container.clientWidth;

    // Get the mouseX as a percentage of the container width
    const percentage = (mouseX / containerWidth) * 100;

    // Clamp the percentage to between 25% and 75% and round to 2 decimal places
    const clampedPercentage = 100 - Math.min(Math.max(percentage, 25), 75).toFixed(2);

    // Set the width of the preview
    document.body.style.setProperty('--block-preview-width', `${clampedPercentage}%`);
  });


  // // Find the iframe
  // const iframe = preview.querySelector('iframe');

  // let width = -1;

  // const onResize = () => {
  //   // Update the width
  //   width = preview.clientWidth;

  //   // The iframe is locked to 1440px wide, but we are going to transform scale it down to fit the width of the preview, then adjust the height of the preview to the height of the body of the iframe
  //   const scale = Math.min(width / 1440, 1);
  //   // const height = iframe.contentDocument.body.clientHeight;
  //   const height = preview.clientHeight;

  //   // Set the scale and height
  //   iframe.style.transform = `scale(${scale})`;
  //   iframe.style.height = `${height / scale}px`;
  //   // preview.style.height = `${height * scale}px`;
  // }

  // // Create a resize observer
  // const PreviewObserver = new ResizeObserver(() => {
  //   // resetInterval();
  //   onResize();
  // });

  // // Observe the preview when the iframe is loaded
  // PreviewObserver.observe(preview);
  // onResize();
});
