document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('file-input');
    const form = document.getElementById('upload-form');
    
    if (fileInput && form) {
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
    
    function showError(message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger alert-dismissible fade show';
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
});