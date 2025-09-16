/**
 * Signature pad field
 *
 * Autor: slinkybass
 * Version: 3.0
 */

import SignaturePad from "signature_pad";

(function () {
	document.addEventListener("DOMContentLoaded", () => {
		formTypeSignature();
	});
	document.addEventListener("ea.collection.item-added", () => {
		formTypeSignature();
	});

	window.formTypeSignature = function formTypeSignature(selector = '[data-signature-field="true"]') {
		document.querySelectorAll(selector).forEach((e) => {
			const canvas = e.parentNode.querySelector(".signature-pad-wrapper canvas");
			if (!canvas) return;
			const currentSignValue = e.value;
			const canvasActions = e.parentNode.querySelector(".signature-pad-actions");

			const signaturePad = new SignaturePad(canvas);
			const showInput = e.hasAttribute("data-signature-show-input") ? e.getAttribute("data-signature-show-input") !== "false" : false;
			const showUndo = e.hasAttribute("data-signature-show-undo") ? e.getAttribute("data-signature-show-undo") !== "false" : true;
			const showClear = e.hasAttribute("data-signature-show-clear") ? e.getAttribute("data-signature-show-clear") !== "false" : true;

			if (!showInput) e.classList.add("d-none");

			resizeCanvas(e, signaturePad);
			window.addEventListener("resize", () => resizeCanvas(e, signaturePad));

			signaturePad.addEventListener("afterUpdateStroke", () => setValue(e, signaturePad));

			if (canvasActions) {
				const undoBtn = canvasActions.querySelector("[data-action='undo']");
				const clearBtn = canvasActions.querySelector("[data-action='clear']");
				if (undoBtn) {
					undoBtn.classList.toggle("d-none", !showUndo);
					setTimeout(() => {
						undoBtn.disabled = true;
						signaturePad.addEventListener("afterUpdateStroke", () => undoBtn.disabled = signaturePad.isEmpty());
						undoBtn.addEventListener("click", () => {
							const data = signaturePad.toData();
							if (!data) return;
							data.pop();
							signaturePad.fromData(data);
							undoBtn.disabled = signaturePad.isEmpty();
							if (signaturePad.isEmpty()) {
								e.value = currentSignValue;
								setSignature(e, signaturePad);
							} else {
								setValue(e, signaturePad);
							}
							if (clearBtn) clearBtn.disabled = !e.value;
						});
					}, 100);
				}
				if (clearBtn) {
					clearBtn.classList.toggle("d-none", !showClear);
					setTimeout(() => {
						clearBtn.disabled = signaturePad.isEmpty();
						signaturePad.addEventListener("afterUpdateStroke", () => clearBtn.disabled = signaturePad.isEmpty());
						clearBtn.addEventListener("click", () => {
							clearBtn.disabled = true;
							if (undoBtn) undoBtn.disabled = false;
							signaturePad.clear();
							e.value = null;
						});
					}, 100);
				}
			}
		});

		function resizeCanvas(e, signaturePad) {
			const ratio = Math.max(window.devicePixelRatio || 1, 1);
			const canvas = signaturePad.canvas;

			canvas.width = canvas.offsetWidth * ratio;
			canvas.height = canvas.offsetHeight * ratio;
			canvas.getContext("2d").scale(ratio, ratio);

			setSignature(e, signaturePad);
		}

		function setSignature(e, signaturePad) {
			const currentSignImg = new Image();
			currentSignImg.onload = function() {
				const currentSignImgW = currentSignImg.width;
				const currentSignImgH = currentSignImg.height;
				signaturePad.fromDataURL(e.value, {
					width: currentSignImgW,
					height: currentSignImgH,
					xOffset: (signaturePad.canvas.width - currentSignImgW) / 2,
					yOffset: (signaturePad.canvas.height - currentSignImgH) / 2
				});
			};
			currentSignImg.src = e.value;
		}

		function setValue(e, signaturePad) {
			if (signaturePad.isEmpty()) {
				e.value = null;
			} else {
				const dataUrl = signaturePad.toDataURL("image/png");
				cropDataURL(dataUrl)
					.then((newDataUrl) => {
						e.value = newDataUrl;
					})
					.catch((error) => {
						e.value = null;
					});
			}
		}

		function cropDataURL(dataUrl) {
			return new Promise((resolve, reject) => {
				const img = new Image();
				img.onload = function () {
					const canvas = document.createElement("canvas");
					const ctx = canvas.getContext("2d");
					canvas.width = img.width;
					canvas.height = img.height;
					ctx.drawImage(img, 0, 0);

					const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
					const pixels = imageData.data;
					let minX = canvas.width;
					let minY = canvas.height;
					let maxX = 0;
					let maxY = 0;

					for (let y = 0; y < canvas.height; y++) {
						for (let x = 0; x < canvas.width; x++) {
							const index = (y * canvas.width + x) * 4;
							const a = pixels[index + 3];
							if (a !== 0) {
								minX = Math.min(minX, x);
								minY = Math.min(minY, y);
								maxX = Math.max(maxX, x);
								maxY = Math.max(maxY, y);
							}
						}
					}

					const width = maxX - minX + 1;
					const height = maxY - minY + 1;
					const padding = 15;
					const newCanvas = document.createElement("canvas");
					const newCtx = newCanvas.getContext("2d");
					newCanvas.width = width + 2 * padding;
					newCanvas.height = height + 2 * padding;
					newCtx.clearRect(0, 0, newCanvas.width, newCanvas.height);
					newCtx.drawImage(canvas, minX, minY, width, height, padding, padding, width, height);

					resolve(minY < maxY ? newCanvas.toDataURL("image/png") : null);
				};
				img.onerror = reject;
				img.src = dataUrl;
			});
		}
	};
})();
