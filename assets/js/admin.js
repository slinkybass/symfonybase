// Mark.js
import Mark from "mark.js";
window.Mark = Mark;

import Admin from "./controllers/admin.js";
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
