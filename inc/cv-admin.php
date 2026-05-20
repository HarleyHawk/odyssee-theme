<?php
/**
 * Painel Customizado do WordPress para Gerenciar Página Sobre Mim
 * - Descrição bilíngue (PT-BR / EN-US)
 * - Experiências profissionais com datas
 * - Cursos com datas
 * - Habilidades (com categorias: softwares, criativos, office, OS)
 * - Upload de SVG/PNG para habilidades
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ==============================================
// 1. REGISTRAR PÁGINA DO MENU
// ==============================================
add_action( 'admin_menu', 'odyssee_cv_add_admin_menu' );
function odyssee_cv_add_admin_menu() {
    add_menu_page(
        'Sobre Mim',                          // Título da página
        'Sobre Mim',                          // Título do menu
        'manage_options',                     // Capacidade
        'odyssee-cv-manager',                 // slug
        'odyssee_cv_admin_page',              // Função callback
        'dashicons-id-alt',                   // Ícone
        25                                    // Posição do menu
    );
}

// ==============================================
// 2. ENQUEUE SCRIPTS E STYLES DO ADMIN
// ==============================================
add_action( 'admin_enqueue_scripts', 'odyssee_cv_enqueue_admin_assets' );
function odyssee_cv_enqueue_admin_assets( $hook_suffix ) {
    if ( 'toplevel_page_odyssee-cv-manager' !== $hook_suffix ) {
        return;
    }

    // CSS customizado
    wp_enqueue_style( 
        'odyssee-cv-admin', 
        get_template_directory_uri() . '/inc/cv-admin.css',
        array(),
        '1.0.0'
    );

    // JS para repeater fields
    wp_enqueue_script(
        'odyssee-cv-admin',
        get_template_directory_uri() . '/inc/cv-admin.js',
        array( 'jquery' ),
        '1.0.0',
        true
    );

    // Media uploader
    wp_enqueue_media();

    // Localizar nonce para segurança
    wp_localize_script( 'odyssee-cv-admin', 'odysseeCVAdmin', array(
        'nonce' => wp_create_nonce( 'odyssee_cv_nonce' ),
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
    ) );
}

// ==============================================
// 3. PÁGINA PRINCIPAL DO ADMIN
// ==============================================
function odyssee_cv_admin_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Acesso negado' );
    }

    // Verificar nonce e salvar dados
    if ( isset( $_POST['odyssee_cv_nonce'] ) && wp_verify_nonce( $_POST['odyssee_cv_nonce'], 'odyssee_cv_nonce' ) ) {
        odyssee_cv_save_data();
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php echo esc_html__( 'Dados salvos com sucesso!', 'odyssee' ); ?></p>
        </div>
        <?php
    }

    // Carregar dados atuais
    $cv_data = odyssee_cv_get_data();
    ?>

    <div class="wrap odyssee-cv-admin-wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

        <form method="post" class="odyssee-cv-form">
            <?php wp_nonce_field( 'odyssee_cv_nonce', 'odyssee_cv_nonce' ); ?>

            <!-- Tabs Navigation -->
            <nav class="cv-admin-tabs">
                <button type="button" class="tab-button active" data-tab="descricao">
                    Descrição
                </button>
                <button type="button" class="tab-button" data-tab="experiencias">
                    Experiências
                </button>
                <button type="button" class="tab-button" data-tab="cursos">
                    Cursos
                </button>
                <button type="button" class="tab-button" data-tab="habilidades">
                    Habilidades
                </button>
            </nav>

            <!-- TAB 1: Descrição -->
            <div id="tab-descricao" class="tab-content active">
                <h2>Descrição Bilíngue</h2>
                
                <div class="cv-admin-section">
                    <h3>Português (PT-BR)</h3>
                    
                    <div class="form-group">
                        <label for="desc_pt_1">Descrição 1 (PT-BR)</label>
                        <textarea 
                            id="desc_pt_1" 
                            name="descricao[pt_br][intro_1]" 
                            class="large-text" 
                            rows="4"
                        ><?php echo esc_textarea( $cv_data['descricao']['pt_br']['intro_1'] ?? '' ); ?></textarea>
                        <p class="description">Primeira parte da introdução em português.</p>
                    </div>

                    <div class="form-group">
                        <label for="desc_pt_2">Descrição 2 (PT-BR)</label>
                        <textarea 
                            id="desc_pt_2" 
                            name="descricao[pt_br][intro_2]" 
                            class="large-text" 
                            rows="4"
                        ><?php echo esc_textarea( $cv_data['descricao']['pt_br']['intro_2'] ?? '' ); ?></textarea>
                        <p class="description">Segunda parte da introdução em português.</p>
                    </div>
                </div>

                <div class="cv-admin-section">
                    <h3>English (EN-US)</h3>
                    
                    <div class="form-group">
                        <label for="desc_en_1">Descrição 1 (EN-US)</label>
                        <textarea 
                            id="desc_en_1" 
                            name="descricao[en_us][intro_1]" 
                            class="large-text" 
                            rows="4"
                        ><?php echo esc_textarea( $cv_data['descricao']['en_us']['intro_1'] ?? '' ); ?></textarea>
                        <p class="description">First part of introduction in English.</p>
                    </div>

                    <div class="form-group">
                        <label for="desc_en_2">Descrição 2 (EN-US)</label>
                        <textarea 
                            id="desc_en_2" 
                            name="descricao[en_us][intro_2]" 
                            class="large-text" 
                            rows="4"
                        ><?php echo esc_textarea( $cv_data['descricao']['en_us']['intro_2'] ?? '' ); ?></textarea>
                        <p class="description">Second part of introduction in English.</p>
                    </div>
                </div>
            </div>

            <!-- TAB 2: Experiências Profissionais -->
            <div id="tab-experiencias" class="tab-content">
                <h2>Experiências Profissionais</h2>
                
                <div class="cv-admin-section">
                    <p class="description">Adicione, edite ou remova suas experiências profissionais.</p>
                    
                    <div id="experiencias-repeater" class="repeater-container">
                        <?php 
                        $experiencias = $cv_data['experiencias'] ?? array();
                        if ( empty( $experiencias ) ) {
                            $experiencias = array( array() );
                        }

                        foreach ( $experiencias as $index => $exp ) : 
                        ?>
                            <div class="repeater-item">
                                <div class="repeater-header">
                                    <span class="repeater-title">Experiência <?php echo esc_html( $index + 1 ); ?></span>
                                    <button type="button" class="button button-small remove-repeater">
                                        <?php esc_html_e( 'Remover', 'odyssee' ); ?>
                                    </button>
                                </div>

                                <div class="repeater-fields">
                                    <div class="field-group field-3col">
                                        <div>
                                            <label>Título/Cargo</label>
                                            <input 
                                                type="text" 
                                                name="experiencias[<?php echo esc_attr( $index ); ?>][titulo]" 
                                                value="<?php echo esc_attr( $exp['titulo'] ?? '' ); ?>"
                                                placeholder="ex: Designer Gráfico"
                                            />
                                        </div>

                                        <div>
                                            <label>Empresa</label>
                                            <input 
                                                type="text" 
                                                name="experiencias[<?php echo esc_attr( $index ); ?>][empresa]" 
                                                value="<?php echo esc_attr( $exp['empresa'] ?? '' ); ?>"
                                                placeholder="ex: First Publicidade"
                                            />
                                        </div>

                                        <div>
                                            <label>Período</label>
                                            <input 
                                                type="text" 
                                                name="experiencias[<?php echo esc_attr( $index ); ?>][periodo]" 
                                                value="<?php echo esc_attr( $exp['periodo'] ?? '' ); ?>"
                                                placeholder="ex: 2023 - 2024"
                                            />
                                        </div>
                                    </div>

                                    <div class="field-group">
                                        <label>Descrição (PT-BR)</label>
                                        <textarea 
                                            name="experiencias[<?php echo esc_attr( $index ); ?>][descricao_pt]" 
                                            rows="3"
                                            placeholder="Descreva as atividades em português"
                                        ><?php echo esc_textarea( $exp['descricao_pt'] ?? '' ); ?></textarea>
                                    </div>

                                    <div class="field-group">
                                        <label>Descrição (EN-US)</label>
                                        <textarea 
                                            name="experiencias[<?php echo esc_attr( $index ); ?>][descricao_en]" 
                                            rows="3"
                                            placeholder="Describe activities in English"
                                        ><?php echo esc_textarea( $exp['descricao_en'] ?? '' ); ?></textarea>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <button type="button" class="button button-primary add-repeater" data-repeater="experiencias-repeater" data-template="experiencia-template">
                        <?php esc_html_e( '+ Adicionar Experiência', 'odyssee' ); ?>
                    </button>
                </div>
            </div>

            <!-- TAB 3: Cursos -->
            <div id="tab-cursos" class="tab-content">
                <h2>Cursos e Formação</h2>
                
                <div class="cv-admin-section">
                    <p class="description">Adicione, edite ou remova seus cursos e formações.</p>
                    
                    <div id="cursos-repeater" class="repeater-container">
                        <?php 
                        $cursos = $cv_data['cursos'] ?? array();
                        if ( empty( $cursos ) ) {
                            $cursos = array( array() );
                        }

                        foreach ( $cursos as $index => $curso ) : 
                        ?>
                            <div class="repeater-item">
                                <div class="repeater-header">
                                    <span class="repeater-title">Curso <?php echo esc_html( $index + 1 ); ?></span>
                                    <button type="button" class="button button-small remove-repeater">
                                        <?php esc_html_e( 'Remover', 'odyssee' ); ?>
                                    </button>
                                </div>

                                <div class="repeater-fields">
                                    <div class="field-group field-3col">
                                        <div>
                                            <label>Nome do Curso</label>
                                            <input 
                                                type="text" 
                                                name="cursos[<?php echo esc_attr( $index ); ?>][nome]" 
                                                value="<?php echo esc_attr( $curso['nome'] ?? '' ); ?>"
                                                placeholder="ex: Designer Gráfico"
                                            />
                                        </div>

                                        <div>
                                            <label>Instituição</label>
                                            <input 
                                                type="text" 
                                                name="cursos[<?php echo esc_attr( $index ); ?>][instituicao]" 
                                                value="<?php echo esc_attr( $curso['instituicao'] ?? '' ); ?>"
                                                placeholder="ex: Universidade X"
                                            />
                                        </div>

                                        <div>
                                            <label>Data Final</label>
                                            <input 
                                                type="text" 
                                                name="cursos[<?php echo esc_attr( $index ); ?>][data_final]" 
                                                value="<?php echo esc_attr( $curso['data_final'] ?? '' ); ?>"
                                                placeholder="ex: 2024 ou presente"
                                            />
                                        </div>
                                    </div>

                                    <div class="field-group">
                                        <label>Descrição (PT-BR) <em>(opcional)</em></label>
                                        <textarea 
                                            name="cursos[<?php echo esc_attr( $index ); ?>][descricao_pt]" 
                                            rows="2"
                                            placeholder="Detalhes sobre o curso em português"
                                        ><?php echo esc_textarea( $curso['descricao_pt'] ?? '' ); ?></textarea>
                                    </div>

                                    <div class="field-group">
                                        <label>Descrição (EN-US) <em>(opcional)</em></label>
                                        <textarea 
                                            name="cursos[<?php echo esc_attr( $index ); ?>][descricao_en]" 
                                            rows="2"
                                            placeholder="Course details in English"
                                        ><?php echo esc_textarea( $curso['descricao_en'] ?? '' ); ?></textarea>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <button type="button" class="button button-primary add-repeater" data-repeater="cursos-repeater" data-template="curso-template">
                        <?php esc_html_e( '+ Adicionar Curso', 'odyssee' ); ?>
                    </button>
                </div>
            </div>

            <!-- TAB 4: Habilidades -->
            <div id="tab-habilidades" class="tab-content">
                <h2>Habilidades</h2>
                
                <div class="cv-admin-section">
                    <p class="description">Gerenciar habilidades por categoria: Softwares, Criativos, Office, Programação e Sistemas Operacionais.</p>
                    
                    <?php
                    $categorias = array(
                        'criativos' => 'Softwares Criativos',
                        'office' => 'Office',
                        'programacao' => 'Programação e suas linguagens',
                        'os' => 'Sistemas Operacionais',
                    );

                    $habilidades = $cv_data['habilidades'] ?? array();

                    foreach ( $categorias as $categoria_key => $categoria_label ) : ?>
                        <div class="habilidades-categoria">
                            <h3><?php echo esc_html( $categoria_label ); ?></h3>
                            
                            <div id="habilidades-<?php echo esc_attr( $categoria_key ); ?>-repeater" class="repeater-container">
                                <?php
                                $items = $habilidades[ $categoria_key ] ?? array();
                                if ( empty( $items ) ) {
                                    $items = array( array() );
                                }

                                foreach ( $items as $index => $item ) : 
                                ?>
                                    <div class="repeater-item habilidade-item">
                                        <div class="repeater-header">
                                            <span class="repeater-title">
                                                <?php echo esc_html( $item['nome'] ?? "Habilidade " . ( $index + 1 ) ); ?>
                                            </span>
                                            <button type="button" class="button button-small remove-repeater">
                                                <?php esc_html_e( 'Remover', 'odyssee' ); ?>
                                            </button>
                                        </div>

                                        <div class="repeater-fields">
                                            <div class="field-group field-2col">
                                                <div>
                                                    <label>Nome <em>(opcional)</em></label>
                                                    <input 
                                                        type="text" 
                                                        name="habilidades[<?php echo esc_attr( $categoria_key ); ?>][<?php echo esc_attr( $index ); ?>][nome]" 
                                                        value="<?php echo esc_attr( $item['nome'] ?? '' ); ?>"
                                                        placeholder="ex: Adobe Photoshop"
                                                        class="habilidade-nome-input"
                                                    />
                                                </div>

                                                <div>
                                                    <label>Nível de Proficiência</label>
                                                    <select name="habilidades[<?php echo esc_attr( $categoria_key ); ?>][<?php echo esc_attr( $index ); ?>][nivel]">
                                                        <option value="">-- Selecione --</option>
                                                        <option value="iniciante" <?php selected( $item['nivel'] ?? '', 'iniciante' ); ?>>Iniciante</option>
                                                        <option value="intermediário" <?php selected( $item['nivel'] ?? '', 'intermediário' ); ?>>Intermediário</option>
                                                        <option value="avançado" <?php selected( $item['nivel'] ?? '', 'avançado' ); ?>>Avançado</option>
                                                        <option value="expert" <?php selected( $item['nivel'] ?? '', 'expert' ); ?>>Expert</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="field-group">
                                                <label>Ícone (SVG ou PNG)</label>
                                                <div class="media-upload-group">
                                                    <input 
                                                        type="hidden" 
                                                        name="habilidades[<?php echo esc_attr( $categoria_key ); ?>][<?php echo esc_attr( $index ); ?>][icon_url]" 
                                                        class="icon-url" 
                                                        value="<?php echo esc_attr( $item['icon_url'] ?? '' ); ?>"
                                                    />
                                                    <input 
                                                        type="hidden" 
                                                        name="habilidades[<?php echo esc_attr( $categoria_key ); ?>][<?php echo esc_attr( $index ); ?>][icon_id]" 
                                                        class="icon-id" 
                                                        value="<?php echo esc_attr( $item['icon_id'] ?? '' ); ?>"
                                                    />

                                                    <div class="icon-preview">
                                                        <?php 
                                                        if ( ! empty( $item['icon_url'] ) ) {
                                                            echo wp_kses_post( odyssee_cv_render_icon( $item['icon_url'], $item['nome'] ?? '' ) );
                                                        } else {
                                                            echo '<span class="placeholder">Nenhuma imagem</span>';
                                                        }
                                                        ?>
                                                    </div>

                                                    <button type="button" class="button upload-icon-button">
                                                        <?php esc_html_e( 'Escolher Arquivo', 'odyssee' ); ?>
                                                    </button>

                                                    <?php if ( ! empty( $item['icon_url'] ) ) : ?>
                                                        <button type="button" class="button remove-icon-button">
                                                            <?php esc_html_e( 'Remover Imagem', 'odyssee' ); ?>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <button type="button" class="button button-primary add-repeater" data-repeater="habilidades-<?php echo esc_attr( $categoria_key ); ?>-repeater" data-template="habilidade-template">
                                <?php 
                                // translators: %s é o nome da categoria
                                echo sprintf( esc_html__( '+ Adicionar %s', 'odyssee' ), esc_html( $categoria_label ) );
                                ?>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Botão Salvar -->
            <div class="form-actions">
                <?php submit_button( 'Salvar Alterações', 'primary', 'odyssee_cv_submit', true ); ?>
            </div>
        </form>
    </div>

    <!-- Templates para Repeater Fields -->
    <script type="text/html" id="experiencia-template">
        <div class="repeater-item">
            <div class="repeater-header">
                <span class="repeater-title">Nova Experiência</span>
                <button type="button" class="button button-small remove-repeater">
                    <?php esc_html_e( 'Remover', 'odyssee' ); ?>
                </button>
            </div>

            <div class="repeater-fields">
                <div class="field-group field-3col">
                    <div>
                        <label>Título/Cargo</label>
                        <input 
                            type="text" 
                            name="experiencias[{index}][titulo]" 
                            placeholder="ex: Designer Gráfico"
                        />
                    </div>

                    <div>
                        <label>Empresa</label>
                        <input 
                            type="text" 
                            name="experiencias[{index}][empresa]" 
                            placeholder="ex: First Publicidade"
                        />
                    </div>

                    <div>
                        <label>Período</label>
                        <input 
                            type="text" 
                            name="experiencias[{index}][periodo]" 
                            placeholder="ex: 2023 - 2024"
                        />
                    </div>
                </div>

                <div class="field-group">
                    <label>Descrição (PT-BR)</label>
                    <textarea 
                        name="experiencias[{index}][descricao_pt]" 
                        rows="3"
                        placeholder="Descreva as atividades em português"
                    ></textarea>
                </div>

                <div class="field-group">
                    <label>Descrição (EN-US)</label>
                    <textarea 
                        name="experiencias[{index}][descricao_en]" 
                        rows="3"
                        placeholder="Describe activities in English"
                    ></textarea>
                </div>
            </div>
        </div>
    </script>

    <script type="text/html" id="curso-template">
        <div class="repeater-item">
            <div class="repeater-header">
                <span class="repeater-title">Novo Curso</span>
                <button type="button" class="button button-small remove-repeater">
                    <?php esc_html_e( 'Remover', 'odyssee' ); ?>
                </button>
            </div>

            <div class="repeater-fields">
                <div class="field-group field-3col">
                    <div>
                        <label>Nome do Curso</label>
                        <input 
                            type="text" 
                            name="cursos[{index}][nome]" 
                            placeholder="ex: Designer Gráfico"
                        />
                    </div>

                    <div>
                        <label>Instituição</label>
                        <input 
                            type="text" 
                            name="cursos[{index}][instituicao]" 
                            placeholder="ex: Universidade X"
                        />
                    </div>

                    <div>
                        <label>Data Final</label>
                        <input 
                            type="text" 
                            name="cursos[{index}][data_final]" 
                            placeholder="ex: 2024 ou presente"
                        />
                    </div>
                </div>

                <div class="field-group">
                    <label>Descrição (PT-BR) <em>(opcional)</em></label>
                    <textarea 
                        name="cursos[{index}][descricao_pt]" 
                        rows="2"
                        placeholder="Detalhes sobre o curso em português"
                    ></textarea>
                </div>

                <div class="field-group">
                    <label>Descrição (EN-US) <em>(opcional)</em></label>
                    <textarea 
                        name="cursos[{index}][descricao_en]" 
                        rows="2"
                        placeholder="Course details in English"
                    ></textarea>
                </div>
            </div>
        </div>
    </script>

    <script type="text/html" id="habilidade-template">
        <div class="repeater-item habilidade-item">
            <div class="repeater-header">
                <span class="repeater-title">Nova Habilidade</span>
                <button type="button" class="button button-small remove-repeater">
                    <?php esc_html_e( 'Remover', 'odyssee' ); ?>
                </button>
            </div>

            <div class="repeater-fields">
                <div class="field-group field-2col">
                    <div>
                        <label>Nome <em>(opcional)</em></label>
                        <input 
                            type="text" 
                            name="habilidades[{categoria}][{index}][nome]" 
                            placeholder="ex: Adobe Photoshop"
                            class="habilidade-nome-input"
                        />
                    </div>

                    <div>
                        <label>Nível de Proficiência</label>
                        <select name="habilidades[{categoria}][{index}][nivel]">
                            <option value="">-- Selecione --</option>
                            <option value="iniciante">Iniciante</option>
                            <option value="intermediário">Intermediário</option>
                            <option value="avançado">Avançado</option>
                            <option value="expert">Expert</option>
                        </select>
                    </div>
                </div>

                <div class="field-group">
                    <label>Ícone (SVG ou PNG)</label>
                    <div class="media-upload-group">
                        <input 
                            type="hidden" 
                            name="habilidades[{categoria}][{index}][icon_url]" 
                            class="icon-url" 
                        />
                        <input 
                            type="hidden" 
                            name="habilidades[{categoria}][{index}][icon_id]" 
                            class="icon-id" 
                        />

                        <div class="icon-preview">
                            <span class="placeholder">Nenhuma imagem</span>
                        </div>

                        <button type="button" class="button upload-icon-button">
                            <?php esc_html_e( 'Escolher Arquivo', 'odyssee' ); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </script>

    <?php
}

// ==============================================
// 4. FUNÇÕES DE UTILIDADE
// ==============================================

/**
 * Renderizar ícone (SVG ou IMG)
 */
