<?php get_header(); ?>

<!-- ==== PÁGINA DE POSTS (BLOG) ====
     Filtro por busca e categoria (tags).
     Grid e tags são renderizados pelo app.js (renderBlog / renderTagPills).
-->

<main class="main-content">
    <!-- Barra de filtro e busca de posts -->
    
    <section class="filter-section">
        <h1 class="section-title" data-key="todos-posts">Todos os Posts</h1>
        
        <div class="filter-bar">
            <input type="search" id="search-input" placeholder="Buscar por título, tag, ou conteúdo...">
            
            <div class="suggested-tags">
                <span data-key="filtrar-por">Filtrar por:</span>
            </div>
        </div>
    </section>

    <!-- Grid de posts (populado via JS/API) -->
    <section class="post-grid-container">
        <div class="post-grid">
             <p style="text-align: center; width: 100%;" data-key="carregando-projetos">Carregando projetos...</p>
        </div>
    </section>
    
</main> 

<?php get_footer(); ?>