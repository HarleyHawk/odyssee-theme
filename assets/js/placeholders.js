/**
 * Placeholders da página principal (home)
 * Torna os blocos .category-image-placeholder clicáveis,
 * redirecionando para /servicos. Inclui efeito de hover com scale.
 */
document.addEventListener('DOMContentLoaded', function() {
    const placeholders = document.querySelectorAll('.category-image-placeholder');
    
    placeholders.forEach(placeholder => {
        placeholder.style.cursor = 'pointer';
        // Clique leva para a pagina de servicos
        placeholder.addEventListener('click', function() {
            // Redireciona para a pagina de servicos
            const baseUrl = window.location.origin;
            window.location.href = baseUrl + '/servicos';
        });
        
        // Efeito de hover simples
        placeholder.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.02)';
            this.style.transition = 'transform 0.3s ease';
        });
        
        placeholder.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
});