function odyssee_cv_render_icon( $icon_url, $alt_text = '' ) {
    if ( empty( $icon_url ) ) {
        return '';
    }

    $parsed_url = parse_url( $icon_url );
    $path = isset( $parsed_url['path'] ) ? $parsed_url['path'] : '';
    
    if ( stripos( $path, '.svg' ) !== false ) {
        return sprintf(
            '<svg class="habilidade-icon-preview" data-src="%s" alt="%s"></svg>',
            esc_url( $icon_url ),
            esc_attr( $alt_text )
        );
    } else {
        return sprintf(
            '<img class="habilidade-icon-preview" src="%s" alt="%s" />',
            esc_url( $icon_url ),
            esc_attr( $alt_text )
        );
    }
}

/**
 * Obter todos os dados do CV
 */
function odyssee_cv_get_data() {
    $data = get_option( 'odyssee_cv_data', array() );

    // Estrutura padrão
    $defaults = array(
        'descricao' => array(
            'pt_br' => array(
                'intro_1' => '',
                'intro_2' => '',
            ),
            'en_us' => array(
                'intro_1' => '',
                'intro_2' => '',
            ),
        ),
        'experiencias' => array(),
        'cursos' => array(),
        'habilidades' => array(
            'criativos' => array(),
            'office' => array(),
            'programacao' => array(),
            'os' => array(),
        ),
    );

    return wp_parse_args( $data, $defaults );
}

