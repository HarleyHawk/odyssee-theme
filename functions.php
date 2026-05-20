<?php

// ==============================================
// SEGURANÇA: Bloquear acesso direto ao arquivo
// ==============================================
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ==============================================
// CARREGAMENTO DE MÓDULOS DO TEMA
// ==============================================

// Módulo de Administração da Página Sobre Mim (CV)
if ( is_admin() ) {
    require_once get_template_directory() . '/inc/cv-admin.php';
}

// ==============================================
// SEGURANÇA: Gerar CSP Nonce único por requisição
// ==============================================
function odyssee_generate_csp_nonce() {
    if ( ! defined( 'ODYSSEE_CSP_NONCE' ) ) {
        define( 'ODYSSEE_CSP_NONCE', base64_encode( random_bytes( 16 ) ) );
    }
    return ODYSSEE_CSP_NONCE;
}

// ==============================================
// SEGURANÇA: Headers HTTP de proteção (2026)
// ==============================================
function odyssee_security_headers() {
    // Não aplicar CSP no painel admin — bloqueia scripts internos do WP
    if ( is_admin() ) {
        return;
    }

    $nonce = odyssee_generate_csp_nonce();

    // Content Security Policy com nonce (elimina unsafe-inline e unsafe-eval para scripts)
    // Inclui domínios do Google Analytics/GTM para quando for ativado
    header( "Content-Security-Policy: default-src 'self'; script-src 'self' 'nonce-{$nonce}' https://www.googletagmanager.com; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' data:; connect-src 'self' https://odysseexp.com https://www.google-analytics.com https://*.analytics.google.com https://*.googletagmanager.com; frame-ancestors 'self'; base-uri 'self'; form-action 'self'; upgrade-insecure-requests;" );

    // HTTP Strict Transport Security (2 anos + preload)
    header( 'Strict-Transport-Security: max-age=63072000; includeSubDomains; preload' );

    // Cross-Origin Opener Policy (permite popups para WhatsApp)
    header( 'Cross-Origin-Opener-Policy: same-origin-allow-popups' );

    // Permissions Policy atualizado 2026 (bloqueia browsing-topics e attribution-reporting)
    header( "Permissions-Policy: accelerometer=(), camera=(), geolocation=(), gyroscope=(), magnetometer=(), microphone=(), payment=(), usb=(), interest-cohort=(), browsing-topics=(), attribution-reporting=()" );
}

// ==============================================
// ENQUEUE SCRIPTS COM LOCALIZAÇÃO
// ==============================================
function odyssee_scripts() {
    // Versão fixa para permitir cache efetivo do browser
    $theme_version = wp_get_theme()->get( 'Version' ) ?: '1.0.0';
    
    // CSS principal (minificado em produção)
    wp_enqueue_style( 'odyssee-style', get_template_directory_uri() . '/style.min.css', array(), $theme_version );
    
    // Fonts (self-hosted) - carregar folha de fontes custom
    wp_enqueue_style( 'odyssee-fonts', get_template_directory_uri() . '/assets/css/fonts.css', array(), $theme_version );

    // FontAwesome (CDN) - carrega ícones do CDN oficial do Font Awesome
    wp_enqueue_style( 'fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0' );

    // JS principal
    wp_enqueue_script( 'odyssee-app', get_template_directory_uri() . '/assets/js/app.js', array(), $theme_version, true );
    
    // Passar nonce e outras dados seguros para JS (usado em fetches/AJAX)
    wp_localize_script( 'odyssee-app', 'odysseeSecure', array(
        'nonce' => wp_create_nonce( 'odyssee_nonce' ),
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'restUrl' => rest_url(),
    ) );

    // Passar preços do admin para o JS (sobreescreve hardcoded)
    $precos_salvos = get_option( 'odyssee_precos', array() );
    $unitarios_salvos = get_option( 'odyssee_precos_unitarios', array() );
    if ( ! empty( $precos_salvos ) || ! empty( $unitarios_salvos ) ) {
        wp_localize_script( 'odyssee-app', 'odysseePrecos', array(
            'prices'     => $precos_salvos,
            'unitPrices' => $unitarios_salvos,
        ) );
    }

    // Passar produtos personalizados do admin para o JS
    $produtos_custom = get_option( 'odyssee_produtos_custom', array() );
    if ( ! empty( $produtos_custom ) && is_array( $produtos_custom ) ) {
        wp_localize_script( 'odyssee-app', 'odysseeProdutos', $produtos_custom );
    }

    // Passar sobrescritas de serviços existentes (nome, descrição, thumbnail)
    $overrides = get_option( 'odyssee_servicos_overrides', array() );
    if ( ! empty( $overrides ) && is_array( $overrides ) ) {
        wp_localize_script( 'odyssee-app', 'odysseeOverrides', $overrides );
    }
    
    // Script para placeholders da página principal
    wp_enqueue_script( 'odyssee-placeholders', get_template_directory_uri() . '/assets/js/placeholders.js', array(), $theme_version, true );
    
    // Script para banners temáticos de categorias
    wp_enqueue_script( 'odyssee-category-banners', get_template_directory_uri() . '/assets/js/category-banners.js', array(), $theme_version, true );

    // Banner de Cookies LGPD
    wp_enqueue_style( 'odyssee-cookie-banner', get_template_directory_uri() . '/assets/css/cookie-banner.css', array(), $theme_version );
    wp_enqueue_script( 'odyssee-cookie-banner', get_template_directory_uri() . '/assets/js/cookie-banner.js', array(), $theme_version, true );
}

// Hooks principais do tema
add_action( 'wp_enqueue_scripts', 'odyssee_scripts' );
add_action( 'send_headers', 'odyssee_security_headers' );
add_theme_support( 'title-tag' );
add_theme_support( 'post-thumbnails' );

// ==============================================
// SEO: Meta Description, Open Graph, Twitter Cards
// ==============================================
function odyssee_seo_meta_tags() {
    $site_name   = 'Odyssee — Creative Experience';
    $default_img = 'https://odysseexp.com/wp-content/uploads/2025/08/logo-colorful-reduced-scaled-e1756493913129.png';
    $site_url    = home_url();
    $locale      = 'pt_BR';

    // Determinar título, descrição e imagem por página
    if ( is_front_page() ) {
        $title = 'Odyssee — Creative Experience | Design, Vídeo, Motion & Ilustração';
        $desc  = 'Portfólio e serviços de Design Gráfico, Edição de Vídeo, Motion Graphics e Ilustração Digital. Experiência criativa de alto nível por Renato Harley Paiva.';
        $url   = $site_url;
        $image = $default_img;
        $type  = 'website';
    } elseif ( is_singular( 'post' ) ) {
        $title = get_the_title() . ' — Odyssee';
        $desc  = has_excerpt() ? wp_strip_all_tags( get_the_excerpt() ) : wp_trim_words( wp_strip_all_tags( get_the_content() ), 30, '…' );
        $url   = get_permalink();
        $image = has_post_thumbnail() ? get_the_post_thumbnail_url( null, 'large' ) : $default_img;
        $type  = 'article';
    } elseif ( is_page( 'faq' ) || is_page_template( 'faq.php' ) ) {
        $title = 'FAQ — Perguntas Frequentes | Odyssee';
        $desc  = 'Tire suas dúvidas sobre prazos, pagamentos, contratos e processos dos serviços de design, vídeo, motion e ilustração.';
        $url   = get_permalink();
        $image = $default_img;
        $type  = 'website';
    } elseif ( is_page( 'sobre-mim' ) || is_page_template( 'page-sobre-mim.php' ) ) {
        $title = 'Sobre Mim — Renato Harley Paiva | Odyssee';
        $desc  = 'Conheça o designer gráfico, ilustrador digital e editor de vídeo por trás da Odyssee. Formação acadêmica, experiência e habilidades.';
        $url   = get_permalink();
        $image = $default_img;
        $type  = 'profile';
    } elseif ( is_page( 'servicos' ) || is_page_template( 'page-servicos.php' ) ) {
        $title = 'Serviços e Preços | Odyssee';
        $desc  = 'Confira os serviços e pacotes de Design Gráfico, Edição de Vídeo, Motion Graphics e Ilustração Digital com preços transparentes.';
        $url   = get_permalink();
        $image = $default_img;
        $type  = 'website';
    } else {
        $title = wp_get_document_title();
        $desc  = 'Portfólio e serviços criativos de design, vídeo, motion e ilustração por Renato Harley Paiva.';
        $url   = get_permalink() ?: $site_url;
        $image = $default_img;
        $type  = 'website';
    }

    $desc = mb_substr( $desc, 0, 160 );

    // Meta description
    echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";

    // Open Graph
    echo '<meta property="og:type" content="' . esc_attr( $type ) . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
    echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr( $site_name ) . '">' . "\n";
    echo '<meta property="og:locale" content="' . esc_attr( $locale ) . '">' . "\n";

    // Twitter Cards
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr( $desc ) . '">' . "\n";
    echo '<meta name="twitter:image" content="' . esc_url( $image ) . '">' . "\n";

    // Canonical URL
    echo '<link rel="canonical" href="' . esc_url( $url ) . '">' . "\n";
}
add_action( 'wp_head', 'odyssee_seo_meta_tags', 1 );

