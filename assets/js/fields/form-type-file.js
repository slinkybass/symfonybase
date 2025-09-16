/**
 * File field
 *
 * Autor: slinkybass
 * Version: 3.0
 */

(function () {
	document.addEventListener("DOMContentLoaded", () => {
		formTypeFile();
	});

	document.addEventListener("ea.collection.item-added", () => {
		formTypeFile();
	});

	window.formTypeFile = function formTypeFile(selector = '[data-file-field="true"]') {
		document.querySelectorAll(selector).forEach((e) => {
			const input = e.querySelector('input[type="file"]');
			const deleteCheckbox = e.querySelector('input[type="checkbox"][id$="_delete"]');
			const deleteBtn = e.querySelector('.file-delete-btn');
			const previewFiles = e.querySelector('.img-preview');

			deleteBtn?.addEventListener('click', () => {
				deleteCheckbox.checked = true;
				input.title = '';
				previewFiles.remove();
				deleteBtn.remove();
			});
		});
	};
})();
