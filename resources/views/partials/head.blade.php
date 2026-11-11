<script>
    (() => {
        try {
            const accent = localStorage.getItem('reporter-accent');

            if (!/^#[0-9a-f]{6}$/i.test(accent ?? '')) {
                return;
            }

            const [r, g, b] = accent.match(/[0-9a-f]{2}/gi)
                .map(value => parseInt(value, 16) / 255);

            const linearize = value =>
                value <= 0.04045 ?
                value / 12.92 :
                Math.pow((value + 0.055) / 1.055, 2.4);

            const luminance =
                0.2126 * linearize(r) +
                0.7152 * linearize(g) +
                0.0722 * linearize(b);

            const whiteContrast = 1.05 / (luminance + 0.05);
            const darkContrast = (luminance + 0.05) / 0.05;

            const contrast = whiteContrast >= darkContrast ?
                '#ffffff' :
                '#111827';

            document.documentElement.style.setProperty(
                '--reporter-accent',
                accent
            );

            document.documentElement.style.setProperty(
                '--reporter-accent-contrast',
                contrast
            );
        } catch (error) {
            // Se conservan los colores predeterminados.
        }
    })();
</script>

<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ filled($title ?? null) ? __($title) . ' - ' . config('app.name', 'Reporte') : config('app.name', 'Reporte') }}
</title>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
