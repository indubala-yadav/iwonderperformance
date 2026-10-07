document.addEventListener('DOMContentLoaded', function () {
    const images = document.querySelectorAll('.article-wrapper .wp-block-image img, .article-wrapper table img');
    const lightbox = document.createElement('div');
    const lightboxImg = document.createElement('img');
    const caption = document.createElement('div');
    const closeBtn = document.createElement('span');
    const prevBtn = document.createElement('button');
    const nextBtn = document.createElement('button');
    const spinner = document.createElement('div');
    const zoomInBtn = document.createElement('button');
    const zoomOutBtn = document.createElement('button');
    const resetZoomBtn = document.createElement('button');

    let currentIndex = 0;
    let galleryImages = [];
    let scale = 1;
    let translateX = 0;
    let translateY = 0;
    let dragMoved = false;

    // Setup lightbox container
    lightbox.className = 'custom-lightbox';
    lightboxImg.className = 'custom-lightbox-img';
    caption.className = 'custom-lightbox-caption';
    closeBtn.className = 'custom-lightbox-close';
    closeBtn.innerHTML = '&times;';
    prevBtn.className = 'custom-lightbox-prev';
    prevBtn.innerHTML = '&#10094;';
    nextBtn.className = 'custom-lightbox-next';
    nextBtn.innerHTML = '&#10095;';
    spinner.className = 'custom-lightbox-spinner';

    // Zoom buttons
    zoomOutBtn.className = 'custom-lightbox-zoom-out';
    zoomOutBtn.textContent = '−';
    resetZoomBtn.className = 'custom-lightbox-zoom-reset';
    resetZoomBtn.textContent = '⭯';
    zoomInBtn.className = 'custom-lightbox-zoom-in';
    zoomInBtn.textContent = '+';

    const zoomControls = document.createElement('div');
    zoomControls.className = 'custom-lightbox-zoom-controls';
    zoomControls.append(zoomOutBtn, resetZoomBtn, zoomInBtn);

    // Detect mobile
    const isMobile = window.innerWidth <= 768;
    if (isMobile) zoomControls.style.display = 'none';

    const zoomWrapper = document.createElement('div');
    zoomWrapper.className = 'custom-lightbox-zoom-wrapper';
    zoomWrapper.append(lightboxImg, caption);

    lightbox.append(closeBtn, zoomWrapper, prevBtn, nextBtn, zoomControls, spinner);
    document.body.appendChild(lightbox);

    function updateZoomTransform() {
        zoomWrapper.style.transform = `scale(${scale}) translate(${translateX}px, ${translateY}px)`;
        zoomWrapper.classList.remove('grabbing');
        if (scale > 1) {
            zoomWrapper.classList.add('grab');
        } else {
            zoomWrapper.classList.remove('grab');
        }
    }

    function showLightbox(index) {
        const img = galleryImages[index];
        lightboxImg.style.display = 'none';
        spinner.style.display = 'block';
        document.body.classList.add('no-scroll');

        lightboxImg.src = img.src;
		lightboxImg.alt = img.alt || '';
        scale = 1;
        translateX = 0;
        translateY = 0;
        updateZoomTransform();

        let foundCaption = '';
        const wpBlock = img.closest('.wp-block-image');
        if (wpBlock) {
            const figcaption = wpBlock.querySelector('figcaption');
            foundCaption = figcaption ? figcaption.textContent.trim() : '';
        }
        caption.textContent = foundCaption || img.getAttribute('data-caption') || img.alt || '';

        lightbox.classList.add('open');
        currentIndex = index;

        lightboxImg.onload = () => {
            spinner.style.display = 'none';
            lightboxImg.style.display = 'block';
        };
    }

    function hideLightbox() {
        lightbox.classList.remove('open');
        document.body.classList.remove('no-scroll');
    }

    function showNextImage() {
        currentIndex = (currentIndex + 1) % galleryImages.length;
        showLightbox(currentIndex);
    }

    function showPrevImage() {
        currentIndex = (currentIndex - 1 + galleryImages.length) % galleryImages.length;
        showLightbox(currentIndex);
    }

    function zoomIn() {
        scale = Math.min(scale + 0.2, 3);
        updateZoomTransform();
    }

    function zoomOut() {
        scale = Math.max(scale - 0.2, 1);
        updateZoomTransform();
    }

    function resetZoom() {
        scale = 1;
        translateX = 0;
        translateY = 0;
        updateZoomTransform();
    }

    // Image click binding
    galleryImages = Array.from(images);
    galleryImages.forEach((img, index) => {
        img.style.cursor = 'zoom-in';
        img.addEventListener('click', () => showLightbox(index));
    });

    closeBtn.addEventListener('click', hideLightbox);
    prevBtn.addEventListener('click', (e) => { e.stopPropagation(); showPrevImage(); });
    nextBtn.addEventListener('click', (e) => { e.stopPropagation(); showNextImage(); });
    zoomInBtn.addEventListener('click', (e) => { e.stopPropagation(); zoomIn(); });
    zoomOutBtn.addEventListener('click', (e) => { e.stopPropagation(); zoomOut(); });
    resetZoomBtn.addEventListener('click', (e) => { e.stopPropagation(); resetZoom(); });

    // Handle accidental closing after dragging
    lightbox.addEventListener('mousedown', () => {
        dragMoved = false;
    });

    lightbox.addEventListener('mousemove', () => {
        dragMoved = true;
    });

    lightbox.addEventListener('click', (e) => {
        const ignore = [lightboxImg, caption, prevBtn, nextBtn, closeBtn, zoomInBtn, zoomOutBtn, resetZoomBtn];
        if (dragMoved) return;
        if (!ignore.includes(e.target)) hideLightbox();
    });

    document.addEventListener('keydown', (e) => {
        if (!lightbox.classList.contains('open')) return;
        if (e.key === 'ArrowRight') showNextImage();
        if (e.key === 'ArrowLeft') showPrevImage();
        if (e.key === 'Escape') hideLightbox();
    });

    // 🖱 Drag to move (desktop only)
    if (!isMobile) {
        let isDragging = false;
        let startX = 0;
        let startY = 0;

        zoomWrapper.addEventListener('mousedown', (e) => {
            if (scale <= 1) return;
            isDragging = true;
            startX = e.clientX - translateX;
            startY = e.clientY - translateY;
            zoomWrapper.classList.remove('grab');
            zoomWrapper.classList.add('grabbing');
            e.preventDefault();
        });

        document.addEventListener('mousemove', (e) => {
            if (!isDragging) return;
            translateX = e.clientX - startX;
            translateY = e.clientY - startY;
            updateZoomTransform();
        });

        document.addEventListener('mouseup', () => {
            if (isDragging) {
                isDragging = false;
                zoomWrapper.classList.remove('grabbing');
                if (scale > 1) {
                    zoomWrapper.classList.add('grab');
                }
            }
        });
    }
});