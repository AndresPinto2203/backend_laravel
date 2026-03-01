# Documento técnico – Products API (Laravel 12)

## 1. Introducción

Este documento describe la arquitectura, requisitos técnicos y uso de la **API de Productos** desarrollada en **Laravel 12**, orientada a una prueba técnica. La API permite:

- Gestionar productos.
- Definir precios en diferentes divisas.
- Asegurar todos los endpoints mediante **autenticación JWT**.
- Exponer documentación interactiva mediante **Swagger (L5-Swagger)**.

Este documento está pensado para ser leído en formato Markdown o exportado a PDF.

---

## 2. Arquitectura general

La aplicación sigue una organización por capas inspirada en **Clean Architecture**, separando claramente:

- **Domain**: contratos y entidades de negocio.
- **Application**: casos de uso y DTOs.
- **Infrastructure**: persistencia (Eloquent) e implementaciones concretas.
- **Http**: controladores, validaciones y serialización HTTP.

### 2.1. Domain (app/Domain)

Responsable de declarar el corazón del dominio sin depender del framework:

- Entidades de dominio: Product, Currency, ProductPrice.
- Interfaces de repositorio:
  - ProductRepository
  - CurrencyRepository
  - ProductPriceRepository

Estas interfaces definen qué operaciones se pueden realizar sobre el dominio.

### 2.2. Application (app/Application)

Orquesta la lógica de negocio a través de **casos de uso**, utilizando las interfaces de repositorio del dominio:

- DTOs, como `ProductData`, que encapsulan datos de entrada/salida.
- Casos de uso de productos: listar, obtener por id, crear, actualizar, eliminar.
- Casos de uso de precios: listar precios por producto y crear un precio nuevo.

La capa Application no conoce detalles de HTTP ni de Eloquent, solo contratos.

### 2.3. Infrastructure (app/Infrastructure)

Implementa los detalles concretos:

- Modelos Eloquent para Product, Currency, ProductPrice.
- Repositorios Eloquent que implementan las interfaces de dominio.
- Configuración de bindings en AppServiceProvider para conectar interfaces con implementaciones.

De esta forma, el dominio puede reutilizarse incluso con otra tecnología de persistencia.

### 2.4. HTTP (app/Http)

Punto de entrada y salida de las peticiones:

- Controladores API (Auth, Product, ProductPrice).
- Form Requests para validar el input.
- Resources para dar una forma uniforme a las respuestas JSON.

Las rutas se definen en `routes/api.php` y están protegidas por el guard `auth:api` (JWT), salvo el login.

---

## 3. Requisitos y despliegue local

### 3.1. Requisitos

- Docker + Docker Compose
- Composer
- (Opcional) Node.js/NPM si se desea ejecutar Vite desde el host

### 3.2. Pasos de instalación

1. Instalar dependencias PHP:

```bash
composer install
```

2. Instalar Laravel Sail si es necesario:

```bash
php artisan sail:install
```

3. Levantar el entorno con Docker/Sail:

```bash
./vendor/bin/sail up -d
```

4. Ejecutar migraciones y seeds:

```bash
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed
```

5. (Opcional) Ejecutar Vite en modo desarrollo:

```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

La API queda disponible en `http://localhost`, con endpoints bajo `/api/...`.

---

## 4. Modelo de datos

Tablas principales:

- **currencies**: catálogo de divisas.
- **products**: productos con precio base y costos asociados.
- **product_prices**: precios adicionales por producto y divisa.

### 4.1. Currencies

Campos principales:

- `id`
- `name` (por ejemplo: US Dollar)
- `symbol` (por ejemplo: USD)
- `exchange_rate` (tipo float)

### 4.2. Products

Campos principales:

- `id`
- `name`
- `description`
- `price` (precio base)
- `currency_id` (FK a currencies)
- `tax_cost`
- `manufacturing_cost`

### 4.3. Product prices

Campos principales:

- `id`
- `product_id` (FK a products)
- `currency_id` (FK a currencies)
- `price`

