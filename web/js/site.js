document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('file-input');
    const form = document.getElementById('upload-form');
    const convertBtn = document.querySelector('button[type="submit"]');
    const loadingDiv = document.getElementById('loading');
    
    if (fileInput && form) {
        // Handle form submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const files = fileInput.files;
            if (files.length === 0) {
                showMessage('Пожалуйста, выберите файлы для конвертации.', 'error');
                return;
            }
            
            // Validate files
            for (let file of files) {
                if (!validateFileType(file)) {
                    showMessage(`Файл ${file.name} имеет неподдерживаемый формат. Поддерживаются только JPG/JPEG.`, 'error');
                    return;
                }
                if (!validateFileSize(file)) {
                    showMessage(`Файл ${file.name} превышает максимальный размер 10MB.`, 'error');
                    return;
                }
            }
            
            // Show loading state
            if (convertBtn) {
                convertBtn.disabled = true;
                convertBtn.textContent = 'Конвертация...';
            }
            if (loadingDiv) {
                loadingDiv.style.display = 'block';
            }
            
            // Prepare form data
            const formData = new FormData(form);
            
            // Send AJAX request
            fetch('/convert', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = data.downloadUrl;
                    a.download = data.filename;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    
                    showMessage('PDF успешно создан и загружен!', 'success');
                } else {
                    showMessage(data.error || 'Произошла ошибка при конвертации.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('Произошла ошибка при конвертации.', 'error');
            })
            .finally(() => {
                resetForm();
            });
        });
        
        // Handle file input change
        fileInput.addEventListener('change', function() {
            const files = this.files;
            if (files.length > 0 && convertBtn) {
                convertBtn.disabled = false;
            }
        });
        fileInput.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.closest('.form-group').classList.add('dragover');
        });
        
        fileInput.addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.closest('.form-group').classList.remove('dragover');
        });
        
        fileInput.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.closest('.form-group').classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            this.files = files;
            
            const event = new Event('change', { bubbles: true });
            this.dispatchEvent(event);
        });
    }
    
    function validateFileSize(file) {
        const maxSize = 10 * 1024 * 1024; // 10MB
        return file.size <= maxSize;
    }
    
    function validateFileType(file) {
        const allowedTypes = ['image/jpeg', 'image/jpg'];
        return allowedTypes.includes(file.type);
    }
    
    function showMessage(message, type = 'error') {
        const alertDiv = document.createElement('div');
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        alertDiv.className = `alert ${alertClass} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const container = document.querySelector('.card-body');
        const existingAlert = container.querySelector('.alert');
        if (existingAlert) {
            existingAlert.remove();
        }
        
        container.insertBefore(alertDiv, container.firstChild);
    }
    
    function resetForm() {
        const convertBtn = document.querySelector('button[type="submit"]');
        const loadingDiv = document.getElementById('loading');
        
        if (convertBtn) {
            convertBtn.disabled = false;
            convertBtn.textContent = 'Конвертировать в PDF';
        }
        if (loadingDiv) {
            loadingDiv.style.display = 'none';
        }
    }
});