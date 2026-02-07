# 💊 Sistema de Gestión de Farmacia

Sistema completo de gestión para farmacias desarrollado con Laravel 11 y Filament 3. Diseñado específicamente para farmacias en Colombia con soporte para gestión de inventario, ventas, lotes, cajas y más.

## ✨ Características Principales

### 📦 Gestión de Inventario
- Control completo de productos con categorías y proveedores
- Sistema de lotes con fechas de vencimiento y trazabilidad
- Alertas automáticas de stock bajo y productos próximos a vencer
- Gestión de ubicaciones físicas en la farmacia
- Soporte para productos que requieren cadena de frío
- Control de productos que requieren receta médica
- Historial completo de movimientos de inventario

### 💰 Punto de Venta (POS)
- Carrito de compras en tiempo real
- Búsqueda rápida de productos por nombre o código de barras
- Gestión de sesiones de caja (apertura/cierre)
- Múltiples métodos de pago
- Cálculo automático de diferencias al cerrar caja
- Formato automático de moneda colombiana ($X.XXX)
- Impresión de facturas

### 📊 Dashboard y Reportes
- Widgets de ganancias (totales, mensuales, diarias)
- Indicadores de inventario en tiempo real
- Alertas visuales de productos críticos
- Gráficos de productos por categoría
- Seguimiento de cajas abiertas por usuario
- Historial de sesiones de caja

### 👥 Gestión de Usuarios
- Sistema de roles (Admin, Técnico/Trabajador)
- Permisos granulares por módulo
- Perfil de usuario personalizable
- Autenticación segura con Laravel Fortify

### ⚙️ Configuración
- Información de la farmacia personalizable
- Configuración de alertas de stock
- Parámetros de vencimiento
- Soporte multi-moneda
- Modo oscuro/claro

## 🛠️ Tecnologías

- **Backend:** Laravel 11
- **Frontend:** Filament 3 (Panel de administración)
- **Base de datos:** SQLite (desarrollo) / MySQL/PostgreSQL (producción)
- **UI:** Tailwind CSS
- **Componentes:** Livewire 3
- **Autenticación:** Laravel Fortify + Jetstream

## 📋 Requisitos

- PHP >= 8.2
- Composer
- Node.js >= 18
- NPM o Yarn
- SQLite (desarrollo) o MySQL/PostgreSQL (producción)

## 🚀 Instalación

### 1. Clonar el repositorio
```bash
git clone <url-del-repositorio>
cd pharma
```

### 2. Instalar dependencias
```bash
composer install
npm install
```

### 3. Configurar el entorno
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configurar la base de datos
Edita el archivo `.env` con tus credenciales de base de datos:

```env
DB_CONNECTION=sqlite
# O para MySQL/PostgreSQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=pharma
# DB_USERNAME=root
# DB_PASSWORD=
```

### 5. Ejecutar migraciones y seeders
```bash
php artisan migrate --seed
```

### 6. Compilar assets
```bash
npm run build
# O para desarrollo:
npm run dev
```

### 7. Iniciar el servidor
```bash
php artisan serve
```

Accede a: `http://localhost:8000/admin`

## 👤 Credenciales por Defecto

Después de ejecutar los seeders, puedes acceder con:

**Administrador:**
- Email: `admin@pharma.com`
- Password: `password`

**Técnico:**
- Email: `tecnico@pharma.com`
- Password: `password`

> ⚠️ **Importante:** Cambia estas credenciales en producción.

## 📁 Estructura del Proyecto

```
pharma/
├── app/
│   ├── Filament/          # Recursos de Filament
│   │   ├── Pages/         # Páginas personalizadas
│   │   ├── Resources/     # Recursos CRUD
│   │   └── Widgets/       # Widgets del dashboard
│   ├── Models/            # Modelos Eloquent
│   ├── Observers/         # Observadores de modelos
│   └── Notifications/     # Notificaciones
├── database/
│   ├── migrations/        # Migraciones de BD
│   └── seeders/          # Datos de prueba
├── resources/
│   ├── views/            # Vistas Blade
│   └── js/               # JavaScript/Vue
└── routes/               # Rutas de la aplicación
```

## 🎯 Módulos Principales

### Productos
- CRUD completo de productos
- Gestión de imágenes
- Control de stock mínimo/máximo
- Precios de venta y costo
- Categorización y etiquetado
- Estados (activo/retirado)

### Lotes
- Registro de lotes con fechas de vencimiento
- Trazabilidad completa de movimientos
- Alertas de vencimiento
- Gestión de ubicación física
- Historial detallado por lote

### Ventas
- Carrito de compras interactivo
- Búsqueda por código de barras
- Múltiples métodos de pago
- Generación de facturas
- Historial de ventas

### Cajas
- Apertura/cierre de sesión de caja
- Control de efectivo inicial
- Cálculo automático de diferencias
- Historial de sesiones
- Vista de cajas abiertas por otros usuarios

### Configuración
- Datos de la farmacia
- Parámetros de inventario
- Alertas y notificaciones
- Configuración de moneda

## 🔧 Comandos Útiles

```bash
# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Verificar stock bajo (programado)
php artisan app:check-low-stock

# Resetear ventas diarias (programado)
php artisan app:reset-daily-sales

# Ejecutar pruebas
php artisan test
```

## 📱 Características Especiales

### Sistema de Alertas
- Notificaciones de stock bajo
- Alertas de productos próximos a vencer
- Notificaciones de productos vencidos
- Alertas de stock excedido

### Colores Visuales
- 🔴 Rojo: Sin stock, vencido, crítico
- 🟠 Naranja: Stock bajo, próximo a vencer
- 🟢 Verde: Stock normal, activo
- 🔵 Azul: Stock excedido
- ⚫ Gris: Productos desactivados

### Formato de Moneda
Todos los valores monetarios se muestran en formato colombiano:
- `$5.000` (cinco mil pesos)
- `$1.250.000` (un millón doscientos cincuenta mil pesos)

## 🔐 Seguridad

- Autenticación de dos factores (2FA) disponible
- Protección CSRF en todos los formularios
- Validación de datos en servidor
- Sanitización de entradas
- Control de acceso basado en roles

## 🤝 Contribuir

Las contribuciones son bienvenidas. Por favor:

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📝 Licencia

Este proyecto es de código abierto bajo la licencia MIT.

## 📧 Contacto

Para soporte o consultas, contacta al equipo de desarrollo.

---

Desarrollado con ❤️ para farmacias en Colombia
