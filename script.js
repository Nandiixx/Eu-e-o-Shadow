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

// Gerencia o dropdown do perfil
document.addEventListener('DOMContentLoaded', function() {
    const profileDropdown = document.querySelector('.profile-dropdown');
    const dropdownTrigger = document.querySelector('.dropdown-trigger');
    const dropdownMenu = document.querySelector('.dropdown-menu');
    
    if (dropdownTrigger && dropdownMenu) {
        // Toggle do dropdown ao clicar
        dropdownTrigger.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
        });

        // Fecha o dropdown ao clicar fora
        document.addEventListener('click', function(e) {
            if (!profileDropdown.contains(e.target)) {
                dropdownMenu.classList.remove('show');
            }
        });

        // Fecha o dropdown ao clicar em um item
        dropdownMenu.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('click', function() {
                dropdownMenu.classList.remove('show');
            });
        });
    }
});
