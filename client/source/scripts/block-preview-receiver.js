class BlockPreviewReceiver {
  constructor() {
    // Create a new XMLHttpRequest object
    this.xhr = new XMLHttpRequest();

    // A timeout to cancel ready signals
    this.timeout = null;

    // Set the block to null
    this.block = null;

    // Set up a ResizeObserver
    window.addEventListener('resize', () => this.scrollTo());

    // Add a CMS preview class to the body
    document.documentElement.classList.add('cms-preview');

    // Delegate the message to the appropriate method
    window.addEventListener('message', (event) => {
      const { data, origin } = event;
      const { action } = data;
      if (this[action] && origin === window.location.origin) this[action](data);
    });

    // Set up some vars specific to colour updates
    this.colours = {
      timeout: null,
      styles: document.createElement('style'),
      properties: {},
      contrasts: {
        dark: 'var(--global-contrast-colour-light, #ffffff)',
        light: 'var(--global-contrast-colour-dark, #000000)',
        body: 'var(--body-text-colour)',
      }
    }

    // Set up some vars specific to template updates
    this.templates = {
      styles: document.createElement('style'),
      memory: {},
      current: null,
    }

    // Append the styles to the head
    document.head.appendChild(this.colours.styles);
    document.head.appendChild(this.templates.styles);


    // Let the parent window know that the block previews are ready
    this.timeout = setInterval(() => {
      if (document.readyState === 'complete') {
        window.parent.postMessage({ action: 'block-previews-ready' }, window.location.origin);
        clearInterval(this.timeout);
      }
    }, 500);
  }

  getBlock(blockID) {
    if (!this.block || !document.body.contains(this.block)) this.block = document.getElementById(blockID);
    if (!this.block) return null;
    return this.block;
  }

  resizeBlocks(block) {
    // Collect the relevant blocks that are COLOUR-BLOCK
    const blocks = [
      block,
      block.nextElementSibling,
      block.previousElementSibling,
    ].filter(element => element && element.tagName === 'COLOUR-BLOCK');

    // Resize the colour blocks
    blocks.forEach((element) => {
      try { element.resize() } catch (err) { console.error(err) }
    });
  }

  learnTemplate(templateResponse) {
    const { blockID, templatePath, elements, styles } = templateResponse;

    let index = 0;

    // Look at the elements, and find the index of the block with the blockID
    elements.forEach((element, i) => {
      if (element.id === blockID) {
        index = i;
      }
    });

    // Update the block memory
    this.templates.memory[templatePath] = {
      blockID,
      elements,
      styles,
      index,
      length: elements.length,
    };

    // If there is no current template, just set it
    if (!this.templates.current) {
      this.templates.current = templatePath;
    }


    // Otherwise we will update the current template
    else {
      // Update the template
      this.updateTemplate(templatePath);
    }
  }

  updateTemplate(templatePath) {
    const { blockID, index, length } = this.templates.memory[this.templates.current];
    const { elements, styles, } = this.templates.memory[templatePath];
    // Find the block using the blockID
    let block = this.getBlock(blockID);

    // If the block is not found, return
    if (!block) return console.error('Block not found', blockID);

    // Remove the block element's siblings based on the index and length, for example, if the length is 2, and the index is 1, remove the previous sibling
    for (let i = 0; i < length; i++) {
      if (i < index) {
        block.previousElementSibling.remove();
      }

      if (i > index) {
        block.nextElementSibling.remove();
      }
    }

    // Convert the elements to a string containing all their HTML
    let template = elements.reduce((acc, element) => {
      return `${acc}${element.outerHTML}`;
    }, '');

    // Replace the block outerHTML with the new template
    block.outerHTML = template;

    // Find the new block
    block = this.getBlock(blockID)

    // If the block is not found, return
    if (!block) return console.error('Block not found', blockID);

    // Add the CMS preview class to the block
    block.classList.add('cms-preview');

    // Update the styles
    this.templates.styles.innerHTML = styles;

    // Update the current template
    this.templates.current = templatePath;

    // Scroll to the block
    this.scrollTo({ blockID });
  }

  getTemplate(data = {}) {
    const { blockID, templatePath } = data;

    // Abort the current request
    this.xhr.abort();

    // Set up the API URL
    let API = `/blocks-api/getBlock?BlockID=${blockID}&nocache=${new Date().getTime()}`;

    // Return if the block ID is not set
    if (!blockID) return;

    // Add the templatePath to the API URL if it is set
    if (templatePath) API += `&Template=${templatePath}`;

    // Open a new request
    this.xhr.open('GET', API, true);

    // Set the request headers
    this.xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

    // Set the request callback
    this.xhr.onreadystatechange = () => {
      if (this.xhr.readyState === 4 && this.xhr.status === 200) {
        const parser = new DOMParser();
        const response = JSON.parse(this.xhr.responseText);

        let template = response.template;
        let styles = response.styles;

        // convert the template to html
        template = parser.parseFromString(template, 'text/html');

        // Get the children of the template
        let elements = [...template.body.children];

        // Learn the new template data
        this.learnTemplate({ blockID, templatePath, elements, styles });
      }
    };

    // Send the request
    this.xhr.send();
  }

  updateColours(data = {}) {
    let { blockID, name, value, brightness } = data;

    let block = this.getBlock(blockID);

    // Update the colours object
    this.colours.properties[name] = { value, brightness };

    if (value == 'rgba(0, 0, 0, 0)') {
      delete this.colours.properties[name];
      brightness = 'body';
    }

    const styles = Object.entries(this.colours.properties).reduce((acc, [name, data]) => {
      const { value, brightness } = data;
      const onContrast = (brightness === 'body') ? 'body' : (brightness === 'dark' ? 'light' : 'dark');

      return `
        ${acc}
        .cms-preview #${blockID}.cms-preview {
          --_${name}: ${value};
          --_${name}-contrast: ${this.colours.contrasts[brightness]};
          --_${name}-on-contrast: ${this.colours.contrasts[onContrast]};
        }
      `;
    }, '');

    // Update the styles
    this.colours.styles.innerHTML = styles;

    if (block) {
      block.classList.add('cms-preview');

      clearTimeout(this.colours.timeout);

      this.colours.timeout = setTimeout(() => this.resizeBlocks(block), 500);
    }
  }

  scrollTo(data = {}) {
    const { blockID } = data;

    if (blockID && window.scrollToElementByID) {
      window.scrollToElementByID(blockID);
    } else if (window.scrollToHash) {
      window.scrollToHash(false, false);
    }
  }
}

// If the window is not the top window, then we are in a preview
if (window.self !== window.top) {
  // Create a new BlockPreviewController
  new BlockPreviewReceiver();
}
