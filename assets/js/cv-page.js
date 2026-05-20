/**
 * CV Page Module - Gerencia tradução e tema da página Sobre Mim
 * Carregado apenas nas páginas: page-sobre-mim.php
 */

// Mapeamento de imagens por tema (light/dark) e cor
const profileImages = {
    'light-blue': 'https://odysseexp.com/wp-content/uploads/2025/11/harley-the-sky.png',
    'light-purple': 'https://odysseexp.com/wp-content/uploads/2025/11/harley-purpur.png',
    'light-red': 'https://odysseexp.com/wp-content/uploads/2025/11/harley-crimson.png',
    'light-yellow': 'https://odysseexp.com/wp-content/uploads/2025/11/harley-oak.png',
    'light-green': 'https://odysseexp.com/wp-content/uploads/2025/11/harley-greenfroglight.png',
    'dark-blue': 'https://odysseexp.com/wp-content/uploads/2025/11/harley-nightsky.png',
    'dark-purple': 'https://odysseexp.com/wp-content/uploads/2025/11/harley-netherportal.png',
    'dark-red': 'https://odysseexp.com/wp-content/uploads/2025/11/harley-redstone.png',
    'dark-yellow': 'https://odysseexp.com/wp-content/uploads/2025/11/harley-honeycomb.png',
    'dark-green': 'https://odysseexp.com/wp-content/uploads/2025/11/harley-grass-alpha.png'
};

/**
 * Atualiza a imagem de perfil baseada no tema e cor atual
 */
function updateProfileImage() {
    const body = document.body;
    const profileImg = document.getElementById('profile-image');
    
    if (!profileImg) return;
    
    // Detectar tema (light ou dark)
    let theme = 'light';
    if (body.classList.contains('theme-dark')) {
        theme = 'dark';
    }
    
    // Detectar cor
    let color = 'blue'; // padrão
    const colors = ['blue', 'purple', 'red', 'yellow', 'green'];
    for (const c of colors) {
        if (body.classList.contains(`color-${c}`)) {
            color = c;
            break;
        }
    }
    
    // Combinar tema + cor
    const key = `${theme}-${color}`;
    
    // Atualizar imagem
    if (profileImages[key]) {
        profileImg.src = profileImages[key];
    }
}

