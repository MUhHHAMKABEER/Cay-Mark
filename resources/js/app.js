import './bootstrap';

import Alpine from 'alpinejs';
import Shepherd from 'shepherd.js';
import 'shepherd.js/dist/css/shepherd.css';

window.Alpine = Alpine;
window.Shepherd = Shepherd; // make Shepherd available globally

Alpine.start();

