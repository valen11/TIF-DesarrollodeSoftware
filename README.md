🧩 Requisitos previos

Antes de comenzar, asegurate de tener instaladas las siguientes herramientas:

1. PHP (>= 8.1)

Laravel requiere PHP 8.1 o superior.
Podés verificar la versión con:

php -v


Si no lo tenés, instalalo con:

sudo apt update
sudo apt install php php-cli php-mbstring php-xml php-bcmath php-zip php-curl unzip -y

2. Composer

Composer es el administrador de dependencias de PHP (como npm para Node.js).

Instalalo con:

sudo apt install composer -y


Verificá la instalación:

composer -V

3. Git (opcional)

Si vas a clonar el proyecto desde GitHub:

sudo apt install git -y

⚙️ Clonar el proyecto

Cloná el repositorio desde GitHub (reemplazá la URL con la tuya):

git clone https://github.com/usuario/nombre-del-proyecto.git


Entrá al directorio del proyecto:

cd nombre-del-proyecto

📦 Instalar dependencias

Instalá las dependencias de Laravel:

composer install 


Si el proyecto incluye un archivo .env.example, copialo para crear el archivo de entorno:

cp .env.example .env ▶️ Ejecutar el servidor de desarrollo

Iniciá el servidor local de Laravel con:

php artisan serve


Luego abrí en tu navegador la dirección que aparece, normalmente:

http://127.0.0.1:8000  


▶️ Ejecutar el servidor de desarrollo

Iniciá el servidor local de Laravel con:

php artisan serve


Luego abrí en tu navegador la dirección que aparece, normalmente:

http://127.0.0.1:8000

| Acción                | Comando                    |
| --------------------- | -------------------------- |
| Instalar dependencias | `composer install`         |
| Limpiar cachés        | `php artisan cache:clear`  |
| Crear clave de app    | `php artisan key:generate` |
| Ejecutar servidor     | `php artisan serve`        |
| Ver rutas             | `php artisan route:list`   |