/**
 * Salvar todos os dados do CV
 */
function odyssee_cv_save_data() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $data = array(
        'descricao' => array(
            'pt_br' => array(
                'intro_1' => isset( $_POST['descricao']['pt_br']['intro_1'] ) ? sanitize_textarea_field( $_POST['descricao']['pt_br']['intro_1'] ) : '',
                'intro_2' => isset( $_POST['descricao']['pt_br']['intro_2'] ) ? sanitize_textarea_field( $_POST['descricao']['pt_br']['intro_2'] ) : '',
            ),
            'en_us' => array(
                'intro_1' => isset( $_POST['descricao']['en_us']['intro_1'] ) ? sanitize_textarea_field( $_POST['descricao']['en_us']['intro_1'] ) : '',
                'intro_2' => isset( $_POST['descricao']['en_us']['intro_2'] ) ? sanitize_textarea_field( $_POST['descricao']['en_us']['intro_2'] ) : '',
            ),
        ),
        'experiencias' => odyssee_cv_sanitize_repeater( $_POST['experiencias'] ?? array() ),
        'cursos' => odyssee_cv_sanitize_repeater( $_POST['cursos'] ?? array() ),
        'habilidades' => odyssee_cv_sanitize_habilidades( $_POST['habilidades'] ?? array() ),
    );

    update_option( 'odyssee_cv_data', $data );
}

