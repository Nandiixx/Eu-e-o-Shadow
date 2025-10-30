// Ativa estado de carregamento no botão ao enviar o formulário de login
(function () {
	var form = document.querySelector('.auth-form');
	if (!form) return;

	var button = form.querySelector('.btn-primary');
	form.addEventListener('submit', function (e) {
		if (!form.checkValidity()) {
			e.preventDefault();
			form.classList.add('was-validated');
			return;
		}
		if (button) {
			button.setAttribute('data-loading', 'true');
			button.setAttribute('aria-busy', 'true');
			button.setAttribute('disabled', 'disabled');
		}
	});
})();
