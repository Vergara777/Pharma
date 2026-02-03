<style>
/* Estilos para filas de productos con problemas */

/* Fila de advertencia (naranja) - Stock bajo o próximo a vencer */
.fi-row-warning {
    background-color: rgb(255 247 237) !important;
}

.dark .fi-row-warning {
    background-color: rgba(194, 65, 12, 0.1) !important;
}

.fi-row-warning:hover {
    background-color: rgb(255 237 213) !important;
}

.dark .fi-row-warning:hover {
    background-color: rgba(194, 65, 12, 0.15) !important;
}

/* Fila de peligro (rojo) - Sin stock o vencido */
.fi-row-danger {
    background-color: rgb(254 242 242) !important;
}

.dark .fi-row-danger {
    background-color: rgba(127, 29, 29, 0.1) !important;
}

.fi-row-danger:hover {
    background-color: rgb(254 226 226) !important;
}

.dark .fi-row-danger:hover {
    background-color: rgba(127, 29, 29, 0.15) !important;
}

/* Logo redondito en el header y login */
.fi-logo img,
.fi-simple-layout-logo img,
img[class*="fi-logo"],
img[class*="brand-logo"] {
    border-radius: 50% !important;
    object-fit: cover !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
    width: 3.5rem !important;
    height: 3.5rem !important;
}

/* Logo en el sidebar */
.fi-sidebar-header img {
    border-radius: 50% !important;
    object-fit: cover !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
    width: 3.5rem !important;
    height: 3.5rem !important;
}

/* Logo en la página de login - más grande */
.fi-simple-page img,
.fi-simple-layout img {
    border-radius: 50% !important;
    object-fit: cover !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
    max-width: 6rem !important;
    max-height: 6rem !important;
    width: auto !important;
    height: auto !important;
}

/* Badge de notificaciones - Color rojo/danger */
.fi-badge[data-slot="badge"] {
    --c-50: var(--danger-50) !important;
    --c-400: var(--danger-400) !important;
    --c-600: var(--danger-600) !important;
}

/* Badge en el modal de notificaciones */
.fi-modal-heading .fi-badge {
    background-color: rgb(239 68 68) !important;
    color: white !important;
}

.dark .fi-modal-heading .fi-badge {
    background-color: rgb(220 38 38) !important;
}
</style>
