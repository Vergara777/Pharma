<div class="fi-sidebar-user-info" style="padding: 1rem; border-bottom: 1px solid rgba(0,0,0,0.1); margin-bottom: 1rem; overflow: hidden;">
    <div style="display: flex; align-items: center; gap: 0.75rem; transition: all 0.3s ease;">
        @php
            $user = auth()->user();
            $avatarUrl = $user->avatar 
                ? (str_starts_with($user->avatar, 'http') 
                    ? $user->avatar 
                    : asset('storage/' . $user->avatar))
                : asset('/Images/Pharma1.jpeg');
        @endphp
        
        <!-- Avatar -->
        <div style="flex-shrink: 0; min-width: 48px;">
            <img 
                src="{{ $avatarUrl }}" 
                alt="{{ $user->name }}"
                style="
                    width: 48px; 
                    height: 48px; 
                    border-radius: 50%; 
                    object-fit: cover;
                    border: 2px solid #10b981;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                "
            >
        </div>
        
        <!-- Info (se oculta cuando está colapsado) -->
        <div class="fi-sidebar-user-details" style="flex: 1; min-width: 0; white-space: nowrap; overflow: hidden;">
            <div style="
                font-weight: 600; 
                font-size: 0.875rem; 
                color: var(--gray-950);
                overflow: hidden;
                text-overflow: ellipsis;
            ">
                {{ $user->name }}
            </div>
            
            @if($user->position || $user->role)
                <div style="margin-top: 0.25rem;">
                    <span style="
                        display: inline-flex;
                        align-items: center;
                        padding: 0.125rem 0.5rem;
                        font-size: 0.75rem;
                        font-weight: 500;
                        border-radius: 0.375rem;
                        background-color: {{ $user->role === 'admin' ? '#fef3c7' : '#dbeafe' }};
                        color: {{ $user->role === 'admin' ? '#92400e' : '#1e40af' }};
                    ">
                        {{ $user->position ?? ucfirst($user->role ?? 'Usuario') }}
                    </span>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .dark .fi-sidebar-user-info {
        border-bottom-color: rgba(255,255,255,0.1);
    }
    
    .dark .fi-sidebar-user-info div[style*="color: var(--gray-950)"] {
        color: var(--gray-50) !important;
    }
    
    /* Cuando el sidebar tiene menos de 100px de ancho, ocultar detalles */
    @container (max-width: 100px) {
        .fi-sidebar-user-details {
            display: none !important;
        }
    }
    
    /* Forzar ocultamiento cuando el sidebar está colapsado */
    body:has(.fi-sidebar[style*="width: 4rem"]) .fi-sidebar-user-details,
    body:has(.fi-sidebar[style*="width: 64px"]) .fi-sidebar-user-details {
        display: none !important;
        width: 0 !important;
        opacity: 0 !important;
    }
    
    body:has(.fi-sidebar[style*="width: 4rem"]) .fi-sidebar-user-info > div,
    body:has(.fi-sidebar[style*="width: 64px"]) .fi-sidebar-user-info > div {
        justify-content: center !important;
    }
</style>

<script>
(function() {
    let checkInterval;
    
    function hideUserDetails() {
        const sidebar = document.querySelector('.fi-sidebar');
        const userDetails = document.querySelector('.fi-sidebar-user-details');
        
        if (!sidebar || !userDetails) return;
        
        const width = sidebar.offsetWidth;
        
        // Si el sidebar tiene menos de 100px de ancho, ocultar detalles
        if (width < 100) {
            userDetails.style.display = 'none';
            userDetails.style.opacity = '0';
            userDetails.style.width = '0';
        } else {
            userDetails.style.display = 'block';
            userDetails.style.opacity = '1';
            userDetails.style.width = 'auto';
        }
    }
    
    // Ejecutar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    function init() {
        // Verificar inmediatamente
        hideUserDetails();
        
        // Verificar periódicamente
        checkInterval = setInterval(hideUserDetails, 100);
        
        // Limpiar después de 10 segundos
        setTimeout(() => {
            if (checkInterval) {
                clearInterval(checkInterval);
            }
        }, 10000);
        
        // Observar cambios en el sidebar
        const sidebar = document.querySelector('.fi-sidebar');
        if (sidebar && window.ResizeObserver) {
            const resizeObserver = new ResizeObserver(hideUserDetails);
            resizeObserver.observe(sidebar);
        }
        
        // Observar cambios de atributos
        if (sidebar && window.MutationObserver) {
            const mutationObserver = new MutationObserver(hideUserDetails);
            mutationObserver.observe(sidebar, {
                attributes: true,
                attributeFilter: ['style', 'class']
            });
        }
    }
})();
</script>
