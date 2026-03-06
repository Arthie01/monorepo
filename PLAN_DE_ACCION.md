# Plan de Acción — MACUIN Frontend (Laravel)

## Dirección de Diseño

**Estética: Industrial-Refinada**
Inspirada en la precisión automotriz. No tan ruidosa como AutoZone, más profesional.
Cortes diagonales en rojo, tipografía condensada, cards técnicas con badges de stock.

| Elemento | Decisión |
|----------|----------|
| **Display font** | `Oswald` — condensed, industrial |
| **Body font** | `DM Sans` — limpio, moderno |
| **Color primario** | `#C41230` — Rojo MACUIN |
| **Color oscuro** | `#0D0D0D` — Negro hero |
| **Acento metálico** | `#8B949E` — Plata/acero |
| **Fondo claro** | `#F5F5F0` — Blanco cálido |
| **Elemento memorable** | Selector de vehículo (Año → Marca → Modelo) como entrada principal al catálogo |

---

## Fase 1 — Setup y Sistema de Diseño

> **Objetivo**: Base sólida antes de tocar ninguna vista.

- [ ] Copiar CSS base de Seals Edition → `laravel_app/public/css/`
  - `style.css`
  - `weiboo-design-system.css`
  - `footer.css`
  - `preloader.css`
- [ ] Crear `resources/css/macuin.css` con variables de color, tipografía y componentes MACUIN
- [ ] Crear `layouts/app.blade.php` con:
  - Google Fonts: Oswald + DM Sans
  - Font Awesome CDN
  - Swiper.js CDN
  - Sistema de Toast notifications
  - Preloader animado
- [ ] Crear `layouts/navbar.blade.php` — header sticky con logo MACUIN + selector de vehículo + ícono carrito
- [ ] Crear `layouts/footer.blade.php`

---

## Fase 2 — Vistas de Autenticación

> **Base**: Seals `auth/login.blade.php` y `auth/register.blade.php`

- [ ] **`login.blade.php`** → `/login`
  - Layout split-screen
  - Panel izquierdo: fondo rojo `#C41230` con diagonal + logo MACUIN + tagline
  - Panel derecho: formulario blanco (email, contraseña, recuérdame, olvidé contraseña)

- [ ] **`register.blade.php`** → `/registro`
  - Misma estructura split-screen
  - Campos: nombre, apellidos, email, contraseña, confirmar contraseña, teléfono
  - Checkbox de términos y condiciones
  - Link a login

---

## Fase 3 — Dashboard del Cliente

> **Base**: Nueva vista inspirada en AutoZone home + wireframe del PDF

- [ ] **`dashboard.blade.php`** → `/dashboard`
  - Banner hero con selector Año → Marca → Modelo (como AutoZone)
  - Grid de categorías destacadas: Motor, Suspensión, Frenos, Eléctrico, Transmisión, Filtros
  - Carousel Swiper de productos destacados
  - Banner promocional con corte diagonal rojo

---

## Fase 4 — Catálogo y Detalle de Producto

> **Base**: Seals `shop/index.blade.php` y `products/show.blade.php`

- [ ] **`catalogo.blade.php`** → `/catalogo`
  - Sidebar de filtros: Categoría, Marca de auto, rango de precio, estado de stock
  - Grid de productos (3 col desktop / 2 tablet / 1 mobile)
  - Cards con: imagen, nombre, precio, badge de stock (Verde/Amarillo/Rojo)
  - Ordenar por: precio, nombre, disponibilidad
  - Paginación

- [ ] **`detalle-producto.blade.php`** → `/catalogo/{id}`
  - Galería de imágenes con thumbnails
  - Info: nombre, SKU, precio, badge stock, descripción
  - Especificaciones técnicas (tabla)
  - Compatibilidad de vehículo
  - Selector de cantidad + botón Agregar al carrito

---

## Fase 5 — Flujo de Compra

