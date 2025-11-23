document.addEventListener('DOMContentLoaded', function () {
    const uploadWrappers = document.querySelectorAll('.file-upload-wrapper');

    uploadWrappers.forEach(wrapper => {
        const input = wrapper.querySelector('input[type="file"]');

        // Drag Enter
        wrapper.addEventListener('dragenter', (e) => {
            e.preventDefault();
            e.stopPropagation();
            wrapper.classList.add('drag-over');
        });

        // Drag Over
        wrapper.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.stopPropagation();
            wrapper.classList.add('drag-over');
        });

        // Drag Leave
        wrapper.addEventListener('dragleave', (e) => {
            e.preventDefault();
            e.stopPropagation();
            wrapper.classList.remove('drag-over');
        });

        // Drop
        wrapper.addEventListener('drop', (e) => {
            e.preventDefault();
            e.stopPropagation();
            wrapper.classList.remove('drag-over');

            // Optional: You can handle the files here if you want custom logic,
            // but the input[type="file"] usually handles the selection if dropped directly on it.
            // Since the input covers the whole wrapper, it should catch the drop.
            // We just need the visual feedback removal.
        });

        // Input Change (Fallback for click selection)
        input.addEventListener('change', () => {
            // Add a brief flash effect or success state if desired
            wrapper.style.borderColor = '#00ff88';
            setTimeout(() => {
                wrapper.style.borderColor = ''; // Reset to CSS default (or hover state)
            }, 500);
        });
    });
});