/**
 * Sanitizar repeater fields (experiências e cursos)
 */
function odyssee_cv_sanitize_repeater( $items ) {
    if ( ! is_array( $items ) ) {
        return array();
    }

    $sanitized = array();
    foreach ( $items as $item ) {
        if ( ! is_array( $item ) ) {
            continue;
        }
        
        // Verificar se pelo menos um campo principal foi preenchido
        $has_content = ! empty( $item['titulo'] ) || ! empty( $item['nome'] ) || 
                      ! empty( $item['empresa'] ) || ! empty( $item['instituicao'] );
        
        if ( $has_content ) {
            $sanitized[] = array(
                'titulo' => isset( $item['titulo'] ) ? sanitize_text_field( $item['titulo'] ) : '',
                'empresa' => isset( $item['empresa'] ) ? sanitize_text_field( $item['empresa'] ) : '',
                'instituicao' => isset( $item['instituicao'] ) ? sanitize_text_field( $item['instituicao'] ) : '',
                'periodo' => isset( $item['periodo'] ) ? sanitize_text_field( $item['periodo'] ) : '',
                'data_final' => isset( $item['data_final'] ) ? sanitize_text_field( $item['data_final'] ) : '',
                'nome' => isset( $item['nome'] ) ? sanitize_text_field( $item['nome'] ) : '',
                'descricao_pt' => isset( $item['descricao_pt'] ) ? sanitize_textarea_field( $item['descricao_pt'] ) : '',
                'descricao_en' => isset( $item['descricao_en'] ) ? sanitize_textarea_field( $item['descricao_en'] ) : '',
            );
        }
    }

    return $sanitized;
}