> **Base**: Seals `cart.blade.php` y `checkout.blade.php`

- [ ] **`carrito.blade.php`** → `/carrito`
  - Tabla: imagen, nombre autoparte, precio unitario, cantidad (- n +), total, eliminar
  - Validación visual de stock disponible
  - Resumen sticky: subtotal, envío, total
  - Botones: Continuar comprando / Proceder al checkout

- [ ] **`checkout.blade.php`** → `/checkout`
  - Columna izquierda: datos del cliente + dirección de envío
  - Columna derecha: resumen del pedido (items, subtotal, total)
  - Botón "Confirmar Pedido"
  - Sin pasarela de pago real por ahora

---

## Fase 6 — Gestión de Pedidos

> **Base**: Seals `track-order.blade.php` y `orders/show.blade.php`

- [ ] **`pedidos.blade.php`** → `/pedidos`
  - Filtros: Estado (Todos / Pendiente / Completado / Cancelado), rango de fechas
  - Tabla: folio, fecha, estado (pill badge con color), total, acciones
  - Link a detalle por folio

- [ ] **`pedido-detalle.blade.php`** → `/pedido/{id}`
  - Timeline visual de estado del pedido
  - Tabla de autopartes incluidas (imagen, nombre, cantidad, precio unitario, subtotal)
  - Datos de envío
  - Botón "Cancelar Pedido" (solo si estado = Pendiente)
  - Botón "Descargar PDF"

---

## Fase 7 — Perfil de Usuario

> **Base**: Seals `mi-cuenta/index.blade.php`

- [ ] **`perfil.blade.php`** → `/perfil`
  - Info personal: nombre, apellidos, email, teléfono (editable)
  - Sección cambio de contraseña
  - Resumen de últimos 3 pedidos con link al historial completo

---

## Fase 8 — Rutas en `web.php`

> Registrar las 10 rutas en Laravel apuntando a vistas estáticas (sin controlador real aún).

```php
// Autenticación
Route::get('/login', fn() => view('login'));
Route::get('/registro', fn() => view('register'));

// Portal cliente (protegidas en el futuro)
Route::get('/dashboard', fn() => view('dashboard'));
Route::get('/catalogo', fn() => view('catalogo'));
Route::get('/catalogo/{id}', fn() => view('detalle-producto'));
Route::get('/carrito', fn() => view('carrito'));
Route::get('/checkout', fn() => view('checkout'));
Route::get('/pedidos', fn() => view('pedidos'));
Route::get('/pedido/{id}', fn() => view('pedido-detalle'));
Route::get('/perfil', fn() => view('perfil'));
```

---

## Resumen de Progreso

| Fase | Descripción | Vistas | Estado |
|------|-------------|--------|--------|
| 1 | Setup y sistema de diseño | Layout + CSS | ⬜ Pendiente |
| 2 | Autenticación | login, register | ⬜ Pendiente |
| 3 | Dashboard | dashboard | ⬜ Pendiente |
| 4 | Catálogo y detalle | catalogo, detalle-producto | ⬜ Pendiente |
| 5 | Flujo de compra | carrito, checkout | ⬜ Pendiente |
| 6 | Pedidos | pedidos, pedido-detalle | ⬜ Pendiente |
| 7 | Perfil | perfil | ⬜ Pendiente |
| 8 | Rutas web.php | — | ⬜ Pendiente |

---

## Archivos de Referencia

| Recurso | Ubicación |
|---------|-----------|
| Seals Edition (base de código) | `C:\Users\Emiliano\Documents\UPQ_SISTEMAS\7mo_Cuatrimestre\Programación Web\ML2 Seals Edition\MercadoLibre2` |
| Wireframes oficiales (PDF) | `C:\Users\Emiliano\Downloads\Entregable 1er parcial (1).pdf` |
| Inspiración AutoZone | `autozone.com.mx` |
| Directrices del proyecto | `CLAUDE.md` (raíz del repo) |
