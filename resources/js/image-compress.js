/**
 * Compress an image file client-side using Canvas API.
 * Target: output under 1MB.
 *
 * @param {File} file - The original image file
 * @param {Object} opts
 * @param {number} opts.targetSizeBytes - Target max size in bytes (default 1MB)
 * @param {number} opts.maxWidth - Max width in pixels before resize (default 1920)
 * @param {number} opts.maxHeight - Max height in pixels before resize (default 1920)
 * @param {number} opts.initialQuality - Starting JPEG quality (default 0.8)
 * @param {number} opts.minQuality - Minimum JPEG quality before giving up (default 0.3)
 * @returns {Promise<File>} - Compressed file or original if already small enough
 */
export function compressImage(file, opts = {}) {
    const targetSize = opts.targetSizeBytes || 1024 * 1024;
    const maxWidth = opts.maxWidth || 1920;
    const maxHeight = opts.maxHeight || 1920;
    const initialQuality = opts.initialQuality || 0.8;
    const minQuality = opts.minQuality || 0.3;

    return new Promise((resolve, reject) => {
        if (!file || !file.type.startsWith('image/')) {
            return reject(new Error('File is not an image'));
        }

        if (file.size <= targetSize) {
            return resolve(file);
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            const img = new Image();
            img.onload = () => {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');

                let { width, height } = img;

                if (width > maxWidth || height > maxHeight) {
                    const ratio = Math.min(maxWidth / width, maxHeight / height);
                    width = Math.round(width * ratio);
                    height = Math.round(height * ratio);
                }

                canvas.width = width;
                canvas.height = height;
                ctx.drawImage(img, 0, 0, width, height);

                let quality = initialQuality;

                const tryCompress = () => {
                    canvas.toBlob((blob) => {
                        if (!blob) {
                            return reject(new Error('Canvas compression failed'));
                        }

                        if (blob.size <= targetSize || quality <= minQuality) {
                            const ext = file.name.split('.').pop();
                            const compressed = new File([blob], file.name, {
                                type: 'image/jpeg',
                                lastModified: Date.now(),
                            });
                            return resolve(compressed);
                        }

                        quality -= 0.05;
                        tryCompress();
                    }, 'image/jpeg', quality);
                };

                tryCompress();
            };
            img.onerror = () => reject(new Error('Failed to load image'));
            img.src = e.target.result;
        };
        reader.onerror = () => reject(new Error('Failed to read file'));
        reader.readAsDataURL(file);
    });
}

/**
 * Attach compression to a file input. Returns a promise that resolves
 * with the compressed File when the user selects an image.
 *
 * @param {HTMLInputElement} fileInput - The file input element
 * @param {Object} opts - Options passed to compressImage
 * @returns {Promise<File>}
 */
export function compressOnFileSelect(fileInput, opts = {}) {
    return new Promise((resolve, reject) => {
        fileInput.addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (!file) return reject(new Error('No file selected'));
            try {
                const compressed = await compressImage(file, opts);
                resolve(compressed);
            } catch (err) {
                reject(err);
            }
        }, { once: true });
    });
}
