# Cómo publicar Cinema World / StreamTec en internet (gratis)

Usaremos:
- **Neon** (https://neon.tech) -> base de datos PostgreSQL gratis y permanente.
- **GitHub** -> para guardar el código.
- **Render** (https://render.com) -> hosting gratis del sitio PHP (vía Docker).

El resultado será una URL pública tipo `https://streamtec.onrender.com` que tu
profesora podrá abrir desde cualquier lugar.

> Nota: el plan gratis de Render "duerme" el servicio tras ~15 minutos sin
> uso. La primera visita después de eso tarda 30-60 segundos en cargar — es
> normal, solo hay que esperar.

---

## 1. Crear la base de datos en Neon

1. Entra a https://neon.tech y crea una cuenta gratis (puedes usar tu cuenta
   de GitHub para registrarte).
2. Crea un nuevo **Project** (por ejemplo, "streamtec").
3. Neon te dará automáticamente una base de datos y un **Connection string**
   parecido a:

   ```
   postgresql://usuario:contraseña@ep-xxxxx.us-east-2.aws.neon.tech/neondb?sslmode=require
   ```

   Guarda estos datos, los necesitarás más adelante. De ahí sacarás:
   - `DB_HOST` -> `ep-xxxxx.us-east-2.aws.neon.tech`
   - `DB_PORT` -> `5432`
   - `DB_NAME` -> `neondb` (o el nombre que Neon te dé)
   - `DB_USER` -> `usuario`
   - `DB_PASS` -> `contraseña`
   - `DB_SSLMODE` -> `require`

4. Abre el **SQL Editor** de Neon (está en el panel izquierdo) y ejecuta, en
   este orden, el contenido de cada archivo de la carpeta `sql/`:

   1. `01_esquema.sql`
   2. `03_vistas.sql`
   3. `04_triggers.sql`
   4. `05_funciones.sql`
   5. `02_datos.sql`

   (Salta `00_usuarios.sql`: en Neon ya tienes un usuario con permisos
   completos, no necesitas crear `app_streamtec`.)

   Puedes copiar y pegar el contenido de cada archivo y darle "Run".

---

## 2. Subir el proyecto a GitHub

1. Entra a https://github.com y crea un **repositorio nuevo**, por ejemplo
   `streamtec`. Puede ser público o privado (si es privado, dale acceso a
   Render más adelante).
2. Sube TODO el contenido de la carpeta `streamtec/` (la que está dentro del
   zip que te compartí) a ese repositorio. Las formas más fáciles:

   - **Opción A (web, sin instalar nada):** En la página del repo, botón
     "Add file" -> "Upload files", arrastra todos los archivos y carpetas
     (`Dockerfile`, `entrypoint.sh`, `includes/`, `public/`, `sql/`, etc.) y
     da "Commit changes".

   - **Opción B (con Git instalado):**
     ```bash
     cd streamtec
     git init
     git add .
     git commit -m "Proyecto StreamTec"
     git branch -M main
     git remote add origin https://github.com/TU_USUARIO/streamtec.git
     git push -u origin main
     ```

---

## 3. Crear el servicio web en Render

1. Entra a https://render.com y crea una cuenta gratis (puedes usar GitHub).
2. Click en **New +** -> **Web Service**.
3. Conecta tu cuenta de GitHub y selecciona el repositorio `streamtec`.
4. Render detectará el `Dockerfile` automáticamente. Configura:
   - **Name**: `streamtec` (o el que quieras; será parte de la URL)
   - **Region**: la más cercana (ej. Oregon u Ohio)
   - **Branch**: `main`
   - **Runtime**: Docker (debería seleccionarse solo)
   - **Instance Type**: Free

5. En la sección **Environment Variables**, agrega las variables de Neon que
   guardaste en el paso 1:

   | Key         | Value                                      |
   |-------------|---------------------------------------------|
   | DB_HOST     | ep-xxxxx.us-east-2.aws.neon.tech            |
   | DB_PORT     | 5432                                         |
   | DB_NAME     | neondb                                       |
   | DB_USER     | tu_usuario_neon                              |
   | DB_PASS     | tu_password_neon                             |
   | DB_SSLMODE  | require                                      |

6. Click **Create Web Service**. Render construirá la imagen Docker y
   desplegará el sitio (puede tardar unos minutos la primera vez).

7. Cuando termine, Render te dará una URL pública, por ejemplo:

   ```
   https://streamtec.onrender.com
   ```

   Esa es la URL que le compartes a tu profesora. La portada
   (`index.html`) se abre en `https://streamtec.onrender.com/index.html`,
   y Render normalmente también la sirve en la raíz `/`.

---

## 4. Verificar que todo funcione

1. Abre la URL pública.
2. Ve a "Registrarme" y crea una cuenta de prueba (o usa
   `norma@correo.com` / `123456`, ya cargada por `02_datos.sql`).
3. Verifica que:
   - Se ven las películas/series/documentales con sus imágenes.
   - Los tráilers se reproducen al dar clic en "▶ Reproducir".
   - "+ Mi Lista" agrega/quita contenido sin recargar la página.
   - El panel "Administración" permite crear, editar y eliminar
     usuarios, directores, contenido e historial (todo vía AJAX).

Si algo falla, revisa los **Logs** de Render (pestaña "Logs" del servicio):
ahí aparecerán los errores de PHP o de conexión a la base de datos.

---

## 5. Actualizar el sitio después de cambios

Cada vez que quieras subir cambios:

```bash
git add .
git commit -m "Descripción del cambio"
git push
```

Render detecta el push y vuelve a desplegar automáticamente.
