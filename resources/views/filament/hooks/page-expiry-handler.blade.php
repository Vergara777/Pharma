<script>
    document.addEventListener('livewire:init', () => {
        Livewire.hook('request', ({ fail }) => {
            fail(({ status, preventDefault }) => {
                if (status === 419) {
                    preventDefault();
                    
                    if (typeof MyCustomNotification !== 'undefined') {
                        // Si tenemos un sistema de notificaciones custom, lo usamos
                        return;
                    }

                    // Intentar usar el sistema de notificaciones de Filament si está disponible
                    if (window.dispatchEvent) {
                        window.dispatchEvent(new CustomEvent('notify', {
                            detail: {
                                status: 'warning',
                                message: 'Tu sesión ha expirado por seguridad. Por favor, recarga la página.',
                                duration: 10000,
                                actions: [
                                    {
                                        label: 'Recargar ahora',
                                        url: window.location.href
                                    }
                                ]
                            }
                        }));
                    } else {
                        // Fallback a un modal de SweetAlert2 si existiera o un diseño simple
                        alert('Tu sesión ha expirado por seguridad. La página se recargará automáticamente.');
                        window.location.reload();
                    }
                }
            });
        });
    });
</script>