// ==============================================
// SEO: JSON-LD Structured Data (Schema.org)
// ==============================================
function odyssee_jsonld_schema() {
    $site_url = home_url();

    // Schema Organization (todas as páginas)
    $organization = array(
        '@context' => 'https://schema.org',
        '@type'    => 'ProfessionalService',
        'name'     => 'Odyssee — Creative Experience',
        'url'      => $site_url,
        'logo'     => 'https://odysseexp.com/wp-content/uploads/2025/08/logo-colorful-reduced-scaled-e1756493913129.png',
        'description' => 'Serviços profissionais de Design Gráfico, Edição de Vídeo, Motion Graphics e Ilustração Digital.',
        'founder'  => array(
            '@type' => 'Person',
            'name'  => 'Renato Harley Paiva',
        ),
        'contactPoint' => array(
            '@type'            => 'ContactPoint',
            'telephone'        => '+55-11-96320-8691',
            'contactType'      => 'customer service',
            'availableLanguage' => array( 'Portuguese', 'English' ),
        ),
        'sameAs' => array(
            'https://www.instagram.com/harley_l.m/',
        ),
    );

    $nonce = odyssee_generate_csp_nonce();
    echo '<script type="application/ld+json" nonce="' . esc_attr( $nonce ) . '">' . wp_json_encode( $organization, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";

    // Schema Article (posts individuais)
    if ( is_singular( 'post' ) ) {
        $article = array(
            '@context'      => 'https://schema.org',
            '@type'         => 'Article',
            'headline'      => get_the_title(),
            'datePublished' => get_the_date( 'c' ),
            'dateModified'  => get_the_modified_date( 'c' ),
            'author'        => array(
                '@type' => 'Person',
                'name'  => get_the_author(),
            ),
            'publisher'     => array(
                '@type' => 'Organization',
                'name'  => 'Odyssee — Creative Experience',
                'logo'  => array(
                    '@type' => 'ImageObject',
                    'url'   => 'https://odysseexp.com/wp-content/uploads/2025/08/logo-colorful-reduced-scaled-e1756493913129.png',
                ),
            ),
            'mainEntityOfPage' => get_permalink(),
        );
        if ( has_post_thumbnail() ) {
            $article['image'] = get_the_post_thumbnail_url( null, 'large' );
        }
        echo '<script type="application/ld+json" nonce="' . esc_attr( $nonce ) . '">' . wp_json_encode( $article, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
    }

    // Schema FAQPage (página FAQ)
    if ( is_page( 'faq' ) || is_page_template( 'faq.php' ) ) {
        $faq_items = array(
            array( 'q' => 'Quais são os prazos para entrega?', 'a' => 'Vídeos curtos até 2 dias úteis, vídeos longos até 5 dias úteis. Design gráfico no mesmo dia para até 5 posts. Ilustrações de 1 a 7 dias conforme complexidade. Motion pode levar até um mês.' ),
            array( 'q' => 'Quais formas de pagamento você aceita?', 'a' => 'PIX ou Mercado Pago.' ),
            array( 'q' => 'Como funcionam os contratos dos serviços?', 'a' => 'Todos os produtos incluem um contrato prévio para garantir os direitos de ambas partes legalmente.' ),
            array( 'q' => 'Você possui algum certificado profissional?', 'a' => 'Sim, possuo formação acadêmica e currículo profissional nos parâmetros.' ),
        );
        $faq_entities = array();
        foreach ( $faq_items as $item ) {
            $faq_entities[] = array(
                '@type'          => 'Question',
                'name'           => $item['q'],
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    'text'  => $item['a'],
                ),
            );
        }
        $faq_schema = array(
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
            'mainEntity' => $faq_entities,
        );
        echo '<script type="application/ld+json" nonce="' . esc_attr( $nonce ) . '">' . wp_json_encode( $faq_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
    }
}
add_action( 'wp_head', 'odyssee_jsonld_schema', 2 );

// ==============================================
// SEGURANÇA WAF: Headers adicionais de proteção
// ==============================================
function odyssee_waf_headers() {
    if ( is_admin() ) {
        return;
    }

    // X-Content-Type-Options: impede MIME-sniffing
    header( 'X-Content-Type-Options: nosniff' );

    // X-Frame-Options: impede clickjacking (complementa frame-ancestors no CSP)
    header( 'X-Frame-Options: SAMEORIGIN' );

    // Referrer-Policy: envia origin apenas para cross-origin
    header( 'Referrer-Policy: strict-origin-when-cross-origin' );

    // X-XSS-Protection: filtro XSS do navegador (legacy, ainda útil para IE)
    header( 'X-XSS-Protection: 1; mode=block' );

    // Cross-Origin-Embedder-Policy (permite imagens externas)
    header( 'Cross-Origin-Resource-Policy: same-site' );
}
add_action( 'send_headers', 'odyssee_waf_headers' );

// ==============================================
// PERFORMANCE: Otimizações de carregamento
// ==============================================
function odyssee_performance_tweaks() {
    // Remover emojis do WordPress (JS + CSS desnecessários)
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

    // Remover links desnecessários do <head>
    remove_action( 'wp_head', 'rsd_link' );
    remove_action( 'wp_head', 'wlwmanifest_link' );
    remove_action( 'wp_head', 'wp_shortlink_wp_head' );
    remove_action( 'wp_head', 'rest_output_link_wp_head' );
    remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
    remove_action( 'wp_head', 'feed_links', 2 );
    remove_action( 'wp_head', 'feed_links_extra', 3 );
}
add_action( 'init', 'odyssee_performance_tweaks' );

// Preconnect para recursos críticos de terceiros
function odyssee_resource_hints( $urls, $relation_type ) {
    if ( 'preconnect' === $relation_type ) {
        $urls[] = array( 'href' => 'https://cdnjs.cloudflare.com', 'crossorigin' => true );
    }
    return $urls;
}
add_filter( 'wp_resource_hints', 'odyssee_resource_hints', 10, 2 );

// ==============================================
// GOOGLE ANALYTICS (GA4) — Descomente e insira seu ID
// ==============================================
// function odyssee_google_analytics() {
//     $ga_id = 'G-XXXXXXXXXX'; // Substitua pelo seu Measurement ID
//     $nonce = odyssee_generate_csp_nonce();
//     echo '<script async src="https://www.googletagmanager.com/gtag/js?id=' . esc_attr( $ga_id ) . '" nonce="' . esc_attr( $nonce ) . '"></script>' . "\n";
//     echo '<script nonce="' . esc_attr( $nonce ) . '">window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag("js",new Date());gtag("config","' . esc_js( $ga_id ) . '");</script>' . "\n";
// }
// add_action( 'wp_head', 'odyssee_google_analytics', 0 );

// ==============================================
// GOOGLE SEARCH CONSOLE — Descomente e insira seu código
// ==============================================
// function odyssee_search_console() {
//     echo '<meta name="google-site-verification" content="SEU_CODIGO_AQUI">' . "\n";
// }
// add_action( 'wp_head', 'odyssee_search_console', 0 );

// ==============================================
// SEGURANÇA: Injetar CSP nonce nos scripts do WordPress
// ==============================================
add_filter( 'wp_script_attributes', function( $attributes ) {
    $attributes['nonce'] = odyssee_generate_csp_nonce();
    return $attributes;
} );

add_filter( 'wp_inline_script_attributes', function( $attributes ) {
    $attributes['nonce'] = odyssee_generate_csp_nonce();
    return $attributes;
} );

// ==============================================
// SEGURANÇA: Hardening geral do WordPress
// ==============================================
add_filter( 'xmlrpc_enabled', '__return_false' );
remove_action( 'wp_head', 'wp_generator' );

// Bloquear enumeração de usuários via REST API
add_filter( 'rest_endpoints', function( $endpoints ) {
    if ( ! is_user_logged_in() ) {
        unset( $endpoints['/wp/v2/users'] );
        unset( $endpoints['/wp/v2/users/(?P<id>[\\d]+)'] );
    }
    return $endpoints;
} );

// ==============================================
// REST API - Registrar custom field com segurança
// ==============================================
add_action( 'rest_api_init', function() {
    register_rest_field( 'post', 'post_title_en', array(
        'get_callback' => function( $post ) {
            // Sanitizar a saída
            return sanitize_text_field( get_post_meta( $post['id'], 'post_title_en', true ) );
        },
        'update_callback' => function( $value, $post ) {
            // Verificar permissões
            if ( ! current_user_can( 'edit_post', $post->ID ) ) {
                return new WP_Error( 'odyssee_forbidden', 'Você não tem permissão para editar este post.', array( 'status' => 403 ) );
            }
            
            // Sanitizar entrada
            $sanitized_value = sanitize_text_field( $value );
            
            // Atualizar com validação
            return update_post_meta( $post->ID, 'post_title_en', $sanitized_value );
        },
        'schema' => array(
            'description' => 'Post title in English',
            'type' => 'string',
            'context' => array( 'view', 'edit' ),
        ),
    ) );
} );

// ==============================================
// PAINEL ADMIN: Avisos de Serviços
// ==============================================

/**
 * Registra a página de admin "Avisos de Serviços"
 * no menu do WordPress em Aparência > Avisos de Serviços.
 */
function odyssee_avisos_admin_menu() {
    add_theme_page(
        'Avisos de Serviços',          // Título da página
        'Avisos de Serviços',          // Texto no menu
        'manage_options',               // Permissão necessária
        'odyssee-avisos-servicos',      // Slug da página
        'odyssee_avisos_admin_page'     // Callback que renderiza
    );
}
add_action( 'admin_menu', 'odyssee_avisos_admin_menu' );

/**
 * Registra as opções (settings) dos avisos no WordPress.
 * Cada categoria de serviço possui duas opções:
 *   - odyssee_aviso_{chave}       → texto do aviso (sanitize_textarea_field)
 *   - odyssee_aviso_{chave}_ativo  → on/off (absint: 0 ou 1)
 * Categorias: geral, design, video, motion, ilustracao.
 */
function odyssee_avisos_settings_init() {
    // Registrar cada opção com sanitização
    register_setting( 'odyssee_avisos_group', 'odyssee_aviso_geral', array(
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_textarea_field',
        'default'           => '',
    ) );
    register_setting( 'odyssee_avisos_group', 'odyssee_aviso_geral_ativo', array(
        'type'              => 'integer',
        'sanitize_callback' => 'absint',
        'default'           => 0,
    ) );
    register_setting( 'odyssee_avisos_group', 'odyssee_aviso_design', array(
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_textarea_field',
        'default'           => '',
    ) );
    register_setting( 'odyssee_avisos_group', 'odyssee_aviso_design_ativo', array(
        'type'              => 'integer',
        'sanitize_callback' => 'absint',
        'default'           => 0,
    ) );
    register_setting( 'odyssee_avisos_group', 'odyssee_aviso_video', array(
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_textarea_field',
        'default'           => '',
    ) );
    register_setting( 'odyssee_avisos_group', 'odyssee_aviso_video_ativo', array(
        'type'              => 'integer',
        'sanitize_callback' => 'absint',
        'default'           => 0,
    ) );
    register_setting( 'odyssee_avisos_group', 'odyssee_aviso_motion', array(
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_textarea_field',
        'default'           => '',
    ) );
    register_setting( 'odyssee_avisos_group', 'odyssee_aviso_motion_ativo', array(
        'type'              => 'integer',
        'sanitize_callback' => 'absint',
        'default'           => 0,
    ) );
    register_setting( 'odyssee_avisos_group', 'odyssee_aviso_ilustracao', array(
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_textarea_field',
        'default'           => '',
    ) );
    register_setting( 'odyssee_avisos_group', 'odyssee_aviso_ilustracao_ativo', array(
        'type'              => 'integer',
        'sanitize_callback' => 'absint',
        'default'           => 0,
    ) );
}
add_action( 'admin_init', 'odyssee_avisos_settings_init' );

/**
 * Renderiza a página de admin dos avisos de serviços.
 * Exibe um formulário com checkbox (ativar/desativar) e textarea
 * para cada categoria, usando a API de Settings do WordPress.
 */
function odyssee_avisos_admin_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // Mostra mensagem de sucesso ao salvar
    if ( isset( $_GET['settings-updated'] ) ) {
        add_settings_error( 'odyssee_avisos_messages', 'odyssee_avisos_updated', 'Avisos atualizados com sucesso!', 'updated' );
    }
    settings_errors( 'odyssee_avisos_messages' );

    $categorias = array(
        array( 'key' => 'geral',       'label' => 'Aviso Geral (aparece no topo da página de serviços)' ),
        array( 'key' => 'design',      'label' => 'Design Gráfico' ),
        array( 'key' => 'video',       'label' => 'Edição de Vídeo' ),
        array( 'key' => 'motion',      'label' => 'Motion' ),
        array( 'key' => 'ilustracao',  'label' => 'Ilustração Digital' ),
    );

    ?>
    <div class="wrap">
        <h1><span class="dashicons dashicons-warning" style="font-size: 1.3em; margin-right: 8px;"></span>Avisos de Serviços — Odyssee</h1>
        <p>Configure avisos de indisponibilidade ou manutenção para cada categoria de serviço. Quando ativo, o aviso aparecerá na página de serviços.</p>

        <form method="post" action="options.php">
            <?php settings_fields( 'odyssee_avisos_group' ); ?>

            <table class="form-table" role="presentation">
                <?php foreach ( $categorias as $cat ) :
                    $aviso  = get_option( 'odyssee_aviso_' . $cat['key'], '' );
                    $ativo  = get_option( 'odyssee_aviso_' . $cat['key'] . '_ativo', 0 );
                ?>
                <tr>
                    <th scope="row" style="vertical-align: top;">
                        <label for="odyssee_aviso_<?php echo esc_attr( $cat['key'] ); ?>">
                            <?php echo esc_html( $cat['label'] ); ?>
                        </label>
                    </th>
                    <td>
                        <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px; font-weight: 600;">
                            <input type="checkbox"
                                   name="odyssee_aviso_<?php echo esc_attr( $cat['key'] ); ?>_ativo"
                                   value="1"
                                   <?php checked( $ativo, 1 ); ?>>
                            Ativar aviso
                        </label>
                        <textarea
                            name="odyssee_aviso_<?php echo esc_attr( $cat['key'] ); ?>"
                            id="odyssee_aviso_<?php echo esc_attr( $cat['key'] ); ?>"
                            rows="3"
                            cols="60"
                            class="large-text"
                            placeholder="Ex: INDISPONÍVEL NO MOMENTO — Motivo: equipamento em manutenção"
                        ><?php echo esc_textarea( $aviso ); ?></textarea>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>

            <?php submit_button( 'Salvar Avisos' ); ?>
        </form>
    </div>
    <?php
}

/**
 * Função helper: retorna o HTML de um aviso se estiver ativo.
 * Usada nos templates para exibir avisos no front-end.
 *
 * @param string $key  Chave da categoria (geral, design, video, motion, ilustracao)
 * @return string      HTML do aviso ou string vazia
 */
function odyssee_get_aviso_html( $key ) {
    $ativo = get_option( 'odyssee_aviso_' . $key . '_ativo', 0 );
    if ( ! $ativo ) {
        return '';
    }

    $aviso = get_option( 'odyssee_aviso_' . $key, '' );
    $aviso = trim( $aviso );
    if ( empty( $aviso ) ) {
        return '';
    }

    return '<div class="service-notice">
        <div class="service-notice-icon"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="service-notice-text">' . esc_html( $aviso ) . '</div>
    </div>';
}

// ==============================================
// PAINEL ADMIN: Preços e Descontos
// ==============================================

/**
 * Definição de todos os serviços e seus preços padrão.
 * Usado tanto na página admin quanto nos defaults do JS.
 */
function odyssee_get_price_definitions() {
    // Estrutura: section_key => { label, items => { item_key => { label, default } } }
    // Cada item_key corresponde ao ID do serviço usado no JS (allPrices[item_key])
    return array(
        // --- Design Gráfico: Serviços individuais ---
        'design_servicos' => array(
            'label' => 'Design Gráfico — Serviços',
            'items' => array(
                'logotipologo'   => array( 'label' => 'Logotipo e Logo',               'default' => 49.00 ),
                'bannersocial'   => array( 'label' => 'Banner para redes sociais',      'default' => 79.00 ),
                'visualid'       => array( 'label' => 'Cartão de Visitas',              'default' => 599.00 ),
                'cartao_visitas' => array( 'label' => 'Cartão de Visitas (antigo)',      'default' => 24.00 ),
                'flyer'          => array( 'label' => 'Flyer',                           'default' => 29.00 ),
                'convites'       => array( 'label' => 'Convites',                        'default' => 23.00 ),
                'banner'         => array( 'label' => 'Banner (impresso)',               'default' => 100.00 ),
                'botton'         => array( 'label' => 'Arte para Botton',                'default' => 19.00 ),
                'adesivos'       => array( 'label' => 'Arte para Adesivos',              'default' => 19.00 ),
            ),
        ),
        // --- Design Gráfico: Pacotes promocionais ---
        'design_pacotes' => array(
            'label' => 'Design Gráfico — Pacotes',
            'items' => array(
                'designmegapackage'    => array( 'label' => 'Mega Pacote',        'default' => 1250.00 ),
                'designpremiumpackage' => array( 'label' => 'Pacote Premium',     'default' => 679.00 ),
                'postpackage'          => array( 'label' => 'Pacote Posts',       'default' => 89.00 ),
                'storiepackage'        => array( 'label' => 'Pacote Stories',     'default' => 105.00 ),
                'carrosselpackage'     => array( 'label' => 'Pacote Carrosséis',  'default' => 449.00 ),
            ),
        ),
        // --- Edição de Vídeo: Serviços individuais ---
        'video_servicos' => array(
            'label' => 'Edição de Vídeo — Serviços',
            'items' => array(
                'video_longo' => array( 'label' => 'Vídeo Longo',  'default' => 350.00 ),
                'video_curto' => array( 'label' => 'Vídeo Curto',  'default' => 100.00 ),
                'thumbnail'   => array( 'label' => 'Thumbnail',    'default' => 20.00 ),
            ),
        ),
        // --- Edição de Vídeo: Pacotes promocionais ---
        'video_pacotes' => array(
            'label' => 'Edição de Vídeo — Pacotes',
            'items' => array(
                'mega_pacote'         => array( 'label' => 'Mega Pacote',             'default' => 2650.00 ),
                'pacote_premium_a'    => array( 'label' => 'Pacote Premium A',        'default' => 1650.00 ),
                'pacote_premium_b'    => array( 'label' => 'Pacote Premium B',        'default' => 475.00 ),
                'cinco_videos_longos' => array( 'label' => 'Cinco Vídeos Longos',     'default' => 1599.00 ),
                'cinco_videos_curtos' => array( 'label' => 'Cinco Vídeos Curtos',     'default' => 399.00 ),
                'cinco_thumbnails'    => array( 'label' => 'Cinco Thumbnails',        'default' => 89.00 ),
                'dez_thumbnails'      => array( 'label' => 'Dez Thumbnails',          'default' => 169.00 ),
            ),
        ),
        // --- Motion Graphics: Serviços individuais ---
        'motion_servicos' => array(
            'label' => 'Motion Graphics',
            'items' => array(
                'intro_animada'  => array( 'label' => 'Intro Animada',      'default' => 200.00 ),
                'artmotion'      => array( 'label' => 'Arte Animada',       'default' => 250.00 ),
                'logomotion'     => array( 'label' => 'Logo Animado',       'default' => 100.00 ),
                'motionmoldura'  => array( 'label' => 'Moldura Animada',    'default' => 140.00 ),
                'waitscreen'     => array( 'label' => 'Tela de Espera',     'default' => 175.00 ),
                'motionbanner'   => array( 'label' => 'Banner Animado',     'default' => 300.00 ),
            ),
        ),
        // --- Ilustração Digital: Serviços individuais ---
        'ilustracao_servicos' => array(
            'label' => 'Ilustração Digital',
            'items' => array(
                'fanart_anime'              => array( 'label' => 'Arte Estilo Anime',         'default' => 120.00 ),
                'fanart_cartoon'            => array( 'label' => 'Arte Estilo Cartoon',       'default' => 180.00 ),
                'fanart_chibi'              => array( 'label' => 'Arte Estilo Chibi',         'default' => 50.00 ),
                'fanart_pixelart'           => array( 'label' => 'Arte Estilo Pixel Art',     'default' => 80.00 ),
                'fanart_vetorial'           => array( 'label' => 'Arte Estilo Vetorial',      'default' => 100.00 ),
                'personagem_rpg'            => array( 'label' => 'Personagem Token RPG',      'default' => 250.00 ),
                'ilustracao_perfil'         => array( 'label' => 'Ilustração Perfil/Busto',   'default' => 150.00 ),
                'ilustracao_corpo_inteiro'  => array( 'label' => 'Ilustração Corpo Inteiro',  'default' => 250.00 ),
                'storyboard'               => array( 'label' => 'Storyboard',                'default' => 300.00 ),
                'cenario_digital'           => array( 'label' => 'Cenário Digital',           'default' => 375.00 ),
                'esboco_rapido'             => array( 'label' => 'Esboço Rápido',             'default' => 25.00 ),
            ),
        ),
    );
}

/**
 * Preços unitários usados para cálculo de desconto nos pacotes.
 */
function odyssee_get_unit_price_definitions() {
    return array(
        'design_post'      => array( 'label' => 'Post (unitário)',        'default' => 20.00 ),
        'design_storie'    => array( 'label' => 'Storie (unitário)',      'default' => 25.00 ),
        'design_carrossel' => array( 'label' => 'Carrossel (unitário)',   'default' => 100.00 ),
        'video_longo'      => array( 'label' => 'Vídeo Longo (unitário)', 'default' => 350.00 ),
        'video_curto'      => array( 'label' => 'Vídeo Curto (unitário)', 'default' => 100.00 ),
        'thumbnail'        => array( 'label' => 'Thumbnail (unitário)',   'default' => 20.00 ),
    );
}

/**
 * Registra a página de admin "Preços e Descontos"
 */
function odyssee_precos_admin_menu() {
    add_theme_page(
        'Preços e Descontos',
        'Preços e Descontos',
        'manage_options',
        'odyssee-precos',
        'odyssee_precos_admin_page'
    );
}
add_action( 'admin_menu', 'odyssee_precos_admin_menu' );

/**
 * Registra as 3 opções de preços no WordPress:
 *   - odyssee_precos             → preços de serviços/pacotes (array id => valor)
 *   - odyssee_precos_unitarios   → preços unitários para cálculo de desconto
 *   - odyssee_servicos_overrides → sobrescritas de nome/desc/thumb/info por serviço
 */
function odyssee_precos_settings_init() {
    // Preços de serviços e pacotes
    register_setting( 'odyssee_precos_group', 'odyssee_precos', array(
        'type'              => 'array',
        'sanitize_callback' => 'odyssee_sanitize_precos',
        'default'           => array(),
    ) );

    // Preços unitários (base para cálculo de economia nos pacotes)
    register_setting( 'odyssee_precos_group', 'odyssee_precos_unitarios', array(
        'type'              => 'array',
        'sanitize_callback' => 'odyssee_sanitize_precos',
        'default'           => array(),
    ) );

    // Sobrescritas dos serviços existentes (nome, descrição, thumbnail, informativo)
    register_setting( 'odyssee_precos_group', 'odyssee_servicos_overrides', array(
        'type'              => 'array',
        'sanitize_callback' => 'odyssee_sanitize_overrides',
        'default'           => array(),
    ) );
}
add_action( 'admin_init', 'odyssee_precos_settings_init' );

/**
 * Sanitiza sobrescritas de serviços existentes (nome, descrição, thumbnail).
 * Campos vazios são ignorados (usará o valor padrão do JS).
 */
function odyssee_sanitize_overrides( $input ) {
    if ( ! is_array( $input ) ) {
        return array();
    }
    $clean = array();
    foreach ( $input as $id => $fields ) {
        $id = sanitize_key( $id );
        if ( ! is_array( $fields ) ) {
            continue;
        }
        $sanitized = array();
        if ( isset( $fields['nome'] ) && trim( $fields['nome'] ) !== '' ) {
            $sanitized['nome'] = sanitize_text_field( $fields['nome'] );
        }
        if ( isset( $fields['descricao'] ) && trim( $fields['descricao'] ) !== '' ) {
            $sanitized['descricao'] = sanitize_textarea_field( $fields['descricao'] );
        }
        if ( isset( $fields['thumbnail'] ) && trim( $fields['thumbnail'] ) !== '' ) {
            $sanitized['thumbnail'] = esc_url_raw( $fields['thumbnail'] );
        }
        if ( isset( $fields['informativo'] ) && trim( $fields['informativo'] ) !== '' ) {
            $sanitized['informativo'] = sanitize_textarea_field( $fields['informativo'] );
        }
        if ( ! empty( $sanitized ) ) {
            $clean[ $id ] = $sanitized;
        }
    }
    return $clean;
}

/**
 * Sanitiza o array de preços — aceita apenas valores numéricos > 0.
 * Campos vazios são ignorados (usará o preço padrão do JS).
 */
function odyssee_sanitize_precos( $input ) {
    if ( ! is_array( $input ) ) {
        return array();
    }
    $clean = array();
    foreach ( $input as $key => $val ) {
        $key = sanitize_key( $key );
        // Pular campos vazios — manter o preço padrão do JS
        if ( $val === '' || $val === null ) {
            continue;
        }
        $val = floatval( $val );
        if ( $val > 0 ) {
            $clean[ $key ] = $val;
        }
    }
    return $clean;
}

/**
 * Renderiza a página admin de preços e descontos.
 * Exibe tabelas com todos os serviços agrupados por categoria,
 * permitindo sobrescrever preços padrão definidos no JS.
 * Também exibe preços unitários usados para calcular economia nos pacotes.
 */
function odyssee_precos_admin_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    if ( isset( $_GET['settings-updated'] ) ) {
        add_settings_error( 'odyssee_precos_messages', 'odyssee_precos_updated', 'Preços atualizados com sucesso!', 'updated' );
    }
    settings_errors( 'odyssee_precos_messages' );

    $definitions  = odyssee_get_price_definitions();
    $unit_defs    = odyssee_get_unit_price_definitions();
    $saved_prices = get_option( 'odyssee_precos', array() );
    $saved_units  = get_option( 'odyssee_precos_unitarios', array() );

    ?>
    <div class="wrap">
        <h1><span class="dashicons dashicons-money-alt" style="font-size: 1.3em; margin-right: 8px;"></span>Preços e Descontos — Odyssee</h1>
        <p>Edite os preços dos serviços e pacotes. Os valores são enviados automaticamente para o site. Deixe vazio para usar o preço padrão.</p>

        <form method="post" action="options.php">
            <?php settings_fields( 'odyssee_precos_group' ); ?>

            <?php foreach ( $definitions as $section_key => $section ) : ?>
                <h2 style="margin-top: 2rem; padding-bottom: 8px; border-bottom: 2px solid #2271b1;">
                    <?php echo esc_html( $section['label'] ); ?>
                </h2>
                <table class="form-table" role="presentation">
                    <?php foreach ( $section['items'] as $item_key => $item ) :
                        $current = isset( $saved_prices[ $item_key ] ) ? $saved_prices[ $item_key ] : '';
                    ?>
                    <tr>
                        <th scope="row">
                            <label for="odyssee_preco_<?php echo esc_attr( $item_key ); ?>">
                                <?php echo esc_html( $item['label'] ); ?>
                            </label>
                        </th>
                        <td>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="font-weight: 600;">R$</span>
                                <input type="number"
                                       name="odyssee_precos[<?php echo esc_attr( $item_key ); ?>]"
                                       id="odyssee_preco_<?php echo esc_attr( $item_key ); ?>"
                                       value="<?php echo esc_attr( $current ); ?>"
                                       step="0.01"
                                       min="0"
                                       style="width: 140px;"
                                       placeholder="<?php echo esc_attr( number_format( $item['default'], 2, ',', '' ) ); ?>">
                                <span class="description">Padrão: R$ <?php echo esc_html( number_format( $item['default'], 2, ',', '.' ) ); ?></span>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php endforeach; ?>

            <h2 style="margin-top: 2rem; padding-bottom: 8px; border-bottom: 2px solid #d63638;">
                Preços Unitários (Cálculo de Desconto)
            </h2>
            <p class="description">Esses valores são usados para calcular a economia exibida nos pacotes (ex: "Você economiza R$ X").</p>
            <table class="form-table" role="presentation">
                <?php foreach ( $unit_defs as $unit_key => $unit ) :
                    $current_unit = isset( $saved_units[ $unit_key ] ) ? $saved_units[ $unit_key ] : '';
                ?>
                <tr>
                    <th scope="row">
                        <label for="odyssee_unit_<?php echo esc_attr( $unit_key ); ?>">
                            <?php echo esc_html( $unit['label'] ); ?>
                        </label>
                    </th>
                    <td>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="font-weight: 600;">R$</span>
                            <input type="number"
                                   name="odyssee_precos_unitarios[<?php echo esc_attr( $unit_key ); ?>]"
                                   id="odyssee_unit_<?php echo esc_attr( $unit_key ); ?>"
                                   value="<?php echo esc_attr( $current_unit ); ?>"
                                   step="0.01"
                                   min="0"
                                   style="width: 140px;"
                                   placeholder="<?php echo esc_attr( number_format( $unit['default'], 2, ',', '' ) ); ?>">
                            <span class="description">Padrão: R$ <?php echo esc_html( number_format( $unit['default'], 2, ',', '.' ) ); ?></span>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>

            <?php submit_button( 'Salvar Preços' ); ?>
        </form>
    </div>
    <?php
}

