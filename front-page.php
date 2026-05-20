<?php get_header(); ?>

<!-- ==== HOME PAGE ====
     Estrutura: hero imersivo com gradientes → seções de categoria
     Cada categoria tem: banner, carrossel de posts e botões de ação.
     Carrosseis e cards são populados pelo app.js via REST API.
-->

<!-- Home: hero com fundo de gradientes animados -->

    <div class="immersive-wrapper">
        <!-- Camada de gradientes/bolas (apenas home) -->
        <div class="gradients-container">
            <div class="g1"></div><div class="g2"></div><div class="g3"></div><div class="g4"></div><div class="g5"></div>
        </div>
        <div class="gradient-fade-bottom"></div>

        <!-- Hero principal (slogan e seta) -->
        <section id="home" class="hero-section">
            <div class="hero-content-centered">
                <img src="<?php echo esc_url( 'https://odysseexp.com/wp-content/uploads/2025/08/logo-colorful-reduced-scaled-e1756493913129.png' ); ?>" class="hero-symbol" alt="Símbolo">
                <h1 class="hero-slogan" data-key="hero-slogan">Experience the Next Level of Creativity</h1>
                <a href="#recentes" class="scroll-indicator"><i class="fas fa-chevron-down"></i></a>
            </div>
        </section>

        <!-- Sessao de trabalhos recentes -->
        <section id="recentes" class="portfolio-section transparent-bg">
            <h2 class="section-title" data-key="recentes">Trabalhos Recentes</h2>
            <div class="carousel-wrapper" id="carousel-recentes"></div>
        </section>
    </div> 

        <!-- Categoria: Design Grafico -->
        <section id="design-grafico" class="category-section">
            <h2 class="section-title" data-key="design-grafico">Design Gráfico</h2>
            
            <!-- Banner Ilustrativo que muda com o tema -->
            <img src="" class="category-banner" id="banner-design-grafico" alt="Design Gráfico" data-category="design-grafico">
            
         <div class="portfolio-showcase">
             <div class="carousel-wrapper" id="carousel-design-grafico"></div>
         </div>
         
         <!-- Botões de ação da categoria -->
         <div class="category-buttons">
             <div class="category-image-placeholder" id="design-placeholder">
                 <i class="fas fa-palette"></i>
                 <span class="placeholder-text" data-key="ver-servicos">Ver Serviços e Preços</span>
             </div>
             <a href="/posts/?categoria=design-grafico" class="category-posts-link">
                 <i class="fas fa-th-large"></i>
                 <span>Ver todos os posts de Design Gráfico</span>
             </a>
         </div>
    </section>

    <!-- Categoria: Edicao de Video -->
    <section id="edicao-video" class="category-section">
        <h2 class="section-title" data-key="edicao-video">Edição de Vídeo</h2>
        
        <!-- Banner Ilustrativo que muda com o tema -->
        <img src="" class="category-banner" id="banner-edicao-video" alt="Edição de Vídeo" data-category="edicao-video">
        
        <div class="portfolio-showcase">
             <div class="carousel-wrapper" id="carousel-edicao-video"></div>
        </div>
        
        <!-- Botões de ação da categoria -->
        <div class="category-buttons">
            <div class="category-image-placeholder" id="video-placeholder">
                <i class="fas fa-film"></i>
                <span class="placeholder-text" data-key="ver-servicos">Ver Serviços e Preços</span>
            </div>
            <a href="/posts/?categoria=edicao-de-video" class="category-posts-link">
                <i class="fas fa-th-large"></i>
                <span>Ver todos os posts de Edição de Vídeo</span>
            </a>
        </div>
    </section>

    <!-- Categoria: Motion -->
    <section id="motion" class="category-section">
        <h2 class="section-title" data-key="motion">Motion</h2>
        
        <!-- Banner Ilustrativo que muda com o tema -->
        <!-- PLACEHOLDER: Adicione as URLs das imagens para cada tema -->
        <img src="" class="category-banner" id="banner-motion" alt="Motion" data-category="motion">
        
        <div class="portfolio-showcase">
           <div class="carousel-wrapper" id="carousel-motion"></div>
        </div>
        
        <!-- Botões de ação da categoria -->
        <div class="category-buttons">
            <div class="category-image-placeholder" id="motion-placeholder">
                <i class="fas fa-play-circle"></i>
                <span class="placeholder-text" data-key="ver-servicos">Ver Serviços e Preços</span>
            </div>
            <a href="/posts/?categoria=motion" class="category-posts-link">
                <i class="fas fa-th-large"></i>
                <span>Ver todos os posts de Motion</span>
            </a>
        </div>
    </section>

    <!-- Categoria: Ilustracao Digital -->
    <section id="ilustracao" class="category-section">
        <h2 class="section-title" data-key="ilustracao">Ilustração Digital</h2>
        
        <!-- Banner Ilustrativo que muda com o tema -->
        <!-- PLACEHOLDER: Adicione as URLs das imagens para cada tema -->
        <img src="" class="category-banner" id="banner-ilustracao" alt="Ilustração Digital" data-category="ilustracao">
        
        <div class="portfolio-showcase">
           <div class="carousel-wrapper" id="carousel-ilustracao"></div>
        </div>
        
        <!-- Botões de ação da categoria -->
        <div class="category-buttons">
            <div class="category-image-placeholder" id="ilustracao-placeholder">
                <i class="fas fa-paint-brush"></i>
                <span class="placeholder-text" data-key="ver-servicos">Ver Serviços e Preços</span>
            </div>
            <a href="/posts/?categoria=ilustracao-digital" class="category-posts-link">
                <i class="fas fa-th-large"></i>
                <span>Ver todos os posts de Ilustração Digital</span>
            </a>
        </div>
    </section>

    <!-- Categoria: Impressos -->
    <section id="impressos" class="category-section">
        <h2 class="section-title" data-key="impressos">Impressos</h2>
        
        <!-- Banner Ilustrativo que muda com o tema -->
        <!-- PLACEHOLDER: Adicione as URLs das imagens para cada tema -->
        <img src="" class="category-banner" id="banner-impressos" alt="Impressos" data-category="impressos">
        
        <div class="portfolio-showcase">
            <div class="carousel-wrapper" id="carousel-impressos"></div>
        </div>
        
        <!-- Botão de posts da categoria -->
        <div class="category-buttons">
            <a href="/posts/?categoria=impressos" class="category-posts-link">
                <i class="fas fa-th-large"></i>
                <span>Ver todos os posts de Impressos</span>
            </a>
        </div>
    </section>

    <!-- Rodape da home com CTA de contato -->
    <section id="contato" class="footer-section">
        <h2 data-key="contato">Dúvidas?</h2>
        <button class="btn-primary" data-key="fale-whatsapp">Fale Comigo no WhatsApp</button>
    </section>

<?php get_footer(); ?>