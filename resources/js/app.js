// Importación de librerías
import "../../vendor/power-components/livewire-powergrid/dist/powergrid";
import "../../vendor/masmerise/livewire-toaster/resources/js";

import Chart from "chart.js/auto";
window.Chart = Chart;

import flatpickr from "flatpickr";
import { Spanish } from "flatpickr/dist/l10n/es.js";

window.flatpickr = flatpickr;
flatpickr.localize(Spanish);
