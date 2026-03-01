## Products API – Prueba técnica (Laravel 12)

Proyecto de API RESTful para gestión de productos y precios en múltiples divisas, desarrollado en Laravel 12 con arquitectura en capas (inspirada en Clean Architecture), autenticación JWT y documentación con Swagger (L5-Swagger).

---

## 1. Requisitos

- Docker + Docker Compose
- PHP no es estrictamente necesario en host (se usa Laravel Sail)
- Node.js/NPM solo si quieres correr Vite desde el host (no es obligatorio para la API)

---

## 2. Puesta en marcha

Desde la raíz del proyecto:

1. Instalar dependencias PHP (host):

```bash
composer install
```

2. Instalar Sail (si aún no está configurado):

```bash
php artisan sail:install
```

3. Levantar los servicios Docker con Sail:

```bash
./vendor/bin/sail up -d
```

4. Instalar dependencias front (dentro de Sail):

```bash
./vendor/bin/sail npm install
```

5. Ejecutar migraciones y seeders:

```bash
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed
```

Esto crea:

- Tablas: `users`, `currencies`, `products`, `product_prices`, etc.
- Seeds:
	- `CurrencySeeder` → divisas base (`USD`, `EUR`, `ARS`, ...).
	- `UserSeeder` → usuario admin para autenticación JWT:
		- email: `admin@test.com`
		- password: `admin_123456`

6. (Opcional) Vite en modo desarrollo:

```bash
./vendor/bin/sail npm run dev
```

La API queda accesible en:

- `http://localhost` (por defecto en Sail)
- Rutas de API bajo `http://localhost/api/...`

---

## 3. Arquitectura y organización por capas

Se utiliza una organización por capas inspirada en Clean Architecture:

### 3.1. Capa Domain (`app/Domain`)

Contiene la lógica de dominio “pura” (sin framework):

- `app/Domain/Product/Entities/Product.php`
- `app/Domain/Currency/Entities/Currency.php`
- `app/Domain/ProductPrice/Entities/ProductPrice.php`

Interfaces de repositorio:

- `app/Domain/Product/Repositories/ProductRepository.php`
- `app/Domain/Currency/Repositories/CurrencyRepository.php`
- `app/Domain/ProductPrice/Repositories/ProductPriceRepository.php`

Estas interfaces definen qué operaciones son posibles sobre el dominio (no cómo se implementan).

### 3.2. Capa Application (`app/Application`)

Orquesta casos de uso y reglas de negocio, usando los repositorios de dominio:

- `app/Application/Product/DTOs/ProductData.php` (DTO para productos).
- Casos de uso de Product:
	- `GetProductList`
	- `GetProductById`
	- `CreateProduct`
	- `UpdateProduct`
	- `DeleteProduct`

- Casos de uso de ProductPrice:
	- `GetProductPrices`
	- `CreateProductPrice`

La capa Application no conoce detalles de Eloquent ni de HTTP, solo habla con interfaces de repositorio.

### 3.3. Capa Infrastructure (`app/Infrastructure`)

Implementa los detalles concretos (Eloquent, base de datos):

- Modelos Eloquent:
	- `app/Infrastructure/Persistence/Eloquent/Models/ProductModel.php`
	- `app/Infrastructure/Persistence/Eloquent/Models/CurrencyModel.php`
	- `app/Infrastructure/Persistence/Eloquent/Models/ProductPriceModel.php`

- Implementaciones de repositorios:
	- `EloquentProductRepository`
	- `EloquentCurrencyRepository`
	- `EloquentProductPriceRepository`

Los bindings de interfaces → implementaciones se registran en:

- `app/Providers/AppServiceProvider.php`

```php
$this->app->bind(ProductRepository::class, EloquentProductRepository::class);
$this->app->bind(CurrencyRepository::class, EloquentCurrencyRepository::class);
$this->app->bind(ProductPriceRepository::class, EloquentProductPriceRepository::class);
```

### 3.4. Capa HTTP (`app/Http`)

Responsable de la entrada/salida HTTP:

- Controladores API:
	- `app/Http/Controllers/Api/AuthController.php` → login/logout/me (JWT).
	- `app/Http/Controllers/Api/ProductController.php` → CRUD de productos.
	- `app/Http/Controllers/Api/ProductPriceController.php` → precios por producto.

