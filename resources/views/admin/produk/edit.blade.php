@extends('layouts.admin')

@section('content')
<div class="form-container" id="formContainer">
    <div class="form-header">
        <div class="header-content">
            <div class="header-info">
                <h1 class="page-title">
                    <span class="title-icon">✏️</span>
                    Edit Product
                </h1>
                <p class="page-subtitle">Update product information and settings</p>
            </div>
            <div class="breadcrumb-nav">
                <span class="breadcrumb-item">Products</span>
                <span class="breadcrumb-separator">→</span>
                <span class="breadcrumb-item active">Edit</span>
            </div>
        </div>
    </div>

    <div class="form-wrapper">
        <div class="form-card">
            <div class="form-progress">
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 100%"></div>
                </div>
                <div class="progress-text">Complete the form below</div>
            </div>

            <form action="{{ route('admin.produk.update', $produk->id) }}" method="POST" enctype="multipart/form-data"
                id="productForm">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="nama" class="form-label">
                        <span class="label-icon">📦</span>
                        Product Name
                    </label>
                    <div class="input-wrapper focused">
                        <input type="text" name="nama" id="nama" class="form-input @error('nama') error @enderror"
                            value="{{ old('nama', $produk->nama) }}"
                            placeholder="Enter product name (e.g., Premium Coffee Beans)" required>
                        <div class="input-icon">📝</div>
                    </div>
                    @error('nama')
                    <div class="error-message">
                        <span class="error-icon">⚠️</span>
                        {{ $message }}
                    </div>
                    @enderror
                    <div class="field-hint">Choose a clear, descriptive name for your product</div>
                </div>

                <div class="form-group">
                    <label for="kategori_id" class="form-label">
                        <span class="label-icon">🏷️</span>
                        Category
                    </label>
                    <div class="select-wrapper focused">
                        <select name="kategori_id" id="kategori_id"
                            class="form-select @error('kategori_id') error @enderror" required>
                            <option value="">Select a category</option>
                            @foreach ($kategoris as $kategori)
                            <option value="{{ $kategori->id }}" {{ old('kategori_id', $produk->kategori_id) ==
                                $kategori->id ? 'selected' : '' }}>
                                {{ $kategori->nama }}
                            </option>
                            @endforeach
                        </select>
                        <div class="select-icon">🔽</div>
                    </div>
                    @error('kategori_id')
                    <div class="error-message">
                        <span class="error-icon">⚠️</span>
                        {{ $message }}
                    </div>
                    @enderror
                    <div class="field-hint">Select the most appropriate category for this product</div>
                </div>

                <div class="form-group">
                    <label for="harga" class="form-label">
                        <span class="label-icon">💰</span>
                        Price
                    </label>
                    <div class="input-wrapper price-wrapper focused">
                        <div class="price-currency">Rp</div>
                        <input type="number" name="harga" id="harga"
                            class="form-input price-input @error('harga') error @enderror"
                            value="{{ old('harga', $produk->harga) }}" placeholder="0" min="0" step="1000" required>
                        <div class="input-icon">💸</div>
                    </div>
                    @error('harga')
                    <div class="error-message">
                        <span class="error-icon">⚠️</span>
                        {{ $message }}
                    </div>
                    @enderror
                    <div class="field-hint">Set the selling price for this product</div>
                </div>

                <div class="form-group">
                    <label for="stok" class="form-label">
                        <span class="label-icon">📦</span>
                        Stock
                    </label>
                    <div class="input-wrapper stock-wrapper focused">
                        <input type="number" name="stok" id="stok"
                            class="form-input stock-input @error('stok') error @enderror"
                            value="{{ old('stok', $produk->stok) }}" placeholder="Enter stock quantity" min="0"
                            required>
                        <div class="input-icon">🔢</div>
                    </div>
                    @error('stok')
                    <div class="error-message">
                        <span class="error-icon">⚠️</span>
                        {{ $message }}
                    </div>
                    @enderror
                    <div class="field-hint">Enter the available quantity of this product</div>
                </div>
                
                {{-- ⭐ PERBAIKAN DAN PENAMBAHAN FIELD DESKRIPSI ⭐ --}}
                <div class="form-group">
                    <label for="deskripsi" class="form-label">
                        <span class="label-icon">📖</span>
                        Product Description
                    </label>
                    <textarea name="deskripsi" id="deskripsi" class="form-textarea @error('deskripsi') error @enderror"
                        rows="6"
                        placeholder="Enter a detailed description of the product, including features, benefits, and specifications...">{{ old('deskripsi', $produk->deskripsi) }}</textarea>

                    {{-- PASTIKAN TAG ERROR BERADA DI LUAR TEXTAREA --}}
                    @error('deskripsi')
                    <div class="error-message">
                        <span class="error-icon">⚠️</span>
                        {{ $message }}
                    </div>
                    @enderror
                    <div class="field-hint">Provide an engaging and comprehensive description for your customers.</div>
                </div>
                {{-- AKHIR FIELD DESKRIPSI --}}


                @if($produk->foto)
                <div class="form-group">
                    <label class="form-label">
                        <span class="label-icon">🖼️</span>
                        Current Image
                    </label>
                    <div class="current-image-wrapper">
                        <img src="{{ asset('storage/' . $produk->foto) }}" alt="Current Product Image"
                            class="current-image">
                        <div class="current-image-overlay">
                            <span class="current-image-label">Current Image</span>
                        </div>
                    </div>
                </div>
                @endif

                <div class="form-group">
                    <label for="foto" class="form-label">
                        <span class="label-icon">📷</span>
                        Update Image (Optional)
                    </label>

                    <div class="file-upload-wrapper">
                        <div class="file-drop-zone" id="dropZone">
                            <div class="file-upload-content">
                                <div class="upload-icon">📁</div>
                                <h4>Drop your new image here</h4>
                                <p>or <span class="upload-link">browse files</span></p>
                                <div class="upload-requirements">
                                    JPG, PNG, GIF up to 5MB
                                </div>
                            </div>
                            <input type="file" name="foto" id="foto"
                                class="file-input @error('foto') error @enderror" accept="image/*">
                        </div>

                        <div class="image-preview" id="imagePreview" style="display: none;">
                            <img id="previewImg" src="" alt="Preview">
                            <div class="preview-overlay">
                                <button type="button" class="remove-image" id="removeImage">
                                    <span>🗑️</span>
                                </button>
                            </div>
                            <div class="image-info">
                                <span id="fileName"></span>
                                <span id="fileSize"></span>
                            </div>
                        </div>
                    </div>

                    @error('foto')
                    <div class="error-message">
                        <span class="error-icon">⚠️</span>
                        {{ $message }}
                    </div>
                    @enderror
                    <div class="field-hint">Leave empty to keep current image, or upload a new one to replace it
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.produk.index') }}" class="btn btn-secondary">
                        <span class="btn-icon">←</span>
                        Back to Products
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span class="btn-icon">💾</span>
                        <span class="btn-text">Update Product</span>
                        <div class="btn-loader" style="display: none;">
                            <div class="spinner"></div>
                        </div>
                    </button>
                </div>
            </form>
        </div>

        <div class="help-card">
            <h4>💡 Tips for Editing Products</h4>
            <ul>
                <li><strong>Keep Consistency:</strong> Maintain naming conventions</li>
                <li><strong>Update Prices:</strong> Ensure prices reflect current market</li>
                <li><strong>Category Check:</strong> Verify product is in correct category</li>
                <li><strong>Image Quality:</strong> Use high-resolution, well-lit photos</li>
                <li><strong>Save Changes:</strong> Don't forget to save your updates</li>
            </ul>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('productForm');
        const nameInput = document.getElementById('nama');
        const categorySelect = document.getElementById('kategori_id');
        const priceInput = document.getElementById('harga');
        const descriptionInput = document.getElementById('deskripsi'); // Ambil element deskripsi
        const fileInput = document.getElementById('foto');
        const dropZone = document.getElementById('dropZone');
        const imagePreview = document.getElementById('imagePreview');
        const previewImg = document.getElementById('previewImg');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const removeImageBtn = document.getElementById('removeImage');
        const progressFill = document.querySelector('.progress-fill');
        const submitBtn = document.getElementById('submitBtn');
        const formContainer = document.getElementById('formContainer');

        // Sync with navbar theme
        const syncTheme = () => {
            const isDark = document.documentElement.classList.contains('dark');
            formContainer.setAttribute('data-theme', isDark ? 'dark' : 'light');
        };

        // Initial sync
        syncTheme();

        // Listen for theme changes from navbar
        const observer = new MutationObserver(syncTheme);
        observer.observe(document.documentElement, { 
            attributes: true, 
            attributeFilter: ['class'] 
        });

        // Progress tracking - start at 100% since we're editing
        function updateProgress() {
            let progress = 100; // Always 100% for edit form since fields are populated
            progressFill.style.width = progress + '%';
            progressFill.style.background = 'linear-gradient(90deg, #10b981, #059669)';
        }

        // Price formatting
        priceInput.addEventListener('input', function() {
            updateProgress();
        });

        // Format price display
        priceInput.addEventListener('blur', function() {
            if (this.value) {
                const value = parseFloat(this.value);
                if (!isNaN(value)) {
                    this.value = Math.round(value);
                }
            }
        });

        nameInput.addEventListener('input', updateProgress);
        categorySelect.addEventListener('change', updateProgress);
        descriptionInput.addEventListener('input', updateProgress); // Tambahkan listener untuk deskripsi

        // File upload handling
        dropZone.addEventListener('click', () => fileInput.click());

        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('drag-over');
        });

        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
        });

        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleFileSelect(files[0]);
            }
        });

        fileInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                handleFileSelect(e.target.files[0]);
            }
        });

        function handleFileSelect(file) {
            if (!file.type.startsWith('image/')) {
                alert('Please select an image file');
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                alert('File size must be less than 5MB');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);

                dropZone.style.display = 'none';
                imagePreview.style.display = 'block';
                updateProgress();
            };
            reader.readAsDataURL(file);
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        removeImageBtn.addEventListener('click', function() {
            fileInput.value = '';
            dropZone.style.display = 'block';
            imagePreview.style.display = 'none';
            updateProgress();
        });

        // Form submission
        form.addEventListener('submit', function(e) {
            const btnText = submitBtn.querySelector('.btn-text');
            const btnLoader = submitBtn.querySelector('.btn-loader');

            btnText.style.display = 'none';
            btnLoader.style.display = 'inline-block';
            submitBtn.disabled = true;
        });

        // Input animations
        const inputs = document.querySelectorAll('.form-input, .form-select, .form-textarea'); // Termasuk textarea
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                // Check if parent has input-wrapper class, as form-textarea doesn't
                let targetElement = this.classList.contains('form-textarea') ? this : this.parentElement;
                targetElement.classList.add('focused');
            });

            input.addEventListener('blur', function() {
                let targetElement = this.classList.contains('form-textarea') ? this : this.parentElement;
                if (!this.value) {
                    targetElement.classList.remove('focused');
                }
            });

            // Check if input has value on load
            let targetElement = input.classList.contains('form-textarea') ? input : input.parentElement;
            if (input.value) {
                targetElement.classList.add('focused');
            }
        });

        // Initialize progress
        updateProgress();
    });