// Traduções completas da página Sobre Mim (PT-BR e EN-US)
// Chaves correspondem aos atributos data-key dos elementos HTML
const cvTranslations = {
    pt: {
        'cv-title': 'Designer Gráfico - Ilustrador Digital - Editor de Vídeo',
        'profile-image-alt': 'Renato "Harley" Paiva',
        'cv-intro-1': 'Tenho 24 anos, sou designer gráfico graduado. Atuo principalmente nas áreas de design gráfico, edição de vídeo, ilustração digital e motion design.',
        'cv-intro-2': 'Trabalho bem em equipe, presencialmente ou remotamente. Consigo atender bem a propostas de briefing, lido bem com diferentes tipos de identidade visual e segmentos.',
        
        // Experiência
        'cv-experience-title': 'Experiência profissional',
        'exp-mm-1': 'Desenvolvimento e criação de banners para o Blog.',
        'exp-mm-2': 'Redator de matérias e revisor das demais matérias publicadas.',
        'exp-mm-3': 'Auxiliar no desenvolvimento da UX/UI do blog.',
        'exp-mm-4': 'Auxiliar na criação e desenvolvimento da identidade visual do site.',
        
        'exp-fleury-title': 'Fleury S.A - Recepcionista',
        'exp-fleury-1': 'Atendimento e abertura de ficha de exames médicos.',
        'exp-fleury-2': 'Atendimento à empresas e convênios credenciados.',
        'exp-fleury-3': 'Treinador de novos trabalhadores para a recepção e abertura de ficha.',
        'exp-fleury-4': 'Armazenador de arquivos da unidade de atendimento.',
        
        'exp-anymous-title': 'ANYMOUS - Estagiário em Design Gráfico',
        'exp-anymous-1': 'Criação e elaboração de posts e carroséis para instagram.',
        'exp-anymous-2': 'Criação e elaboração de reels para instagram.',
        'exp-anymous-3': 'Redator e tratar fotos de produtos para marketplace.',
        'exp-anymous-4': 'UX/UI do site. Gestão de ícones, símbolos e acessibilidade da página.',
        
        'exp-divterm-title': 'Div Term Tecnomoldura - Corte de letras-caixa',
        'exp-divterm-1': 'Gerar arquivos escalonados e compatíveis para recorte em máquina laser, fio quente e MDF.',
        'exp-divterm-2': 'Elaborar aproveitamento de material em software para recorte em máquinas.',
        'exp-divterm-3': 'Operação de corte laser e de corte a fio quente.',
        'exp-divterm-4': 'Elaborar tipografias e adapta-las para corte em maquinarias de acordo com suas limitações.',
        
        'exp-first-title': 'First Publicidade - Designer Gráfico',
        'exp-first-1': 'Criação e elaboração de posts e carroséis para instagram de diversas identidades visuais.',
        'exp-first-2': 'Criação de posts para as campanhas políticas de candidatos vereadores e prefeitos do estado de São Paulo.',
        'exp-first-3': 'Branding de posts trens e interativos para redes sociais.',
        
        'exp-percons-title': 'Percons - Designer Gráfico Jr.',
        'exp-percons-1': 'Criação de artes para posts em redes sociais.',
        'exp-percons-2': 'Edição de vídeos curtos e reels animados',
        'exp-percons-3': 'Criar e fechar arquivos para impressões',
        'exp-percons-4': 'Elaborar e criar identidades visuais e logos para marcas',

        // Formação
        'cv-education-title': 'Formação e cursos',
        'edu-caution-title': 'Curso - Caution Pontocom',
        'edu-caution-1': 'Curso de informática e inglês',
        'edu-caution-2': '194h/aula',
        'edu-unicid-title': 'Faculdade - UNICID',
        'edu-unicid-1': 'CST em Design Gráfico',
        'edu-unicid-2': '4 semestres',
        
        // Habilidades
        'cv-skills-title': 'Idiomas e outras habilidades',
        'lang-reading-title': 'Idiomas - Escrita e Leitura',
        'lang-pt-native': 'Português Brasileiro - Nativo',
        'lang-en-advanced': 'Inglês - Avançado',
        'lang-de-basic': 'Alemão - Básico',
        'lang-speaking-title': 'Idiomas - Comunicação Verbal',
        'lang-pt-native-speaking': 'Português Brasileiro - Nativo',
        'lang-en-intermediate': 'Inglês - Intermediário',
        'lang-de-basic-speaking': 'Alemão - Básico',
        
        'skill-communication-title': 'Comunicação inclusiva e acessível',
        'skill-communication-desc': 'Me comunico de acordo com o perfil de cada pessoa, uso linguajar de uma forma que ela possa entender a mensagem que quero passar e se sentir confortável em se expressar a forma como ela faz normalmente.',
        'skill-tech-title': 'Afinidade tecnológica',
        'skill-tech-desc': 'Isso é algo bem nítido logo abaixo com os softwares e sistemas que eu uso frequentemente. Mas, possuo uma habilidade muito rápida de aprender novos softwares, interfaces, códigos e sistemas operacionais. Mesmo que eu não saiba determinada ferramenta, eu consigo aprender muito rapidamente. Uma afinidade com tecnologia eu diria.',
        
        'software-skills-title': 'Habilidades em Softwares',
        'software-creative-title': 'Softwares de Criativos',
        'software-office-title': 'Softwares de Escritório',
        'software-programming-title': 'Programação e suas linguagens',
        'software-os-title': 'Sistemas Operacionais',
        
        // Níveis
        'level-advanced': 'Avançado',
        'level-intermediate': 'Intermediário',
        'level-basic': 'Básico',
        // Citação de rodapé
        'cv-quote': '"Intelligence is the ability to avoid doing work, yet getting the work done."',
        'cv-quote-author': '~Linus Torvalds'
    },
    en: {
        'cv-title': 'Graphic Designer - Digital Illustrator - Video Editor',
        'profile-image-alt': 'Portrait of Renato Harley Paiva',
        'cv-intro-1': "I'm 24 years old, a graduate graphic designer. I work mainly in the areas of graphic design, video editing, digital illustration and motion design.",
        'cv-intro-2': 'I work well in teams, on-site or remotely. I can handle briefing proposals well, and deal with different types of visual identity and segments.',
        
        // Experience
        'cv-experience-title': 'Professional Experience',
        'exp-mm-1': 'Development and creation of banners for the Blog.',
        'exp-mm-2': 'Writer of articles and reviewer of other published articles.',
        'exp-mm-3': 'Assist in the development of the blog UX/UI.',
        'exp-mm-4': 'Assist in the creation and development of the site visual identity.',
        
        'exp-fleury-title': 'Fleury S.A - Receptionist',
        'exp-fleury-1': 'Customer service and opening of medical exam records.',
        'exp-fleury-2': 'Service to accredited companies and health plans.',
        'exp-fleury-3': 'Trainer of new workers for reception and record opening.',
        'exp-fleury-4': 'Storage of service unit files.',
        
        'exp-anymous-title': 'ANYMOUS - Graphic Design Intern',
        'exp-anymous-1': 'Creation and development of posts and carousels for Instagram.',
        'exp-anymous-2': 'Creation and development of reels for Instagram.',
        'exp-anymous-3': 'Writer and product photo editor for marketplace.',
        'exp-anymous-4': 'Website UX/UI. Management of icons, symbols and page accessibility.',
        
        'exp-divterm-title': 'Div Term Tecnomoldura - Box Letter Cutting',
        'exp-divterm-1': 'Generate scaled and compatible files for laser cutting, hot-wire cutting and MDF.',
        'exp-divterm-2': 'Develop material optimization in software for machine cutting.',
        'exp-divterm-3': 'Laser cutting and hot wire cutting operation.',
        'exp-divterm-4': 'Develop typography and adapt it for cutting in machinery according to its limitations.',
        
        'exp-first-title': 'First Publicidade - Graphic Designer',
        'exp-first-1': 'Creation and development of posts and carousels for Instagram with various visual identities.',
        'exp-first-2': 'Creation of posts for political campaigns of city council and mayor candidates in São Paulo state.',
        'exp-first-3': 'Branding of trendy and interactive posts for social media.',
        
        'exp-percons-title': 'Percons - Junior Graphic Designer',
        'exp-percons-1': 'Creating artwork for social media posts.',
        'exp-percons-2': 'Editing short videos and animated reels',
        'exp-percons-3': 'Preparing print-ready files',
        'exp-percons-4': 'Designing visual identities and logos for brands',

        // Education
        'cv-education-title': 'Education and Courses',
        'edu-caution-title': 'Course - Caution Pontocom',
        'edu-caution-1': 'Computer science and English course',
        'edu-caution-2': '194 hours',
        'edu-unicid-title': 'College - UNICID',
        'edu-unicid-1': 'Associate Degree in Graphic Design',
        'edu-unicid-2': '4 semesters',
        
        // Skills
        'cv-skills-title': 'Languages and Other Skills',
        'lang-reading-title': 'Languages - Writing and Reading',
        'lang-pt-native': 'Brazilian Portuguese - Native',
        'lang-en-advanced': 'English - Advanced',
        'lang-de-basic': 'German - Basic',
        'lang-speaking-title': 'Languages - Verbal Communication',
        'lang-pt-native-speaking': 'Brazilian Portuguese - Native',
        'lang-en-intermediate': 'English - Intermediate',
        'lang-de-basic-speaking': 'German - Basic',
        
        'skill-communication-title': 'Inclusive and Accessible Communication',
        'skill-communication-desc': 'I communicate according to each person\'s profile, using language in a way that they can understand the message I want to convey and feel comfortable expressing themselves the way they normally do.',
        'skill-tech-title': 'Tech Affinity',
        'skill-tech-desc': 'This is quite clear below with the software and systems I use frequently. However, I have a very fast ability to learn new software, interfaces, code and operating systems. Even if I don\'t know a particular tool, I can learn very quickly. A tech affinity, I would say.',
        
        'software-skills-title': 'Software Skills',
        'software-creative-title': 'Creative Software',
        'software-office-title': 'Office Software',
        'software-programming-title': 'Programming and Languages',
        'software-os-title': 'Operating Systems',
        
        // Levels
        'level-advanced': 'Advanced',
        'level-intermediate': 'Intermediate',
        'level-basic': 'Basic',
        // Footer quote
        'cv-quote': '"Intelligence is the ability to avoid doing work, yet getting the work done."',
        'cv-quote-author': '~Linus Torvalds'
    }
};

