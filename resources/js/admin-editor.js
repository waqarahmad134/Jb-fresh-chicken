/**
 * Initialize Summernote editor for admin content fields
 * This is a reusable function for all admin content editors
 */
function initSummernoteEditor(selector, options = {}) {
    const defaultOptions = {
        height: 600,
        toolbar: [
            ['style', ['style', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p']],
            ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
            ['fontname', ['fontname']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'mediaLibrary', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        buttons: {
            mediaLibrary: function(context) {
                const ui = $.summernote.ui;
                const button = ui.button({
                    contents: '<i class="note-icon-picture"></i> Media',
                    tooltip: 'Insert from Media Library',
                    click: function () {
                        openMediaLibrary((url, altText) => {
                            context.invoke('editor.insertImage', url, altText || '');
                        });
                    }
                });
                return button.render();
            }
        },
        fontNames: ['Arial', 'Arial Black', 'Comic Sans MS', 'Courier New', 'Helvetica', 'Impact', 'Tahoma', 'Times New Roman', 'Verdana'],
        fontSizes: ['8', '9', '10', '11', '12', '14', '16', '18', '20', '22', '24', '36', '48'],
        placeholder: 'Start writing your content... Use Code View (< / >) to add HTML directly.',
        codeviewFilter: false, // Allow all HTML tags - no filtering
        codeviewIframeFilter: false, // Allow all HTML in code view
        callbacks: {
            onInit: function() {
                // Form validation
                const textarea = document.querySelector(selector);
                if (!textarea) return;
                
                const form = textarea.closest('form');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        const content = $(selector).summernote('code');
                        const textContent = content.replace(/<[^>]*>/g, '').trim();
                        
                        // Check if required validation is needed
                        const isRequired = textarea.hasAttribute('required');
                        
                        // Validate that editor has content
                        if (isRequired && (!textContent || textContent.length === 0)) {
                            e.preventDefault();
                            alert('Please enter some content.');
                            $(selector).summernote('focus');
                            return false;
                        }
                    });
                }
            }
        }
    };

    // Merge user options with defaults
    const finalOptions = { ...defaultOptions, ...options };
    
    // Initialize Summernote
    $(selector).summernote(finalOptions);
}

// Media Library Integration
function openMediaLibrary(callback) {
    window.mediaSelectCallback = callback;
    
    // Create modal
    const modal = document.createElement('div');
    modal.id = 'mediaLibraryModal';
    modal.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50';
    modal.innerHTML = `
        <div class="w-full max-w-6xl rounded-lg bg-white p-6 shadow-xl dark:bg-gray-800" style="max-height: 90vh; overflow-y: auto;">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">Select Media</h2>
                <button onclick="closeMediaLibrary()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div id="mediaLibraryContent" class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
                <div class="col-span-full text-center text-gray-500">Loading...</div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    
    // Load media
    fetch('/admin/media/all')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.media.length > 0) {
                const content = document.getElementById('mediaLibraryContent');
                content.innerHTML = data.media.map(media => {
                    const isImage = media.is_image;
                    const safeUrl = media.url.replace(/'/g, "\\'");
                    const safeAlt = (media.alt_text || '').replace(/'/g, "\\'");
                    return `
                        <div class="group relative cursor-pointer overflow-hidden rounded-lg border border-gray-200 bg-white transition-shadow hover:shadow-lg dark:border-gray-700 dark:bg-gray-800" onclick="selectMediaForEditor('${safeUrl}', '${safeAlt}')">
                            ${isImage ? 
                                `<img src="${safeUrl}" alt="${safeAlt}" class="h-32 w-full object-cover">` :
                                `<div class="flex h-32 w-full items-center justify-center bg-gray-100 dark:bg-gray-700">
                                    <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>`
                            }
                            <div class="p-2">
                                <p class="truncate text-xs font-medium text-gray-900 dark:text-gray-100">${media.original_name}</p>
                            </div>
                        </div>
                    `;
                }).join('');
            } else {
                document.getElementById('mediaLibraryContent').innerHTML = `
                    <div class="col-span-full text-center text-gray-500">
                        <p>No media found. Upload some files first!</p>
                        <a href="/admin/media" class="mt-2 inline-block rounded-md bg-primary px-4 py-2 text-sm text-white">Go to Media Library</a>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading media:', error);
            document.getElementById('mediaLibraryContent').innerHTML = `
                <div class="col-span-full text-center text-red-500">Error loading media</div>
            `;
        });
}

function closeMediaLibrary() {
    const modal = document.getElementById('mediaLibraryModal');
    if (modal) {
        modal.remove();
    }
    window.mediaSelectCallback = null;
}

function selectMediaForEditor(url, altText) {
    if (window.mediaSelectCallback) {
        window.mediaSelectCallback(url, altText);
    }
    closeMediaLibrary();
}

// Initialize all editors when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Wait for jQuery to be available
    if (typeof $ === 'undefined') {
        console.error('jQuery is required for Summernote editor');
        return;
    }

    // Check if we're on pages create/edit
    const pagesForm = document.querySelector('form[action*="pages"]');
    const pagesContent = document.getElementById('content');
    if (pagesForm && pagesContent) {
        initSummernoteEditor('#content', {
            height: 600,
            placeholder: 'Start writing your page content... Use Code View (< / >) to add HTML directly.'
        });
        return; // Exit early to avoid conflicts
    }

    // Check if we're on blog create/edit
    const blogForm = document.querySelector('form[action*="blog"]');
    const blogContent = document.getElementById('content');
    if (blogForm && blogContent) {
        initSummernoteEditor('#content', {
            height: 600,
            placeholder: 'Start writing your blog post content... Use Code View (< / >) to add HTML directly.'
        });
        return; // Exit early to avoid conflicts
    }

    // Check if we're on products create/edit
    const productsForm = document.querySelector('form[action*="products"]');
    const productDescription = document.getElementById('description');
    if (productsForm && productDescription) {
        initSummernoteEditor('#description', {
            height: 300,
            placeholder: 'Enter product description... Use Code View (< / >) to add HTML directly.'
        });
    }
});

