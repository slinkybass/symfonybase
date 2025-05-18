import Admin from "./admin.controller.js";

document.addEventListener("DOMContentLoaded", function () {
	Admin.createFilters();
	Admin.createBatchActions();
	Admin.createModalWindowsForDeleteActions();
});

// CSS
import "../css/admin.css";
