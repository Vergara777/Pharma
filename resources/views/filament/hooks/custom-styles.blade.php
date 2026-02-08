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

/* Fila de info (azul) - Stock excedido */
.fi-row-info {
    background-color: rgb(239 246 255) !important;
}

.dark .fi-row-info {
    background-color: rgba(30, 64, 175, 0.1) !important;
}

.fi-row-info:hover {
    background-color: rgb(219 234 254) !important;
}

.dark .fi-row-info:hover {
    background-color: rgba(30, 64, 175, 0.15) !important;
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
.fi-no-database-notifications-trigger span,
.fi-database-notifications-trigger span,
.fi-topbar-database-notifications-trigger span,
.fi-icon-btn-badge,
[aria-label*="notificaciones"] span,
.fi-badge[data-slot="badge"] {
    background-color: #ff0000 !important;
    color: #ffffff !important;
    --c-50: 254, 242, 242 !important;
    --c-400: 248, 113, 113 !important;
    --c-600: 220, 38, 38 !important;
}

/* Forzar el color de fondo específicamente para el círculo */
.fi-icon-btn-badge {
    background-color: #ff0000 !important;
}

/* Badge en el modal de notificaciones */
.fi-modal-heading .fi-badge,
.fi-no-database-modal-heading .fi-badge {
    background-color: #ff0000 !important;
    color: #ffffff !important;
}

.dark .fi-modal-heading .fi-badge {
    background-color: rgb(220 38 38) !important;
}

/* Forzar botones de submit/guardar a color primary (verde) */
.fi-modal-footer-actions button[type="submit"],
.fi-form-actions button[type="submit"],
button[wire\:click*="save"],
button[wire\:click*="create"] {
    --c-400: var(--primary-400) !important;
    --c-500: var(--primary-500) !important;
    --c-600: var(--primary-600) !important;
}

/* Buscador de tabla más ancho */
.fi-ta-search-field {
    min-width: 500px !important;
}

@media (max-width: 768px) {
    .fi-ta-search-field {
        min-width: 100% !important;
    }
}
/* Acciones de notificación más limpias y sin scroll feo */
div[class*="notification-actions"],
.fi-no-notification-actions,
.fi-no-notification-actions > div {
    display: flex !important;
    gap: 0.5rem !important;
    padding-top: 0.5rem !important;
    flex-wrap: wrap !important; /* Permitir que bajen si no caben, pero juntos */
}

.fi-no-notification-actions button,
.fi-no-notification-actions a {
    white-space: nowrap !important;
    padding: 0.3rem 0.6rem !important;
    font-size: 0.75rem !important;
    border-radius: 0.375rem !important;
    border: none !important; /* Quitar cualquier borde blanco/feo */
    box-shadow: none !important;
}

/* Ocultar scrollbars si aparecen por casualidad */
.fi-no-notification-actions::-webkit-scrollbar {
    display: none !important;
}
.fi-no-notification-actions {
    -ms-overflow-style: none !important;
    scrollbar-width: none !important;
}

/* Estilizar scrollbar del sidebar */
.fi-sidebar-nav::-webkit-scrollbar,
.fi-sidebar::-webkit-scrollbar,
aside::-webkit-scrollbar {
    width: 8px;
}

.fi-sidebar-nav::-webkit-scrollbar-track,
.fi-sidebar::-webkit-scrollbar-track,
aside::-webkit-scrollbar-track {
    background: transparent;
}

.fi-sidebar-nav::-webkit-scrollbar-thumb,
.fi-sidebar::-webkit-scrollbar-thumb,
aside::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.2);
    border-radius: 10px;
    transition: background 0.2s ease;
}

.fi-sidebar-nav::-webkit-scrollbar-thumb:hover,
.fi-sidebar::-webkit-scrollbar-thumb:hover,
aside::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 0, 0, 0.3);
}

/* Modo oscuro */
.dark .fi-sidebar-nav::-webkit-scrollbar-thumb,
.dark .fi-sidebar::-webkit-scrollbar-thumb,
.dark aside::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
}

.dark .fi-sidebar-nav::-webkit-scrollbar-thumb:hover,
.dark .fi-sidebar::-webkit-scrollbar-thumb:hover,
.dark aside::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.3);
}

/* Firefox */
.fi-sidebar-nav,
.fi-sidebar,
aside {
    scrollbar-width: thin;
    scrollbar-color: rgba(0, 0, 0, 0.2) transparent;
}

.dark .fi-sidebar-nav,
.dark .fi-sidebar,
.dark aside {
    scrollbar-color: rgba(255, 255, 255, 0.2) transparent;
}
</style>
