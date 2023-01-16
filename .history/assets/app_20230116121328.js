/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import bootstrap from "bootstrap";
window.bootstrap = require('bootstrap/dist/js/bootstrap.bundle.js');
import bsCustomFileInput from 'bs-custom-file-input';
import './navbar-mobile';
import '@grafikart/drop-files-element';

// start the Stimulus application
import './bootstrap';
