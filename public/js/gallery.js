/**
 * Modern Image Gallery JavaScript
 * Handles lightbox, lazy loading, and interactive features
 */

class ImageGallery {
    constructor() {
        this.lightboxOverlay = null;
        this.lightboxImage = null;
        this.currentImageIndex = 0;
        this.images = [];
        this.init();
    }

    init() {
        this.createLightbox();
        this.bindEvents();
        this.setupLazyLoading();
        this.collectImages();
    }

    createLightbox() {
        // Create lightbox overlay
        this.lightboxOverlay = document.createElement('div');
        this.lightboxOverlay.className = 'lightbox-overlay';
        this.lightboxOverlay.innerHTML = `
            <div class="lightbox-content">
                <img class="lightbox-image" alt="">
                <button class="lightbox-close" aria-label="Закрыть">×</button>
            </div>
        `;
        document.body.appendChild(this.lightboxOverlay);

        // Get references
        this.lightboxImage = this.lightboxOverlay.querySelector('.lightbox-image');
        this.lightboxClose = this.lightboxOverlay.querySelector('.lightbox-close');
    }

    bindEvents() {
        // Gallery item clicks
        document.addEventListener('click', (e) => {
            const galleryItem = e.target.closest('.gallery-item');
            if (galleryItem) {
                e.preventDefault();
                this.openLightbox(galleryItem);
            }
        });

        // Lightbox close events
        this.lightboxClose.addEventListener('click', () => this.closeLightbox());
        this.lightboxOverlay.addEventListener('click', (e) => {
            if (e.target === this.lightboxOverlay) {
                this.closeLightbox();
            }
        });

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (this.lightboxOverlay.classList.contains('active')) {
                switch (e.key) {
                    case 'Escape':
                        this.closeLightbox();
                        break;
                    case 'ArrowLeft':
                        this.previousImage();
                        break;
                    case 'ArrowRight':
                        this.nextImage();
                        break;
                }
            }
        });

        // Resize handling
        window.addEventListener('resize', () => {
            if (this.lightboxOverlay.classList.contains('active')) {
                this.positionLightbox();
            }
        });
    }

    setupLazyLoading() {
        // Create intersection observer for lazy loading
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        this.loadImage(img);
                        observer.unobserve(img);
                    }
                });
            });

            // Observe all gallery images
            document.querySelectorAll('.gallery-item img').forEach(img => {
                imageObserver.observe(img);
            });
        } else {
            // Fallback for older browsers
            document.querySelectorAll('.gallery-item img').forEach(img => {
                this.loadImage(img);
            });
        }
    }

    loadImage(img) {
        if (img.dataset.src) {
            const tempImg = new Image();
            tempImg.onload = () => {
                img.src = img.dataset.src;
                img.classList.remove('loading-placeholder');
                img.classList.add('loaded');
            };
            tempImg.onerror = () => {
                img.src = '/images/placeholder-error.png';
                img.classList.remove('loading-placeholder');
                img.classList.add('error');
            };
            tempImg.src = img.dataset.src;
        }
    }

    collectImages() {
        this.images = Array.from(document.querySelectorAll('.gallery-item')).map(item => ({
            element: item,
            src: item.querySelector('img').src,
            filename: item.querySelector('.image-filename').textContent
        }));
    }

    openLightbox(galleryItem) {
        this.currentImageIndex = this.images.findIndex(img => img.element === galleryItem);
        
        if (this.currentImageIndex === -1) {
            this.currentImageIndex = 0;
        }

        this.showImage(this.currentImageIndex);
        this.lightboxOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
        
        // Analytics or other tracking can be added here
        this.trackImageView(this.images[this.currentImageIndex]);
    }

    closeLightbox() {
        this.lightboxOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    showImage(index) {
        if (index < 0 || index >= this.images.length) return;
        
        const imageData = this.images[index];
        this.lightboxImage.src = imageData.src;
        this.lightboxImage.alt = imageData.filename;
        
        this.positionLightbox();
    }

    positionLightbox() {
        // Ensure image fits viewport
        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;
        
        const maxWidth = viewportWidth * 0.9;
        const maxHeight = viewportHeight * 0.9;
        
        this.lightboxImage.style.maxWidth = maxWidth + 'px';
        this.lightboxImage.style.maxHeight = maxHeight + 'px';
    }

    nextImage() {
        this.currentImageIndex = (this.currentImageIndex + 1) % this.images.length;
        this.showImage(this.currentImageIndex);
    }

    previousImage() {
        this.currentImageIndex = (this.currentImageIndex - 1 + this.images.length) % this.images.length;
        this.showImage(this.currentImageIndex);
    }

    trackImageView(imageData) {
        // Placeholder for analytics tracking
        console.log('Image viewed:', imageData.filename);
        
        // Example: Send to analytics service
        // gtag('event', 'image_view', {
        //     image_name: imageData.filename
        // });
    }
}

