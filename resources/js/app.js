// resources/js/app.js

import './bootstrap';
import '../css/app.css';
import Swal from 'sweetalert2';
window.Swal = Swal;

// Import your custom helpers & expose them globally
import { Toast, Alert, success, error } from './helpers/sweetalert.js';

window.Toast   = Toast;
window.Alert   = Alert;
window.success = success;
window.error   = error;


import Alpine from 'alpinejs'
window.Alpine = Alpine
Alpine.start()

import ApexCharts from 'apexcharts'
window.ApexCharts = ApexCharts