/**
 * Sanitizar habilidades (estrutura mais complexa)
 */
function odyssee_cv_sanitize_habilidades( $habilidades_raw ) {
    if ( ! is_array( $habilidades_raw ) ) {
        return array();
    }

    $sanitized = array();
    $categorias = array( 'criativos', 'office', 'programacao', 'os' );

    foreach ( $categorias as $categoria ) {
        $sanitized[ $categoria ] = array();

        if ( isset( $habilidades_raw[ $categoria ] ) && is_array( $habilidades_raw[ $categoria ] ) ) {
            foreach ( $habilidades_raw[ $categoria ] as $item ) {
                if ( is_array( $item ) && ! empty( $item['nome'] ?? $item['icon_url'] ?? '' ) ) {
                    $sanitized[ $categoria ][] = array(
                        'nome' => isset( $item['nome'] ) ? sanitize_text_field( $item['nome'] ) : '',
                        'nivel' => isset( $item['nivel'] ) ? sanitize_text_field( $item['nivel'] ) : '',
                        'icon_url' => isset( $item['icon_url'] ) ? esc_url_raw( $item['icon_url'] ) : '',
                        'icon_id' => isset( $item['icon_id'] ) ? intval( $item['icon_id'] ) : 0,
                    );
                }
            }
        }
    }

    return $sanitized;
}