// ==============================================
// PAINEL ADMIN: Gerenciar Produtos (CRUD)
// ==============================================

/**
 * Categorias disponíveis para os produtos.
 */
function odyssee_get_categorias_produto() {
    return array(
        'design-grafico'   => 'Design Gráfico',
        'edicao-de-video'  => 'Edição de Vídeo',
        'motion'           => 'Motion Graphics',
        'ilustracao'       => 'Ilustração Digital',
    );
}

/**
 * Registra a página de admin "Gerenciar Produtos"
 */
function odyssee_produtos_admin_menu() {
    add_theme_page(
        'Gerenciar Produtos',
        'Gerenciar Produtos',
        'manage_options',
        'odyssee-produtos',
        'odyssee_produtos_admin_page'
    );
}
add_action( 'admin_menu', 'odyssee_produtos_admin_menu' );

/**
 * Registra a opção que armazena os produtos.
 */
function odyssee_produtos_settings_init() {
    register_setting( 'odyssee_produtos_group', 'odyssee_produtos_custom', array(
        'type'              => 'array',
        'sanitize_callback' => 'odyssee_sanitize_produtos',
        'default'           => array(),
    ) );
}
add_action( 'admin_init', 'odyssee_produtos_settings_init' );

/**
 * Sanitiza o array de produtos customizados.
 * Valida e limpa todos os campos: id, nome, categoria, tipo, descricao,
 * informativo, itens, thumbnail e preco. Produtos sem nome são descartados.
 * O campo 'tipo' aceita apenas 'servico' ou 'pacote'.
 */