- Form Requests (validación):
	- `StoreProductRequest`, `UpdateProductRequest`.
	- `StoreProductPriceRequest`.

- Resources (serialización a JSON):
	- `ProductResource`, `ProductPriceResource`.

Rutas definidas en:

- `routes/api.php`

```php
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
		Route::get('me', [AuthController::class, 'me']);
		Route::post('logout', [AuthController::class, 'logout']);

		Route::apiResource('products', ProductController::class);

		Route::get('products/{product}/prices', [ProductPriceController::class, 'index']);
		Route::post('products/{product}/prices', [ProductPriceController::class, 'store']);
});
```

---

## 4. Base de datos y migraciones

Migraciones principales en `database/migrations`:

- `create_currencies_table`
- `create_products_table`
- `create_product_prices_table`

Se ejecutan con:

```bash
./vendor/bin/sail artisan migrate
```

### Seeds

En `database/seeders`:

- `CurrencySeeder` → inserta divisas iniciales si la tabla está vacía.
- `UserSeeder` → crea el usuario admin (`admin@test.com`).
- `DatabaseSeeder` → llama a ambos seeders.

Ejecución:

```bash
./vendor/bin/sail artisan db:seed
```

---

## 5. Autenticación JWT

Se utiliza el paquete `php-open-source-saver/jwt-auth` y el guard `api` configurado en `config/auth.php`:

- Guard `api` → `driver: jwt`, `provider: users`.
- `App\Models\User` implementa `JWTSubject`.

Flujo básico:

1. **Login**

```http
POST /api/login
Content-Type: application/json

{
	"email": "admin@test.com",
	"password": "admin_123456"
}
```

Respuesta:

```json
{
	"access_token": "...",
	"token_type": "Bearer",
	"expires_in": 3600
}
```

2. **Uso del token**

En todas las rutas protegidas:

```http
Authorization: Bearer <access_token>
```

3. **Me / Logout**

- `GET /api/me`
- `POST /api/logout`

Si se accede a rutas protegidas sin token, se devuelve `401 Unauthenticated` en JSON (configurado en `bootstrap/app.php` y middleware `Authenticate`).

---

## 6. Endpoints principales

### 6.1. Productos

- `GET /api/products` → lista paginada de productos.
- `POST /api/products` → crear producto.
- `GET /api/products/{id}` → obtener producto por ID.
- `PUT /api/products/{id}` → actualizar producto.
- `DELETE /api/products/{id}` → eliminar producto.

### 6.2. Precios de producto

- `GET /api/products/{id}/prices` → obtener lista de precios por divisa.
- `POST /api/products/{id}/prices` → crear un nuevo precio en una divisa.

Todas las rutas anteriores requieren `Authorization: Bearer <token>` (excepto `/api/login`).

---

## 7. Documentación Swagger

Se utiliza **L5-Swagger** con **swagger-php 6** y atributos PHP 8.

- Archivo de definición OpenAPI por atributos:
	- `app/Docs/OpenApiAttributes.php`

Ahí se definen:

- `Info`, `SecurityScheme bearerAuth`.
- Todos los `PathItem` y operaciones (`/api/login`, `/api/products`, `/api/products/{id}`, `/api/products/{id}/prices`).

### Generar documentación

```bash
./vendor/bin/sail artisan l5-swagger:generate
```

### UI de Swagger

Con Sail levantado:

- `http://localhost/api/documentation`

Desde la UI se puede:

- Hacer **Authorize** pegando el Bearer token.
- Probar todos los endpoints directamente.

---

## 8. Colecciones Postman / Insomnia y PDF

- Postman: colección en `docs/postman/Products-API.postman_collection.json`.
- Insomnia: export en `docs/insomnia/insomnia-export.json`.
- Documento técnico extendido (pensado para exportar a PDF): `docs/Documento-tecnico-API.md`.

Estos recursos permiten importar rápidamente la API en clientes HTTP y contar con un documento complementario para entregar en formato PDF.

---

## 9. Notas de seguridad

- La API exige JWT para todas las rutas de productos y precios.
- Validación de payloads mediante Form Requests.
- Campos sensibles (`password`) ocultos en el modelo `User`.
- Uso de `$fillable` en modelos Eloquent para evitar asignación masiva insegura.