// ==============================================
// 5. AJAX PARA UPLOAD DE ÍCONES
// ==============================================

add_action( 'wp_ajax_odyssee_upload_icon', 'odyssee_cv_ajax_upload_icon' );
function odyssee_cv_ajax_upload_icon() {
    check_ajax_referer( 'odyssee_cv_nonce', 'nonce' );

    if ( ! current_user_can( 'upload_files' ) ) {
        wp_send_json_error( 'Sem permissão para fazer upload' );
    }

    if ( empty( $_FILES['file'] ) ) {
        wp_send_json_error( 'Nenhum arquivo enviado' );
    }

    $file = $_FILES['file'];
    $allowed_types = array( 'image/svg+xml', 'image/png', 'image/jpeg' );

    if ( ! in_array( $file['type'], $allowed_types, true ) ) {
        wp_send_json_error( 'Tipo de arquivo não permitido. Use SVG, PNG ou JPEG.' );
    }

    $upload = wp_handle_upload( $file, array( 'test_form' => false ) );

    if ( isset( $upload['error'] ) ) {
        wp_send_json_error( $upload['error'] );
    }

    wp_send_json_success( array(
        'url' => $upload['url'],
        'id' => attachment_url_to_postid( $upload['url'] ),
    ) );
}

// ==============================================
// 6. MIGRAÇÃO AUTOMÁTICA DOS DADOS HARDCODED
// ==============================================