function odyssee_sanitize_produtos( $input ) {
    if ( ! is_array( $input ) ) {
        return array();
    }
    $clean = array();
    foreach ( $input as $produto ) {
        if ( ! is_array( $produto ) ) {
            continue;
        }
        $nome = isset( $produto['nome'] ) ? sanitize_text_field( $produto['nome'] ) : '';
        if ( empty( $nome ) ) {
            continue; // Ignora produtos sem nome
        }
        $tipo_val = isset( $produto['tipo'] ) ? sanitize_key( $produto['tipo'] ) : 'servico';
        if ( ! in_array( $tipo_val, array( 'servico', 'pacote' ), true ) ) {
            $tipo_val = 'servico';
        }
        $clean[] = array(
            'id'          => isset( $produto['id'] ) ? sanitize_key( $produto['id'] ) : sanitize_key( 'custom_' . wp_generate_password( 8, false ) ),
            'nome'        => $nome,
            'categoria'   => isset( $produto['categoria'] ) ? sanitize_key( $produto['categoria'] ) : 'design-grafico',
            'tipo'        => $tipo_val,
            'descricao'   => isset( $produto['descricao'] ) ? sanitize_textarea_field( $produto['descricao'] ) : '',
            'informativo' => isset( $produto['informativo'] ) ? sanitize_textarea_field( $produto['informativo'] ) : '',
            'itens'       => isset( $produto['itens'] ) ? sanitize_textarea_field( $produto['itens'] ) : '',
            'thumbnail'   => isset( $produto['thumbnail'] ) ? esc_url_raw( $produto['thumbnail'] ) : '',
            'preco'       => isset( $produto['preco'] ) ? floatval( $produto['preco'] ) : 0,
        );
    }
    return $clean;
}

