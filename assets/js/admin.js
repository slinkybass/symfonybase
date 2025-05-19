// Mark.js
import Mark from "mark.js";
window.Mark = Mark;

import Admin from "./admin.controller.js";
window.Admin = Admin;

document.addEventListener("DOMContentLoaded", function () {
	Admin.removeHashFormUrl();
	Admin.createSearchHighlight();
	Admin.createFilters();
	Admin.createBatchActions();
	Admin.createModalWindowsForDeleteActions();
});

// CSS
import "../css/admin.css";
