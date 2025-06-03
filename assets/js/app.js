// Stimulus
import "../bootstrap.js";

// Tabler
import * as bootstrap from "@tabler/core";
window.bootstrap = bootstrap;
import "@tabler/core/dist/css/tabler.min.css";
import "@tabler/core/dist/css/tabler-flags.min.css";

// DirtyForm
import DirtyForm from "dirty-form";
window.DirtyForm = DirtyForm;

// SweetAlert2
import _swal from "sweetalert2";
const Swal = _swal.mixin({
	customClass: {
		confirmButton: "btn btn-primary",
		denyButton: "btn btn-danger",
		cancelButton: "btn btn-secondary",
	},
	buttonsStyling: false,
});
window.Swal = Swal;
const Toast = Swal.mixin({
	toast: true,
	position: "bottom-end",
	showConfirmButton: false,
	timer: 3000,
	timerProgressBar: true,
	didOpen: (toast) => {
		toast.onmouseenter = Swal.stopTimer;
		toast.onmouseleave = Swal.resumeTimer;
	},
});
window.Toast = Toast;
import "sweetalert2/dist/sweetalert2.min.css";

// Moment
import moment from 'moment/min/moment-with-locales.min.js';
window.moment = moment;

// HierarchyFields
import "./fields/hierarchyFields.js";

// TomSelect
import Autocomplete from "./fields/autocomplete.js";
window.Autocomplete = Autocomplete;
import "tom-select/dist/css/tom-select.bootstrap5.css";

// Tabler Vendors
import "@tabler/core/dist/css/tabler-vendors.min.css";

import App from "./controllers/app.js";
window.App = App;

document.addEventListener("DOMContentLoaded", function () {
	App.setMomentLocale();
	App.createAutoCompleteFields();
	App.persistSelectedTab();
	App.createUnsavedFormChangesWarning();
	App.createFieldsWithErrors();
	App.setTabAsActive();
	App.preventMultipleFormSubmission();
});

document.addEventListener("ea.collection.item-added", () => {
	App.createAutoCompleteFields();
});

// CSS
import "../css/app.css";