/**
 * Processa ações de adicionar/remover/editar produto via POST.
 * Ações reconhecidas (via name do submit):
 *   - odyssee_add_produto    → adiciona novo produto ao array
 *   - odyssee_remove_produto → remove produto pelo ID
 *   - odyssee_save_produtos  → salva edições em massa dos produtos existentes
 * Todas as ações exigem nonce válido e permissão manage_options.
 */
function odyssee_produtos_handle_actions() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // Adicionar produto
    if ( isset( $_POST['odyssee_add_produto'] ) && check_admin_referer( 'odyssee_produto_action', 'odyssee_produto_nonce' ) ) {
        $produtos = get_option( 'odyssee_produtos_custom', array() );
        if ( ! is_array( $produtos ) ) {
            $produtos = array();
        }

        $nome = isset( $_POST['novo_nome'] ) ? sanitize_text_field( $_POST['novo_nome'] ) : '';
        if ( ! empty( $nome ) ) {
            $novo_id = 'custom_' . sanitize_key( str_replace( ' ', '_', strtolower( $nome ) ) ) . '_' . substr( md5( time() ), 0, 4 );
            $novo_tipo = isset( $_POST['novo_tipo'] ) ? sanitize_key( $_POST['novo_tipo'] ) : 'servico';
            if ( ! in_array( $novo_tipo, array( 'servico', 'pacote' ), true ) ) {
                $novo_tipo = 'servico';
            }
            $produtos[] = array(
                'id'          => $novo_id,
                'nome'        => $nome,
                'categoria'   => isset( $_POST['novo_categoria'] ) ? sanitize_key( $_POST['novo_categoria'] ) : 'design-grafico',
                'tipo'        => $novo_tipo,
                'descricao'   => isset( $_POST['novo_descricao'] ) ? sanitize_textarea_field( $_POST['novo_descricao'] ) : '',
                'informativo' => isset( $_POST['novo_informativo'] ) ? sanitize_textarea_field( $_POST['novo_informativo'] ) : '',
                'itens'       => isset( $_POST['novo_itens'] ) ? sanitize_textarea_field( $_POST['novo_itens'] ) : '',
                'thumbnail'   => isset( $_POST['novo_thumbnail'] ) ? esc_url_raw( $_POST['novo_thumbnail'] ) : '',
                'preco'       => isset( $_POST['novo_preco'] ) ? floatval( $_POST['novo_preco'] ) : 0,
            );
            update_option( 'odyssee_produtos_custom', $produtos );
            add_settings_error( 'odyssee_produtos_messages', 'added', 'Produto adicionado com sucesso!', 'updated' );
        }
    }

    // Remover produto
    if ( isset( $_POST['odyssee_remove_produto'] ) && check_admin_referer( 'odyssee_produto_action', 'odyssee_produto_nonce' ) ) {
        $remove_id = isset( $_POST['remove_id'] ) ? sanitize_key( $_POST['remove_id'] ) : '';
        if ( ! empty( $remove_id ) ) {
            $produtos = get_option( 'odyssee_produtos_custom', array() );
            if ( ! is_array( $produtos ) ) {
                $produtos = array();
            }
            $produtos = array_filter( $produtos, function( $p ) use ( $remove_id ) {
                return isset( $p['id'] ) && $p['id'] !== $remove_id;
            } );
            $produtos = array_values( $produtos ); // Reindexa
            update_option( 'odyssee_produtos_custom', $produtos );
            add_settings_error( 'odyssee_produtos_messages', 'removed', 'Produto removido com sucesso!', 'updated' );
        }
    }

    // Editar produto
    if ( isset( $_POST['odyssee_save_produtos'] ) && check_admin_referer( 'odyssee_produto_action', 'odyssee_produto_nonce' ) ) {
        $dados = isset( $_POST['produtos'] ) ? $_POST['produtos'] : array();
        if ( is_array( $dados ) ) {
            $produtos = array();
            foreach ( $dados as $p ) {
                if ( ! is_array( $p ) ) continue;
                $nome = isset( $p['nome'] ) ? sanitize_text_field( $p['nome'] ) : '';
                if ( empty( $nome ) ) continue;
                $edit_tipo = isset( $p['tipo'] ) ? sanitize_key( $p['tipo'] ) : 'servico';
                if ( ! in_array( $edit_tipo, array( 'servico', 'pacote' ), true ) ) {
                    $edit_tipo = 'servico';
                }
                $produtos[] = array(
                    'id'          => isset( $p['id'] ) ? sanitize_key( $p['id'] ) : '',
                    'nome'        => $nome,
                    'categoria'   => isset( $p['categoria'] ) ? sanitize_key( $p['categoria'] ) : 'design-grafico',
                    'tipo'        => $edit_tipo,
                    'descricao'   => isset( $p['descricao'] ) ? sanitize_textarea_field( $p['descricao'] ) : '',
                    'informativo' => isset( $p['informativo'] ) ? sanitize_textarea_field( $p['informativo'] ) : '',
                    'itens'       => isset( $p['itens'] ) ? sanitize_textarea_field( $p['itens'] ) : '',
                    'thumbnail'   => isset( $p['thumbnail'] ) ? esc_url_raw( $p['thumbnail'] ) : '',
                    'preco'       => isset( $p['preco'] ) ? floatval( $p['preco'] ) : 0,
                );
            }
            update_option( 'odyssee_produtos_custom', $produtos );
            add_settings_error( 'odyssee_produtos_messages', 'saved', 'Produtos atualizados com sucesso!', 'updated' );
        }
    }
}
add_action( 'admin_init', 'odyssee_produtos_handle_actions' );

