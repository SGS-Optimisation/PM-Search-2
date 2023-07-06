<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>

    <!-- Scripts -->
    @routes
    @vite(['resources/js/app.ts', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead

    <script type="text/javascript">
        var matomo_settings = <?php
                              echo json_encode([
                                  'matomo_tracking_enabled' => (bool)nova_get_setting('matomo_tracking_enabled', false),
                                  'matomo_host' => nova_get_setting('matomo_host'),
                                  'matomo_site_id' => nova_get_setting('matomo_site_id'),
                              ]); ?>
    </script>

    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