---

## 5. Seeds y datos iniciales

Los seeders crean datos mínimos para probar el sistema:

- `CurrencySeeder`:
  - Inserta divisas base (USD, EUR, ARS, etc.) solo si la tabla está vacía.
- `UserSeeder`:
  - Crea un usuario administrador:
    - email: `admin@test.com`
    - password: `admin_123456`

Se ejecutan con:

```bash
./vendor/bin/sail artisan db:seed
```

---

## 6. Autenticación y seguridad

### 6.1. JWT

La API utiliza **JWT** mediante el paquete `php-open-source-saver/jwt-auth`.

- Guard `api` configurado en `config/auth.php` con driver `jwt`.
- El modelo `User` implementa `JWTSubject`.

Flujo:

1. **Login** (`POST /api/login`)
   - Credenciales: email y password.
   - Devuelve `access_token`, `token_type` y `expires_in`.

2. **Acceso a endpoints protegidos**
   - Enviar el token en el header:

```http
Authorization: Bearer <access_token>
```

3. **Usuario autenticado** (`GET /api/me`)
   - Devuelve los datos del usuario logueado.

4. **Logout** (`POST /api/logout`)
   - Invalida el token.

Las rutas de productos y precios requieren siempre un JWT válido.

---

## 7. Endpoints principales

### 7.1. Auth

- `POST /api/login`
  - Body JSON: `{ "email": string, "password": string }`.
  - Respuesta 200: token JWT.
- `GET /api/me`
- `POST /api/logout`

### 7.2. Productos

- `GET /api/products`
  - Lista paginada de productos.
- `POST /api/products`
  - Crea un nuevo producto.
- `GET /api/products/{id}`
  - Obtiene producto por ID.
- `PUT /api/products/{id}`
  - Actualiza parcialmente un producto.
- `DELETE /api/products/{id}`
  - Elimina un producto.

### 7.3. Precios de producto

- `GET /api/products/{id}/prices`
  - Lista precios del producto en distintas divisas.
- `POST /api/products/{id}/prices`
  - Crea un nuevo precio en una divisa específica.

Todas las rutas (salvo `/api/login`) requieren header `Authorization: Bearer <token>`.

---

## 8. Documentación Swagger

Se utiliza **L5-Swagger** con **swagger-php 6** y atributos de PHP 8.

- La especificación OpenAPI se define en `app/Docs/OpenApiAttributes.php`.
- Incluye:
  - Información general (`Info`).
  - Seguridad (`bearerAuth`).
  - Todos los paths y operaciones principales.

Para regenerar la documentación:

```bash
./vendor/bin/sail artisan l5-swagger:generate
```

La UI de Swagger se encuentra en:

- `http://localhost/api/documentation`

Desde allí se puede realizar **Authorize** con el token Bearer y probar los endpoints.

---

## 9. Colecciones para Postman e Insomnia

Para facilitar las pruebas manuales se incluyen:

- **Postman**: `docs/postman/Products-API.postman_collection.json`
  - Importar desde Postman → Import → File.
  - Configurar la variable `base_url` (por defecto `http://localhost`).
  - Hacer login y copiar el token en la variable `token`.

- **Insomnia**: `docs/insomnia/insomnia-export.json`
  - Importar desde Insomnia → Application → Import/Export → Import Data → From File.
  - Revisar el Environment y ajustar `base_url` si es necesario.

---

## 10. Exportar este documento a PDF

Este archivo está en `docs/Documento-tecnico-API.md`. Puede exportarse a PDF mediante:

- Un editor Markdown con opción "Export to PDF".
- O herramientas de consola como `pandoc`, por ejemplo:

```bash
pandoc docs/Documento-tecnico-API.md -o Documento-tecnico-API.pdf
```

---

## 11. Conclusiones

La API implementa una separación clara de capas, autenticación robusta mediante JWT y una documentación fácilmente accesible (Swagger, Postman, Insomnia, y este documento técnico). Esto facilita su comprensión, mantenimiento y extensión futura.
