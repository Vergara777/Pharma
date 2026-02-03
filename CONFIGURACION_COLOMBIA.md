# 🇨🇴 Configuración para Colombia

## ✅ Cambios Realizados

### 1. Zona Horaria
- **Configurada:** `America/Bogota` (UTC-5)
- **Archivos modificados:**
  - `config/app.php` → `timezone` = `America/Bogota`
  - `.env` → `APP_TIMEZONE=America/Bogota`

### 2. Idioma y Localización
- **Idioma:** Español (es)
- **Locale:** es_CO (Colombia)
- **Archivos modificados:**
  - `config/app.php` → `locale` = `es`
  - `.env` → `APP_LOCALE=es`, `APP_FAKER_LOCALE=es_CO`

### 3. Filtros de Fecha en Ventas
Se agregaron los siguientes filtros:

#### Filtros Rápidos (Toggle):
- ✅ **Hoy** - Ventas del día actual
- ✅ **Ayer** - Ventas del día anterior
- ✅ **Esta Semana** - Ventas de la semana actual
- ✅ **Semana Pasada** - Ventas de la semana anterior
- ✅ **Este Mes** - Ventas del mes actual
- ✅ **Mes Pasado** - Ventas del mes anterior

#### Filtro de Rango Personalizado:
- 📅 **Desde/Hasta** - Selecciona un rango de fechas específico
- Formato: dd/mm/yyyy
- Muestra indicador visual del rango seleccionado

### 4. Reseteo Automático de Ventas Diarias
- **Comando:** `php artisan sales:reset-daily`
- **Programado:** Se ejecuta automáticamente a las 00:00 (medianoche) hora de Colombia
- **Función:** Limpia el caché de estadísticas diarias

### 5. Persistencia de Preferencias de Tabla
- **Tabla:** `user_table_preferences`
- **Guarda:**
  - Columnas visibles/ocultas
  - Cantidad de registros por página
  - Preferencias por usuario y por tabla
- **Persiste:** Indefinidamente en la base de datos

## 🕐 Verificación de Hora

Para verificar que la hora esté correcta:

```bash
php artisan tinker --execute="echo now()->format('Y-m-d H:i:s');"
```

Debería mostrar la hora actual de Colombia (UTC-5).

## 📊 Estadísticas de Ventas

Las estadísticas del dashboard ahora usan la zona horaria de Colombia:

- **Ventas Hoy:** Cuenta desde las 00:00 hasta las 23:59 hora de Colombia
- **Ventas del Mes:** Mes actual según calendario colombiano
- **Comparaciones:** Se comparan con el día/mes anterior en hora local

## 🔄 Tareas Programadas

Para que las tareas programadas funcionen, debes tener el scheduler corriendo:

```bash
# En desarrollo (ejecutar en una terminal separada)
php artisan schedule:work

# En producción (agregar al crontab)
* * * * * cd /ruta/a/tu/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

## 📝 Notas Importantes

1. **Hora del Servidor:** Asegúrate de que el servidor también esté en hora de Colombia o que PHP esté configurado correctamente.

2. **Base de Datos:** Las fechas se guardan en la zona horaria configurada (America/Bogota).

3. **Filtros:** Los filtros de fecha ahora respetan la zona horaria de Colombia.

4. **Widgets:** Las tarjetas del dashboard se actualizan automáticamente según la hora local.

## 🧪 Pruebas

### Verificar Zona Horaria:
```bash
php artisan tinker --execute="echo 'Zona horaria: ' . config('app.timezone');"
```

### Verificar Hora Actual:
```bash
php artisan tinker --execute="echo 'Hora actual: ' . now()->format('d/m/Y H:i:s');"
```

### Probar Reseteo Manual:
```bash
php artisan sales:reset-daily
```

### Ver Ventas de Hoy:
```bash
php artisan tinker --execute="echo 'Ventas hoy: ' . App\Models\Ventas::whereDate('created_at', today())->count();"
```

## 🎯 Resultado

Ahora todo el sistema funciona con:
- ✅ Hora de Colombia (UTC-5)
- ✅ Formato de fecha colombiano (dd/mm/yyyy)
- ✅ Filtros de fecha precisos
- ✅ Estadísticas correctas por día
- ✅ Reseteo automático a medianoche
- ✅ Preferencias de tabla persistentes
