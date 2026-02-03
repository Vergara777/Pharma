<script>
document.addEventListener('livewire:initialized', () => {
    Livewire.on('profile-updated', () => {
        // Recargar todas las imágenes de avatar agregando un timestamp único
        const timestamp = Date.now();
        
        // Actualizar TODAS las imágenes que contengan "storage" en su src
        const allImages = document.querySelectorAll('img');
        allImages.forEach(img => {
            if (img.src && (img.src.includes('storage/avatars') || img.src.includes('storage%2Favatars'))) {
                const src = img.src.split('?')[0];
                img.src = src + '?t=' + timestamp;
            }
        });
        
        // Actualizar avatares en backgrounds
        const allElements = document.querySelectorAll('[style*="storage"]');
        allElements.forEach(el => {
            const style = el.style.backgroundImage;
            if (style && (style.includes('storage/avatars') || style.includes('storage%2Favatars'))) {
                const urlMatch = style.match(/url\(['"]?([^'"]+)['"]?\)/);
                if (urlMatch) {
                    const url = urlMatch[1].split('?')[0];
                    el.style.backgroundImage = `url('${url}?t=${timestamp}')`;
                }
            }
        });
        
        // Forzar actualización del componente de Livewire del user menu
        setTimeout(() => {
            const userMenuButton = document.querySelector('[x-data*="userMenu"]') || 
                                   document.querySelector('[aria-label*="User menu"]') ||
                                   document.querySelector('.fi-user-menu-trigger');
            if (userMenuButton) {
                // Buscar imágenes dentro del botón del user menu
                const menuImages = userMenuButton.querySelectorAll('img');
                menuImages.forEach(img => {
                    if (img.src && img.src.includes('storage')) {
                        const src = img.src.split('?')[0];
                        img.src = src + '?t=' + timestamp;
                    }
                });
            }
        }, 100);
        
        console.log('Avatar actualizado silenciosamente en todos los lugares');
    });
});
</script>
