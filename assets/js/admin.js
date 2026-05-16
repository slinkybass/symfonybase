import Admin from "./page/admin.js";
window.Admin = Admin;

document.addEventListener("DOMContentLoaded", () => {
    Admin.removeHashFromUrl();
    Admin.createSearchHighlight();
    Admin.createFilters();
    Admin.createBatchActions();
    Admin.createActionConfirmationModals();
    Admin.createDefaultRowAction();
    Admin.createActionHandlers();
});

import "../css/admin.css";
