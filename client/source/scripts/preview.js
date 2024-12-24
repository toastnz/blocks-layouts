// /*------------------------------------------------------------------
// Import styles
// ------------------------------------------------------------------*/

// import 'styles/preview.scss';

// /*------------------------------------------------------------------
// Scripts
// ------------------------------------------------------------------*/

import DomObserverController from 'domobserverjs';

/*------------------------------------------------------------------
Setup
------------------------------------------------------------------*/

// Create a new instance of the DomObserverController
const CMSObserver = new DomObserverController();

function movePreviewToNewPanel(preview) {
  // Find the root element
  const root = document.querySelector('.cms-content .panel');

  // Create a new fieldset element inside the root
  const fieldset = document.createElement('fieldset');

  // Make sure the preview and root exist
  if (!preview || !root) return;

  root.appendChild(fieldset);

  // Move the preview to the root
  fieldset.appendChild(preview);
}

function createThumb(preview) {
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
}

// Observe the CMS for the toast-block-layouts fieldsets
CMSObserver.observe('#BlockPreviewFrame', (items) => {
  // Grab the preview
  const preview = items[0];
  // Move the preview to the root
  movePreviewToNewPanel(preview);
  // Create the thumb
  createThumb(preview);
});

// Chuck this on the normal page preivew cus why not ;)
CMSObserver.observe('.cms-preview .panel', (items) => {
  // Grab the preview
  const preview = items[0];
  // Create the thumb
  createThumb(preview);
});
