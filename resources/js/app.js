// Importación de librerías
import "../../vendor/power-components/livewire-powergrid/dist/powergrid";
import "../../vendor/masmerise/livewire-toaster/resources/js";

import Chart from "chart.js/auto";
window.Chart = Chart;

import flatpickr from "flatpickr";
import { Spanish } from "flatpickr/dist/l10n/es.js";

window.flatpickr = flatpickr;
flatpickr.localize(Spanish);

/* =========================================================
   REPORTEADOR - RESTAURAR COLOR DE IDENTIDAD
   ========================================================= */

const applyReporterAccent = () => {
    try {
        const accent = localStorage.getItem("reporter-accent");

        if (!/^#[0-9a-f]{6}$/i.test(accent ?? "")) {
            return;
        }

        const [r, g, b] = accent
            .match(/[0-9a-f]{2}/gi)
            .map((value) => parseInt(value, 16) / 255);

        const linearize = (value) =>
            value <= 0.04045
                ? value / 12.92
                : Math.pow((value + 0.055) / 1.055, 2.4);

        const luminance =
            0.2126 * linearize(r) +
            0.7152 * linearize(g) +
            0.0722 * linearize(b);

        const whiteContrast = 1.05 / (luminance + 0.05);
        const darkContrast = (luminance + 0.05) / 0.05;

        const contrast = whiteContrast >= darkContrast ? "#ffffff" : "#111827";

        const root = document.documentElement;

        root.style.setProperty("--reporter-accent", accent);
        root.style.setProperty("--reporter-accent-contrast", contrast);

        root.style.setProperty("--color-accent", accent);
        root.style.setProperty("--color-accent-content", accent);
        root.style.setProperty("--color-accent-foreground", contrast);
    } catch (error) {
        console.error("Error al restaurar la apariencia:", error);
    }
};

// Aplicar al cargar la aplicación
applyReporterAccent();

// Restaurar después de cada navegación Livewire
document.addEventListener("livewire:navigated", applyReporterAccent);
