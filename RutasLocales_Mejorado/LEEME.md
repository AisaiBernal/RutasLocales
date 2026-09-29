# RutasLocales (versión mejorada)

Marketplace de experiencias turísticas locales (Bogotá y Cundinamarca) con tres roles: turista, guía y administrador.

## Instalación (XAMPP / Laragon / WAMP, PHP 8.0 o superior)
1. Copia esta carpeta a `htdocs/RutasLocales`.
2. En phpMyAdmin importa `database.sql` (crea la BD `rutas_locales` con datos de prueba).
3. Si tu MySQL tiene contraseña, edita `includes/config.php`.
4. Abre `http://localhost/RutasLocales/`.

Usuarios de prueba (contraseña `Demo1234`): `admin@rutaslocales.com`, `guia@rutaslocales.com`, `turista@rutaslocales.com`.

## Cumplimiento de la rúbrica
| Requisito | Dónde |
|---|---|
| Página principal | `index.php` |
| Registro / login | `registro.php`, `login.php`, `logout.php` |
| Dashboard por rol | `dashboard.php` |
| 4 tablas relacionadas | `usuarios`, `categorias`, `experiencias`, `reservas` |
| CRUD completo | `mis_experiencias.php` + `experiencia_form.php` (crear, leer, editar, eliminar) |
| Formularios con validación | cliente (`assets/app.js`) y servidor (cada PHP) |
| Buscador / filtros | `index.php` (SQL: texto, categoría, precio, orden) + filtro en vivo JS |
| Acción principal | Reservar: `experiencia.php` → `reservar.php` → tabla `reservas` |
| Consulta de información | Dashboard, tablas de reservas, detalle |
| Perfil | `perfil.php` |
| Panel admin | `admin.php` (usuarios, experiencias, categorías) |
| Responsive | `assets/style.css` |
| 5+ funcionalidades JS | 10 en `assets/app.js` (menú móvil, ver contraseña, filtro en vivo, confirmaciones, total dinámico, medidor de contraseña, contador, pestañas, contadores animados, avisos) |

Flujo: Usuario → Interfaz → Formulario → PHP → MySQL → Resultado.