// Utility functions
const GalleryUtils = {
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    },

    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('ru-RU', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    },

    getImageAspectRatio(img) {
        return img.naturalWidth / img.naturalHeight;
    },

    // Dynamic grid sizing based on image proportions
    adjustGridItem(item, img) {
        const aspectRatio = this.getImageAspectRatio(img);
        const gridRow = item.closest('.gallery-grid');
        
        if (aspectRatio > 1.5) {
            // Wide image
            item.style.gridRowEnd = 'span 3';
        } else if (aspectRatio < 0.7) {
            // Tall image
            item.style.gridRowEnd = 'span 5';
        } else {
            // Square-ish image
            item.style.gridRowEnd = 'span 4';
        }
    }
};

// Initialize gallery when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Initialize the gallery
    const gallery = new ImageGallery();
    
    // Make gallery instance globally available for debugging
    window.imageGallery = gallery;
    
    // Add loading states to images
    document.querySelectorAll('.gallery-item img').forEach(img => {
        img.classList.add('loading-placeholder');
        
        img.onload = () => {
            img.classList.remove('loading-placeholder');
            GalleryUtils.adjustGridItem(img.closest('.gallery-item'), img);
        };
        
        img.onerror = () => {
            img.classList.remove('loading-placeholder');
            img.classList.add('error');
            img.src = '/images/placeholder-error.png';
        };
    });
    
    // Add search functionality with debounce
    const searchInput = document.querySelector('#gallery-search');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const searchTerm = e.target.value.trim();
                this.performSearch(searchTerm);
            }, 300); // Debounce for 300ms
        });
    }

    performSearch(searchTerm) {
        const galleryItems = document.querySelectorAll('.gallery-item');
        let visibleCount = 0;
        
        galleryItems.forEach(item => {
            const filename = item.querySelector('.image-filename').textContent.toLowerCase();
            const shouldShow = !searchTerm || filename.includes(searchTerm.toLowerCase());
            
            item.style.display = shouldShow ? 'block' : 'none';
            if (shouldShow) visibleCount++;
        });
        
        // Update visible count in header
        this.updateVisibleCount(visibleCount);
    }

    updateVisibleCount(count) {
        const statsElement = document.querySelector('.gallery-stats');
        if (statsElement && count !== null) {
            const totalElement = document.querySelector('.gallery-stats');
            if (totalElement) {
                const text = totalElement.textContent;
                const updatedText = text.replace(/Показано: \d+/, `Показано: ${count}`);
                totalElement.textContent = updatedText;
            }
        }
    }
    
    // Add filter functionality if filter exists
    const filterButtons = document.querySelectorAll('.gallery-filter');
    filterButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            
            const filter = button.dataset.filter;
            const galleryItems = document.querySelectorAll('.gallery-item');
            
            galleryItems.forEach(item => {
                if (filter === 'all') {
                    item.style.display = 'block';
                } else {
                    // Add your filtering logic here based on image metadata
                    item.style.display = 'block'; // Placeholder
                }
            });
        });
    });
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { ImageGallery, GalleryUtils };
}