/**
 * Migrar dados hardcoded para o painel admin (executa uma única vez)
 * Se odyssee_cv_data não existir, importa dados padrão
 */
add_action( 'admin_init', 'odyssee_cv_migrate_legacy_data' );
function odyssee_cv_migrate_legacy_data() {
    // Verificar se dados já foram migrados
    if ( get_option( 'odyssee_cv_data' ) ) {
        return; // Dados já existem, não migrar
    }

    // Dados padrão extraídos do cv-page.js (hardcoded)
    $default_data = array(
        'descricao' => array(
            'pt_br' => array(
                'intro_1' => 'Tenho 24 anos, sou designer gráfico graduado. Atuo principalmente nas áreas de design gráfico, edição de vídeo, ilustração digital e motion design.',
                'intro_2' => 'Trabalho bem em equipe, presencialmente ou remotamente. Consigo atender bem a propostas de briefing, lido bem com diferentes tipos de identidade visual e segmentos.',
            ),
            'en_us' => array(
                'intro_1' => "I'm 24 years old, a graduate graphic designer. I work mainly in the areas of graphic design, video editing, digital illustration and motion design.",
                'intro_2' => 'I work well in teams, on-site or remotely. I can handle briefing proposals well, and deal with different types of visual identity and segments.',
            ),
        ),
        'experiencias' => array(
            array(
                'titulo' => 'Mobile Minecraft',
                'empresa' => 'Mobile Minecraft',
                'periodo' => '2016 - 2018',
                'descricao_pt' => "Desenvolvimento e criação de banners para o Blog.\nRedator de matérias e revisor das demais matérias publicadas.\nAuxiliar no desenvolvimento da UX/UI do blog.\nAuxiliar na criação e desenvolvimento da identidade visual do site.",
                'descricao_en' => "Development and creation of banners for the Blog.\nWriter of articles and reviewer of other published articles.\nAssist in the development of the blog UX/UI.\nAssist in the creation and development of the site visual identity.",
            ),
            array(
                'titulo' => 'Recepcionista',
                'empresa' => 'Fleury S.A',
                'periodo' => '2020 - 2022',
                'descricao_pt' => "Atendimento e abertura de ficha de exames médicos.\nAtendimento à empresas e convênios credenciados.\nTreinador de novos trabalhadores para a recepção e abertura de ficha.\nArmazenador de arquivos da unidade de atendimento.",
                'descricao_en' => "Customer service and opening of medical exam records.\nService to accredited companies and health plans.\nTrainer of new workers for reception and record opening.\nStorage of service unit files.",
            ),
            array(
                'titulo' => 'Estagiário em Design Gráfico',
                'empresa' => 'ANYMOUS',
                'periodo' => '2023 - 2023',
                'descricao_pt' => "Criação e elaboração de posts e carroséis para instagram.\nCriação e elaboração de reels para instagram.\nRedator e tratar fotos de produtos para marketplace.\nUX/UI do site. Gestão de ícones, símbolos e acessibilidade da página.",
                'descricao_en' => "Creation and development of posts and carousels for Instagram.\nCreation and development of reels for Instagram.\nWriter and product photo editor for marketplace.\nWebsite UX/UI. Management of icons, symbols and page accessibility.",
            ),
            array(
                'titulo' => 'Corte de letras-caixa',
                'empresa' => 'Div Term Tecnomoldura',
                'periodo' => '2023 - 2023',
                'descricao_pt' => "Gerar arquivos escalonados e compatíveis para recorte em máquina laser, fio quente e MDF.\nElaborar aproveitamento de material em software para recorte em máquinas.\nOperação de corte laser e de corte a fio quente.\nElaborar tipografias e adapta-las para corte em máquinas de acordo com suas limitações.",
                'descricao_en' => "Generate scaled and compatible files for laser cutting, hot-wire cutting and MDF.\nDevelop material optimization in software for machine cutting.\nLaser cutting and hot wire cutting operation.\nDevelop typography and adapt it for cutting in machinery according to its limitations.",
            ),
            array(
                'titulo' => 'Designer Gráfico',
                'empresa' => 'First Publicidade',
                'periodo' => '2024 - 2024',
                'descricao_pt' => "Criação e elaboração de posts e carroséis para instagram de diversas identidades visuais.\nCriação de posts para as campanhas políticas de candidatos vereadores e prefeitos do estado de São Paulo.\nBranding de posts trens e interativos para redes sociais.",
                'descricao_en' => "Creation and development of posts and carousels for Instagram with various visual identities.\nCreation of posts for political campaigns of city council and mayor candidates in São Paulo state.\nBranding of trendy and interactive posts for social media.",
            ),
            array(
                'titulo' => 'Designer Gráfico Jr.',
                'empresa' => 'Percons',
                'periodo' => '2025 - 2026',
                'descricao_pt' => "Criação de artes para posts em redes sociais.\nEdição de vídeos curtos e reels animados\nCriar e fechar arquivos para impressões\nElaborar e criar identidades visuais e logos para marcas",
                'descricao_en' => "Creating artwork for social media posts.\nEditing short videos and animated reels\nPreparing print-ready files\nDesigning visual identities and logos for brands",
            ),
        ),
        'cursos' => array(
            array(
                'nome' => 'Curso - Caution Pontocom',
                'instituicao' => 'Caution Pontocom',
                'data_final' => '',
                'descricao_pt' => 'Curso de informática e inglês - 194h/aula',
                'descricao_en' => 'Computer science and English course - 194 hours',
            ),
            array(
                'nome' => 'CST em Design Gráfico',
                'instituicao' => 'UNICID',
                'data_final' => '2023',
                'descricao_pt' => 'Faculdade - 4 semestres',
                'descricao_en' => 'College - 4 semesters',
            ),
        ),
        'habilidades' => array(
            'softwares' => array(),
            'criativos' => array(),
            'office' => array(),
            'os' => array(),
        ),
    );

    // Salvar dados padrão
    update_option( 'odyssee_cv_data', $default_data );
    
    // Log da migração
    error_log( '[ODYSSEE CV] Dados legados migrados com sucesso para odyssee_cv_data' );
}

