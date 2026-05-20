</main> 
    </div>
    <!-- Backdrop para fechar o painel de configuracoes -->
    <div id="settings-backdrop"></div>
    <!-- Painel lateral de configuracoes (idioma/tema/cor) -->
    <aside id="settings-sidebar">
        <div class="sidebar-header">
            <h3 data-key="configuracoes">Configurações</h3>
            <button id="close-settings-btn">&times;</button>
        </div>
        <div class="sidebar-content">
            <div class="setting-group">
                <h4 data-key="idioma">Idioma</h4>
                <div class="button-group" style="gap: 0.5rem;">
                    <button id="set-lang-pt">PT-BR</button>
                    <button id="set-lang-en">EN-US</button>
                </div>
            </div>
            <hr class="divider" style="margin: 2rem 0; border: 0; height: 1px; background-color: var(--color-accent); opacity: 0.3;">
            <div class="setting-group">
                <h4 data-key="tema">Tema</h4>
                <div class="theme-picker">
                    <button id="set-theme-light" aria-label="Tema claro"><svg class="icon-svg" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" role="img" aria-hidden="true"><path d="M6.76 4.84l-1.8-1.79-1.41 1.41 1.79 1.79zM4 10.5H1v3h3zm9-9.95h-3v3.95h3zm7.45 3.91l-1.41-1.41-1.79 1.79 1.41 1.41zm-3.21 13.7l1.79 1.8 1.41-1.41-1.8-1.79zM20 10.5v3h3v-3zm-8-5c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6zm-1 16.95h3v-3.95h-3zm-7.45-3.91l1.41 1.41 1.79-1.8-1.41-1.41z"/></svg></button>
                    <button id="set-theme-dark" aria-label="Tema escuro"><svg class="icon-svg" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" role="img" aria-hidden="true"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg></button>
                </div>
            </div>
            <hr class="divider" style="margin: 2rem 0; border: 0; height: 1px; background-color: var(--color-accent); opacity: 0.3;">
            <div class="setting-group">
                <h4 data-key="cor-destaque">Cor de Destaque</h4>
                <div class="color-picker" style="flex-wrap: wrap;">
                    <button class="color-dot blue" id="set-color-blue"></button>
                    <button class="color-dot purple" id="set-color-purple"></button>
                    <button class="color-dot red" id="set-color-red"></button>
                    <button class="color-dot yellow" id="set-color-yellow"></button>
                    <button class="color-dot green" id="set-color-green"></button>
                </div>
            </div>
        </div>
    </aside>

    <!-- Rodapé com direitos reservados -->
    <footer class="site-footer">
        <div class="footer-copyright">
            <p>&copy; <?php echo date( 'Y' ); ?> Odyssee — Creative Experience. Todos os direitos reservados.</p>
            <p class="footer-author">Desenvolvido por <strong>Renato Harley Paiva</strong></p>
            <p class="footer-policy" style="margin-top: 10px; font-size: 0.85em;">
                <a href="<?php echo esc_url( home_url( '/politica-de-privacidade/' ) ); ?>" style="color: var(--color-accent); text-decoration: none; font-weight: 500;">Política de Privacidade</a>
            </p>
        </div>
    </footer>

    <!-- Banner de Cookies LGPD -->
    <div id="lgpd-cookie-banner" class="cookie-banner-overlay">
        <div class="cookie-banner-header">
            <svg class="icon-svg" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" role="img" aria-hidden="true">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 16h-2v-2h2v2zm0-4h-2V7h2v7z"/>
            </svg>
            <h3>Sua Privacidade</h3>
        </div>
        <div class="cookie-banner-content">
            <p>
                Utilizamos cookies apenas para métricas de acesso (Google Site Kit) e para lembrar suas preferências de tema e idioma. Não armazenamos informações pessoais e não possuímos sistema de login. Saiba mais na nossa <a href="<?php echo esc_url( home_url( '/politica-de-privacidade/' ) ); ?>">Política de Privacidade</a>.
            </p>
        </div>
        <div class="cookie-banner-actions">
            <button id="cookie-btn-reject" class="cookie-btn-reject">Rejeitar</button>
            <button id="cookie-btn-accept" class="cookie-btn-accept">Aceitar</button>
        </div>
    </div>

    <!-- Hooks do WordPress (scripts e rodape) -->
    <?php wp_footer(); ?>
</body>
</html>