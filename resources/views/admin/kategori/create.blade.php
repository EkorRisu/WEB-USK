
@extends('layouts.admin')

@section('content')
<div class="form-container" id="formContainer">
    <div class="form-header">
        <div class="header-content">
            <div class="header-info">
                <h1 class="page-title">
                    <span class="title-icon">📚</span>
                    Add New Category
                </h1>
                <p class="page-subtitle">Create a new book category to organize your inventory</p>
            </div>
            <div class="breadcrumb-nav">
                <span class="breadcrumb-item">Categories</span>
                <span class="breadcrumb-separator">→</span>
                <span class="breadcrumb-item active">Add New</span>
            </div>
        </div>
    </div>

    <div class="form-wrapper">
        <div class="form-card">
            <div class="form-progress">
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 0%"></div>
                </div>
                <div class="progress-text">Complete the form below</div>
            </div>

            <form action="{{ route('admin.kategori.store') }}" method="POST" id="categoryForm">
                @csrf

                <div class="form-group">
                    <label for="nama" class="form-label">
                        <span class="label-icon">🏷️</span>
                        Category Name
                    </label>
                    <div class="input-wrapper">
                        <input type="text" name="nama" id="nama" class="form-input @error('nama') error @enderror"
                            placeholder="Enter category name (e.g., Fiction, Science, History)" required>
                        <div class="input-icon">📝</div>
                    </div>
                    @error('nama')
                    <div class="error-message">
                        <span class="error-icon">⚠️</span>
                        {{ $message }}
                    </div>
                    @enderror
                    <div class="field-hint">Choose a clear, descriptive name for your category</div>
                </div>

                <div class="form-group">
                    <label for="deskripsi" class="form-label">
                        <span class="label-icon">📄</span>
                        Description
                    </label>
                    <div class="textarea-wrapper">
                        <textarea name="deskripsi" id="deskripsi" rows="4"
                            class="form-textarea @error('deskripsi') error @enderror"
                            placeholder="Describe what types of books belong to this category..."></textarea>
                        <div class="textarea-counter">
                            <span id="charCount">0</span>/500 characters
                        </div>
                    </div>
                    @error('deskripsi')
                    <div class="error-message">
                        <span class="error-icon">⚠️</span>
                        {{ $message }}
                    </div>
                    @enderror
                    <div class="field-hint">Provide a brief description to help users understand this category</div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.kategori.index') }}" class="btn btn-secondary">
                        <span class="btn-icon">←</span>
                        Back to Categories
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span class="btn-icon">💾</span>
                        <span class="btn-text">Save Category</span>
                        <div class="btn-loader" style="display: none;">
                            <div class="spinner"></div>
                        </div>
                    </button>
                </div>
            </form>
        </div>

        <div class="help-card">
            <h4>💡 Tips for Creating Categories</h4>
            <ul>
                <li><strong>Be Specific:</strong> Use clear, descriptive names</li>
                <li><strong>Think Ahead:</strong> Consider how users will search</li>
                <li><strong>Good Descriptions:</strong> Help users understand the category scope</li>
            </ul>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('categoryForm');
                const nameInput = document.getElementById('nama');
                const descriptionInput = document.getElementById('deskripsi');
                const charCount = document.getElementById('charCount');
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

                // Progress tracking
                function updateProgress() {
                    let progress = 0;
                    if (nameInput.value.trim()) progress += 50;
                    if (descriptionInput.value.trim()) progress += 50;

                    progressFill.style.width = progress + '%';

                    if (progress === 100) {
                        progressFill.style.background = 'linear-gradient(90deg, #10b981, #059669)';
                    } else {
                        progressFill.style.background = 'linear-gradient(90deg, #667eea, #764ba2)';
                    }
                }

                // Character counter
                descriptionInput.addEventListener('input', function() {
                    const count = this.value.length;
                    charCount.textContent = count;

                    if (count > 500) {
                        charCount.style.color = '#ef4444';
                    } else if (count > 400) {
                        charCount.style.color = '#f59e0b';
                    } else {
                        charCount.style.color = '#6b7280';
                    }

                    updateProgress();
                });

                nameInput.addEventListener('input', updateProgress);

                // Form submission
                form.addEventListener('submit', function(e) {
                    const btnText = submitBtn.querySelector('.btn-text');
                    const btnLoader = submitBtn.querySelector('.btn-loader');

                    btnText.style.display = 'none';
                    btnLoader.style.display = 'inline-block';
                    submitBtn.disabled = true;
                });

                // Input animations
                const inputs = document.querySelectorAll('.form-input, .form-textarea');
                inputs.forEach(input => {
                    input.addEventListener('focus', function() {
                        this.parentElement.classList.add('focused');
                    });

                    input.addEventListener('blur', function() {
                        if (!this.value) {
                            this.parentElement.classList.remove('focused');
                        }
                    });

                    // Check if input has value on load
                    if (input.value) {
                        input.parentElement.classList.add('focused');
                    }
                });
            });
</script>
@endpush

<style>
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

    .form-container {
        background: linear-gradient(135deg, var(--bg-gradient-start) 0%, var(--bg-gradient-end) 100%);
        min-height: 100vh;
        padding: 2rem;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        transition: background 0.5s ease;
        position: relative;
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

    .form-container[data-theme="light"] .progress-bar {
        background: rgba(0, 0, 0, 0.05);
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #667eea, #764ba2);
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

    /* Textarea Styles */
    .textarea-wrapper {
        position: relative;
    }

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
        min-height: 120px;
    }

    .form-textarea:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-textarea.error {
        border-color: #ef4444;
    }

    .textarea-counter {
        position: absolute;
        bottom: 0.75rem;
        right: 1rem;
        font-size: 0.75rem;
        color: var(--text-hint);
        background: var(--input-bg);
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        transition: all 0.3s ease;
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
</style>
@endsection