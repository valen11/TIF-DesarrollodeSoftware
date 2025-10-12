
# 🚀 Proyecto Laravel sin Base de Datos

Este proyecto demuestra cómo crear y ejecutar una aplicación básica en **Laravel** sin necesidad de conectar una base de datos.  
Ideal para prácticas, demostraciones o pequeños sistemas que no requieren persistencia de datos.

---

## 🧩 Requisitos previos

Asegurate de tener instaladas las siguientes herramientas:

- **PHP** (>= 8.1)
  ```bash
  php -v
````

* **Composer**

  ```bash
  composer -v
  ```

> 💡 No es necesario instalar MySQL, MariaDB ni ningún motor de base de datos.

---

## 🛠️ Crear el proyecto

Podés crear un nuevo proyecto Laravel de dos maneras:

### Opción 1 — Usando Composer directamente

```bash
composer create-project laravel/laravel nombre-proyecto
```

Ejemplo:

```bash
composer create-project laravel/laravel mi-app
```

### Opción 2 — Usando el instalador de Laravel (opcional)

```bash
composer global require laravel/installer
laravel new mi-app
```

---

## 📂 Entrar al directorio del proyecto

```bash
cd mi-app
```

---

## 🖥️ Iniciar el servidor local

Ejecutá el siguiente comando:

```bash
php artisan serve
```

Esto iniciará un servidor en:

```
http://127.0.0.1:8000
```

Abrí esa URL en el navegador para ver la página de bienvenida de Laravel 🎉

---

## 🧾 Crear una ruta de ejemplo

Editá el archivo `routes/web.php` y agregá:

```php
Route::get('/saludo', function () {
    return '¡Hola desde Laravel sin base de datos! 😄';
});
```

Abrí en el navegador:

```
http://127.0.0.1:8000/saludo
```

---

## 💡 Ejemplo con datos simulados

Podés mostrar datos sin usar una base de datos, simplemente usando arrays.

### Ruta:

```php
Route::get('/productos', function () {
    $productos = [
        ['nombre' => 'Camiseta', 'precio' => 2500],
        ['nombre' => 'Pantalón', 'precio' => 4200],
        ['nombre' => 'Zapatillas', 'precio' => 7800],
    ];

    return view('productos', ['productos' => $productos]);
});
```

### Vista (`resources/views/home.blade.php`):

```html
<!DOCTYPE html>
<html>
<head>
    <title>Lista de Productos</title>
</head>
<body>
    <h1>Productos disponibles</h1>
    <ul>
        @foreach ($productos as $p)
            <li>{{ $p['nombre'] }} - ${{ $p['precio'] }}</li>
        @endforeach
    </ul>
</body>
</html>
```

---

## 📁 Estructura básica del proyecto

```
mi-app/
├── app/
├── bootstrap/
├── config/
├── public/
├── resources/
│   └── views/
├── routes/
│   └── web.php
├── storage/
├── .env
└── artisan
```

¿Querés que te lo prepare como archivo descargable (`README.md`) para poner directo en tu carpeta del proyecto?
```