// ==============================================
// 6. OBTER DADOS DO CV PARA O FRONTEND
// ==============================================

/**
 * Disponibilizar dados do CV para o JavaScript
 * Injeta os dados na página via script inline (antes dos scripts deferred)
 */
add_action( 'wp_head', 'odyssee_cv_localize_frontend_data', 5 );
function odyssee_cv_localize_frontend_data() {
    if ( is_page_template( 'page-sobre-mim.php' ) ) {
        $cv_data = odyssee_cv_get_data();
        
        // Log para verificar execução
        $has_skills = ! empty( $cv_data['habilidades'] );
        $skills_count = 0;
        if ( is_array( $cv_data['habilidades'] ) ) {
            foreach ( $cv_data['habilidades'] as $cat => $items ) {
                $skills_count += count( $items );
            }
        }
        
        error_log( '[odyssee-cv] odyssee_cv_localize_frontend_data executada: ' . ( $has_skills ? 'com ' . $skills_count . ' habilidades' : 'sem habilidades' ) );
        
        // Injetar dados via script inline (aparece antes dos scripts deferred)
        echo '<script>';
        echo 'window.odysseeCVData = ' . wp_json_encode( $cv_data ) . ';';
        echo 'console.log("[odyssee-cv] Dados injetados no window:", window.odysseyCVData);';
        echo '</script>';
    }
}

// ==============================================
// Fim do arquivo
// ==============================================
