/**
 * Password Visibility & Generator Plugin
 *
 * Autor: slinkybass
 * Version: 3.0
 *
 * Description:
 * Manages password field visibility and provides secure password generation.
 */

(function () {
	document.addEventListener("DOMContentLoaded", () => {
		passwordEvents();
	});

	document.addEventListener("ea.collection.item-added", () => {
		passwordEvents();
	});

	/**
	 * Initializes password show/hide and random password generation functionality.
	 *
	 * Called on DOMContentLoaded and ea.collection.item-added.
	 */
	function passwordEvents() {
		document.querySelectorAll(".btn-pass").forEach((btn) => {
			btn.addEventListener("click", () => {
				const input = btn.closest(".input-group").querySelector("input");
				const input2 = document.getElementById(input.id.replace("first", "second"));
				const isPassword = input.type === "password";

				switchVisibility(input, isPassword);
				if (input2) switchVisibility(input2, isPassword);
			});
		});

		document.querySelectorAll(".btn-pass-generator").forEach((btn) => {
			btn.addEventListener("click", () => {
				const input = btn.closest(".row").querySelector("input");
				const input2 = document.getElementById(input.id.replace("first", "second"));
				const minLength = parseInt(input.getAttribute("minlength")) || parseInt(input2?.getAttribute("minlength")) || undefined;
				const maxLength = parseInt(input.getAttribute("maxlength")) || parseInt(input2?.getAttribute("maxlength")) || undefined;
				const passwordLength = minLength && maxLength ? Math.floor(Math.random() * (maxLength - minLength + 1)) + minLength : undefined;
				const newPassword = generatePassword(passwordLength);

				input.value = newPassword;
				switchVisibility(input);

				if (input2) {
					input2.value = newPassword;
					switchVisibility(input2);
				}
			});
		});
	}

	/**
	 * Toggles the input field type between "text" and "password" and switches visibility icons.
	 *
	 * @param {HTMLInputElement} input - The password input field.
	 * @param {boolean} show - `true` to show, `false` to hide.
	 */
	function switchVisibility(input, show = true) {
		const group = input.closest(".input-group");
		const btnShow = group?.querySelector(".btn-pass .btn-pass-show");
		const btnHide = group?.querySelector(".btn-pass .btn-pass-hide");

		if (btnShow && btnHide) {
			btnShow.classList.toggle("d-none", show);
			btnHide.classList.toggle("d-none", !show);
		}

		input.type = show ? "text" : "password";
	}

	/**
	 * Generates a random password with uppercase, lowercase, digits, and special characters.
	 *
	 * @param {number} length - Desired password length.
	 * @returns {string} Generated password.
	 */
	function generatePassword(length = 8) {
		const uppercase = "ABCDEFGHJKLMNPQRSTUVWXYZ";
		const lowercase = "abcdefghijkmnpqrstuvwxyz";
		const numbers = "23456789";
		const specials = "!@#$%&_";
		const all = uppercase + lowercase + numbers;

		let password = "";
		password += specials.pick(1);
		password += lowercase.pick(1);
		password += uppercase.pick(1);
		password += numbers.pick(1);
		if (length > password.length) {
			password += all.pick(length - password.length);
		}
		return password.shuffle();
	}

	/**
	 * String extension: pick(n) selects `n` random characters from the string.
	 */
	String.prototype.pick = function (n) {
		var chars = "";
		for (var i = 0; i < n; i++) {
			chars += this.charAt(Math.floor(Math.random() * this.length));
		}
		return chars;
	};

	/**
	 * String extension: shuffle() randomly shuffles the characters of the string.
	 */
	String.prototype.shuffle = function () {
		var array = this.split("");
		var tmp,
			current,
			top = array.length;
		if (top)
			while (--top) {
				current = Math.floor(Math.random() * (top + 1));
				tmp = array[current];
				array[current] = array[top];
				array[top] = tmp;
			}
		return array.join("");
	};
})();
