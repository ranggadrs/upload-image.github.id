<!DOCTYPE html><html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Pixelite - Hosting Gambar Minimalis</title><link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"><style>
        .drop-zone {
            border: 2px dashed #cbd5e0;
            transition: all 0.3s ease;
        }
        .drop-zone:hover, .drop-zone.drag-over {
            border-color: #4299e1;
            background-color: rgba(237, 242, 247, 0.5);
        }
        .upload-icon {
            font-size: 48px;
            color: #cbd5e0;
            transition: color 0.3s ease;
        }
        .drop-zone:hover .upload-icon, .drop-zone.drag-over .upload-icon {
            color: #4299e1;
        }
        .img-preview {
            max-height: 300px;
            object-fit: contain;
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style></head><body class="bg-gray-50 min-h-screen"><div class="container mx-auto px-4 py-8"><header class="text-center mb-8"><h1 class="text-3xl font-bold text-gray-800 mb-2">Pixelite</h1><p class="text-gray-600">Hosting Gambar Minimalis</p></header><div class="max-w-xl mx-auto bg-white rounded-lg shadow-md p-6"><div id="upload-section" class="block"><h2 class="text-xl font-semibold text-gray-700 mb-4">Upload Gambar</h2><form id="upload-form" action="upload.php" method="post" enctype="multipart/form-data"><div id="drop-zone" class="drop-zone rounded-lg p-8 text-center cursor-pointer mb-4"><i class="fas fa-cloud-upload-alt upload-icon mb-3"></i><p class="text-gray-600 mb-2">Seret gambar ke sini atau klik untuk memilih</p><p class="text-gray-500 text-sm">Format: JPG, JPEG, PNG, GIF (Max: 5MB)</p><input type="file" id="file-input" name="image" class="hidden" accept="image/jpeg,image/jpg,image/png,image/gif"></div><div class="mb-4"><button type="submit" id="upload-btn" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md transition duration-300 ease-in-out flex items-center justify-center"><i class="fas fa-upload mr-2"></i>Upload</button></div></form><div id="upload-progress" class="hidden"><div class="w-full bg-gray-200 rounded-full h-2.5 mb-4"><div class="bg-blue-500 h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div></div><p class="text-center text-sm text-gray-600">Mengupload gambar...</p></div></div><div id="result-section" class="hidden fade-in"><div class="flex items-center justify-between mb-4"><h2 class="text-xl font-semibold text-gray-700">Berhasil Diupload</h2><button id="upload-new" class="text-blue-500 hover:text-blue-700 transition duration-300"><i class="fas fa-plus-circle mr-1"></i>Upload Baru</button></div><div class="mb-5 text-center"><img id="preview-image" src="" alt="Gambar terupload" class="img-preview mx-auto mb-4 rounded-md shadow"></div><div class="mb-4"><label class="block text-gray-700 text-sm font-medium mb-2">Link Gambar:</label><div class="flex"><input id="image-url" type="text" readonly class="flex-grow px-3 py-2 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-700 focus:outline-none" value=""><button id="copy-btn" class="bg-blue-500 hover:bg-blue-600 text-white px-4 rounded-r-md transition duration-300 flex items-center"><i class="far fa-copy mr-2"></i>Salin</button></div></div><div class="text-sm text-gray-600 mb-1"><p>Gunakan pada HTML:</p></div><div class="mb-4"><div class="flex"><input id="image-html" type="text" readonly class="flex-grow px-3 py-2 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-700 text-sm focus:outline-none" value=""><button id="copy-html-btn" class="bg-blue-500 hover:bg-blue-600 text-white px-4 rounded-r-md transition duration-300 flex items-center"><i class="far fa-copy mr-2"></i>Salin</button></div></div><div id="copy-success" class="hidden text-center py-2 mb-3 bg-green-100 text-green-600 rounded-md"><i class="fas fa-check-circle mr-1"></i>Link berhasil disalin!</div></div><div id="error-message" class="hidden text-center py-3 bg-red-100 text-red-600 rounded-md"><i class="fas fa-exclamation-circle mr-1"></i><span id="error-text">Error message here</span></div></div><footer class="text-center mt-8 text-gray-500 text-sm"><p>&copy; 2025 Pixelite</p></footer></div><script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropZone = document.getElementById('drop-zone');
            const fileInput = document.getElementById('file-input');
            const uploadForm = document.getElementById('upload-form');
            const uploadSection = document.getElementById('upload-section');
            const resultSection = document.getElementById('result-section');
            const uploadProgress = document.getElementById('upload-progress');
            const progressBar = uploadProgress.querySelector('div > div');
            const previewImage = document.getElementById('preview-image');
            const imageUrl = document.getElementById('image-url');
            const imageHtml = document.getElementById('image-html');
            const copyBtn = document.getElementById('copy-btn');
            const copyHtmlBtn = document.getElementById('copy-html-btn');
            const copySuccess = document.getElementById('copy-success');
            const errorMessage = document.getElementById('error-message');
            const errorText = document.getElementById('error-text');
            const uploadNew = document.getElementById('upload-new');
            
            dropZone.addEventListener('click', () => {
                fileInput.click();
            });
            
            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    validateFile(this.files[0]);
                }
            });
            
            ['dragover', 'dragenter'].forEach(eventName => {
                dropZone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    dropZone.classList.add('drag-over');
                });
            });
            
            ['dragleave', 'dragend', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    dropZone.classList.remove('drag-over');
                });
            });
            
            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                    fileInput.files = e.dataTransfer.files;
                    validateFile(e.dataTransfer.files[0]);
                }
            });
            
            function validateFile(file) {
                hideError();
                
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    showError('File harus berformat JPG, JPEG, PNG, atau GIF');
                    fileInput.value = '';
                    return false;
                }
                
                // Check file size (5MB = 5 * 1024 * 1024)
                if (file.size > 5 * 1024 * 1024) {
                    showError('Ukuran file maksimal 5MB');
                    fileInput.value = '';
                    return false;
                }
                
                return true;
            }
            
            // Form submission
            uploadForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (!fileInput.files || !fileInput.files[0]) {
                    showError('Silakan pilih gambar terlebih dahulu');
                    return;
                }
                
                if (!validateFile(fileInput.files[0])) {
                    return;
                }
                
                // Show progress
                uploadSection.classList.add('hidden');
                uploadProgress.classList.remove('hidden');
                
                // Create FormData for AJAX upload
                const formData = new FormData();
                formData.append('image', fileInput.files[0]);
                
                // Simulate upload progress (in a real app, you'd track actual progress)
                let progress = 0;
                const progressInterval = setInterval(() => {
                    progress += Math.random() * 15;
                    if (progress >= 100) {
                        progress = 100;
                        clearInterval(progressInterval);
                    }
                    progressBar.style.width = `${progress}%`;
                }, 200);
                
                // AJAX request to upload the file
                fetch('upload.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    clearInterval(progressInterval);
                    progressBar.style.width = '100%';
                    
                    setTimeout(() => {
                        uploadProgress.classList.add('hidden');
                        
                        if (data.success) {
                            // Show result
                            previewImage.src = data.file_url;
                            imageUrl.value = data.file_url;
                            imageHtml.value = `<img src="${data.file_url}" alt="Uploaded Image">`;
                            resultSection.classList.remove('hidden');
                        } else {
                            // Show error
                            uploadSection.classList.remove('hidden');
                            showError(data.message || 'Terjadi kesalahan saat upload');
                        }
                    }, 500);
                })
                .catch(error => {
                    clearInterval(progressInterval);
                    uploadProgress.classList.add('hidden');
                    uploadSection.classList.remove('hidden');
                    showError('Terjadi kesalahan saat upload');
                    console.error('Error:', error);
                });
            });
            
            // Copy image URL to clipboard
            copyBtn.addEventListener('click', () => {
                copyToClipboard(imageUrl.value);
            });
            
            // Copy HTML code to clipboard
            copyHtmlBtn.addEventListener('click', () => {
                copyToClipboard(imageHtml.value);
            });
            
            // Copy function
            function copyToClipboard(text) {
                navigator.clipboard.writeText(text).then(() => {
                    // Show success message
                    copySuccess.classList.remove('hidden');
                    
                    // Hide after 2 seconds
                    setTimeout(() => {
                        copySuccess.classList.add('hidden');
                    }, 2000);
                }).catch(err => {
                    console.error('Failed to copy: ', err);
                });
            }
            
            // Upload new image
            uploadNew.addEventListener('click', () => {
                resultSection.classList.add('hidden');
                uploadSection.classList.remove('hidden');
                fileInput.value = '';
            });
            
            // Show error message
            function showError(message) {
                errorText.textContent = message;
                errorMessage.classList.remove('hidden');
                
                // Hide after 5 seconds
                setTimeout(() => {
                    hideError();
                }, 5000);
            }
            
            // Hide error message
            function hideError() {
                errorMessage.classList.add('hidden');
            }
        });
    </script></body></html>