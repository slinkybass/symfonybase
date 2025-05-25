// Stimulus
import "../bootstrap.js";

// Tabler
import * as bootstrap from "@tabler/core";
window.bootstrap = bootstrap;
import "@tabler/core/dist/css/tabler.min.css";
import "@tabler/core/dist/css/tabler-flags.min.css";
import "@tabler/core/dist/css/tabler-vendors.min.css";

// DirtyForm
import DirtyForm from "dirty-form";
window.DirtyForm = DirtyForm;

import App from "./controllers/app.js";
window.App = App;

document.addEventListener("DOMContentLoaded", function () {
	App.persistSelectedTab();
	App.createUnsavedFormChangesWarning();
	App.createFieldsWithErrors();
	App.setTabAsActive();
	App.preventMultipleFormSubmission();
});

// CSS
import "../css/app.css";