</script>
@endpush

<style>
    {{-- CSS tidak diubah, karena sudah ada style untuk .form-textarea --}}

    /* Root Variables for Theming */
    :root {
        --bg-gradient-start: #18181b;
        --bg-gradient-end: #27272a;
        --card-bg: rgba(255, 255, 255, 0.95);
        --header-bg: rgba(71, 71, 71, 0.95);
        --text-primary: #374151;
        --text-secondary: #6b7280;
        --text-hint: #9ca3af;
        --border-color: #e5e7eb;
        --input-bg: white;
        --breadcrumb-bg: rgba(255, 255, 255, 0.7);
        --breadcrumb-text: #64748b;
        --subtitle-text: #c7c7c8;
    }

    .form-container[data-theme="light"] {
        --bg-gradient-start: #f0f4ff;
        --bg-gradient-end: #e0e7ff;
        --card-bg: rgba(255, 255, 255, 0.98);
        --header-bg: rgba(255, 255, 255, 0.95);
        --text-primary: #1e293b;
        --text-secondary: #475569;
        --text-hint: #64748b;
        --border-color: #cbd5e1;
        --input-bg: #ffffff;
        --breadcrumb-bg: rgba(99, 102, 241, 0.1);
        --breadcrumb-text: #475569;
        --subtitle-text: #64748b;
    }

    /* Pastikan ada style untuk form-textarea agar terlihat bagus */
    .form-textarea {
        width: 100%;
        padding: 1rem;
        border: 2px solid var(--border-color);
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: var(--input-bg);
        color: var(--text-primary);
        resize: vertical;
    }
    
    .form-textarea:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-textarea.error {
        border-color: #ef4444;
    }

    .form-textarea.focused {
        border-color: #667eea;
    }


    .form-container {
        background: linear-gradient(135deg, var(--bg-gradient-start) 0%, var(--bg-gradient-end) 100%);
        min-height: 100vh;
        padding: 2rem;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        transition: background 0.5s ease;
    }

    /* Header Section */
    .form-header {
        background: var(--header-bg);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: background 0.3s ease;
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .page-title {
        font-size: 2.5rem;
        font-weight: 800;
        background: linear-gradient(90deg, #667eea, #764ba2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .form-container[data-theme="dark"] .page-title {
        background: linear-gradient(90deg, #fff, #ddd);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .title-icon {
        font-size: 2rem;
    }

    .page-subtitle {
        color: var(--subtitle-text);
        font-size: 1.1rem;
        margin: 0.5rem 0 0 0;
        transition: color 0.3s ease;
    }

    .breadcrumb-nav {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
    }

    .breadcrumb-item {
        color: var(--breadcrumb-text);
        padding: 0.5rem 1rem;
        background: var(--breadcrumb-bg);
        border-radius: 20px;
        transition: all 0.3s ease;
    }

    .breadcrumb-item.active {
        background: #667eea;
        color: white;
    }

    .breadcrumb-separator {
        color: #94a3b8;
    }

    /* Form Wrapper */
    .form-wrapper {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
        max-width: 1200px;
        margin: 0 auto;
    }

    .form-card {
        background: var(--card-bg);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: background 0.3s ease;
    }

    .form-progress {
        margin-bottom: 2rem;
        text-align: center;
    }

    .progress-bar {
        width: 100%;
        height: 6px;
        background: rgba(0, 0, 0, 0.1);
        border-radius: 3px;
        overflow: hidden;
        margin-bottom: 0.5rem;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #10b981, #059669);
        border-radius: 3px;
        transition: width 0.3s ease;
    }

    .progress-text {
        font-size: 0.875rem;
        color: var(--text-secondary);
        font-weight: 500;
        transition: color 0.3s ease;
    }

    /* Form Groups */
    .form-group {
        margin-bottom: 2rem;
    }

    .form-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.75rem;
        font-size: 1rem;
        transition: color 0.3s ease;
    }

    .label-icon {
        font-size: 1.1rem;
    }

    /* Input Styles */
    .input-wrapper {
        position: relative;
    }

    .form-input {
        width: 100%;
        padding: 1rem 3rem 1rem 1rem;
        border: 2px solid var(--border-color);
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: var(--input-bg);
        color: var(--text-primary);
    }

    .form-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-input.error {
        border-color: #ef4444;
    }

    .input-icon {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-hint);
        font-size: 1.1rem;
        transition: color 0.3s ease;
    }

    .input-wrapper.focused .input-icon {
        color: #667eea;
    }

    /* Price Input Styles */
    .price-wrapper {
        display: flex;
        align-items: center;
    }

    .price-currency {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
        font-weight: 600;
        z-index: 10;
    }

    .price-input {
        padding-left: 3rem !important;
    }

    /* Select Styles */
    .select-wrapper {
        position: relative;
    }

    .form-select {
        width: 100%;
        padding: 1rem 3rem 1rem 1rem;
        border: 2px solid var(--border-color);
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: var(--input-bg);
        color: var(--text-primary);
        appearance: none;
    }

    .form-select:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-select.error {
        border-color: #ef4444;
    }

    .select-icon {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-hint);
        font-size: 0.875rem;
        pointer-events: none;
        transition: color 0.3s ease;
    }

    .select-wrapper.focused .select-icon {
        color: #667eea;
    }

    /* Current Image Styles */
    .current-image-wrapper {
        position: relative;
        display: inline-block;
        border-radius: 12px;
        overflow: hidden;
        border: 2px solid #e5e7eb;
        margin-bottom: 1rem;
    }

    .current-image {
        width: 200px;
        height: 150px;
        object-fit: cover;
        display: block;
    }

    .current-image-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 0.5rem;
        text-align: center;
    }

    .current-image-label {
        font-size: 0.875rem;
        font-weight: 500;
    }

    /* File Upload Styles */
    .file-upload-wrapper {
        position: relative;
    }

    .file-drop-zone {
        border: 2px dashed #d1d5db;
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background: #fafafa;
    }

    .file-drop-zone:hover,
    .file-drop-zone.drag-over {
        border-color: #667eea;
        background: rgba(102, 126, 234, 0.05);
    }

    .file-upload-content h4 {
        margin: 0.5rem 0;
        color: #374151;
        font-weight: 600;
    }

    .file-upload-content p {
        margin: 0.5rem 0;
        color: #6b7280;
    }

    .upload-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
    }

    .upload-link {
        color: #667eea;
        font-weight: 600;
        cursor: pointer;
    }

    .upload-requirements {
        font-size: 0.75rem;
        color: #9ca3af;
        margin-top: 0.5rem;
    }

    .file-input {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
    }

    /* Image Preview */
    .image-preview {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        border: 2px solid #e5e7eb;
    }

    .image-preview img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .preview-overlay {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
    }

    .remove-image {
        background: rgba(0, 0, 0, 0.7);
        border: none;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        color: white;
        cursor: pointer;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .remove-image:hover {
        background: #ef4444;
    }

    .image-info {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 0.75rem;
        display: flex;
        justify-content: space-between;
        font-size: 0.875rem;
    }

    /* Error Messages */
    .error-message {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 0.5rem;
    }

    .error-icon {
        font-size: 1rem;
    }

    /* Field Hints */
    .field-hint {
        font-size: 0.875rem;
        color: var(--text-secondary);
        margin-top: 0.5rem;
        transition: color 0.3s ease;
    }

    /* Form Actions */
    .form-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid var(--border-color);
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        position: relative;
    }

    .btn-secondary {
        background: #f3f4f6;
        color: #374151;
    }

    .form-container[data-theme="light"] .btn-secondary {
        background: #e0e7ff;
        color: #4c1d95;
    }

    .btn-secondary:hover {
        background: #e5e7eb;
        transform: translateY(-2px);
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        min-width: 150px;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #5a67d8, #6b46c1);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    }

    .btn-primary:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
    }

    .btn-icon {
        font-size: 1rem;
    }

    .spinner {
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-top: 2px solid white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* Help Card */
    .help-card {
        background: var(--card-bg);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        height: fit-content;
        transition: background 0.3s ease;
    }

    .help-card h4 {
        color: var(--text-primary);
        margin-bottom: 1rem;
        font-size: 1.1rem;
        transition: color 0.3s ease;
    }

    .help-card ul {
        margin: 0;
        padding-left: 1rem;
    }

    .help-card li {
        color: var(--text-secondary);
        margin-bottom: 0.75rem;
        line-height: 1.5;
        transition: color 0.3s ease;
    }

    .help-card strong {
        color: var(--text-primary);
        transition: color 0.3s ease;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .form-container {
            padding: 1rem;
        }

        .header-content {
            flex-direction: column;
            gap: 1rem;
            text-align: center;
        }

        .page-title {
            font-size: 2rem;
        }

        .form-wrapper {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }

        .current-image {
            width: 150px;
            height: 112px;
        }
    }

    /* Animations */
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .form-card,
    .help-card {
        animation: slideInUp 0.5s ease-out;
    }

    .form-group {
        animation: slideInUp 0.6s ease-out;
        animation-fill-mode: both;
    }

    .form-group:nth-child(1) {
        animation-delay: 0.1s;
    }

    .form-group:nth-child(2) {
        animation-delay: 0.2s;
    }

    .form-group:nth-child(3) {
        animation-delay: 0.3s;
    }

    .form-group:nth-child(4) {
        animation-delay: 0.4s;
    }

    .form-group:nth-child(5) {
        animation-delay: 0.5s;
    }
</style>
@endsection