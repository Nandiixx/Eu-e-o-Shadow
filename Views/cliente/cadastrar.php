<!DOCTYPE html>
<html lang="pt-BR">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>MyBeauty - Registre-se</title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="../../style.css">
	<script defer src="../../script.js"></script>
</head>
<body>
	<main class="auth-bg register-page">
		<section class="auth-wrapper">
			<div class="auth-card">
				<div class="auth-brand">
					<div class="brand-logo" aria-hidden="true">
						<svg width="36" height="36" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M12 3c2.8 0 5 2.2 5 5 0 1.6-.8 3.1-2 4 2.8.4 5 2.8 5 5.7V19c0 1.1-.9 2-2 2H6c-1.1 0-2-.9-2-2v-1.3c0-2.9 2.2-5.3 5-5.7-1.2-.9-2-2.4-2-4 0-2.8 2.2-5 5-5Z" fill="currentColor"/>
						</svg>
					</div>
					<div>
						<h1 class="brand-title">Registre-se</h1>
						<p class="brand-subtitle">Crie sua conta para começar</p>
					</div>
				</div>

				<form class="auth-form form-grid-2" action="../../registrar.php" method="POST" novalidate>
					<div class="input-field">
						<span class="input-icon" aria-hidden="true">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M12 12c2.761 0 5-2.239 5-5s-2.239-5-5-5-5 2.239-5 5 2.239 5 5 5Zm0 2c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5Z" fill="currentColor"/>
							</svg>
						</span>
						<input type="text" id="nome" name="nome" placeholder="Nome completo" required aria-label="Nome completo">
					</div>

					<div class="input-field">
						<span class="input-icon" aria-hidden="true">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M20 4H4c-1.1 0-2 .9-2 2v12a2 2 0 002 2h16a2 2 0 002-2V6a2 2 0 00-2-2Zm0 4-8 5-8-5V6l8 5 8-5v2Z" fill="currentColor"/>
							</svg>
						</span>
						<input type="email" id="email" name="email" placeholder="E-mail" required aria-label="E-mail">
					</div>

					<div class="input-field">
						<span class="input-icon" aria-hidden="true">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M3 5h18v2H3V5Zm0 6h18v2H3v-2Zm0 6h18v2H3v-2Z" fill="currentColor"/>
							</svg>
						</span>
						<input type="tel" id="numero" name="numero" placeholder="Número" required aria-label="Número">
					</div>

					<div class="input-field">
						<span class="input-icon" aria-hidden="true">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M7 2h10a2 2 0 012 2v16a2 2 0 01-2 2H7a2 2 0 01-2-2V4a2 2 0 012-2Zm8 6h2v2h-2V8Zm0 4h2v2h-2v-2Z" fill="currentColor"/>
							</svg>
						</span>
						<input type="date" id="data_nascimento" name="data_nascimento" placeholder="Data de nascimento" required aria-label="Data de nascimento">
					</div>

					<div class="input-field">
						<span class="input-icon" aria-hidden="true">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M17 8h-1V6a4 4 0 10-8 0v2H7a2 2 0 00-2 2v8a2 2 0 002 2h10a2 2 0 002-2v-8a2 2 0 00-2-2Zm-7-2a2 2 0 114 0v2h-4V6Z" fill="currentColor"/>
							</svg>
						</span>
						<input type="password" id="senha" name="senha" placeholder="Senha" required aria-label="Senha">
					</div>

					<div class="input-field">
						<span class="input-icon" aria-hidden="true">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M17 8h-1V6a4 4 0 10-8 0v2H7a2 2 0 00-2 2v8a2 2 0 002 2h10a2 2 0 002-2v-8a2 2 0 00-2-2Z" fill="currentColor"/>
							</svg>
						</span>
						<input type="password" id="confirma_senha" name="confirma_senha" placeholder="Confirmar senha" required aria-label="Confirmar senha">
					</div>

					<button type="submit" class="btn-primary" data-loading="false" aria-busy="false">
						<span class="btn-label">Criar conta</span>
						<span class="btn-spinner" aria-hidden="true"></span>
					</button>
				</form>

				<div class="auth-actions">
					<span class="link-muted">Já tem uma conta? </span><a class="auth-login-link" href="../../Index.php"><strong>Entrar</strong></a>
				</div>
			</div>
		</section>
	</main>
</body>
</html>