/**
 * Renderiza a página admin de gerenciamento de produtos.
 */
function odyssee_produtos_admin_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    settings_errors( 'odyssee_produtos_messages' );

    $produtos   = get_option( 'odyssee_produtos_custom', array() );
    if ( ! is_array( $produtos ) ) {
        $produtos = array();
    }
    $categorias = odyssee_get_categorias_produto();

    // Serviços já existentes no site (espelhando os arrays do app.js)
    // Cada entrada contém: id (chave única), nome, descricao, thumbnail e tipo (servico|pacote)
    // Esses dados são usados como placeholder nos campos de edição da tabela admin
    $servicos_existentes = array(
        'design-grafico' => array(
            array( 'id' => 'logotipologo',   'nome' => 'Logotipo e Logo',               'descricao' => 'Uma logo única, criativa e estrategicamente feita para captar seu público alvo.', 'thumbnail' => 'https://odysseexp.com/wp-content/uploads/2025/09/Artboard-3-1-e1757565255458.png', 'tipo' => 'servico' ),
            array( 'id' => 'bannersocial',   'nome' => 'Banner para redes sociais',      'descricao' => 'Banner para YouTube, Facebook, site, etc.', 'thumbnail' => 'https://odysseexp.com/wp-content/uploads/2025/07/Screenshot-2025-06-30-184152.png', 'tipo' => 'servico' ),
            array( 'id' => 'cartao_visitas', 'nome' => 'Cartão de Visitas',              'descricao' => 'Design profissional para seu cartão de visitas.', 'thumbnail' => 'https://odysseexp.com/wp-content/uploads/2025/12/art-card-services.png', 'tipo' => 'servico' ),
            array( 'id' => 'flyer',          'nome' => 'Flyer',                           'descricao' => 'Arte criativa para seu flyer promocional.', 'thumbnail' => 'https://odysseexp.com/wp-content/uploads/2025/12/art-card-services-2.png', 'tipo' => 'servico' ),
            array( 'id' => 'convites',       'nome' => 'Convites',                        'descricao' => 'Design elegante para seus convites.', 'thumbnail' => 'https://odysseexp.com/wp-content/uploads/2025/12/art-card-services-5.png', 'tipo' => 'servico' ),
            array( 'id' => 'banner',         'nome' => 'Banner (impresso)',               'descricao' => 'Arte para banners de qualquer tamanho.', 'thumbnail' => 'https://odysseexp.com/wp-content/uploads/2025/12/art-card-services-4.png', 'tipo' => 'servico' ),
            array( 'id' => 'botton',         'nome' => 'Arte para Botton',                'descricao' => 'Arte simples para botton personalizado.', 'thumbnail' => 'https://odysseexp.com/wp-content/uploads/2025/12/art-card-services-1.png', 'tipo' => 'servico' ),
            array( 'id' => 'adesivos',       'nome' => 'Arte para Adesivos',              'descricao' => 'Design para adesivos personalizados.', 'thumbnail' => 'https://odysseexp.com/wp-content/uploads/2025/12/art-card-services-3.png', 'tipo' => 'servico' ),
            array( 'id' => 'visualid',       'nome' => 'Identidade Visual',               'descricao' => 'Manual de identidade completa, mockups, brindes e mais!', 'thumbnail' => 'https://odysseexp.com/wp-content/uploads/2025/05/Untitled-1-11.jpg', 'tipo' => 'servico' ),
            array( 'id' => 'designmegapackage',    'nome' => 'Mega Pacote Design',        'descricao' => '10 posts + 10 stories + 10 carrosséis', 'thumbnail' => '', 'tipo' => 'pacote' ),
            array( 'id' => 'designpremiumpackage', 'nome' => 'Pacote Premium Design',     'descricao' => '5 posts + 5 stories + 5 carrosséis', 'thumbnail' => '', 'tipo' => 'pacote' ),
            array( 'id' => 'postpackage',          'nome' => 'Pacote Posts',               'descricao' => '5 posts', 'thumbnail' => '', 'tipo' => 'pacote' ),
            array( 'id' => 'storiepackage',        'nome' => 'Pacote Stories',             'descricao' => '5 stories', 'thumbnail' => '', 'tipo' => 'pacote' ),
            array( 'id' => 'carrosselpackage',     'nome' => 'Pacote Carrosséis',          'descricao' => '5 carrosséis', 'thumbnail' => '', 'tipo' => 'pacote' ),
        ),
        'edicao-de-video' => array(
            array( 'id' => 'video_longo',          'nome' => 'Vídeo Longo',               'descricao' => 'Um vídeo longo, perfeito para YouTube.', 'thumbnail' => 'https://odysseexp.com/wp-content/uploads/2026/01/art-card-services.png', 'tipo' => 'servico' ),
            array( 'id' => 'video_curto',          'nome' => 'Vídeo Curto',               'descricao' => 'Um vídeo curto, ideal para redes sociais.', 'thumbnail' => 'https://odysseexp.com/wp-content/uploads/2026/01/art-card-services-1.png', 'tipo' => 'servico' ),
            array( 'id' => 'thumbnail',            'nome' => 'Thumbnail',                  'descricao' => 'Thumbnail personalizada para seu vídeo.', 'thumbnail' => 'https://odysseexp.com/wp-content/uploads/2026/01/art-card-services-2.png', 'tipo' => 'servico' ),
            array( 'id' => 'mega_pacote',          'nome' => 'Mega Pacote Vídeo',          'descricao' => '5 longos + 10 curtos + 15 thumbnails', 'thumbnail' => '', 'tipo' => 'pacote' ),
            array( 'id' => 'pacote_premium_a',     'nome' => 'Pacote Premium A',           'descricao' => '5 vídeos longos + 5 thumbnails', 'thumbnail' => '', 'tipo' => 'pacote' ),
            array( 'id' => 'pacote_premium_b',     'nome' => 'Pacote Premium B',           'descricao' => '5 vídeos curtos + 5 thumbnails', 'thumbnail' => '', 'tipo' => 'pacote' ),
            array( 'id' => 'cinco_videos_longos',  'nome' => 'Cinco Vídeos Longos',        'descricao' => '5 vídeos longos', 'thumbnail' => '', 'tipo' => 'pacote' ),
            array( 'id' => 'cinco_videos_curtos',  'nome' => 'Cinco Vídeos Curtos',        'descricao' => '5 vídeos curtos', 'thumbnail' => '', 'tipo' => 'pacote' ),
            array( 'id' => 'cinco_thumbnails',     'nome' => 'Cinco Thumbnails',            'descricao' => '5 thumbnails', 'thumbnail' => '', 'tipo' => 'pacote' ),
            array( 'id' => 'dez_thumbnails',       'nome' => 'Dez Thumbnails',              'descricao' => '10 thumbnails', 'thumbnail' => '', 'tipo' => 'pacote' ),
        ),
        'motion' => array(
            array( 'id' => 'intro_animada',  'nome' => 'Intro Animada',      'descricao' => 'Uma intro simples e criativa com elementos 2D', 'thumbnail' => 'https://odysseexp.com/wp-content/uploads/2026/01/FalaAiWillcomsom-ezgif.com-optimize.gif', 'tipo' => 'servico' ),
            array( 'id' => 'artmotion',      'nome' => 'Arte Animada',       'descricao' => 'Sua arte animada com elementos 2D', 'thumbnail' => 'https://odysseexp.com/wp-content/uploads/2025/06/harley-animated-.gif', 'tipo' => 'servico' ),
            array( 'id' => 'logomotion',     'nome' => 'Logo Animado',       'descricao' => 'Logo animado para vídeos e apresentações', 'thumbnail' => 'https://odysseexp.com/wp-content/uploads/2025/12/art-card-services-motion.png', 'tipo' => 'servico' ),
            array( 'id' => 'motionmoldura',  'nome' => 'Moldura Animada',    'descricao' => 'Moldura animada para transmissões ao vivo', 'thumbnail' => 'https://odysseexp.com/wp-content/uploads/2025/12/art-card-services-motion.png', 'tipo' => 'servico' ),
            array( 'id' => 'waitscreen',     'nome' => 'Tela de Espera',     'descricao' => 'Animação em looping para telas de espera', 'thumbnail' => 'https://odysseexp.com/wp-content/uploads/2025/12/art-card-services-motion.png', 'tipo' => 'servico' ),
            array( 'id' => 'motionbanner',   'nome' => 'Banner Animado',     'descricao' => 'O banner da sua marca animado', 'thumbnail' => 'https://odysseexp.com/wp-content/uploads/2025/12/art-card-services-motion.png', 'tipo' => 'servico' ),
        ),
        'ilustracao' => array(
            array( 'id' => 'fanart_anime',             'nome' => 'Arte Estilo Anime',         'descricao' => 'Arte digital no estilo anime', 'thumbnail' => 'https://odysseexp.com/wp-content/uploads/2026/01/art-card-services_14.png', 'tipo' => 'servico' ),
            array( 'id' => 'fanart_cartoon',           'nome' => 'Arte Estilo Cartoon',       'descricao' => 'Arte digital no estilo cartoon', 'thumbnail' => 'https://odysseexp.com/wp-content/uploads/2026/01/art-card-services_13.png', 'tipo' => 'servico' ),
            array( 'id' => 'fanart_chibi',             'nome' => 'Arte Estilo Chibi',         'descricao' => 'Arte digital no estilo chibi', 'thumbnail' => 'https://odysseexp.com/wp-content/uploads/2026/01/art-card-services_15.png', 'tipo' => 'servico' ),
            array( 'id' => 'fanart_pixelart',          'nome' => 'Arte Estilo Pixel Art',     'descricao' => 'Arte digital no estilo pixel art', 'thumbnail' => 'https://odysseexp.com/wp-content/uploads/2026/01/art-card-services_12.png', 'tipo' => 'servico' ),
            array( 'id' => 'fanart_vetorial',          'nome' => 'Arte Estilo Vetorial',      'descricao' => 'Arte digital no estilo vetorial', 'thumbnail' => 'https://odysseexp.com/wp-content/uploads/2025/05/Jinx-Powder-Vector-lightroom-scaled-e1747298364610.jpg', 'tipo' => 'servico' ),
            array( 'id' => 'personagem_rpg',           'nome' => 'Personagem Token RPG',      'descricao' => 'Arte para RPG no estilo token', 'thumbnail' => 'https://odysseexp.com/wp-content/uploads/2025/12/art-card-services-motion.png', 'tipo' => 'servico' ),
            array( 'id' => 'ilustracao_perfil',        'nome' => 'Ilustração Perfil/Busto',   'descricao' => 'Arte de perfil nos diversos estilos', 'thumbnail' => 'https://odysseexp.com/wp-content/uploads/2026/01/art-card-services_11.png', 'tipo' => 'servico' ),
            array( 'id' => 'ilustracao_corpo_inteiro', 'nome' => 'Ilustração Corpo Inteiro',  'descricao' => 'Arte de corpo inteiro nos diversos estilos', 'thumbnail' => 'https://odysseexp.com/wp-content/uploads/2026/01/art-card-services_8.png', 'tipo' => 'servico' ),
            array( 'id' => 'esboco_rapido',            'nome' => 'Esboço Rápido',             'descricao' => 'Um esboço rápido e simples', 'thumbnail' => 'https://odysseexp.com/wp-content/uploads/2026/01/art-card-services_10.png', 'tipo' => 'servico' ),
            array( 'id' => 'storyboard',               'nome' => 'Storyboard',                'descricao' => 'Storyboard cena a cena', 'thumbnail' => 'https://odysseexp.com/wp-content/uploads/2026/01/art-card-services_9.png', 'tipo' => 'servico' ),
            array( 'id' => 'cenario_digital',          'nome' => 'Cenário Digital',           'descricao' => 'Cenário digital completo', 'thumbnail' => '', 'tipo' => 'servico' ),
        ),
    );

    // Preços salvos para exibir ao lado dos serviços existentes
    $precos_salvos = get_option( 'odyssee_precos', array() );
    if ( ! is_array( $precos_salvos ) ) {
        $precos_salvos = array();
    }
    // Sobrescritas salvas (nome, descrição, thumbnail)
    $overrides_salvos = get_option( 'odyssee_servicos_overrides', array() );
    if ( ! is_array( $overrides_salvos ) ) {
        $overrides_salvos = array();
    }
    $definitions = odyssee_get_price_definitions();
    // Montar mapa de preços padrão
    $precos_default = array();
    foreach ( $definitions as $section ) {
        foreach ( $section['items'] as $key => $item ) {
            $precos_default[ $key ] = $item['default'];
        }
    }

    ?>
    <div class="wrap">
        <h1><span class="dashicons dashicons-store" style="font-size: 1.3em; margin-right: 8px;"></span>Gerenciar Produtos — Odyssee</h1>
        <p>Veja todos os serviços do site, edite nome, descrição, capa e preços. Deixe um campo vazio para usar o valor padrão do código.</p>

        <!-- SEÇÃO: Serviços já existentes no site (edição completa) -->
        <div style="background: #fff; border: 1px solid #c3c4c7; padding: 20px; margin: 20px 0; border-radius: 4px;">
            <h2 style="margin-top: 0;"><span class="dashicons dashicons-admin-links" style="color: #2271b1;"></span> Serviços Existentes no Site</h2>
            <p class="description">Edite qualquer campo dos serviços existentes. Deixe vazio para manter o valor padrão. A capa aceita URLs de imagem (use a Biblioteca de Mídia do WP para fazer upload).</p>

            <form method="post" action="options.php">
                <?php settings_fields( 'odyssee_precos_group' ); ?>

                <?php foreach ( $servicos_existentes as $cat_slug => $servicos ) :
                    $cat_label = isset( $categorias[ $cat_slug ] ) ? $categorias[ $cat_slug ] : $cat_slug;
                ?>
                <h3 style="margin-top: 20px; padding-bottom: 6px; border-bottom: 1px solid #ddd; color: #2271b1;">
                    <?php echo esc_html( $cat_label ); ?>
                </h3>
                <table class="widefat striped" style="margin-bottom: 12px;">
                    <thead>
                        <tr>
                            <th style="width: 60px;">Capa</th>
                            <th style="width: 160px;">Nome</th>
                            <th>Descrição</th>
                            <th>Informativo <span class="dashicons dashicons-info" title="Texto do tooltip ao clicar no ícone (i). Ex: Prazo, revisões, formato de entrega." style="font-size: 14px; color: #999;"></span></th>
                            <th style="width: 200px;">URL da Capa</th>
                            <th style="width: 110px;">Preço (R$)</th>
                            <th style="width: 80px;">Padrão</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $servicos as $svc ) :
                            $svc_id       = $svc['id'];
                            $preco_atual  = isset( $precos_salvos[ $svc_id ] ) ? $precos_salvos[ $svc_id ] : '';
                            $preco_padrao = isset( $precos_default[ $svc_id ] ) ? $precos_default[ $svc_id ] : 0;
                            $override     = isset( $overrides_salvos[ $svc_id ] ) ? $overrides_salvos[ $svc_id ] : array();
                            $nome_over    = isset( $override['nome'] ) ? $override['nome'] : '';
                            $desc_over    = isset( $override['descricao'] ) ? $override['descricao'] : '';
                            $thumb_over   = isset( $override['thumbnail'] ) ? $override['thumbnail'] : '';
                            $info_over    = isset( $override['informativo'] ) ? $override['informativo'] : '';
                            $thumb_show   = ! empty( $thumb_over ) ? $thumb_over : $svc['thumbnail'];
                        ?>
                        <tr>
                            <td style="vertical-align: middle; text-align: center;">
                                <?php if ( ! empty( $thumb_show ) ) : ?>
                                    <img src="<?php echo esc_url( $thumb_show ); ?>" style="width: 50px; height: 34px; object-fit: cover; border-radius: 3px; border: 1px solid #ddd;" alt="">
                                <?php else : ?>
                                    <span class="dashicons dashicons-format-image" style="font-size: 28px; color: #ccc;"></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <input type="text"
                                       name="odyssee_servicos_overrides[<?php echo esc_attr( $svc_id ); ?>][nome]"
                                       value="<?php echo esc_attr( $nome_over ); ?>"
                                       placeholder="<?php echo esc_attr( $svc['nome'] ); ?>"
                                       style="width: 100%; font-size: 12px;">
                            </td>
                            <td>
                                <textarea name="odyssee_servicos_overrides[<?php echo esc_attr( $svc_id ); ?>][descricao]"
                                          rows="2"
                                          placeholder="<?php echo esc_attr( $svc['descricao'] ); ?>"
                                          style="width: 100%; font-size: 12px;"><?php echo esc_textarea( $desc_over ); ?></textarea>
                            </td>
                            <td>
                                <textarea name="odyssee_servicos_overrides[<?php echo esc_attr( $svc_id ); ?>][informativo]"
                                          rows="2"
                                          placeholder="Ex: Prazo: 3-5 dias. 2 revisões inclusas."
                                          style="width: 100%; font-size: 11px;"><?php echo esc_textarea( $info_over ); ?></textarea>
                            </td>
                            <td>
                                <input type="url"
                                       name="odyssee_servicos_overrides[<?php echo esc_attr( $svc_id ); ?>][thumbnail]"
                                       value="<?php echo esc_attr( $thumb_over ); ?>"
                                       placeholder="<?php echo esc_attr( $svc['thumbnail'] ); ?>"
                                       style="width: 100%; font-size: 11px;">
                            </td>
                            <td>
                                <input type="number"
                                       name="odyssee_precos[<?php echo esc_attr( $svc_id ); ?>]"
                                       value="<?php echo esc_attr( $preco_atual ); ?>"
                                       step="0.01" min="0"
                                       style="width: 100px;"
                                       placeholder="<?php echo esc_attr( number_format( $preco_padrao, 2, '.', '' ) ); ?>">
                            </td>
                            <td style="color: #888; font-size: 11px;">
                                R$ <?php echo esc_html( number_format( $preco_padrao, 2, ',', '.' ) ); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endforeach; ?>

                <?php submit_button( 'Salvar Alterações dos Serviços' ); ?>
            </form>
        </div>

        <hr style="margin: 30px 0;">

        <!-- FORMULÁRIO: Adicionar novo produto -->
        <div style="background: #fff; border: 1px solid #c3c4c7; border-left: 4px solid #2271b1; padding: 20px; margin: 20px 0; border-radius: 4px;">
            <h2 style="margin-top: 0;"><span class="dashicons dashicons-plus" style="color: #2271b1;"></span> Adicionar Novo Produto Personalizado</h2>
            <p class="description">Produtos adicionados aqui aparecem automaticamente na página de serviços, na categoria escolhida, com o botão de contratar pelo WhatsApp.</p>
            <form method="post">
                <?php wp_nonce_field( 'odyssee_produto_action', 'odyssee_produto_nonce' ); ?>
                <table class="form-table" role="presentation">
                    <tr>
                        <th><label for="novo_nome">Nome do Produto *</label></th>
                        <td><input type="text" name="novo_nome" id="novo_nome" class="regular-text" required placeholder="Ex: Logo Animada Premium"></td>
                    </tr>
                    <tr>
                        <th><label for="novo_categoria">Categoria</label></th>
                        <td>
                            <select name="novo_categoria" id="novo_categoria">
                                <?php foreach ( $categorias as $slug => $label ) : ?>
                                    <option value="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $label ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="novo_tipo">Tipo</label></th>
                        <td>
                            <select name="novo_tipo" id="novo_tipo" onchange="document.getElementById('novo_itens_row').style.display = this.value === 'pacote' ? '' : 'none';">
                                <option value="servico">Serviço Individual</option>
                                <option value="pacote">Pacote</option>
                            </select>
                            <p class="description">Serviço Individual aparece na seção "Serviços e Preços". Pacote aparece na seção "Pacotes Promocionais".</p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="novo_descricao">Descrição</label></th>
                        <td><textarea name="novo_descricao" id="novo_descricao" rows="3" cols="60" class="large-text" placeholder="Breve descrição do serviço/produto"></textarea></td>
                    </tr>
                    <tr>
                        <th><label for="novo_informativo">Informativo</label></th>
                        <td>
                            <textarea name="novo_informativo" id="novo_informativo" rows="2" cols="60" class="large-text" placeholder="Ex: Tempo de entrega: 3-5 dias úteis. Inclui 2 revisões."></textarea>
                            <p class="description">Texto do tooltip ao clicar no ícone (i) do card. Prazo, revisões, formato de entrega, etc.</p>
                        </td>
                    </tr>
                    <tr id="novo_itens_row" style="display: none;">
                        <th><label for="novo_itens">Itens do Pacote</label></th>
                        <td>
                            <textarea name="novo_itens" id="novo_itens" rows="3" cols="60" class="large-text" placeholder="5 vídeos longos&#10;10 thumbnails&#10;3 revisões"></textarea>
                            <p class="description">Um item por linha. Cada linha aparece como item na lista do pacote.</p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="novo_thumbnail">URL da Thumbnail</label></th>
                        <td>
                            <input type="url" name="novo_thumbnail" id="novo_thumbnail" class="large-text" placeholder="https://odysseexp.com/wp-content/uploads/...">
                            <p class="description">Cole a URL da imagem. Use a Biblioteca de Mídia do WP para fazer upload e copiar a URL.</p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="novo_preco">Preço (R$)</label></th>
                        <td><input type="number" name="novo_preco" id="novo_preco" step="0.01" min="0" style="width: 140px;" placeholder="0,00"></td>
                    </tr>
                </table>
                <?php submit_button( 'Adicionar Produto', 'primary', 'odyssee_add_produto' ); ?>
            </form>
        </div>

        <?php if ( ! empty( $produtos ) ) : ?>
        <!-- LISTA: Produtos personalizados cadastrados -->
        <div style="background: #fff; border: 1px solid #c3c4c7; border-left: 4px solid #00a32a; padding: 20px; margin: 20px 0; border-radius: 4px;">
            <h2 style="margin-top: 0;"><span class="dashicons dashicons-list-view" style="color: #00a32a;"></span> Produtos Personalizados (<?php echo count( $produtos ); ?>)</h2>
            <form method="post">
                <?php wp_nonce_field( 'odyssee_produto_action', 'odyssee_produto_nonce' ); ?>
                <table class="widefat striped" style="margin-top: 12px;">
                    <thead>
                        <tr>
                            <th style="width: 50px;">Thumb</th>
                            <th>Nome</th>
                            <th>Categoria</th>
                            <th style="width: 130px;">Tipo</th>
                            <th>Descrição / Itens</th>
                            <th style="width: 100px;">Preço</th>
                            <th style="width: 80px;">Remover</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $produtos as $i => $prod ) :
                            $prod_tipo  = isset( $prod['tipo'] ) ? $prod['tipo'] : 'servico';
                            $prod_itens = isset( $prod['itens'] ) ? $prod['itens'] : '';
                        ?>
                        <tr>
                            <td>
                                <?php if ( ! empty( $prod['thumbnail'] ) ) : ?>
                                    <img src="<?php echo esc_url( $prod['thumbnail'] ); ?>" style="width: 48px; height: 48px; object-fit: cover; border-radius: 4px;">
                                <?php else : ?>
                                    <span class="dashicons dashicons-format-image" style="font-size: 32px; color: #ccc;"></span>
                                <?php endif; ?>
                                <input type="hidden" name="produtos[<?php echo $i; ?>][id]" value="<?php echo esc_attr( $prod['id'] ); ?>">
                                <input type="hidden" name="produtos[<?php echo $i; ?>][thumbnail]" value="<?php echo esc_attr( $prod['thumbnail'] ); ?>">
                            </td>
                            <td>
                                <input type="text" name="produtos[<?php echo $i; ?>][nome]" value="<?php echo esc_attr( $prod['nome'] ); ?>" class="regular-text" style="width: 100%;">
                            </td>
                            <td>
                                <select name="produtos[<?php echo $i; ?>][categoria]" style="width: 100%;">
                                    <?php foreach ( $categorias as $slug => $label ) : ?>
                                        <option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $prod['categoria'], $slug ); ?>><?php echo esc_html( $label ); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <select name="produtos[<?php echo $i; ?>][tipo]" style="width: 100%;" onchange="document.getElementById('itens_row_<?php echo $i; ?>').style.display = this.value === 'pacote' ? '' : 'none';">
                                    <option value="servico" <?php selected( $prod_tipo, 'servico' ); ?>>Serviço</option>
                                    <option value="pacote" <?php selected( $prod_tipo, 'pacote' ); ?>>Pacote</option>
                                </select>
                            </td>
                            <td>
                                <textarea name="produtos[<?php echo $i; ?>][descricao]" rows="2" style="width: 100%;" placeholder="Descrição do serviço"><?php echo esc_textarea( $prod['descricao'] ); ?></textarea>
                                <div style="margin-top: 6px;">
                                    <label style="font-size: 11px; color: #666;">Informativo (tooltip do ícone <em>i</em>):</label>
                                    <textarea name="produtos[<?php echo $i; ?>][informativo]" rows="2" style="width: 100%; font-size: 11px;" placeholder="Ex: Prazo: 3-5 dias. 2 revisões."><?php echo esc_textarea( isset( $prod['informativo'] ) ? $prod['informativo'] : '' ); ?></textarea>
                                </div>
                                <div id="itens_row_<?php echo $i; ?>" style="<?php echo $prod_tipo === 'pacote' ? '' : 'display: none;'; ?> margin-top: 6px;">
                                    <label style="font-size: 11px; color: #666;">Itens do pacote (um por linha):</label>
                                    <textarea name="produtos[<?php echo $i; ?>][itens]" rows="2" style="width: 100%; font-size: 11px;" placeholder="5 vídeos longos&#10;10 thumbnails"><?php echo esc_textarea( $prod_itens ); ?></textarea>
                                </div>
                            </td>
                            <td>
                                <input type="number" name="produtos[<?php echo $i; ?>][preco]" value="<?php echo esc_attr( $prod['preco'] ); ?>" step="0.01" min="0" style="width: 90px;">
                            </td>
                            <td></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div style="margin-top: 16px; display: flex; gap: 12px; align-items: center;">
                    <?php submit_button( 'Salvar Alterações', 'primary', 'odyssee_save_produtos', false ); ?>
                </div>
            </form>

            <!-- Botões de remover (forms separados para cada um) -->
            <h3 style="margin-top: 24px;">Remover Produtos Personalizados</h3>
            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                <?php foreach ( $produtos as $prod ) : ?>
                <form method="post" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja remover &quot;<?php echo esc_js( $prod['nome'] ); ?>&quot;?');">
                    <?php wp_nonce_field( 'odyssee_produto_action', 'odyssee_produto_nonce' ); ?>
                    <input type="hidden" name="remove_id" value="<?php echo esc_attr( $prod['id'] ); ?>">
                    <button type="submit" name="odyssee_remove_produto" class="button button-link-delete" style="color: #b32d2e;">
                        <span class="dashicons dashicons-trash" style="font-size: 14px; vertical-align: text-bottom;"></span>
                        <?php echo esc_html( $prod['nome'] ); ?>
                    </button>
                </form>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php
}