/**
 * Traduz todos os elementos da página CV que possuem data-key.
 * Também traduz os níveis de habilidade em software
 * (Avançado ↔ Advanced, Intermediário ↔ Intermediate, Básico ↔ Basic)
 * e atualiza o atributo alt da imagem de perfil para acessibilidade.
 */
function translateCVPage() {
    // Lê o idioma TODA VEZ (não usa variável global)
    const currentLang = (window.safeStorage && window.safeStorage.getItem('userLang')) || localStorage.getItem('userLang') || 'pt';
    
    console.log('[CV-PAGE] Traduzindo para idioma:', currentLang);
    
    document.querySelectorAll('[data-key]').forEach(element => {
        const key = element.getAttribute('data-key');
        if (cvTranslations[currentLang] && cvTranslations[currentLang][key]) {
            element.textContent = cvTranslations[currentLang][key];
        }
    });

    // Atualiza alt da imagem de perfil (acessibilidade)
    const profileImg = document.getElementById('profile-image');
    if (profileImg && cvTranslations[currentLang] && cvTranslations[currentLang]['profile-image-alt']) {
        profileImg.alt = cvTranslations[currentLang]['profile-image-alt'];
    }
    
    // Traduz os niveis de habilidade (Avancado, Intermediario, Basico)
    document.querySelectorAll('.cv-software-item span').forEach(span => {
        const text = span.textContent.trim();
        if (text === 'Avançado' || text === 'Advanced') {
            span.textContent = cvTranslations[currentLang]['level-advanced'];
        } else if (text === 'Intermediário' || text === 'Intermediate') {
            span.textContent = cvTranslations[currentLang]['level-intermediate'];
        } else if (text === 'Básico' || text === 'Basic') {
            span.textContent = cvTranslations[currentLang]['level-basic'];
        }
    });
}

