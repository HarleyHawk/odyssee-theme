// --- 1. DADOS (AGORA VINDO DO WORDPRESS) ---
// Arquivo principal do tema: dados, renderizacao, interacoes e traducoes.

/* ==============================================
   ARQUIVO app.js (VERSÃO FINAL P/ WORDPRESS)
   ============================================== */

// ==============================================
// SEGURANÇA: Funções de sanitização contra XSS
// ==============================================

/**
 * Escapa caracteres especiais HTML
 * @param {string} str - String a ser escapada
 * @returns {string} String escapada
 */
function escapeHtml(str) {
    if (typeof str !== 'string') return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

/**
 * Decodifica entidades HTML como &#8211; para caracteres reais (–)
 * @param {string} str - String possivelmente com entidades HTML
 * @returns {string} String com entidades decodificadas
 */
function decodeHtmlEntities(str) {
    if (!str || typeof str !== 'string') return '';
    try {
        // Usamos DOMParser para interpretar como HTML e obter textContent
        const doc = new DOMParser().parseFromString(str, 'text/html');
        return doc.documentElement.textContent || '';
    } catch (e) {
        // Fallback simples: tenta substituir algumas entidades comuns
        return str.replace(/&#8211;/g, '–').replace(/&amp;#/g, '&#');
    }
}

/**
 * Cria elementos DOM de forma segura (evita innerHTML)
 * @param {string} tag - Nome da tag HTML
 * @param {object} attrs - Atributos do elemento
 * @param {string|HTMLElement} content - Conteúdo da tag
 * @returns {HTMLElement}
 */
function createElement(tag, attrs = {}, content = '') {
    const el = document.createElement(tag);

    Object.keys(attrs).forEach(key => {
        if (key === 'class') {
            el.className = attrs[key];
        } else if (key === 'data' && typeof attrs[key] === 'object') {
            Object.keys(attrs[key]).forEach(dataKey => {
                el.dataset[dataKey] = attrs[key][dataKey];
            });
        } else if (key.startsWith('on')) {
            // Não permitir event handlers inline
            console.warn(`Atributo ${key} não permitido por segurança`);
        } else {
            el.setAttribute(key, attrs[key]);
        }
    });

    if (typeof content === 'string') {
        el.textContent = content;
    } else if (content instanceof HTMLElement) {
        el.appendChild(content);
    }

    return el;
}

/**
 * Valida e sanitiza URLs antes de usar em href
 * @param {string} url - URL a validizar
 * @returns {string} URL segura ou vazia
 */
function sanitizeUrl(url) {
    if (!url) return '';

    // Whitelist de protocolos seguros
    const safeProtocols = ['http://', 'https://', 'mailto:', 'tel:', 'whatsapp:', '/'];
    const trimmed = url.trim().toLowerCase();

    const isSafe = safeProtocols.some(protocol => trimmed.startsWith(protocol));

    if (!isSafe) {
        console.warn(`URL insegura detectada e bloqueada: ${url}`);
        return '';
    }

    return url;
}

/**
 * Sanitiza entrada de busca do usuário
 * Remove tags HTML e caracteres perigosos
 * @param {string} input - Texto de entrada
 * @returns {string} Texto sanitizado
 */
function sanitizeSearchInput(input) {
    if (!input) return '';

    // Remove tags HTML
    let sanitized = input.replace(/<[^>]*>/g, '');

    // Remove caracteres especiais perigosos (mantém pontuação comum)
    sanitized = sanitized.replace(/[<>"{};]/g, '');

    // Limita o tamanho da busca (previne DoS)
    if (sanitized.length > 200) {
        sanitized = sanitized.substring(0, 200);
    }

    return sanitized.trim();
}

// ==============================================
// TRADUÇÕES: Textos da interface em PT-BR e EN-US
// Chaves usadas via data-key nos elementos HTML
// ==============================================
const translations = {
    // Português Brasileiro
    'pt': {
        'titulo': "Odyssee Experience",
        'subtitulo': "mais que um portfólio, uma experiência web criativa",
        'hero-slogan': 'Experiencie o Próximo Nível da Criatividade',
        'escolha-idioma': "Escolha um idioma de preferência",
        'escolha-tema': "Escolha um tema para começar a navegar",
        'aviso-tema': "Você pode alterar isso depois nas configurações.",
        'recentes': 'Trabalhos Recentes',
        'design-grafico': 'Design Gráfico',
        'edicao-video': 'Edição de Vídeo',
        'motion': 'Motion',
        'ilustracao': 'Ilustração Digital',
        'impressos': 'Impressos',
        'contato': 'Dúvidas?',
        'contato-sub': 'Entre em contato comigo.',
        'fale-whatsapp': 'Fale Comigo no WhatsApp',
        'servicos-precos': 'Serviços e Preços',
        'todos-posts': 'Todos os Posts',
        'carregando-projetos': 'Carregando projetos...',
        'buscar-placeholder': 'Buscar por título, tag, ou conteúdo...',
        'filtrar-por': 'Filtrar por:',
        'todos': 'Todos',
        'desenvolvido-em': 'Projeto desenvolvido em',
        'clique-detalhes': 'Clique para ver os detalhes completos deste trabalho.',
        'ver-detalhes': 'Ver Detalhes',
        'portfolio': 'portfolio',
        // Subcategorias
        'carrossel': 'Carrossel',
        'video-curto': 'Vídeo Curto',
        'video-longo': 'Vídeo Longo',
        'identidade-visual': 'Identidade Visual',
        'logotipo-logo': 'Logotipo/Logo',
        'swipe': 'Deslize',
        'esboco-de-perfil': 'Esboço de Perfil',
        'fanart-anime': 'Fanart Anime',
        'fanart-cartoon': 'Fanart Cartoon',
        'fanart-chibi': 'Fanart Chibi',
        'servicos': 'Serviços',
        'servicos-titulo': 'Serviços e Preços',
        'servicos-subtitulo': 'Confira todos os nossos serviços e pacotes promocionais',
        'exemplo-produto': 'Exemplo de Produto',
        'exemplo-desc': 'Este é um exemplo visual de como seu produto pode ficar.',
        'ver-servicos': 'Ver Serviços e Preços'
    },
    // Inglês (EN-US)
    'en': {
        'titulo': "Odyssee Experience",
        'subtitulo': "more than a portfolio, a creative web experience",
        'hero-slogan': 'Experience the Next Level of Creativity',
        'escolha-idioma': "Choose your preferred language",
        'escolha-tema': "Choose a theme to start browsing",
        'aviso-tema': "You can change this later in the settings.",
        'recentes': 'Recent Works',
        'design-grafico': 'Graphic Design',
        'edicao-video': 'Video Editing',
        'motion': 'Motion',
        'ilustracao': 'Digital Illustration',
        'impressos': 'Print',
        'contato': 'Questions?',
        'contato-sub': 'Get in touch with me.',
        'fale-whatsapp': 'Contact me on WhatsApp',
        'servicos-precos': 'Services & Pricing',
        'todos-posts': 'All Posts',
        'carregando-projetos': 'Loading projects...',
        'buscar-placeholder': 'Search by title, tag, or content...',
        'filtrar-por': 'Filter by:',
        'todos': 'All',
        'desenvolvido-em': 'Project developed on',
        'clique-detalhes': 'Click to see the complete details of this work.',
        'ver-detalhes': 'View Details',
        'portfolio': 'portfolio',
        // Subcategorias
        'carrossel': 'Carousel',
        'video-curto': 'Short Video',
        'video-longo': 'Long Video',
        'identidade-visual': 'Visual Identity',
        'logotipo-logo': 'Logo/Branding',
        'swipe': 'Swipe',
        'esboco-de-perfil': 'Profile Sketch',
        'fanart-anime': 'Anime Fanart',
        'fanart-cartoon': 'Cartoon Fanart',
        'fanart-chibi': 'Chibi Fanart',
        'servicos': 'Services',
        'servicos-titulo': 'Services & Pricing',
        'servicos-subtitulo': 'Check out all our services and promotional packages',
        'exemplo-produto': 'Product Example',
        'exemplo-desc': 'This is a visual example of how your product could look.',
        'ver-servicos': 'View Services & Pricing'
    }
};

// Chaves adicionais de UI (adicionadas separadamente para organização)
translations.pt['sobre-mim'] = 'Sobre Mim';
translations.en['sobre-mim'] = 'About Me';
translations.pt['configuracoes'] = 'Configurações';
translations.en['configuracoes'] = 'Settings';
translations.pt['idioma'] = 'Idioma';
translations.en['idioma'] = 'Language';
translations.pt['tema'] = 'Tema';
translations.en['tema'] = 'Theme';
translations.pt['cor-destaque'] = 'Cor de Destaque';
translations.en['cor-destaque'] = 'Accent Color';
translations.pt['menu'] = 'Menu';
translations.en['menu'] = 'Menu';
translations.pt['continuar'] = 'Continuar →';
translations.en['continuar'] = 'Continue →';

// Chaves de economia traduzidas (usadas no cálculo de desconto dos pacotes)
translations.pt['you-save-prefix'] = 'Você economiza R$ ';
translations.en['you-save-prefix'] = 'You save R$ ';
translations.pt['consulte'] = 'Consulte';
translations.en['consulte'] = 'Contact';
translations.pt['no-results'] = 'Nenhum projeto encontrado com esses termos.';
translations.en['no-results'] = 'No projects found for these terms.';
// Service/package section keys
translations.pt['pacotes-promocionais'] = 'Pacotes Promocionais';
translations.en['pacotes-promocionais'] = 'Promotional Packages';

// Ilustração Digital - Configurador
translations.pt['ilustracao-tutorial'] = 'Você primeiro escolhe o estilo da arte e depois escolhe qual será o tipo da arte e por último adicionais. Após selecionar as opções, basta interagir com o botão contratar que você será redirecionado para contato com o ilustrador.';
translations.en['ilustracao-tutorial'] = 'First choose the art style, then choose the type of art, and finally the add-ons. After selecting the options, simply click the hire button to be redirected to contact the illustrator.';
translations.pt['ilustracao-estilo-label'] = 'Estilo da Arte:';
translations.en['ilustracao-estilo-label'] = 'Art Style:';
translations.pt['ilustracao-tipo-label'] = 'Tipo da Arte:';
translations.en['ilustracao-tipo-label'] = 'Art Type:';
translations.pt['ilustracao-adicionais-label'] = 'Adicionais:';
translations.en['ilustracao-adicionais-label'] = 'Add-ons:';
translations.pt['ilustracao-resumo-title'] = 'Seu Serviço';
translations.en['ilustracao-resumo-title'] = 'Your Service';

// ========== FUNÇÕES DE OTIMIZAÇÃO ==========
// Throttle: limita a frequência de execução de uma função
function throttle(func, limit) {
    let inThrottle;
    return function (...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// Debounce: aguarda o usuário parar de disparar o evento para executar
function debounce(func, delay) {
    let timeoutId;
    return function (...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func.apply(this, args), delay);
    };
}

// ==============================================
// MAPEAMENTO DE SLUGS: WordPress -> chave de tradução
// Permite que categorias do WP com slugs variados
// (ex: 'edicao-de-video' ou 'edicao-video') apontem
// para a mesma chave de tradução.
// ==============================================
const wpSlugToCategoryKey = {
    'design-grafico': 'design-grafico',
    'edicao-video': 'edicao-video',
    'edicao-de-video': 'edicao-video',
    'motion': 'motion',
    'motion-graphics': 'motion',
    'ilustracao': 'ilustracao',
    'ilustracao-digital': 'ilustracao',
    'impressos': 'impressos',
    // Subcategorias
    'carrossel': 'carrossel',
    'video-curto': 'video-curto',
    'video-longo': 'video-longo',
    'identidade-visual': 'identidade-visual',
    'logotipo-logo': 'logotipo-logo',
    'esboco-de-perfil': 'esboco-de-perfil',
    'fanart-anime': 'fanart-anime',
    'fanart-cartoon': 'fanart-cartoon',
    'fanart-chibi': 'fanart-chibi'
};

// ==============================================
// GLOSSÁRIO DE TÍTULOS: Tradução manual de posts
// Formato: 'Título em PT': 'Title in EN'
// Usado como fallback quando o post não tem
// o custom field 'post_title_en' preenchido.
// ==============================================
const postTitleTranslations = {
    // Exemplo:
    // 'Identidade Visual Completa': 'Complete Visual Identity',
    // 'Motion Graphics para YouTube': 'YouTube Motion Graphics',
};

// Helper: retorna tradução do título do post
// Prioridade: 1) custom field post_title_en  2) glossário manual  3) título original
function getPostTitleTranslation(postTitle, customMetaEN) {
    if (!postTitle) return '';

    // 1. Se existir custom field 'post_title_en' no post, usa
    if (customMetaEN && customMetaEN.trim()) {
        return customMetaEN;
    }

    // 2. Se existir no glossário, usa
    if (postTitleTranslations[postTitle]) {
        return postTitleTranslations[postTitle];
    }

    // 3. Senão retorna vazio para manter original
    return '';
}

// ==============================================
// TRADUÇÕES DE SERVIÇOS E PACOTES
// Cada serviço/pacote possui tradução PT e EN
// para título (title) e descrição (desc).
// Chave = ID do serviço (ex: 'logotipologo')
// ==============================================
const serviceTranslations = {
    'logotipologo': {
        pt: { title: 'Logotipo e Logo', desc: 'Uma logo única, criativa e estrategicamente feita para captar seu público alvo.' },
        en: { title: 'Logo and Branding', desc: 'A unique, creative logo strategically designed to reach your target audience.' }
    },
    'bannersocial': {
        pt: { title: 'Banner para redes sociais', desc: 'Banner para YouTube, Facebook, site, etc.' },
        en: { title: 'Social Media Banner', desc: 'Banners for YouTube, Facebook, websites, etc.' }
    },
    'visualid': {
        pt: { title: 'Identidade Visual', desc: 'Manual de identidade completa, mockups, brindes e mais!' },
        en: { title: 'Visual Identity', desc: 'Complete identity manual, mockups, swag and more!' }
    },
    'cartao_visitas': {
        pt: { title: 'Cartão de Visitas', desc: 'Design profissional para seu cartão de visitas.' },
        en: { title: 'Business Card', desc: 'Professional design for your business card.' }
    },
    'flyer': {
        pt: { title: 'Flyer', desc: 'Arte criativa para seu flyer promocional.' },
        en: { title: 'Flyer', desc: 'Creative artwork for your promotional flyer.' }
    },
    'convites': {
        pt: { title: 'Convites', desc: 'Design elegante para seus convites.' },
        en: { title: 'Invitations', desc: 'Elegant design for your invitations.' }
    },
    'banner': {
        pt: { title: 'Banner', desc: 'Arte para banners de qualquer tamanho (impresso).' },
        en: { title: 'Banner', desc: 'Artwork for banners of any size (print).' }
    },
    'botton': {
        pt: { title: 'Arte para Botton', desc: 'Arte simples para botton personalizado.' },
        en: { title: 'Button Art', desc: 'Simple artwork for custom buttons.' }
    },
    'adesivos': {
        pt: { title: 'Arte para Adesivos', desc: 'Design para adesivos personalizados.' },
        en: { title: 'Sticker Design', desc: 'Design for custom stickers.' }
    },

    // Video
    'video_longo': { pt: { title: 'Vídeo Longo', desc: 'Um vídeo longo, perfeito para YouTube e outras plataformas.' }, en: { title: 'Long Video', desc: 'A long-format video, great for YouTube and other platforms.' } },
    'video_curto': { pt: { title: 'Vídeo Curto', desc: 'Um vídeo curto, ideal para redes sociais verticais e anúncios.' }, en: { title: 'Short Video', desc: 'Short video, ideal for vertical socials and ads.' } },
    'thumbnail': { pt: { title: 'Thumbnail', desc: 'Thumbnail personalizada para seu vídeo.' }, en: { title: 'Thumbnail', desc: 'Custom thumbnail for your video.' } },

    // Motion
    'intro_animada': { pt: { title: 'Intro Animada', desc: 'Uma intro simples e criativa com elementos 2D' }, en: { title: 'Animated Intro', desc: 'A simple and creative 2D intro.' } },
    'logomotion': { pt: { title: 'Logo/logotipo Animado', desc: 'Logo animado para vídeos e apresentações' }, en: { title: 'Animated Logo', desc: 'Animated logo for videos and presentations.' } },
    'motionmoldura': { pt: { title: 'Moldura Animada', desc: 'Moldura animada para vídeos e transmissões ao vivo' }, en: { title: 'Animated Frame', desc: 'Animated frame for videos and streams.' } },
    'waitscreen': { pt: { title: 'Tela de Espera', desc: 'Animação em looping para telas de espera' }, en: { title: 'Waitscreen', desc: 'Looping animation for waiting screens.' } },
    'motionbanner': { pt: { title: 'Banner animado', desc: 'O banner da sua marca animado para chamar atenção' }, en: { title: 'Animated Banner', desc: 'Animated banner for your brand to attract attention.' } },

    // Ilustração
    'fanart_anime': { pt: { title: 'Arte Estilo Anime', desc: 'Arte digital de personagem no estilo anime personalizada para você' }, en: { title: 'Anime-style Art', desc: 'Character art in anime style, custom made for you.' } },
    'fanart_cartoon': { pt: { title: 'Arte Estilo Cartoon', desc: 'Arte digital de personagem no estilo cartoon personalizada para você' }, en: { title: 'Cartoon-style Art', desc: 'Character art in cartoon style, custom made for you.' } },
    'fanart_chibi': { pt: { title: 'Arte Estilo Chibi', desc: 'Arte digital de personagem no estilo chibi personalizada para você' }, en: { title: 'Chibi-style Art', desc: 'Chibi-style character art, custom made.' } },
    'personagem_rpg': { pt: { title: 'Personagem token RPG', desc: 'Arte de personagem para RPG no estilo token de tabuleiro' }, en: { title: 'RPG Character Token', desc: 'Character art for RPG in token style.' } },
    'ilustracao_perfil': { pt: { title: 'Ilustração de perfil', desc: 'Arte digital de perfil nos estilos cartoon, anime ou realista' }, en: { title: 'Profile Illustration', desc: 'Digital profile art in cartoon, anime or realistic styles.' } },
    'esboco_rapido': { pt: { title: 'Esboço Rápido', desc: 'Um esboço rápido e simples de sua ideia ou personagem' }, en: { title: 'Quick Sketch', desc: 'A quick, simple sketch of your idea or character.' } },
    'cenario_digital': { pt: { title: 'Cenário digital', desc: 'Um cenário digital detalhado para livros, jogos ou projetos pessoais' }, en: { title: 'Digital Scene', desc: 'A detailed digital scene for books, games or personal projects.' } },
};

// Traduções de pacotes (título + lista de itens traduzida)
const packageTranslations = {
    'designmegapackage': { pt: { title: 'Mega Pacote', items: ['10 posts', '10 stories criativos', '10 carrosséis'] }, en: { title: 'Mega Package', items: ['10 posts', '10 creative stories', '10 carousels'] } },
    'designpremiumpackage': { pt: { title: 'Pacote Premium', items: ['5 posts', '5 stories', '5 carrosséis'] }, en: { title: 'Premium Package', items: ['5 posts', '5 stories', '5 carousels'] } },
    'postpackage': { pt: { title: 'Pacote Posts', items: ['5 posts'] }, en: { title: 'Posts Package', items: ['5 posts'] } },
    'storiepackage': { pt: { title: 'Pacote Stories', items: ['5 stories'] }, en: { title: 'Stories Package', items: ['5 stories'] } },
    'carrosselpackage': { pt: { title: 'Pacote Carrosséis', items: ['5 carrosséis'] }, en: { title: 'Carousel Package', items: ['5 carousels'] } },

    'mega_pacote': { pt: { title: 'Mega Pacote', items: ['5 vídeos longos', '10 vídeos curtos', '15 thumbnails'] }, en: { title: 'Mega Package', items: ['5 long videos', '10 short videos', '15 thumbnails'] } },
    'pacote_premium_a': { pt: { title: 'Pacote Premium A', items: ['5 vídeos longos', '5 thumbnails'] }, en: { title: 'Premium Package A', items: ['5 long videos', '5 thumbnails'] } },
    'pacote_premium_b': { pt: { title: 'Pacote Premium B', items: ['5 vídeos curtos', '5 thumbnails'] }, en: { title: 'Premium Package B', items: ['5 short videos', '5 thumbnails'] } },
    'cinco_videos_longos': { pt: { title: 'Cinco Vídeos Longos', items: ['5 vídeos longos'] }, en: { title: 'Five Long Videos', items: ['5 long videos'] } },
    'cinco_videos_curtos': { pt: { title: 'Cinco Vídeos Curtos', items: ['5 vídeos curtos'] }, en: { title: 'Five Short Videos', items: ['5 short videos'] } },
    'cinco_thumbnails': { pt: { title: 'Cinco Thumbnails', items: ['5 thumbnails'] }, en: { title: 'Five Thumbnails', items: ['5 thumbnails'] } },
    'dez_thumbnails': { pt: { title: 'Dez Thumbnails', items: ['10 thumbnails'] }, en: { title: 'Ten Thumbnails', items: ['10 thumbnails'] } },
};

// Helper: retorna título traduzido do serviço (fallback: busca nos arrays originais)
function getServiceTitle(id) {
    return (serviceTranslations[id] && serviceTranslations[id][currentLanguage] && serviceTranslations[id][currentLanguage].title) || (() => {
        // fallback: procurar no array original
        const s = designServices.concat(videoServices, motionGraphicsServices, ilustracaoDigitalServices).find(x => x.id === id);
        return s ? s.title : id;
    })();
}

// Helper: retorna descrição traduzida do serviço
function getServiceDesc(id) {
    return (serviceTranslations[id] && serviceTranslations[id][currentLanguage] && serviceTranslations[id][currentLanguage].desc) || '';
}

// Helper: retorna informativo customizado do admin (tooltip do ícone i)
function getServiceInfo(id) {
    if (typeof odysseeOverrides !== 'undefined' && odysseeOverrides[id] && odysseeOverrides[id].informativo) {
        return odysseeOverrides[id].informativo;
    }
    return '';
}

// Helper: retorna título traduzido do pacote
function getPackageTitle(id) {
    return (packageTranslations[id] && packageTranslations[id][currentLanguage] && packageTranslations[id][currentLanguage].title) || id;
}

// Helper: retorna itens traduzidos do pacote (array de strings)
function getPackageItems(id) {
    return (packageTranslations[id] && packageTranslations[id][currentLanguage] && packageTranslations[id][currentLanguage].items) || [];
}

let allPosts = []; // Cache local dos posts (preenchido via API REST do WP)

// ==============================================
// CONFIGURAÇÃO DO WORDPRESS
// baseUrl: domínio do site (para chamadas REST API)
// postsPerPage: limite de posts por requisição
// cacheExpire: tempo de validade do cache (5 min)
// ==============================================
const wpConfig = {
    baseUrl: 'https://odysseexp.com',
    postsPerPage: 100,
    cacheExpire: 5 * 60 * 1000
};

// ========== FUNÇÃO PARA EXTRAIR EXCERPT DO CONTEÚDO ==========
function extractExcerpt(htmlContent, maxLength = 150) {
    if (!htmlContent) return '';

    // Remove tags HTML
    let text = htmlContent.replace(/<[^>]*>/g, '');

    // Remove múltiplos espaços
    text = text.replace(/\s+/g, ' ').trim();

    // Se for mais longo que maxLength, corta e adiciona "..."
    if (text.length > maxLength) {
        text = text.substring(0, maxLength).trim() + '...';
    }

    return text;
}

// ========== FUNÇÃO PARA EXTRAIR THUMBNAIL DO YOUTUBE ==========
function extractYoutubeThumbnail(content) {
    // 1. Procura em iframes (embed)
    const iframeRegex = /src=["']https:\/\/(?:www\.)?youtube(?:-nocookie)?\.com\/embed\/([a-zA-Z0-9_-]{11})/;
    let match = content.match(iframeRegex);

    if (match && match[1]) {
        console.log('[YOUTUBE] ID extraído de iframe:', match[1]);
        return `https://img.youtube.com/vi/${match[1]}/maxresdefault.jpg`;
    }

    // 2. Procura em URLs diretas do YouTube
    const youtubeRegex = /(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/;
    match = content.match(youtubeRegex);

    if (match && match[1]) {
        console.log('[YOUTUBE] ID extraído de URL:', match[1]);
        return `https://img.youtube.com/vi/${match[1]}/maxresdefault.jpg`;
    }

    return null;
}

// ==============================================
// FETCH DE POSTS: Busca posts reais via REST API
// com cache local (localStorage) de 5 minutos.
// Após buscar, renderiza carrosséis e blog.
// ==============================================
async function fetchRealPosts() {
    try {
        // Verifica cache local
        const cacheKey = 'odyssee_posts_cache';
        const cached = (window.safeStorage && window.safeStorage.getItem(cacheKey)) || null;
        const cacheTime = (window.safeStorage && window.safeStorage.getItem(cacheKey + '_time')) || null;
        const now = Date.now();

        if (cached && cacheTime && (now - parseInt(cacheTime)) < wpConfig.cacheExpire) {
            console.log('[CACHE] Posts recuperados do cache local');
            allPosts = JSON.parse(cached);
            return;
        }

        console.log('[API] Buscando posts de:', wpConfig.baseUrl + '/wp-json/wp/v2/posts');

        // Pega os posts do seu site utilizando a URL dinâmica fornecida pelo WordPress
        const restBaseUrl = (typeof odysseeSecure !== 'undefined' && odysseeSecure.restUrl)
            ? odysseeSecure.restUrl
            : wpConfig.baseUrl + '/wp-json/';

        const url = restBaseUrl + 'wp/v2/posts?_embed&per_page=' + wpConfig.postsPerPage;
        const response = await fetch(url);

        if (!response.ok) throw new Error(`Erro HTTP: ${response.status}`);

        const data = await response.json();
        console.log('[API] Posts recebidos:', data.length);

        allPosts = data.map(post => {
            // 1. Tenta achar a imagem destacada
            let imgUrl = null;

            if (post._embedded && post._embedded['wp:featuredmedia'] && post._embedded['wp:featuredmedia'][0]) {
                imgUrl = post._embedded['wp:featuredmedia'][0].source_url;
            }

            // 2. Se não tem imagem destacada, procura por YouTube thumbnail no conteúdo
            if (!imgUrl) {
                const youtubeThumb = extractYoutubeThumbnail(post.content.rendered);
                if (youtubeThumb) {
                    imgUrl = youtubeThumb;
                    console.log('[API] Usando thumbnail do YouTube para:', post.title.rendered);
                }
            }

            // 3. Se ainda não tiver imagem, usa placeholder
            if (!imgUrl) {
                imgUrl = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="300" height="200"%3E%3Crect width="300" height="200" fill="%23ccc"/%3E%3Ctext x="50%25" y="50%25" font-size="16" fill="%23999" text-anchor="middle" dy=".3em"%3ESem Imagem%3C/text%3E%3C/svg%3E';
            }

            // 4. Tenta achar as categorias PAI e FILHA (SUPORTA MÚLTIPLAS)
            let categoryList = ['geral']; // Array para suportar múltiplas categorias
            let subCategoryList = []; // Array para múltiplas subcategorias

            if (post._embedded && post._embedded['wp:term'] && post._embedded['wp:term'][0]) {
                const terms = post._embedded['wp:term'][0];

                // Separa categorias por nível (parentais vs filhas)
                // Se a URL tem /category/X/Y/ é filha, se tem /category/X/ é pai
                const parentCats = [];
                const childCats = [];

                terms.forEach(cat => {
                    const urlMatch = cat.link.match(/\/category\/([^\/]+)\/([^\/]+)\//);
                    if (urlMatch) {
                        // Tem 2 níveis = subcategoria
                        childCats.push(cat);
                    } else {
                        // 1 nível = categoria pai
                        parentCats.push(cat);
                    }
                });

                // Armazena TODAS as categorias pai encontradas
                if (parentCats.length > 0) {
                    categoryList = parentCats.map(cat => cat.slug);
                } else if (terms.length > 0) {
                    categoryList = terms.map(t => t.slug);
                }

                // Armazena TODAS as subcategorias encontradas
                if (childCats.length > 0) {
                    subCategoryList = childCats.map(cat => cat.slug);
                }
            }

            const decodedTitle = decodeHtmlEntities(post.title && post.title.rendered ? post.title.rendered : '');
            const decodedContent = decodeHtmlEntities(post.content && post.content.rendered ? post.content.rendered : '');
            const decodedTitleEN = post.meta && post.meta.post_title_en ? decodeHtmlEntities(post.meta.post_title_en) : '';

            const postMapped = {
                title: decodedTitle,
                titleEN: decodedTitleEN, // Custom field para título em EN
                categories: categoryList, // MUDOU: agora é um array
                category: categoryList[0], // Mantém a primeira para compatibilidade
                subCategories: subCategoryList, // NOVO: array de subcategorias
                subCategory: subCategoryList[0] || '', // Mantém a primeira para compatibilidade
                image: imgUrl,
                date: post.date,
                link: post.link,
                content: decodedContent // Captura o conteúdo completo para extrair excerpt (decodificado)
            };

            if (postMapped.category === 'motion') {
                console.log('[API] Post Motion encontrado:', postMapped.title, '| Imagem:', postMapped.image.substring(0, 50) + '...');
            }

            return postMapped;
        });

        console.log("[API] ✅ Posts carregados com sucesso:", allPosts.length);

        // Remove posts inválidos
        allPosts = allPosts.filter(post => post && post.title && post.image && post.date);
        console.log("[API] Posts válidos após filtro:", allPosts.length);

        // Salva no cache local
        try {
            if (window.safeStorage) {
                window.safeStorage.setItem(cacheKey, JSON.stringify(allPosts));
                window.safeStorage.setItem(cacheKey + '_time', now.toString());
            } else {
                try { localStorage.setItem(cacheKey, JSON.stringify(allPosts)); localStorage.setItem(cacheKey + '_time', now.toString()); } catch (e) { }
            }
            console.log('[CACHE] Posts salvos em cache local');
        } catch (e) {
            console.warn('[CACHE] Erro ao salvar cache:', e.message);
        }

        // DEBUG: mostra categorias únicas encontradas (pai e filha)
        try {
            const cats = new Set();
            const subcats = new Set();
            allPosts.forEach(p => { if (p.category) cats.add(p.category); if (p.subCategory) subcats.add(p.subCategory); });
            console.log('[API] Categorias detectadas:', Array.from(cats).join(', '));
            console.log('[API] Subcategorias detectadas:', Array.from(subcats).join(', '));
        } catch (e) { /* ignore */ }

        // Renderiza os carrosseis com os posts
        renderCarousel('carousel-recentes', null);
        renderCarousel('carousel-design-grafico', 'design-grafico');
        renderCarousel('carousel-edicao-video', 'edicao-de-video');
        renderCarousel('carousel-motion', 'motion');
        renderCarousel('carousel-ilustracao', 'ilustracao-digital');
        renderCarousel('carousel-impressos', 'impressos');

        // E renderiza o blog (se estiver na página de posts)
        if (typeof renderBlog === 'function') renderBlog();

    } catch (err) {
        console.error("[API] ❌ Erro ao buscar posts:", err);
        console.error("[API] URL tentada:", wpConfig.baseUrl + '/wp-json/wp/v2/posts');
    }
}

/* ==============================================
   BANCO DE PREÇOS PADRÃO
   Valores default para cada serviço/pacote.
   Podem ser sobrescritos via admin (odysseePrecos).
   Chave = ID do serviço (mesmo usado nos arrays).
   ============================================== */
const defaultPrices = {
    // Preços de Serviços Design Gráfico
    'logotipologo': 49.00,
    'bannersocial': 79.00,
    'visualid': 599.00,
    'cartao_visitas': 24.00,
    'flyer': 29.00,
    'convites': 23.00,
    'banner': 100.00,
    'botton': 19.00,
    'adesivos': 19.00,

    // Pacotes de Design
    'designmegapackage': 1250.00,
    'designpremiumpackage': 679.00,
    'postpackage': 89.00,
    'storiepackage': 105.00,
    'carrosselpackage': 449.00,

    // Preços de Serviços Edição de Vídeo
    'video_longo': 350.00,
    'video_curto': 100.00,
    'thumbnail': 20.00,

    // Pacotes de Edição de Vídeo
    'mega_pacote': 2650.00,
    'pacote_premium_a': 1650.00,
    'pacote_premium_b': 475.00,
    'cinco_videos_longos': 1599.00,
    'cinco_videos_curtos': 399.00,
    'cinco_thumbnails': 89.00,
    'dez_thumbnails': 169.00,

    // Preços de Serviços Motion Graphics
    'intro_animada': 200.00,
    'artmotion': 250.00,
    'logomotion': 100.00,
    'motionmoldura': 140.00,
    'waitscreen': 175.00,
    'motionbanner': 300.00,

    // Preços de Serviços Ilustração
    'fanart_anime': 120.00,
    'fanart_cartoon': 180.00,
    'fanart_chibi': 50.00,
    'fanart_pixelart': 80.00,
    'fanart_vetorial': 100.00,
    'personagem_rpg': 250.00,
    'ilustracao_perfil': 150.00,
    'ilustracao_corpo_inteiro': 250.00,
    'storyboard': 300.00,
    'cenario_digital': 375.00,
    'esboco_rapido': 25.00,

};

// Preços unitários padrão (base para cálculo: economia = soma_unitários - preço_pacote)
const defaultUnitPrices = {
    'design_post': 20.00,
    'design_storie': 25.00,
    'design_carrossel': 100.00,
    'video_longo': 350.00,
    'video_curto': 100.00,
    'thumbnail': 20.00
};

// Mesclar preços do WordPress admin (se disponíveis) com os defaults
// wp_localize_script converte tudo para string, precisamos converter de volta para number
function parseWpPrices(obj) {
    if (!obj || typeof obj !== 'object') return {};
    const parsed = {};
    for (const key in obj) {
        if (obj.hasOwnProperty(key)) {
            const num = parseFloat(obj[key]);
            if (!isNaN(num) && num > 0) {
                parsed[key] = num;
            }
        }
    }
    return parsed;
}
const allPrices = Object.assign({}, defaultPrices,
    (typeof odysseePrecos !== 'undefined' && odysseePrecos.prices) ? parseWpPrices(odysseePrecos.prices) : {}
);
const unitPrices = Object.assign({}, defaultUnitPrices,
    (typeof odysseePrecos !== 'undefined' && odysseePrecos.unitPrices) ? parseWpPrices(odysseePrecos.unitPrices) : {}
);

// Mesclar produtos customizados do WordPress admin (se disponíveis)
// odysseeProdutos vem como objeto indexado do wp_localize_script
// Cada produto é convertido de formato PHP para formato JS:
//   PHP: {id, nome, categoria, tipo, descricao, informativo, itens, thumbnail, preco}
//   JS:  {id, title, desc, informativo, image, icon, category, tipo, items, price}
const customProducts = (function () {
    if (typeof odysseeProdutos === 'undefined') return [];
    // wp_localize_script converte arrays PHP em objetos JS indexados por número
    const arr = [];
    for (const key in odysseeProdutos) {
        if (odysseeProdutos.hasOwnProperty(key) && typeof odysseeProdutos[key] === 'object') {
            const p = odysseeProdutos[key];
            const tipo = p.tipo || 'servico';
            const itensRaw = p.itens || '';
            const itensArr = itensRaw ? itensRaw.split('\n').map(s => s.trim()).filter(Boolean) : [];
            arr.push({
                id: p.id || 'custom_' + key,
                title: p.nome || '',
                desc: p.descricao || '',
                informativo: p.informativo || '',
                image: p.thumbnail || '',
                icon: '',
                category: p.categoria || 'design-grafico',
                tipo: tipo,
                items: itensArr,
                price: parseFloat(p.preco) || 0
            });
            // Adicionar preço ao allPrices para que renderize corretamente
            if (p.id && parseFloat(p.preco) > 0) {
                allPrices[p.id] = parseFloat(p.preco);
            }
        }
    }
    return arr;
})();

// ==============================================
// MAPA DE CATEGORIAS: slug -> { label, gridId, packageGridId }
// Usado para saber em qual grid renderizar cada
// serviço/pacote customizado, e para montar a
// mensagem do WhatsApp com o nome da categoria.
// ==============================================
const categoryMap = {
    'design-grafico': { label: 'Design Gráfico', gridId: 'design-services-grid', packageGridId: 'design-packages-grid' },
    'edicao-de-video': { label: 'Edição de Vídeo', gridId: 'video-services-grid', packageGridId: 'video-packages-grid' },
    'motion': { label: 'Motion Graphics', gridId: 'motion-graphics-services-grid', packageGridId: 'motion-graphics-services-grid' },
    'ilustracao': { label: 'Ilustração Digital', gridId: 'ilustracao-estilos', packageGridId: 'ilustracao-estilos' },
};

/**
 * Renderiza os produtos customizados do admin nas grids correspondentes.
 * Suporta tipo 'servico' (card com thumbnail/desc) e 'pacote' (card com lista de itens).
 * Deve ser chamada APÓS os renders de serviços hardcoded.
 */
function renderCustomProducts() {
    if (!customProducts.length) return;

    customProducts.forEach(product => {
        const catInfo = categoryMap[product.category];
        if (!catInfo) return;

        const isPacote = product.tipo === 'pacote';
        const gridId = isPacote ? catInfo.packageGridId : catInfo.gridId;
        const grid = document.getElementById(gridId);
        if (!grid) return;

        const price = allPrices[product.id] || product.price;
        const priceString = price > 0
            ? `R$ ${price.toFixed(2).replace('.', ',')}`
            : (translations[currentLanguage] && translations[currentLanguage]['consulte'] ? translations[currentLanguage]['consulte'] : 'Consulte');

        const hireLabel = translations[currentLanguage] && translations[currentLanguage]['btn-hire']
            ? translations[currentLanguage]['btn-hire'] : 'Contratar';

        const cleanTitle = product.title.replace(/<[^>]*>/g, '');
        const whatsappMsg = encodeURIComponent(`Olá, gostaria de apresentar a minha ideia e solicitar um orçamento para o produto/serviço: *${catInfo.label} - ${cleanTitle}*.`);
        const whatsappLink = `https://wa.me/5511963208691?text=${whatsappMsg}`;

        if (isPacote) {
            // Renderiza como card de pacote (igual aos pacotes hardcoded)
            const itemsHTML = product.items.map(item => `<li><i class="fa fa-check-circle"></i> ${item}</li>`).join('');
            grid.innerHTML += `
                <div class="service-card package-card">
                    <h3>${cleanTitle}</h3>
                    <ul class="package-items">${itemsHTML}</ul>
                    <p>${priceString}</p>
                    <a href="${whatsappLink}" target="_blank" rel="noopener noreferrer"><button>${hireLabel}</button></a>
                </div>
            `;
        } else {
            // Renderiza como card de serviço individual (com thumbnail e descrição)
            const thumbnailHTML = product.image
                ? `<img class="service-thumb-img" src="${product.image}" alt="${cleanTitle}" loading="lazy" onerror="this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22300%22 height=%22180%22%3E%3Crect width=%22300%22 height=%22180%22 fill=%22%23ccc%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 font-size=%2216%22 fill=%22%23999%22 text-anchor=%22middle%22 dy=%22.3em%22%3ESem%20Imagem%3C/text%3E%3C/svg%3E';">`
                : '';

            grid.innerHTML += `
                <div class="service-card">
                    <div class="service-thumbnail">${thumbnailHTML}</div>
                    <div class="service-content">
                        <h3>${cleanTitle}</h3>
                        <p class="service-price">${priceString}</p>
                        <span class="service-desc">${product.desc}</span>
                        <div class="service-actions">
                            <a href="${whatsappLink}" target="_blank" rel="noopener noreferrer"><button>${hireLabel}</button></a>
                            <div class="info-icon-wrapper">
                                <i class="fas fa-info-circle info-icon"></i>
                                <div class="info-tooltip">${product.informativo || 'Clique em Contratar para solicitar orçamento.'}</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
    });
}

/* ==============================================
   CATÁLOGO DE SERVIÇOS: Arrays com dados de cada card
   Cada objeto contém: id, icon, image, title, desc
   O id deve coincidir com a chave em allPrices e
   serviceTranslations para preço e tradução.
   ============================================== */

// --- DESIGN GRÁFICO: Serviços individuais ---
const designServices = [
    { id: 'logotipologo', icon: 'fas fa-id-card', image: 'https://odysseexp.com/wp-content/uploads/2025/09/Artboard-3-1-e1757565255458.png', title: 'Logotipo e Logo', desc: 'Uma logo única, criativa e estrategicamente feita para captar seu público alvo.' },
    { id: 'bannersocial', icon: 'fas fa-scroll', image: 'https://odysseexp.com/wp-content/uploads/2025/07/Screenshot-2025-06-30-184152.png', title: 'Banner para redes sociais', desc: 'Banner para YouTube, Facebook, site, etc.' },
    { id: 'cartao_visitas', icon: 'fas fa-palette', image: 'https://odysseexp.com/wp-content/uploads/2025/12/art-card-services.png', title: 'Cartão de Visitas', desc: 'Design profissional para seu cartão de visitas.' },
    { id: 'flyer', icon: 'fas fa-newspaper', image: 'https://odysseexp.com/wp-content/uploads/2025/12/art-card-services-2.png', title: 'Flyer', desc: 'Arte criativa para seu flyer promocional.' },
    { id: 'convites', icon: 'fas fa-envelope-open-text', image: 'https://odysseexp.com/wp-content/uploads/2025/12/art-card-services-5.png', title: 'Convites', desc: 'Design elegante para seus convites.' },
    { id: 'banner', icon: 'fas fa-scroll', image: 'https://odysseexp.com/wp-content/uploads/2025/12/art-card-services-4.png', title: 'Banner', desc: 'Arte para banners de qualquer tamanho (impresso).' },
    { id: 'botton', image: 'https://odysseexp.com/wp-content/uploads/2025/12/art-card-services-1.png', title: 'Arte para Botton', desc: 'Arte simples para botton personalizado.' },
    { id: 'adesivos', icon: 'fas fa-sticky-note', image: 'https://odysseexp.com/wp-content/uploads/2025/12/art-card-services-3.png', title: 'Arte para Adesivos', desc: 'Design para adesivos personalizados.' },
    { id: 'visualid', icon: 'fas fa-palette', image: 'https://odysseexp.com/wp-content/uploads/2025/05/Untitled-1-11.jpg', title: 'Identidade Visual', desc: 'Manual de identidade completa, mockups, brindes e mais!' },
];

// Pacotes de Design (items = lista exibida, quantities = base para cálculo de desconto)
const designPackages = [
    { id: 'designmegapackage', icon: 'fas fa-star', title: 'Mega Pacote', items: ['10 posts', '10 stories criativos', '10 carrosséis'], quantities: { post: 10, storie: 10, carrossel: 10 } },
    { id: 'designpremiumpackage', icon: 'fas fa-crown', title: 'Pacote Premium', items: ['5 posts', '5 stories', '5 carrosséis'], quantities: { post: 5, storie: 5, carrossel: 5 } },
    { id: 'postpackage', icon: 'fas fa-file-image', title: 'Pacote Posts', items: ['5 posts'], quantities: { post: 5 } },
    { id: 'storiepackage', icon: 'fas fa-bolt', title: 'Pacote Stories', items: ['5 stories'], quantities: { storie: 5 } },
    { id: 'carrosselpackage', icon: 'fas fa-images', title: 'Pacote Carrosséis', items: ['5 carrosséis'], quantities: { carrossel: 5 } }
];

// --- EDIÇÃO DE VÍDEO: Serviços individuais ---
const videoServices = [
    { id: 'video_longo', icon: 'fas fa-film', image: 'https://odysseexp.com/wp-content/uploads/2026/01/art-card-services.png', title: 'Vídeo Longo', desc: 'Um vídeo longo, perfeito para YouTube e outras plataformas.' },
    { id: 'video_curto', icon: 'fas fa-mobile-alt', image: 'https://odysseexp.com/wp-content/uploads/2026/01/art-card-services-1.png', title: 'Vídeo Curto', desc: 'Um vídeo curto, ideal para redes sociais verticais e anúncios.' },
    { id: 'thumbnail', icon: 'fas fa-image', image: 'https://odysseexp.com/wp-content/uploads/2026/01/art-card-services-2.png', title: 'Thumbnail', desc: 'Thumbnail personalizada para seu vídeo.' },
];
// Pacotes de Vídeo
const videoPackages = [
    { id: 'mega_pacote', icon: 'fas fa-gem', title: 'Mega Pacote', items: ['5 vídeos longos', '10 vídeos curtos', '15 thumbnails'], quantities: { longo: 5, curto: 10, thumbnail: 15 } },
    { id: 'pacote_premium_a', icon: 'fas fa-star', title: 'Pacote Premium A', items: ['5 vídeos longos', '5 thumbnails'], quantities: { longo: 5, thumbnail: 5 } },
    { id: 'pacote_premium_b', icon: 'far fa-star', title: 'Pacote Premium B', items: ['5 vídeos curtos', '5 thumbnails'], quantities: { curto: 5, thumbnail: 5 } },
    { id: 'cinco_videos_longos', icon: 'fas fa-film', title: 'Cinco Vídeos Longos', items: ['5 vídeos longos'], quantities: { longo: 5 } },
    { id: 'cinco_videos_curtos', icon: 'fas fa-mobile-alt', title: 'Cinco Vídeos Curtos', items: ['5 vídeos curtos'], quantities: { curto: 5 } },
    { id: 'cinco_thumbnails', icon: 'far fa-images', title: 'Cinco Thumbnails', items: ['5 thumbnails'], quantities: { thumbnail: 5 } },
    { id: 'dez_thumbnails', icon: 'fas fa-images', title: 'Dez Thumbnails', items: ['10 thumbnails'], quantities: { thumbnail: 10 } },
];

// --- MOTION GRAPHICS: Serviços individuais ---
const motionGraphicsServices = [
    { id: 'intro_animada', icon: 'fas fa-play-circle fa-bounce', image: 'https://odysseexp.com/wp-content/uploads/2026/01/FalaAiWillcomsom-ezgif.com-optimize.gif', title: 'Intro Animada', desc: 'Uma intro simples e criativa com elementos 2D' },
    { id: 'artmotion', icon: 'fas fa-scroll', image: 'https://odysseexp.com/wp-content/uploads/2025/06/harley-animated-.gif', title: 'Arte Animada', desc: 'Sua arte animada com elementos 2D' },
    { id: 'logomotion', icon: 'fas fa-scroll', image: 'https://odysseexp.com/wp-content/uploads/2025/12/art-card-services-motion.png', title: 'Logo/logotipo Animado', desc: 'Logo animado para vídeos e apresentações' },
    { id: 'motionmoldura', icon: 'fas fa-expand fa-fade', image: 'https://odysseexp.com/wp-content/uploads/2025/12/art-card-services-motion.png', title: 'Moldura Animada', desc: 'Moldura animada para vídeos e transmissões ao vivo' },
    { id: 'waitscreen', icon: 'fas fa-spinner fa-spin', image: 'https://odysseexp.com/wp-content/uploads/2025/12/art-card-services-motion.png', title: 'Tela de Espera', desc: 'Animação em looping para telas de espera' },
    { id: 'motionbanner', icon: 'fas fa-newspaper', image: 'https://odysseexp.com/wp-content/uploads/2025/12/art-card-services-motion.png', title: 'Banner animado', desc: 'O banner da sua marca animado para chamar atenção' },
];

// --- ILUSTRAÇÃO DIGITAL: Estilos de arte disponíveis ---
const ilustracaoDigitalServices = [
    { id: 'fanart_anime', icon: 'fas fa-female', image: 'https://odysseexp.com/wp-content/uploads/2026/01/art-card-services_14.png', title: 'Arte Estilo Anime', desc: 'Arte digital de personagem no estilo anime personalizada para você' },
    { id: 'fanart_cartoon', icon: 'fas fa-walking', image: 'https://odysseexp.com/wp-content/uploads/2026/01/art-card-services_13.png', title: 'Arte Estilo Cartoon', desc: 'Arte digital de personagem no estilo cartoon personalizada para você' },
    { id: 'fanart_chibi', icon: 'fab fa-github-alt', image: 'https://odysseexp.com/wp-content/uploads/2026/01/art-card-services_15.png', title: 'Arte Estilo Chibi', desc: 'Arte digital de personagem no estilo chibi personalizada para você' },
    { id: 'fanart_pixelart', icon: 'fas fa-pixel', image: 'https://odysseexp.com/wp-content/uploads/2026/01/art-card-services_12.png', title: 'Arte Estilo Pixel Art', desc: 'Arte digital de personagem no estilo pixel art personalizada para você' },
    { id: 'fanart_vetorial', icon: 'fas fa-vector-square', image: 'https://odysseexp.com/wp-content/uploads/2025/05/Jinx-Powder-Vector-lightroom-scaled-e1747298364610.jpg', title: 'Arte Estilo Vetorial', desc: 'Arte digital de personagem no estilo vetorial personalizada para você' },
    { id: 'personagem_rpg', icon: 'fas fa-dice-d20', image: 'https://odysseexp.com/wp-content/uploads/2025/12/art-card-services-motion.png', title: 'Personagem token RPG', desc: 'Arte de personagem para RPG no estilo token de tabuleiro.' },
    { id: 'ilustracao_perfil', icon: 'fas fa-id-badge', image: 'https://odysseexp.com/wp-content/uploads/2026/01/art-card-services_11.png', title: 'Ilustração de perfil/busto', desc: 'Arte digital de perfil nos estilos cartoon, anime, chibi e pixel art' },
    { id: 'ilustracao_corpo_inteiro', icon: 'fas fa-user', image: 'https://odysseexp.com/wp-content/uploads/2026/01/art-card-services_8.png', title: 'Ilustração corpo inteiro', desc: 'Arte digital de corpo inteiro nos estilos cartoon, anime, chibi e pixel art' },
    { id: 'esboco_rapido', icon: '\tfar fa-id-badge', image: 'https://odysseexp.com/wp-content/uploads/2026/01/art-card-services_10.png', title: 'Esboço Rápido', desc: 'Um esboço rápido e simples de sua ideia ou personagem' },
    { id: 'storyboard', icon: 'fas fa-landmark', image: 'https://odysseexp.com/wp-content/uploads/2026/01/art-card-services_9.png', title: 'Storyboard', desc: 'Storyboard cena a cena para seu projeto' },
];

// ==============================================
// SOBRESCRITAS DO ADMIN: Aplica edições feitas no
// painel WP (nome, descrição, thumbnail) sobre os
// arrays de serviços hardcoded acima.
// odysseeOverrides vem de wp_localize_script.
// ==============================================
if (typeof odysseeOverrides !== 'undefined') {
    const allServiceArrays = [designServices, designPackages, videoServices, videoPackages, motionGraphicsServices, ilustracaoDigitalServices];
    allServiceArrays.forEach(function (arr) {
        arr.forEach(function (svc) {
            const override = odysseeOverrides[svc.id];
            if (override) {
                if (override.nome) svc.title = override.nome;
                if (override.descricao) svc.desc = override.descricao;
                if (override.thumbnail) svc.image = override.thumbnail;
            }
        });
    });
}

/* ==============================================
   FUNÇÕES DE CÁLCULO E RENDERIZAÇÃO
   Calculam descontos e constroem os cards HTML
   das seções de serviços e pacotes.
   ============================================== */

// Calcula a economia de um pacote comparando preço unitário vs preço do pacote
// Retorna HTML com o valor economizado ou string vazia se não houver desconto
function calculateDiscount(packagePrice, unitQuantities) {
    let bruteTotal = 0;
    bruteTotal += (unitQuantities.post || 0) * unitPrices.design_post;
    bruteTotal += (unitQuantities.storie || 0) * unitPrices.design_storie;
    bruteTotal += (unitQuantities.carrossel || 0) * unitPrices.design_carrossel;
    bruteTotal += (unitQuantities.longo || 0) * unitPrices.video_longo;
    bruteTotal += (unitQuantities.curto || 0) * unitPrices.video_curto;
    bruteTotal += (unitQuantities.thumbnail || 0) * unitPrices.thumbnail;
    const discount = bruteTotal - packagePrice;

    // Retorna a string de economia formatada
    if (discount > 0) {
        const prefix = translations[currentLanguage] && translations[currentLanguage]['you-save-prefix'] ? translations[currentLanguage]['you-save-prefix'] : 'Você economiza R$ ';
        // Formata o número com duas casas e vírgula para pt
        const amount = currentLanguage === 'en' ? discount.toFixed(2) : discount.toFixed(2).replace('.', ',');
        return `<span class="service-discount">${prefix}${amount}</span>`;
    }
    return ''; // Retorna nada se não tiver desconto
}

// Renderiza cards de Design Gráfico (serviços individuais + pacotes)
// Popula as grids #design-services-grid e #design-packages-grid
function renderDesignServices() {
    const servicesGrid = document.getElementById('design-services-grid');
    const packagesGrid = document.getElementById('design-packages-grid');

    if (servicesGrid) {
        servicesGrid.innerHTML = ''; // Limpa a grid
        designServices.forEach(service => {
            const price = allPrices[service.id];
            const priceString = price ? `R$ ${price.toFixed(2).replace('.', ',')}` : (translations[currentLanguage] && translations[currentLanguage]['consulte'] ? translations[currentLanguage]['consulte'] : 'Consulte');

            const svcTitle = getServiceTitle(service.id);
            const svcDesc = getServiceDesc(service.id) || service.desc || '';
            const hireLabel = translations[currentLanguage] && translations[currentLanguage]['btn-hire'] ? translations[currentLanguage]['btn-hire'] : 'Contratar';
            const whatsappMsg = encodeURIComponent(`Olá, gostaria de apresentar a minha ideia e solicitar um orçamento para o produto/serviço: *Design Gráfico - ${svcTitle.replace(/<[^>]*>/g, '')}*.`);
            const whatsappLink = `https://wa.me/5511963208691?text=${whatsappMsg}`;
            const infoText = getServiceInfo(service.id) || 'Tempo de entrega: 3-5 dias úteis. Inclui 2 revisões. Formato final em alta resolução.';
            const thumbnailHTML = service.image ? `<img class="service-thumb-img" src="${service.image}" alt="${svcTitle}" loading="lazy" onerror="this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22300%22 height=%22180%22%3E%3Crect width=%22300%22 height=%22180%22 fill=%22%23ccc%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 font-size=%2216%22 fill=%22%23999%22 text-anchor=%22middle%22 dy=%22.3em%22%3ESem%20Imagem%3C/text%3E%3C/svg%3E';">` : (service.icon ? `<i class="${service.icon}"></i>` : '');
            servicesGrid.innerHTML += `
                    <div class="service-card">
                        <div class="service-thumbnail">${thumbnailHTML}</div>
                        <div class="service-content">
                            <h3>${svcTitle}</h3>
                            <p class="service-price">${priceString}</p>
                            <span class="service-desc">${svcDesc}</span>
                            <div class="service-actions">
                                <a href="${whatsappLink}" target="_blank" rel="noopener noreferrer"><button>${hireLabel}</button></a>
                                <div class="info-icon-wrapper">
                                    <i class="fas fa-info-circle info-icon"></i>
                                    <div class="info-tooltip">${infoText}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
        });
    }

    if (packagesGrid) {
        packagesGrid.innerHTML = ''; // Limpa a grid
        designPackages.forEach(pkg => {
            const price = allPrices[pkg.id];
            const priceString = price ? `R$ ${price.toFixed(2).replace('.', ',')}` : (translations[currentLanguage] && translations[currentLanguage]['consulte'] ? translations[currentLanguage]['consulte'] : 'Consulte');
            const discountHTML = calculateDiscount(price, pkg.quantities);

            // Cria o HTML da lista de itens
            const itemsHTML = pkg.items.map(item => `<li><i class="fa fa-check-circle"></i> ${item}</li>`).join('');

            const pkgTitle = getPackageTitle(pkg.id) || pkg.title;
            const pkgItems = getPackageItems(pkg.id).length ? getPackageItems(pkg.id) : pkg.items;
            const itemsHTMLTranslated = pkgItems.map(item => `<li><i class="fa fa-check-circle"></i> ${item}</li>`).join('');
            const hireLabelPkg = translations[currentLanguage] && translations[currentLanguage]['btn-hire'] ? translations[currentLanguage]['btn-hire'] : 'Contratar';
            const whatsappMsgPkg = encodeURIComponent(`Olá, gostaria de apresentar a minha ideia e solicitar um orçamento para o produto/serviço: *Design Gráfico - ${pkgTitle.replace(/<[^>]*>/g, '')}*.`);
            const whatsappLinkPkg = `https://wa.me/5511963208691?text=${whatsappMsgPkg}`;
            const pkgIconHTML = pkg.icon ? (`<i class="${pkg.icon} package-icon-inline" aria-hidden="true"></i>` + `<svg class="package-icon-svg icon-svg small" role="img" aria-hidden="true" style="display:none" viewBox="0 0 16 16"><circle cx="8" cy="8" r="7"/></svg>`) : (pkg.iconImage ? `<img class="package-icon" src="${pkg.iconImage}" alt="${pkgTitle.replace(/<[^>]*>/g, '')}">` : '');
            packagesGrid.innerHTML += `
                    <div class="service-card package-card">
                        <h3>${pkgIconHTML} ${pkgTitle}</h3>
                        <ul class="package-items">${itemsHTMLTranslated}</ul>
                        <p>${priceString}</p>
                        ${discountHTML} <a href="${whatsappLinkPkg}" target="_blank" rel="noopener noreferrer"><button>${hireLabelPkg}</button></a>
                    </div>
                `;
        });
    }
}

// Renderiza cards de Edição de Vídeo (serviços + pacotes)
// Popula as grids #video-services-grid e #video-packages-grid
function renderVideoServices() {
    const servicesGrid = document.getElementById('video-services-grid');
    const packagesGrid = document.getElementById('video-packages-grid');

    if (servicesGrid) {
        servicesGrid.innerHTML = ''; // Limpa a grid
        videoServices.forEach(service => {
            const price = allPrices[service.id];
            const priceString = price ? `R$ ${price.toFixed(2).replace('.', ',')}` : (translations[currentLanguage] && translations[currentLanguage]['consulte'] ? translations[currentLanguage]['consulte'] : 'Consulte');

            const svcTitle = getServiceTitle(service.id);
            const svcDesc = getServiceDesc(service.id) || service.desc || '';
            const hireLabel = translations[currentLanguage] && translations[currentLanguage]['btn-hire'] ? translations[currentLanguage]['btn-hire'] : 'Contratar';
            const whatsappMsg = encodeURIComponent(`Olá, gostaria de apresentar a minha ideia e solicitar um orçamento para o produto/serviço: *Edição de Vídeo - ${svcTitle.replace(/<[^>]*>/g, '')}*.`);
            const whatsappLink = `https://wa.me/5511963208691?text=${whatsappMsg}`;
            const infoText = getServiceInfo(service.id) || 'Prazo: 5-7 dias úteis. Inclui correção de cor, áudio e efeitos. Até 3 revisões incluídas.';
            const thumbnailHTML = service.image ? `<img class="service-thumb-img" src="${service.image}" alt="${svcTitle}" loading="lazy" onerror="this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22300%22 height=%22180%22%3E%3Crect width=%22300%22 height=%22180%22 fill=%22%23ccc%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 font-size=%2216%22 fill=%22%23999%22 text-anchor=%22middle%22 dy=%22.3em%22%3ESem%20Imagem%3C/text%3E%3C/svg%3E';">` : (service.icon ? `<i class="${service.icon}"></i>` : '');
            servicesGrid.innerHTML += `
                <div class="service-card">
                    <div class="service-thumbnail">${thumbnailHTML}</div>
                    <div class="service-content">
                        <h3>${svcTitle}</h3>
                        <p class="service-price">${priceString}</p>
                        <span class="service-desc">${svcDesc}</span>
                        <div class="service-actions">
                            <a href="${whatsappLink}" target="_blank" rel="noopener noreferrer"><button>${hireLabel}</button></a>
                            <div class="info-icon-wrapper">
                                <i class="fas fa-info-circle info-icon"></i>
                                <div class="info-tooltip">${infoText}</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
    }

    if (packagesGrid) {
        packagesGrid.innerHTML = ''; // Limpa a grid
        videoPackages.forEach(pkg => {
            const price = allPrices[pkg.id];
            const priceString = price ? `R$ ${price.toFixed(2).replace('.', ',')}` : (translations[currentLanguage] && translations[currentLanguage]['consulte'] ? translations[currentLanguage]['consulte'] : 'Consulte');
            const discountHTML = calculateDiscount(price, pkg.quantities);

            // Cria o HTML da lista de itens
            const itemsHTML = pkg.items.map(item => `<li><i class="fa fa-check-circle"></i> ${item}</li>`).join('');

            const pkgTitle = getPackageTitle(pkg.id) || pkg.title;
            const pkgItems = getPackageItems(pkg.id).length ? getPackageItems(pkg.id) : pkg.items;
            const itemsHTMLTranslated = pkgItems.map(item => `<li><i class="fa fa-check-circle"></i> ${item}</li>`).join('');
            const hireLabelPkg = translations[currentLanguage] && translations[currentLanguage]['btn-hire'] ? translations[currentLanguage]['btn-hire'] : 'Contratar';
            const whatsappMsgPkg = encodeURIComponent(`Olá, gostaria de apresentar a minha ideia e solicitar um orçamento para o produto/serviço: *Edição de Vídeo - ${pkgTitle.replace(/<[^>]*>/g, '')}*.`);
            const whatsappLinkPkg = `https://wa.me/5511963208691?text=${whatsappMsgPkg}`;
            const pkgIconHTML = pkg.icon ? (`<i class="${pkg.icon} package-icon-inline" aria-hidden="true"></i>` + `<svg class="package-icon-svg icon-svg small" role="img" aria-hidden="true" style="display:none" viewBox="0 0 16 16"><circle cx="8" cy="8" r="7"/></svg>`) : (pkg.iconImage ? `<img class="package-icon" src="${pkg.iconImage}" alt="${pkgTitle.replace(/<[^>]*>/g, '')}">` : '');
            packagesGrid.innerHTML += `
                <div class="service-card package-card">
                    <h3>${pkgIconHTML} ${pkgTitle}</h3>
                    <ul class="package-items">${itemsHTMLTranslated}</ul>
                    <p>${priceString}</p>
                    ${discountHTML} <a href="${whatsappLinkPkg}" target="_blank" rel="noopener noreferrer"><button>${hireLabelPkg}</button></a>
                </div>
            `;
        });
    }
}

// Renderiza cards de Motion Graphics (somente serviços, sem pacotes)
// Popula a grid #motion-graphics-services-grid
function rendermotionGraphicsServices() {
    const servicesGrid = document.getElementById('motion-graphics-services-grid');

    if (servicesGrid) {
        servicesGrid.innerHTML = ''; // Limpa a grid
        motionGraphicsServices.forEach(service => {
            const price = allPrices[service.id];
            const priceString = price ? `R$ ${price.toFixed(2).replace('.', ',')}` : (translations[currentLanguage] && translations[currentLanguage]['consulte'] ? translations[currentLanguage]['consulte'] : 'Consulte');

            const svcTitle = getServiceTitle(service.id);
            const svcDesc = getServiceDesc(service.id) || service.desc || '';
            const hireLabel = translations[currentLanguage] && translations[currentLanguage]['btn-hire'] ? translations[currentLanguage]['btn-hire'] : 'Contratar';
            const whatsappMsg = encodeURIComponent(`Olá, gostaria de apresentar a minha ideia e solicitar um orçamento para o produto/serviço: *Motion Graphics - ${svcTitle.replace(/<[^>]*>/g, '')}*.`);
            const whatsappLink = `https://wa.me/5511963208691?text=${whatsappMsg}`;
            const infoText = getServiceInfo(service.id) || 'Duração: até 15 segundos. Entrega em 4-6 dias úteis. Formatos: MP4, MOV. 2 revisões incluídas.';
            const thumbnailHTML = service.image ? `<img class="service-thumb-img" src="${service.image}" alt="${svcTitle}" loading="lazy" onerror="this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22300%22 height=%22180%22%3E%3Crect width=%22300%22 height=%22180%22 fill=%22%23ccc%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 font-size=%2216%22 fill=%22%23999%22 text-anchor=%22middle%22 dy=%22.3em%22%3ESem%20Imagem%3C/text%3E%3C/svg%3E';">` : (service.icon ? `<i class="${service.icon}"></i>` : '');
            servicesGrid.innerHTML += `
                <div class="service-card">
                    <div class="service-thumbnail">${thumbnailHTML}</div>
                    <div class="service-content">
                        <h3>${svcTitle}</h3>
                        <p class="service-price">${priceString}</p>
                        <span class="service-desc">${svcDesc}</span>
                        <div class="service-actions">
                            <a href="${whatsappLink}" target="_blank" rel="noopener noreferrer"><button>${hireLabel}</button></a>
                            <div class="info-icon-wrapper">
                                <i class="fas fa-info-circle info-icon"></i>
                                <div class="info-tooltip">${infoText}</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
    }
}

// ==============================================
// CONFIGURADOR DE ILUSTRAÇÃO DIGITAL
// Sistema multi-step: Estilo → Tipo → Adicionais
// IDs referenciam os arrays ilustracaoDigitalServices
// e motionGraphicsServices para dados dos cards.
// ==============================================
const ilustracaoConfigurador = {
    estilos: ['fanart_anime', 'fanart_cartoon', 'fanart_pixelart', 'fanart_chibi', 'fanart_vetorial'],       // Passo 1: estilo da arte
    tipos: ['ilustracao_perfil', 'ilustracao_corpo_inteiro', 'personagem_rpg', 'esboco_rapido', 'storyboard'], // Passo 2: tipo de arte
    adicionais: ['nenhum', 'artmotion']  // Passo 3: adicionais opcionais
};

// Estado da seleção atual do usuário no configurador
let ilustracaoSelecionada = {
    estilo: null,
    tipo: null,
    adicional: null
};

// Renderiza os 3 grupos de cards do configurador (estilos, tipos, adicionais)
// Cards selecionados recebem classe 'active'. Grupos ocultos até o passo anterior ser preenchido.
function renderilustracaoConfigurador() {
    // Renderizar cards de estilos
    const estiloscont = document.getElementById('ilustracao-estilos');
    if (estiloscont) {
        estiloscont.innerHTML = '';
        ilustracaoConfigurador.estilos.forEach(estiloId => {
            const serviceData = ilustracaoDigitalServices.find(s => s.id === estiloId);
            if (!serviceData) return;

            const isActive = ilustracaoSelecionada.estilo === estiloId;
            const card = document.createElement('div');
            card.className = `service-card ${isActive ? 'active' : ''}`;
            card.dataset.id = estiloId;
            card.style.cursor = 'pointer';
            card.addEventListener('click', () => selecionarEstiloIlustracao(estiloId));

            const thumbnailHTML = serviceData.image ? `<img class="service-thumb-img" src="${serviceData.image}" alt="${serviceData.title}" loading="lazy" onerror="this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22300%22 height=%22180%22%3E%3Crect width=%22300%22 height=%22180%22 fill=%22%23ccc%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 font-size=%2216%22 fill=%22%23999%22 text-anchor=%22middle%22 dy=%22.3em%22%3ESem%20Imagem%3C/text%3E%3C/svg%3E';">` : '';

            card.innerHTML = `
                <div class="service-thumbnail">${thumbnailHTML}</div>
                <div class="service-content">
                    <h3>${getTranslation(serviceData.title)}</h3>
                    <p class="service-price">R$ ${allPrices[estiloId].toFixed(2).replace('.', ',')}</p>
                    <span class="service-desc">${serviceData.desc}</span>
                </div>
            `;
            estiloscont.appendChild(card);
        });
    }

    // Renderizar cards de tipos
    const tiposGroup = document.getElementById('ilustracao-tipos-group');
    const tiposcont = document.getElementById('ilustracao-tipos');
    if (tiposcont && tiposGroup) {
        if (ilustracaoSelecionada.estilo) {
            tiposGroup.style.display = 'block';
            tiposcont.innerHTML = '';
            ilustracaoConfigurador.tipos.forEach(tipoId => {
                const serviceData = ilustracaoDigitalServices.find(s => s.id === tipoId);
                if (!serviceData) return;

                const isActive = ilustracaoSelecionada.tipo === tipoId;
                const card = document.createElement('div');
                card.className = `service-card ${isActive ? 'active' : ''}`;
                card.dataset.id = tipoId;
                card.style.cursor = 'pointer';
                card.addEventListener('click', () => selecionarTipoIlustracao(tipoId));

                const thumbnailHTML = serviceData.image ? `<img class="service-thumb-img" src="${serviceData.image}" alt="${serviceData.title}" loading="lazy" onerror="this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22300%22 height=%22180%22%3E%3Crect width=%22300%22 height=%22180%22 fill=%22%23ccc%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 font-size=%2216%22 fill=%22%23999%22 text-anchor=%22middle%22 dy=%22.3em%22%3ESem%20Imagem%3C/text%3E%3C/svg%3E';">` : '';

                card.innerHTML = `
                    <div class="service-thumbnail">${thumbnailHTML}</div>
                    <div class="service-content">
                        <h3>${getTranslation(serviceData.title)}</h3>
                        <p class="service-price">R$ ${allPrices[tipoId].toFixed(2).replace('.', ',')}</p>
                        <span class="service-desc">${serviceData.desc}</span>
                    </div>
                `;
                tiposcont.appendChild(card);
            });
        } else {
            tiposGroup.style.display = 'none';
        }
    }

    // Renderizar cards de adicionais
    const adicionaiscont = document.getElementById('ilustracao-adicionais');
    const adicionaisgroup = document.getElementById('ilustracao-adicionais-group');
    if (adicionaiscont && adicionaisgroup) {
        if (ilustracaoSelecionada.tipo) {
            adicionaisgroup.style.display = 'block';
            adicionaiscont.innerHTML = '';
            ilustracaoConfigurador.adicionais.forEach(adicionalId => {
                let serviceData = null;
                let title = '';
                let desc = '';
                let image = '';

                if (adicionalId === 'nenhum') {
                    title = 'Sem adicionais';
                    desc = 'Mantém o preço base sem acréscimos';
                } else if (adicionalId === 'artmotion') {
                    serviceData = ilustracaoDigitalServices.find(s => s.id === 'artmotion' || (s.id === 'intro_animada'));
                    if (!serviceData) {
                        // Procurar por Motion Graphics - artmotion
                        const motionService = motionGraphicsServices && motionGraphicsServices.find(s => s.id === 'artmotion');
                        if (motionService) {
                            title = motionService.title;
                            desc = motionService.desc;
                            image = motionService.image;
                        } else {
                            title = 'Arte Animada';
                            desc = 'Adiciona animação à sua ilustração (tipo motion)';
                            image = 'https://odysseexp.com/wp-content/uploads/2025/12/art-card-services-motion.png';
                        }
                    } else {
                        title = serviceData.title;
                        desc = serviceData.desc;
                        image = serviceData.image;
                    }
                } else {
                    serviceData = ilustracaoDigitalServices.find(s => s.id === adicionalId);
                    if (serviceData) {
                        title = serviceData.title;
                        desc = serviceData.desc;
                        image = serviceData.image;
                    }
                }

                const isActive = ilustracaoSelecionada.adicional === adicionalId;
                const card = document.createElement('div');
                card.className = `service-card ${isActive ? 'active' : ''}`;
                card.dataset.id = adicionalId;
                card.style.cursor = 'pointer';
                card.addEventListener('click', () => selecionarAdicionalIlustracao(adicionalId));

                const thumbnailHTML = image ? `<img class="service-thumb-img" src="${image}" alt="${title}" loading="lazy" onerror="this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22300%22 height=%22180%22%3E%3Crect width=%22300%22 height=%22180%22 fill=%22%23ccc%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 font-size=%2216%22 fill=%22%23999%22 text-anchor=%22middle%22 dy=%22.3em%22%3ESem%20Imagem%3C/text%3E%3C/svg%3E';">` : '';

                const price = adicionalId === 'nenhum' ? 0 : (allPrices[adicionalId] || 0);

                card.innerHTML = `
                    <div class="service-thumbnail">${thumbnailHTML}</div>
                    <div class="service-content">
                        <h3>${getTranslation(title)}</h3>
                        <p class="service-price">R$ ${price.toFixed(2).replace('.', ',')}</p>
                        <span class="service-desc">${desc}</span>
                    </div>
                `;
                adicionaiscont.appendChild(card);
            });
        } else {
            adicionaisgroup.style.display = 'none';
        }
    }
}

// Helper: traduz labels dinâmicos do configurador de ilustração
function getTranslation(text) {
    // Dicionário de traduções para os labels
    const translations_config = {
        pt: {
            'Arte Estilo Anime': 'Arte Estilo Anime',
            'Arte Estilo Cartoon': 'Arte Estilo Cartoon',
            'Arte Estilo Pixel Art': 'Arte Estilo Pixel Art',
            'Arte Estilo Chibi': 'Arte Estilo Chibi',
            'Arte Estilo Vetorial': 'Arte Estilo Vetorial',
            'Personagem perfil/busto': 'Personagem perfil/busto',
            'Personagem corpo inteiro': 'Personagem corpo inteiro',
            'Personagem token RPG': 'Personagem token RPG',
            'Esboço rápido': 'Esboço rápido',
            'Story board': 'Story board',
            'Sem adicionais': 'Sem adicionais',
            'Arte Animada': 'Arte Animada'
        },
        en: {
            'Arte Estilo Anime': 'Anime Art Style',
            'Arte Estilo Cartoon': 'Cartoon Art Style',
            'Arte Estilo Pixel Art': 'Pixel Art Style',
            'Arte Estilo Chibi': 'Chibi Art Style',
            'Arte Estilo Vetorial': 'Vector Art Style',
            'Personagem perfil/busto': 'Character Profile/Bust',
            'Personagem corpo inteiro': 'Full Body Character',
            'Personagem token RPG': 'RPG Token Character',
            'Esboço rápido': 'Quick Sketch',
            'Story board': 'Story Board',
            'Sem adicionais': 'No Addons',
            'Arte Animada': 'Animated Art'
        }
    };

    const lang = currentLanguage || 'pt';
    return translations_config[lang] && translations_config[lang][text] ? translations_config[lang][text] : text;
}

// Callbacks de seleção para cada passo do configurador
// Ao selecionar um estilo, reseta tipo e adicional
function selecionarEstiloIlustracao(estiloId) {
    ilustracaoSelecionada.estilo = estiloId;
    ilustracaoSelecionada.tipo = null;
    ilustracaoSelecionada.adicional = null;
    renderilustracaoConfigurador();
}

function selecionarTipoIlustracao(tipoId) {
    ilustracaoSelecionada.tipo = tipoId;
    ilustracaoSelecionada.adicional = null;
    renderilustracaoConfigurador();
    mostrarResumoIlustracao();
}

function selecionarAdicionalIlustracao(adicionalId) {
    ilustracaoSelecionada.adicional = adicionalId;
    renderilustracaoConfigurador();
    mostrarResumoIlustracao();
}

// Calcula e exibe o resumo da ilustração configurada
// Preço total = preço_estilo + preço_tipo + preço_adicional
// Gera link do WhatsApp com detalhes da seleção
function mostrarResumoIlustracao() {
    const resumoBox = document.getElementById('ilustracao-resumo');
    const resumoTexto = document.getElementById('ilustracao-resumo-texto');
    const resumoPreco = document.getElementById('ilustracao-resumo-preco');
    const btnContratar = document.getElementById('ilustracao-btn-contratar');

    if (!ilustracaoSelecionada.estilo || !ilustracaoSelecionada.tipo || !ilustracaoSelecionada.adicional) {
        resumoBox.style.display = 'none';
        return;
    }

    // Puxar dados dos serviços
    const estiloData = ilustracaoDigitalServices.find(s => s.id === ilustracaoSelecionada.estilo);
    const tipoData = ilustracaoDigitalServices.find(s => s.id === ilustracaoSelecionada.tipo);

    const estiloTitle = estiloData ? estiloData.title : '';
    const tipoTitle = tipoData ? tipoData.title : '';

    // Calcular preço somando ESTILO + TIPO + ADICIONAL
    let priceEstilo = allPrices[ilustracaoSelecionada.estilo] || 0;
    let priceBase = allPrices[ilustracaoSelecionada.tipo] || 0;
    let priceAdicional = ilustracaoSelecionada.adicional === 'artmotion' ? allPrices['artmotion'] : 0;
    let totalPrice = priceEstilo + priceBase + priceAdicional;

    // Montar texto do resumo
    let resumoText = `${getTranslation(estiloTitle)} - ${getTranslation(tipoTitle)}`;
    if (ilustracaoSelecionada.adicional !== 'nenhum') {
        let adicionalTitle = 'Arte Animada';
        if (ilustracaoSelecionada.adicional === 'artmotion') {
            const motionData = ilustracaoDigitalServices.find(s => s.id === 'intro_animada');
            adicionalTitle = motionData ? motionData.title : 'Arte Animada';
        }
        resumoText += ` + ${getTranslation(adicionalTitle)}`;
    }

    // Formatar preço
    const priceFormatted = currentLanguage === 'en' ? totalPrice.toFixed(2) : totalPrice.toFixed(2).replace('.', ',');

    // Atualizar HTML
    resumoTexto.textContent = resumoText;
    resumoPreco.innerHTML = `<strong>R$ ${priceFormatted}</strong>`;

    // Montar mensagem do WhatsApp
    const whatsappMsg = encodeURIComponent(
        `Olá, gostaria de solicitar um orçamento para uma ilustração digital com as seguintes características:\n\n*Estilo:* ${getTranslation(estiloTitle)}\n*Tipo:* ${getTranslation(tipoTitle)}\n*Adicionais:* ${ilustracaoSelecionada.adicional === 'nenhum' ? 'Nenhum' : 'Arte Animada'}\n*Preço base:* R$ ${priceFormatted}`
    );
    btnContratar.href = `https://wa.me/5511963208691?text=${whatsappMsg}`;

    // Mostrar resumo
    resumoBox.style.display = 'block';
}

// Renderiza o configurador de ilustração (substitui cards individuais)
function renderilustracaoDigitalServices() {
    // Agora renderiza o configurador em vez dos cards individuais
    renderilustracaoConfigurador();
}

// ==============================================
// ESTADO GLOBAL E ELEMENTOS DOM
// ==============================================
let currentLanguage = (window.safeStorage && window.safeStorage.getItem('userLang')) || 'pt';
const body = document.body;
const container = document.getElementById('onboarding-container');
const sidebar = document.getElementById('settings-sidebar');
const backdrop = document.getElementById('settings-backdrop');
const settingsBtn = document.getElementById('btn-settings');
const closeSettingsBtn = document.getElementById('close-settings-btn');

/* ==============================================
   FUNÇÕES AUXILIARES: Tradução, tema, navegação
   ============================================== */

// Traduz todos os elementos com data-key e re-renderiza cards dinâmicos
function translatePage() {
    document.documentElement.lang = currentLanguage;
    const elements = document.querySelectorAll('[data-key]');
    elements.forEach(element => {
        const key = element.dataset.key;
        if (translations[currentLanguage] && translations[currentLanguage][key]) {
            element.textContent = translations[currentLanguage][key];
        }
    });

    // 1) Tradução de títulos de seção baseados no id do <section>
    document.querySelectorAll('.section-title').forEach(el => {
        const section = el.closest('section');
        if (section && translations[currentLanguage] && translations[currentLanguage][section.id]) {
            el.textContent = translations[currentLanguage][section.id];
        }
    });

    // 2) Tradução dos links do menu principal (por href)
    document.querySelectorAll('.main-nav a').forEach(a => {
        const href = a.getAttribute('href') || '';
        if (href.startsWith('#')) {
            const id = href.replace('#', '');
            if (translations[currentLanguage] && translations[currentLanguage][id]) a.textContent = translations[currentLanguage][id];
        } else if (href.includes('/posts')) {
            if (translations[currentLanguage] && translations[currentLanguage]['todos-posts']) a.textContent = translations[currentLanguage]['todos-posts'];
        }
    });

    // 3) Menu mobile
    document.querySelectorAll('.mobile-nav-links a').forEach(a => {
        const href = a.getAttribute('href') || '';
        if (href.startsWith('#')) {
            const id = href.replace('#', '');
            if (translations[currentLanguage] && translations[currentLanguage][id]) a.textContent = translations[currentLanguage][id];
        } else if (href.includes('/posts')) {
            if (translations[currentLanguage] && translations[currentLanguage]['todos-posts']) a.textContent = translations[currentLanguage]['todos-posts'];
        }
    });

    // 4) Busca - placeholder
    const searchInput = document.getElementById('search-input');
    if (searchInput && translations[currentLanguage] && translations[currentLanguage]['buscar-placeholder']) {
        searchInput.placeholder = translations[currentLanguage]['buscar-placeholder'];
    }

    // 5) Onboarding texts (se existir)
    const onboardingTitle = document.querySelector('.onboarding-title');
    if (onboardingTitle && translations[currentLanguage] && translations[currentLanguage]['titulo']) onboardingTitle.textContent = translations[currentLanguage]['titulo'];
    const onboardingSubtitle = document.querySelector('.onboarding-subtitle');
    if (onboardingSubtitle && translations[currentLanguage] && translations[currentLanguage]['subtitulo']) onboardingSubtitle.textContent = translations[currentLanguage]['subtitulo'];
    const stepLang = document.querySelector('#step-language p');
    if (stepLang && translations[currentLanguage] && translations[currentLanguage]['escolha-idioma']) stepLang.textContent = translations[currentLanguage]['escolha-idioma'];
    const stepTheme = document.querySelector('#step-theme p');
    if (stepTheme && translations[currentLanguage] && translations[currentLanguage]['escolha-tema']) stepTheme.textContent = translations[currentLanguage]['escolha-tema'];
    const note = document.querySelector('#step-theme .note');
    if (note && translations[currentLanguage] && translations[currentLanguage]['aviso-tema']) note.textContent = translations[currentLanguage]['aviso-tema'];

    // 6) Footer / contact area
    const contato = document.querySelector('#contato h2');
    if (contato && translations[currentLanguage] && translations[currentLanguage]['contato']) contato.textContent = translations[currentLanguage]['contato'];
    const faleBtn = document.querySelector('.footer-section .btn-primary');
    if (faleBtn && translations[currentLanguage] && translations[currentLanguage]['fale-whatsapp']) faleBtn.textContent = translations[currentLanguage]['fale-whatsapp'];

    // Re-render dynamic service cards (se existirem) para aplicar traduções
    try { if (typeof renderDesignServices === 'function') renderDesignServices(); } catch (e) { console.error('[RENDER] renderDesignServices error:', e); }
    try { if (typeof renderVideoServices === 'function') renderVideoServices(); } catch (e) { console.error('[RENDER] renderVideoServices error:', e); }
    try { if (typeof rendermotionGraphicsServices === 'function') rendermotionGraphicsServices(); } catch (e) { console.error('[RENDER] rendermotionGraphicsServices error:', e); }
    try { if (typeof renderilustracaoDigitalServices === 'function') renderilustracaoDigitalServices(); } catch (e) { console.error('[RENDER] renderilustracaoDigitalServices error:', e); }
    // Renderizar produtos custom do admin (após os hardcoded)
    try { if (typeof renderCustomProducts === 'function') renderCustomProducts(); } catch (e) { console.error('[RENDER] renderCustomProducts error:', e); }

    // Re-render tag pills com tradução
    try { if (typeof renderTagPills === 'function') renderTagPills(); } catch (e) { console.error('[RENDER] renderTagPills error:', e); }

    // Re-render blog posts com textos traduzidos
    const grid = document.querySelector('.post-grid');
    if (grid && grid.children.length > 0) {
        try { renderBlog(); } catch (e) { console.error('[RENDER] renderBlog error:', e); }
    }

    // Habilita indicadores de swipe nas grids de serviços em mobile
    try {
        if (typeof enableServiceGridMobileScrollHints === 'function') {
            // Deixa um timeout pequeno para garantir que o DOM tenha sido atualizado
            setTimeout(enableServiceGridMobileScrollHints, 120);
        }
    } catch (e) { console.error('[RENDER] enableServiceGridMobileScrollHints error:', e); }
}

// --- Mobile: indicador de swipe e melhoria de scroll para grids de serviços ---
function enableServiceGridMobileScrollHints() {
    // Seleciona grids de serviços e pacotes pelo sufixo do id
    const grids = document.querySelectorAll('[id$="-services-grid"], [id$="-packages-grid"]');
    if (!grids || grids.length === 0) return;

    grids.forEach(grid => {
        // aplica classe para o CSS (opcional)
        grid.classList.add('mobile-h-scroll');

        // garante posição relativa para o hint
        if (getComputedStyle(grid).position === 'static') grid.style.position = 'relative';

        // remove hint existente
        const existing = grid.querySelector('.swipe-hint');
        if (existing) existing.remove();

        // se houver overflow (mais conteúdo que a largura), adiciona hint
        if (grid.scrollWidth > grid.clientWidth + 10) {
            const hint = document.createElement('div');
            hint.className = 'swipe-hint';
            hint.innerHTML = `<span class="swipe-text">${(translations[currentLanguage] && translations[currentLanguage]['swipe']) ? translations[currentLanguage]['swipe'] : 'Swipe'}</span> <span class="swipe-chevron"><i class="fas fa-chevron-right"></i></span>`;
            grid.appendChild(hint);

            // Esconder o hint ao primeiro scroll ou toque
            const hideHint = () => {
                hint.style.opacity = '0';
                setTimeout(() => { try { hint.remove(); } catch (e) { } }, 300);
                grid.removeEventListener('touchstart', hideHint);
                grid.removeEventListener('scroll', hideHint);
            };

            grid.addEventListener('touchstart', hideHint, { once: true });
            grid.addEventListener('scroll', hideHint, { once: true });
        }
    });
}

function selectLanguage(lang) {
    currentLanguage = lang;
    if (window.safeStorage) { window.safeStorage.setItem('userLang', lang); } else { try { localStorage.setItem('userLang', lang); } catch (e) { } }
    translatePage();
    if (container) container.classList.add('show-theme');
}

function finalizarOnboarding() {
    body.classList.remove('modo-onboarding');
    if (window.safeStorage) { window.safeStorage.setItem('onboardingFeito', 'sim'); } else { try { localStorage.setItem('onboardingFeito', 'sim'); } catch (e) { } }
}

function openSettings() {
    if (sidebar) sidebar.classList.add('open');
    if (backdrop) {
        backdrop.style.display = "block";
        setTimeout(() => backdrop.classList.add('visible'), 10);
    }
}

function closeSettings() {
    if (sidebar) sidebar.classList.remove('open');
    if (backdrop) {
        backdrop.classList.remove('visible');
        setTimeout(() => backdrop.style.display = "none", 300);
    }
}

function toggleSocial() {
    const socialLinks = document.getElementById('social-links');
    if (socialLinks) socialLinks.classList.toggle('hidden');
}

// ==============================================
// CARROSSEL: Cria cards de post para o carrossel
// Suporta drag (mouse/touch) e clique para navegar
// ==============================================
function createCard(post) {
    const card = document.createElement('div');
    card.className = 'carousel-card';
    let isDragging = false;

    // DRAGGABLE FALSE É CRUCIAL
    card.innerHTML = `
        <img src="${escapeHtml(post.image)}" class="carousel-img" alt="${escapeHtml(post.title)}" draggable="false" loading="lazy">
        <div class="carousel-info">
            <h4 class="carousel-title">${escapeHtml(post.title)}</h4>
            <span class="carousel-date">${new Date(post.date).toLocaleDateString(currentLanguage === 'en' ? 'en-US' : 'pt-BR')}</span>
        </div>
    `;

    // Detecta se está arrastando ou clicando
    let startPos = 0;
    card.addEventListener('mousedown', (e) => {
        startPos = e.pageX;
        isDragging = false;
    });

    card.addEventListener('mousemove', (e) => {
        if (Math.abs(e.pageX - startPos) > 5) {
            isDragging = true;
        }
    });

    // Adiciona evento de clique apenas se não estiver arrastando
    card.addEventListener('click', (e) => {
        if (!isDragging && post.link) {
            const safeLink = sanitizeUrl(post.link);
            if (safeLink) window.location.href = safeLink;
        }
        isDragging = false;
    });

    card.style.cursor = 'pointer';
    return card;
}

// ==============================================
// CARROSSEL: Esteira contínua com tripla duplicação
// Usa requestAnimationFrame para scroll suave.
// Suporta drag, touch, hover-pause e loop infinito.
// ==============================================
function renderCarousel(containerId, filterCategory = null) {
    const container = document.getElementById(containerId);
    if (!container) return; // Se o container não existe, aborta sem erro

    // 1. Filtrar e Ordenar
    let filteredPosts = allPosts.sort((a, b) => new Date(b.date) - new Date(a.date));
    if (filterCategory) {
        // Filtra posts que têm a categoria (pode estar em categories array)
        filteredPosts = filteredPosts.filter(post =>
            post.categories && post.categories.includes(filterCategory)
        );
    }

    const basePosts = filteredPosts.slice(0, 8);

    // 2. Criar o Trilho
    const track = document.createElement('div');
    track.className = 'carousel-track';

    // 3. TRIPLA DUPLICAÇÃO (Set A + Set B + Set C)
    // Isso garante que sempre temos conteúdo para esquerda e direita
    const triplePosts = [...basePosts, ...basePosts, ...basePosts];
    triplePosts.forEach(post => {
        track.appendChild(createCard(post));
    });
    container.appendChild(track);

    // 4. Variáveis de Animação
    let position = 0;
    let speed = 0.2;
    let isDown = false;
    let startX = 0;
    let scrollLeftStart = 0;
    let isHovering = false;
    let initialized = false;
    let singleSetWidth = 0;
    let animationId = null; // Para controlar o loop de animação

    // Espera imagens do trilho carregarem para evitar mudanças de largura
    function waitForTrackImages(timeout = 1500) {
        const imgs = Array.from(track.querySelectorAll('img'));
        if (imgs.length === 0) return Promise.resolve();
        return new Promise(resolve => {
            let remaining = imgs.length;
            const check = () => {
                remaining -= 1;
                if (remaining <= 0) resolve();
            };
            imgs.forEach(img => {
                if (img.complete && img.naturalWidth !== 0) {
                    check();
                } else {
                    img.addEventListener('load', check, { once: true });
                    img.addEventListener('error', check, { once: true });
                }
            });
            // fallback: resolve após timeout mesmo se algo travar
            setTimeout(resolve, timeout);
        });
    }

    function animate() {
        // Calcula a largura de UM conjunto de posts de forma robusta
        const totalWidth = track.scrollWidth;
        let calculatedWidth = totalWidth / 3;
        try {
            const children = track.children;
            const baseLen = basePosts.length;
            if (children.length >= baseLen * 2) {
                const startB = children[baseLen];
                const startC = children[baseLen * 2];
                const rectB = startB.getBoundingClientRect();
                const rectC = startC.getBoundingClientRect();
                calculatedWidth = Math.round(rectC.left - rectB.left);
            }
        } catch (e) { }

        singleSetWidth = calculatedWidth;

        // Inicialização
        if (!initialized && totalWidth > 0 && singleSetWidth > 0) {
            position = -singleSetWidth;
            initialized = true;
        }

        // Auto-scroll apenas quando NÃO está em drag
        if (!isDown && !isHovering && initialized) {
            position -= speed;

            // Teleporte infinito
            if (position <= -(singleSetWidth * 2)) {
                position += singleSetWidth;
            } else if (position >= 0) {
                position -= singleSetWidth;
            }
        }

        // Aplica transform
        track.style.transform = `translateX(${position}px)`;

        // Continua o loop
        animationId = requestAnimationFrame(animate);
    }

    // Aguarda imagens para estabilizar larguras antes de iniciar a animação
    waitForTrackImages(1500).then(() => {
        // Força recálculo e posicionamento inicial baseado no tamanho real
        const totalWidth = track.scrollWidth;
        let singleSetWidth = totalWidth / 3;
        try {
            const children = track.children;
            const baseLen = basePosts.length;
            if (children.length >= baseLen * 2) {
                const startB = children[baseLen];
                const startC = children[baseLen * 2];
                const rectB = startB.getBoundingClientRect();
                const rectC = startC.getBoundingClientRect();
                singleSetWidth = Math.round(rectC.left - rectB.left);
            }
        } catch (e) { }

        if (totalWidth > 0 && singleSetWidth > 0) {
            position = -singleSetWidth;
            initialized = true;
        }
        requestAnimationFrame(animate);
    });

    // --- EVENTOS DE MOUSE E TOUCH (SISTEMA SIMPLIFICADO) ---

    let dragStartX = 0;
    let dragStartPosition = 0;

    function handleDragStart(clientX) {
        isDown = true;
        isHovering = true;
        dragStartX = clientX;
        dragStartPosition = position;
        track.style.cursor = 'grabbing';
        track.classList.add('active');
    }

    function handleDragMove(clientX) {
        if (!isDown) return;
        const deltaX = clientX - dragStartX;
        position = dragStartPosition + deltaX;
    }

    function handleDragEnd() {
        if (!isDown) return;
        isDown = false;
        track.style.cursor = 'grab';
        track.classList.remove('active');
        setTimeout(() => { isHovering = false; }, 500);
    }

    // Mouse (com throttle para melhor performance)
    track.addEventListener('mousedown', (e) => {
        e.preventDefault();
        handleDragStart(e.clientX);
    });

    const throttledMouseMove = throttle((clientX) => {
        if (isDown) {
            handleDragMove(clientX);
        }
    }, 16); // ~60fps

    window.addEventListener('mousemove', (e) => {
        throttledMouseMove(e.clientX);
    });

    window.addEventListener('mouseup', () => {
        handleDragEnd();
    });

    // Touch (com throttle para melhor performance)
    track.addEventListener('touchstart', (e) => {
        handleDragStart(e.touches[0].clientX);
    }, { passive: true });

    const throttledTouchMove = throttle((clientX) => {
        if (isDown) {
            handleDragMove(clientX);
        }
    }, 16); // ~60fps

    track.addEventListener('touchmove', (e) => {
        throttledTouchMove(e.touches[0].clientX);
    }, { passive: true });

    track.addEventListener('touchend', () => {
        handleDragEnd();
    }, { passive: true });

    // Hover para pausar
    track.addEventListener('mouseenter', () => {
        if (!isDown) isHovering = true;
    });

    track.addEventListener('mouseleave', () => {
        if (!isDown) isHovering = false;
    });
}


/* ==============================================
   EVENT LISTENERS GERAIS
   Header scroll, settings sidebar, temas, cores,
   idioma, menu mobile.
   ============================================== */

// Header: alterna entre transparente e scrolled
window.addEventListener('scroll', () => {
    const header = document.querySelector('.main-header');
    // Removi a restrição de tamanho de tela! Agora roda sempre.
    if (header) {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
            header.classList.remove('transparent');
        } else {
            header.classList.add('transparent');
            header.classList.remove('scrolled');
        }
    }
});

if (settingsBtn) {
    settingsBtn.addEventListener('click', (e) => {
        e.preventDefault();
        openSettings();
    });
}
if (closeSettingsBtn) closeSettingsBtn.addEventListener('click', closeSettings);
if (backdrop) backdrop.addEventListener('click', closeSettings);


// Temas
const btnLight = document.getElementById('btn-light');
const btnDark = document.getElementById('btn-dark');
const setLight = document.getElementById('set-theme-light');
const setDark = document.getElementById('set-theme-dark');
const colorDots = document.querySelectorAll('.color-dot');

function applyTheme(theme) {
    body.classList.remove('theme-light', 'theme-dark');
    body.classList.add(`theme-${theme}`);
    if (window.safeStorage) { window.safeStorage.setItem('userTheme', theme); } else { try { localStorage.setItem('userTheme', theme); } catch (e) { } }
}

if (btnLight) btnLight.addEventListener('click', () => applyTheme('light'));
if (btnDark) btnDark.addEventListener('click', () => applyTheme('dark'));
if (setLight) setLight.addEventListener('click', () => applyTheme('light'));
if (setDark) setDark.addEventListener('click', () => applyTheme('dark'));

// Fallback: if theme buttons were rendered without SVG (older markups or CSS-injection), inject inline SVGs
if (setLight && !setLight.querySelector('svg')) {
    setLight.insertAdjacentHTML('afterbegin', '<svg class="icon-svg" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" role="img" aria-hidden="true"><path d="M6.76 4.84l-1.8-1.79-1.41 1.41 1.79 1.79zM4 10.5H1v3h3zm9-9.95h-3v3.95h3zm7.45 3.91l-1.41-1.41-1.79 1.79 1.41 1.41zm-3.21 13.7l1.79 1.8 1.41-1.41-1.8-1.79zM20 10.5v3h3v-3zm-8-5c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6zm-1 16.95h3v-3.95h-3zm-7.45-3.91l1.41 1.41 1.79-1.8-1.41-1.41z"/></svg>');
}
if (setDark && !setDark.querySelector('svg')) {
    setDark.insertAdjacentHTML('afterbegin', '<svg class="icon-svg" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" role="img" aria-hidden="true"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>');
}

// Ensure mobile footer items have icons
document.querySelectorAll('#mobile-menu-overlay .mobile-menu-footer .btn-mobile-faq, #mobile-menu-overlay .mobile-menu-footer .btn-mobile-settings').forEach(el => {
    if (!el.querySelector('svg')) {
        // small generic icon fallback (question or cog based on class)
        if (el.classList.contains('btn-mobile-settings')) {
            el.insertAdjacentHTML('afterbegin', '<svg class="icon-svg" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" role="img" aria-hidden="true"><path d="M19.14 12.94c.04-.31.06-.63.06-.94s-.02-.63-.06-.94l2.03-1.58a.5.5 0 00.11-.65l-1.92-3.32a.5.5 0 00-.6-.22l-2.39.96a7.066 7.066 0 00-1.62-.94l-.36-2.54A.5.5 0 0013.4 2h-2.8a.5.5 0 00-.5.42l-.36 2.54c-.57.21-1.11.5-1.62.94L5.6 5.96a.5.5 0 00-.6.22L3.08 9.5a.5.5 0 00.11.65l2.03 1.58c-.04.31-.06.63-.06.94s.02.63.06.94L3.19 15.2a.5.5 0 00-.11.65l1.92 3.32c.14.24.44.34.68.22l2.39-.96c.5.44 1.05.8 1.62.94l.36 2.54c.05.28.28.48.5.48h2.8c.28 0 .46-.2.5-.48l.36-2.54c.57-.21 1.11-.5 1.62-.94l2.39.96c.24.12.54.02.68-.22l1.92-3.32a.5.5 0 00-.11-.65l-2.03-1.58zM12 15.5A3.5 3.5 0 1112 8.5a3.5 3.5 0 010 7z"/></svg>');
        } else if (el.classList.contains('btn-mobile-faq')) {
            el.insertAdjacentHTML('afterbegin', '<svg class="icon-svg" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" role="img" aria-hidden="true"><path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm.25 17h-1.5v-1.5h1.5V19zm1.34-7.03c-.24.24-.41.48-.53.71-.18.35-.27.73-.26 1.21h-1.5c0-.84.21-1.56.63-2.16.25-.35.6-.7 1.03-1.06.31-.25.55-.49.72-.73.17-.25.26-.56.26-.94 0-.6-.22-1.1-.66-1.49-.44-.4-1.02-.6-1.74-.6-1.02 0-1.93.38-2.72 1.13l-.96-1.11C9.32 6.1 10.68 5.4 12 5.4c1.06 0 1.87.34 2.45 1.03.58.69.87 1.53.87 2.5 0 .86-.24 1.56-.74 2.03z"/></svg>');
        }
    }
});

// Debug: report whether theme buttons contain SVGs
console.debug('theme icons:', { setLightHasSVG: !!(setLight && setLight.querySelector('svg')), setDarkHasSVG: !!(setDark && setDark.querySelector('svg')) });
colorDots.forEach(dot => {
    dot.addEventListener('click', () => {
        const newColor = dot.classList[1];
        if (newColor) {
            body.classList.remove('color-blue', 'color-purple', 'color-red', 'color-yellow', 'color-green');
            body.classList.add(`color-${newColor}`);
            if (window.safeStorage) { window.safeStorage.setItem('userColor', newColor); } else { try { localStorage.setItem('userColor', newColor); } catch (e) { } }
        }
    });
});

document.getElementById('set-lang-pt')?.addEventListener('click', () => {
    currentLanguage = 'pt';
    if (window.safeStorage) { window.safeStorage.setItem('userLang', 'pt'); } else { try { localStorage.setItem('userLang', 'pt'); } catch (e) { } }
    translatePage();
    // Notifica outras partes do app (como a página Sobre) que o idioma mudou
    try { window.dispatchEvent(new CustomEvent('odyssee-storage', { detail: { key: 'userLang', value: 'pt' } })); } catch (e) { }
});
document.getElementById('set-lang-en')?.addEventListener('click', () => {
    currentLanguage = 'en';
    if (window.safeStorage) { window.safeStorage.setItem('userLang', 'en'); } else { try { localStorage.setItem('userLang', 'en'); } catch (e) { } }
    translatePage();
    // Notifica outras partes do app (como a página Sobre) que o idioma mudou
    try { window.dispatchEvent(new CustomEvent('odyssee-storage', { detail: { key: 'userLang', value: 'en' } })); } catch (e) { }
});

const socialBtn = document.getElementById('social-media-btn');
if (socialBtn) socialBtn.addEventListener('click', toggleSocial);

// Menu Mobile
const btnMobile = document.getElementById('btn-mobile-menu');
const mobileMenu = document.getElementById('mobile-menu-overlay');
const closeMobile = document.getElementById('close-mobile-menu');
const btnSettingsMobile = document.getElementById('btn-settings-mobile');

// Debug: check presence of header controls in runtime
console.debug('UI elements:', {
    settingsBtn: !!settingsBtn,
    btnMobile: !!btnMobile,
    btnSettingsMobile: !!btnSettingsMobile,
    btnFaq: !!document.getElementById('btn-faq')
});

if (btnMobile) btnMobile.addEventListener('click', () => mobileMenu.classList.add('open'));
if (closeMobile) closeMobile.addEventListener('click', () => mobileMenu.classList.remove('open'));
document.querySelectorAll('.mobile-link').forEach(link => {
    link.addEventListener('click', () => mobileMenu.classList.remove('open'));
});
if (btnSettingsMobile) {
    btnSettingsMobile.addEventListener('click', () => {
        mobileMenu.classList.remove('open');
        setTimeout(openSettings, 300);
    });
}

/* --- 4. INICIALIZAÇÃO --- */
// Global error handlers to avoid silent failures and help debugging
window.addEventListener('error', (ev) => { try { console.error('[GLOBAL ERROR]', ev.message || ev.error || ev); } catch (e) { } });
window.addEventListener('unhandledrejection', (ev) => { try { console.error('[UNHANDLED REJECTION]', ev.reason); } catch (e) { } });

// SEGURANÇA: Garantir rel="noopener noreferrer" em todos os links target="_blank"
// Defesa em profundidade para links criados dinamicamente via innerHTML
document.addEventListener('click', function (e) {
    const link = e.target.closest('a[target="_blank"]');
    if (link && !link.getAttribute('rel')) {
        link.setAttribute('rel', 'noopener noreferrer');
    } else if (link && link.getAttribute('rel') && !link.getAttribute('rel').includes('noreferrer')) {
        link.setAttribute('rel', link.getAttribute('rel') + ' noreferrer');
    }
}, true);

// Roda o tradutor imediatamente
translatePage();

// Inicialização de UI feita após fetchRealPosts() para garantir dados disponíveis

/* ==============================================
   MÓDULO BLOG: Renderização de posts, filtros,
   busca com sanitização e tags de categoria.
   ============================================== */

// Renderiza grid de posts com filtro de texto e categoria
function renderBlog(searchTerm = '', categoryFilter = '') {
    const grid = document.querySelector('.post-grid');
    if (!grid) return; // Não estamos na página de posts

    grid.innerHTML = ''; // Limpa os exemplos estáticos

    // 1. Filtragem
    const filtered = allPosts.filter(post => {
        // Busca por texto (Título ou Categoria)
        const textMatch = searchTerm === '' ||
            post.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
            (post.categories && post.categories.some(cat => cat.toLowerCase().includes(searchTerm.toLowerCase())));

        // Busca por Categoria (clique nas tags)
        // Verifica se a categoria está no array de categorias do post
        const catMatch = categoryFilter === '' ||
            (post.categories && post.categories.includes(categoryFilter));

        return textMatch && catMatch;
    });

    // Se não achar nada
    if (filtered.length === 0) {
        const noResults = translations[currentLanguage] && translations[currentLanguage]['no-results'] ? translations[currentLanguage]['no-results'] : 'Nenhum projeto encontrado com esses termos.';
        const p = document.createElement('p');
        p.style.gridColumn = '1/-1';
        p.style.textAlign = 'center';
        p.style.opacity = '0.7';
        p.style.fontSize = '1.2rem';
        p.textContent = noResults;
        grid.appendChild(p);
        return;
    }

    // 2. Renderização
    filtered.forEach(post => {
        // Verifica se post é válido
        if (!post || !post.image || !post.date || !post.title) {
            console.warn('[BLOG] Post inválido:', post);
            return;
        }

        const card = document.createElement('div');
        card.className = 'post-card';

        // Formata a data
        const dateStr = new Date(post.date).toLocaleDateString(currentLanguage === 'en' ? 'en-US' : 'pt-BR');

        // Pega traduções
        const verDetalhes = translations[currentLanguage]['ver-detalhes'] || 'Ver Detalhes';
        const portfolioText = translations[currentLanguage]['portfolio'] || 'portfolio';

        // Traduz TODAS as categorias (suporta múltiplas)
        const categoriesTranslated = post.categories.map(cat => {
            const categoryKey = wpSlugToCategoryKey[cat] || cat;
            return translations[currentLanguage][categoryKey] ||
                cat.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase());
        });

        // Traduz TODAS as subcategorias também
        let subCategoriesTranslated = [];
        if (post.subCategories && post.subCategories.length > 0) {
            subCategoriesTranslated = post.subCategories.map(subCat => {
                const subCategoryKey = wpSlugToCategoryKey[subCat] || subCat;
                return translations[currentLanguage][subCategoryKey] ||
                    subCat.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase());
            });
        }

        // Pega o título traduzido (se existir no glossário, senão usa o original)
        let postTitle = post.title;
        if (currentLanguage === 'en') {
            // Tenta custom field primeiro, depois glossário
            const translatedTitle = getPostTitleTranslation(post.title, post.titleEN);
            if (translatedTitle) {
                postTitle = translatedTitle;
            } else {
                // Log para facilitar adição de traduções
                console.log(`[TRADUÇÃO] Título não traduzido: "${post.title}" → adicione em postTitleTranslations ou no custom field 'post_title_en'`);
            }
        }

        // Extrai excerpt do conteúdo real do post (primeiras 150 caracteres)
        const excerptText = extractExcerpt(post.content, 150);

        // Renderiza TODAS as categorias e subcategorias
        const categoriesHTML = categoriesTranslated.map(cat => `<span class="post-category">${escapeHtml(cat)}</span>`).join('');
        const subCategoriesHTML = subCategoriesTranslated.map(subCat => `<span class="post-sub-category">${escapeHtml(subCat)}</span>`).join('');

        card.innerHTML = `
            <img src="${escapeHtml(post.image)}" class="post-thumbnail" alt="${escapeHtml(postTitle)}">
            <div class="post-content">
                <div class="post-categories">
                    ${categoriesHTML}
                    ${subCategoriesHTML}
                </div>
                <h3 class="post-title">${escapeHtml(postTitle)}</h3>
                <p class="post-excerpt">${escapeHtml(excerptText)}</p>
                <button class="btn-primary">${verDetalhes}</button>
            </div>
        `;

        // Adiciona evento de clique para redirecionar para o post individual
        card.style.cursor = 'pointer';
        card.addEventListener('click', () => {
            if (post.link) {
                const safeLink = sanitizeUrl(post.link);
                if (safeLink) window.location.href = safeLink;
            }
        });

        grid.appendChild(card);
    });
}

// --- EVENTOS DA PÁGINA DE BLOG ---

// Renderiza botões de filtro por categoria (tag pills) com tradução
function renderTagPills() {
    const suggestedTags = document.querySelector('.suggested-tags');
    if (!suggestedTags) return;

    // Remove os botões antigos (mantém o span "Filtrar por:")
    const existingPills = suggestedTags.querySelectorAll('.tag-pill');
    existingPills.forEach(pill => pill.remove());

    // Define as categorias (use slugs reais retornados pelo WP em `post.category`)
    const categories = [
        { id: '', label: 'todos' },
        { id: 'design-grafico', label: 'design-grafico' },
        { id: 'edicao-de-video', label: 'edicao-video' },
        { id: 'motion', label: 'motion' },
        { id: 'ilustracao-digital', label: 'ilustracao' },
        { id: 'impressos', label: 'impressos' }
    ];

    // Cria os botões
    categories.forEach(cat => {
        const btn = document.createElement('button');
        btn.className = 'tag-pill';
        btn.dataset.tag = cat.id;

        // Pega a tradução
        const translation = translations[currentLanguage][cat.label] || cat.label;
        btn.textContent = translation;

        suggestedTags.appendChild(btn);

        // Adiciona listener para filtro
        btn.addEventListener('click', () => {
            // Remove classe ativa de todos
            document.querySelectorAll('.tag-pill').forEach(b => b.style.backgroundColor = '');

            // Ativa o clicado
            btn.style.backgroundColor = 'var(--color-accent)';
            btn.style.color = '#fff';

            // Filtra posts
            renderBlog('', cat.id);
        });
    });
}

const searchInput = document.getElementById('search-input');

// 1. Busca ao digitar COM VALIDAÇÃO
if (searchInput) {
    searchInput.addEventListener('input', (e) => {
        let searchValue = e.target.value;

        // Sanitizar: remover tags HTML e caracteres perigosos
        searchValue = sanitizeSearchInput(searchValue);

        // Atualizar input se foi alterado (remover tags)
        if (searchValue !== e.target.value) {
            e.target.value = searchValue;
        }

        renderBlog(searchValue);
    });
}

// 2. Renderiza tags com tradução ao carregar página
function detectFontAwesomeAndEnableSvgFallback() {
    // Cria um elemento temporário para verificar se o pseudo-elemento ::before do FA está entregando conteúdo
    const temp = document.createElement('i');
    temp.className = 'fas fa-star test-fa';
    temp.style.position = 'absolute';
    temp.style.left = '-9999px';
    document.body.appendChild(temp);
    let faAvailable = false;
    try {
        const content = window.getComputedStyle(temp, '::before').getPropertyValue('content');
        if (content && content !== 'none' && content !== '""') faAvailable = true;
    } catch (e) {
        // Falha ao acessar ::before — assume que não está disponível
        faAvailable = false;
    }
    temp.remove();

    if (!faAvailable) {
        // Mostra as SVGs fallback que inserimos junto com os <i>
        document.querySelectorAll('.package-icon-svg').forEach(svg => {
            svg.style.display = 'inline-flex';
        });
    }
}

/* ==============================================
   INICIALIZAÇÃO: DOMContentLoaded
   Vincula event listeners e renderiza UI inicial
   ============================================== */
document.addEventListener('DOMContentLoaded', () => {
    // ==============================================
    // SEGURANÇA: Event listeners (remover onclick)
    // ==============================================

    // Botões de seleção de idioma (substituir onclick)
    const langPtBtn = document.getElementById('lang-pt');
    const langEnBtn = document.getElementById('lang-en');

    if (langPtBtn) {
        langPtBtn.addEventListener('click', () => selectLanguage('pt'));
    }
    if (langEnBtn) {
        langEnBtn.addEventListener('click', () => selectLanguage('en'));
    }

    // Botão continuar do onboarding (substituir onclick)
    const btnContinuar = document.getElementById('btn-continuar');
    if (btnContinuar) {
        btnContinuar.addEventListener('click', finalizarOnboarding);
    }

    // Renderizar tags
    renderTagPills();
    detectFontAwesomeAndEnableSvgFallback();
});

/* ==============================================
   INICIALIZAÇÃO: Window Load
   Busca posts da API REST e renderiza carrosséis,
   blog e tags. Suporta pré-filtro via ?categoria=
   ============================================== */
window.addEventListener('load', () => {
    console.log('[INIT] Página carregada, buscando posts...');
    fetchRealPosts().then(() => {
        renderTagPills();

        // Verifica se há parâmetro ?categoria= na URL para pré-filtrar
        const urlParams = new URLSearchParams(window.location.search);
        const categoriaParam = urlParams.get('categoria');
        if (categoriaParam) {
            // Ativa visualmente a tag-pill correspondente
            document.querySelectorAll('.tag-pill').forEach(pill => {
                if (pill.dataset.tag === categoriaParam) {
                    pill.style.backgroundColor = 'var(--color-accent)';
                    pill.style.color = '#fff';
                }
            });
            renderBlog('', categoriaParam);
        } else {
            renderBlog();
        }
    });
});