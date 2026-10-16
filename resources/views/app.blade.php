<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmacia</title>

    <!-- 1. Script oficial de Metabase -->
    <script defer src="http://localhost:3000/app/embed.js"></script>

    <!-- 2. Configuración global requerida por Metabase -->
    <script>
      function defineMetabaseConfig(config) {
        window.metabaseConfig = config;
      }

      defineMetabaseConfig({
        "theme": { "preset": "light" },
        "isGuest": true,
        "instanceUrl": "http://localhost:3000"
      });
    </script>

    @viteReactRefresh
    @vite(['resources/js/app.js'])
</head>
<body>
    <div id="root"></div>
</body>
</html>