/**
 * Inicializa a página CV:
 * 1. Atualiza foto de perfil conforme tema/cor
 * 2. Traduz textos para o idioma salvo
 * 3. Escuta eventos de mudança de idioma (storage e custom event)
 * 4. Observa mutações de classe no body para sincronizar a foto
 */
function initCVPage() {
    console.log('[CV-PAGE] Inicializando página...');
    updateProfileImage();
    translateCVPage();
    
    // Atualiza traducao quando idioma muda em outra aba
    window.addEventListener('storage', (e) => {
        if (e.key === 'userLang') {
            console.log('[CV-PAGE] storage event - traduzindo');
            translateCVPage();
        }
    });

    // Atualiza traducao quando app.js dispara evento local
    window.addEventListener('odyssee-storage', (e) => {
        if (e.detail && e.detail.key === 'userLang') {
            console.log('[CV-PAGE] odyssee-storage event - traduzindo');
            translateCVPage();
        }
    });

    // Sincroniza imagem quando o tema/cor mudar
    const observerTheme = new MutationObserver(updateProfileImage);
    observerTheme.observe(document.body, { 
        attributes: true, 
        attributeFilter: ['class'] 
    });
}

// Executa ao carregar o DOM
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCVPage);
} else {
    // Se DOM já carregou, executa imediatamente
    initCVPage();
}
