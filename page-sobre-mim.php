<?php
/**
 * Template Page: Sobre Mim - Currículo Web
 * Exibe: hero com foto/contatos, timeline de experiência,
 *        formação, habilidades, softwares e sistemas operacionais.
 * Traduzido via cv-page.js (initCVPage / translateCVPage).
 */

get_header();

// Função para resolver Case Sensitivity dos SVGs no Linux Server
function get_actual_logo_uri($filename)
{
    static $logos_map = null;
    $theme_uri = get_template_directory_uri();
    if ($logos_map === null) {
        $logos_map = [];
        $logos_dir = get_template_directory() . '/assets/images/logos';
        if (is_dir($logos_dir)) {
            $files = scandir($logos_dir);
            if ($files !== false) {
                foreach ($files as $f) {
                    if ($f !== '.' && $f !== '..') {
                        $logos_map[strtolower($f)] = $f;
                    }
                }
            }
        }
    }
    $lower_filename = strtolower($filename);
    if (isset($logos_map[$lower_filename])) {
        return $theme_uri . '/assets/images/logos/' . $logos_map[$lower_filename];
    }
    return $theme_uri . '/assets/images/logos/' . $filename;
}
?>

<main class="cv-page">
    <!-- Hero do curriculo (foto, nome e contatos) -->
    <section class="cv-hero">
        <div class="cv-hero-content">
            <div class="cv-profile-image">
                <img id="profile-image" src="" alt="Renato Harley Paiva">
            </div>
            <div class="cv-hero-text">
                <h1 class="cv-name">Renato "Harley" Paiva</h1>
                <h2 class="cv-title" data-key="cv-title">Designer Gráfico - Ilustrador Digital - Editor de Vídeo</h2>
                <p class="cv-intro" data-key="cv-intro-1">
                    Tenho 24 anos, sou designer gráfico graduado. Atuo principalmente nas áreas de design gráfico,
                    edição de vídeo, ilustração digital e motion design.
                </p>
                <p class="cv-intro" data-key="cv-intro-2">
                    Trabalho bem em equipe, presencialmente ou remotamente. Consigo atender bem a propostas de briefing,
                    lido bem com diferentes tipos de identidade visual e segmentos.
                </p>
                <div class="cv-contact">
                    <a href="tel:+5511963208691" class="cv-contact-item">
                        <i class="fab fa-whatsapp"></i> +55 (11) 96320-8691
                    </a>
                    <a href="https://www.instagram.com/odysseexp/" class="cv-contact-item" target="_blank"
                        rel="noopener noreferrer">
                        <i class="fab fa-instagram"></i> @Odysseexp
                    </a>
                    <a href="https://odysseexp.com" class="cv-contact-item" target="_blank" rel="noopener noreferrer">
                        <i class="fas fa-globe"></i> Odyssee Experience
                    </a>
                    <a href="https://github.com/HarleyHawk?tab=repositories" class="cv-contact-item" target="_blank" rel="noopener noreferrer">
                        <i class="fa fa-github"></i> GitHub
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Experiencia profissional (linha do tempo) -->
    <section class="cv-section cv-experience">
        <h2 class="cv-section-title" data-key="cv-experience-title">Experiência profissional</h2>
        <div class="cv-timeline">
            <div class="cv-timeline-item">
                <div class="cv-year">2016 - 2018</div>
                <div class="cv-content">
                    <h3>Mobile Minecraft</h3>
                    <ul>
                        <li data-key="exp-mm-1">Desenvolvimento e criação de banners para o Blog.</li>
                        <li data-key="exp-mm-2">Redator de matérias e revisor das demais matérias publicadas.</li>
                        <li data-key="exp-mm-3">Auxiliar no desenvolvimento da UX/UI do blog.</li>
                        <li data-key="exp-mm-4">Auxiliar na criação e desenvolvimento da identidade visual do site.</li>
                    </ul>
                </div>
            </div>

            <div class="cv-timeline-item">
                <div class="cv-year">2020 - 2022</div>
                <div class="cv-content">
                    <h3 data-key="exp-fleury-title">Fleury S.A - Recepcionista</h3>
                    <ul>
                        <li data-key="exp-fleury-1">Atendimento e abertura de ficha de exames médicos.</li>
                        <li data-key="exp-fleury-2">Atendimento à empresas e convênios credenciados.</li>
                        <li data-key="exp-fleury-3">Treinador de novos trabalhadores para a recepção e abertura de
                            ficha.</li>
                        <li data-key="exp-fleury-4">Armazenador de arquivos da unidade de atendimento.</li>
                    </ul>
                </div>
            </div>

            <div class="cv-timeline-item">
                <div class="cv-year">2023 - 2023</div>
                <div class="cv-content">
                    <h3 data-key="exp-anymous-title">ANYMOUS - Estagiário em Design Gráfico</h3>
                    <ul>
                        <li data-key="exp-anymous-1">Criação e elaboração de posts e carroséis para instagram.</li>
                        <li data-key="exp-anymous-2">Criação e elaboração de reels para instagram.</li>
                        <li data-key="exp-anymous-3">Redator e tratar fotos de produtos para marketplace.</li>
                        <li data-key="exp-anymous-4">UX/UI do site. Gestão de ícones, símbolos e acessibilidade da
                            página.</li>
                    </ul>
                </div>
            </div>

            <div class="cv-timeline-item">
                <div class="cv-year">2023 - 2023</div>
                <div class="cv-content">
                    <h3 data-key="exp-divterm-title">Div Term Tecnomoldura - Corte de letras-caixa</h3>
                    <ul>
                        <li data-key="exp-divterm-1">Gerar arquivos escalonados e compatíveis para recorte em máquina
                            laser, fio quente e MDF.</li>
                        <li data-key="exp-divterm-2">Elaborar aproveitamento de material em software para recorte em
                            máquinas.</li>
                        <li data-key="exp-divterm-3">Operação de corte laser e de corte a fio quente.</li>
                        <li data-key="exp-divterm-4">Elaborar tipografias e adapta-las para corte em máquinas de acordo
                            com suas limitações.</li>
                    </ul>
                </div>
            </div>

            <div class="cv-timeline-item">
                <div class="cv-year">2024 - 2024</div>
                <div class="cv-content">
                    <h3 data-key="exp-first-title">First Publicidade - Designer Gráfico</h3>
                    <ul>
                        <li data-key="exp-first-1">Criação e elaboração de posts e carroséis para instagram de diversas
                            identidades visuais.</li>
                        <li data-key="exp-first-2">Criação de posts para as campanhas políticas de candidatos vereadores
                            e prefeitos do estado de São Paulo.</li>
                        <li data-key="exp-first-3">Branding de posts trens e interativos para redes sociais.</li>
                    </ul>
                </div>
            </div>

            <div class="cv-timeline-item">
                <div class="cv-year">2025 - 2026</div>
                <div class="cv-content">
                    <h3 data-key="exp-percons-title">Percons - Designer Gráfico Jr.</h3>
                    <ul>
                        <li data-key="exp-percons-1">Criação de artes para posts em redes sociais.</li>
                        <li data-key="exp-percons-2">Redigir documentos internos com a identidade visual da empresa.
                        </li>
                        <li data-key="exp-percons-3">Criar e fechar arquivos para impressões.</li>
                        <li data-key="exp-percons-4">Elaborar e criar identidades visuais e logos para marcas.</li>
                        <li data-key="exp-percons-5">Design de apresentações empresariais.</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Formacao e cursos -->
    <section class="cv-section cv-education">
        <h2 class="cv-section-title" data-key="cv-education-title">Formação e cursos</h2>
        <div class="cv-education-grid">
            <div class="cv-education-item">
                <div class="cv-year">2015 - 2017</div>
                <h3 data-key="edu-caution-title">Curso - Caution Pontocom</h3>
                <ul>
                    <li data-key="edu-caution-1">Curso de informática e inglês</li>
                    <li data-key="edu-caution-2">194h/aula</li>
                </ul>
            </div>
            <div class="cv-education-item">
                <div class="cv-year">2022 - 2024</div>
                <h3 data-key="edu-unicid-title">Faculdade - UNICID</h3>
                <ul>
                    <li data-key="edu-unicid-1">CST em Design Gráfico</li>
                    <li data-key="edu-unicid-2">4 semestres</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Idiomas e habilidades -->
    <section class="cv-section cv-skills">
        <h2 class="cv-section-title" data-key="cv-skills-title">Idiomas e outras habilidades</h2>

        <div class="cv-skills-grid">
            <div class="cv-skill-category">
                <h3 data-key="lang-reading-title">Idiomas - Escrita e Leitura</h3>
                <ul>
                    <li data-key="lang-pt-native">Português Brasileiro - Nativo</li>
                    <li data-key="lang-en-advanced">Inglês - Avançado</li>
                    <li data-key="lang-de-basic">Alemão - Básico</li>
                </ul>
            </div>

            <div class="cv-skill-category">
                <h3 data-key="lang-speaking-title">Idiomas - Comunicação Verbal</h3>
                <ul>
                    <li data-key="lang-pt-native-speaking">Português Brasileiro - Nativo</li>
                    <li data-key="lang-en-intermediate">Inglês - Intermediário</li>
                    <li data-key="lang-de-basic-speaking">Alemão - Básico</li>
                </ul>
            </div>
        </div>

        <div class="cv-skill-text">
            <h3 data-key="skill-communication-title">Comunicação inclusiva e acessível</h3>
            <p data-key="skill-communication-desc">Me comunico de acordo com o perfil de cada pessoa, uso linguajar de
                uma forma que ela possa entender a mensagem que quero passar e se sentir confortável em se expressar a
                forma como ela faz normalmente.</p>

            <h3 data-key="skill-tech-title">Afinidade tecnológica</h3>
            <p data-key="skill-tech-desc">Isso é algo bem nítido logo abaixo com os softwares e sistemas que eu uso
                frequentemente. Mas, possuo uma habilidade muito rápida de aprender novos softwares, interfaces, códigos
                e sistemas operacionais. Mesmo que eu não saiba determinada ferramenta, eu consigo aprender muito
                rapidamente. Uma afinidade com tecnologia eu diria.</p>
        </div>

        <h3 class="cv-software-title" data-key="software-skills-title">Habilidades em Softwares</h3>

        <div class="cv-software-section">
            <h4 data-key="software-creative-title">Softwares de Criativos</h4>
            <div class="cv-software-grid" id="creative-software-grid">
                <div class="cv-software-item">
                    <img src="<?php echo get_actual_logo_uri('adobe_photoshop_cc_icon.svg'); ?>" alt="Photoshop">
                    <span>Avançado</span>
                </div>
                <div class="cv-software-item">
                    <img src="<?php echo get_actual_logo_uri('adobe_illustrator_cc_icon.svg'); ?>" alt="Illustrator">
                    <span>Avançado</span>
                </div>
                <div class="cv-software-item">
                    <img src="<?php echo get_actual_logo_uri('adobe_indesign_cc_2026_icon.svg'); ?>" alt="InDesign">
                    <span>Intermediário</span>
                </div>
                <div class="cv-software-item">
                    <img src="<?php echo get_actual_logo_uri('adobe_photoshop_lightroom_cc_logo.svg'); ?>"
                        alt="Lightroom">
                    <span>Intermediário</span>
                </div>
                <div class="cv-software-item">
                    <img src="<?php echo get_actual_logo_uri('adobe_premiere_pro_cc_2026_icon.svg'); ?>" alt="Premiere">
                    <span>Avançado</span>
                </div>
                <div class="cv-software-item">
                    <img src="<?php echo get_actual_logo_uri('adobe_after_effects_cc_2026_icon.svg'); ?>"
                        alt="After Effects">
                    <span>Intermediário</span>
                </div>
                <div class="cv-software-item">
                    <img src="<?php echo get_actual_logo_uri('blender_logo.svg'); ?>" alt="Blender">
                    <span>Básico</span>
                </div>
                <div class="cv-software-item">
                    <img src="<?php echo get_actual_logo_uri('affinity_(app)_logo.svg'); ?>" alt="Affinity">
                    <span>Avançado</span>
                </div>
                <div class="cv-software-item">
                    <img src="<?php echo get_actual_logo_uri('gimp.svg'); ?>" alt="GIMP">
                    <span>Avançado</span>
                </div>
                <div class="cv-software-item">
                    <img src="<?php echo get_actual_logo_uri('davinci_resolve.png'); ?>" alt="DaVinci Resolve">
                    <span>Intermediário</span>
                </div>
                <div class="cv-software-item">
                    <img src="<?php echo get_actual_logo_uri('inkscape.svg'); ?>" alt="Inkscape">
                    <span>Avançado</span>
                </div>
                <div class="cv-software-item">
                    <img src="<?php echo get_actual_logo_uri('krita.svg'); ?>" alt="Krita">
                    <span>Intermediário</span>
                </div>
                <div class="cv-software-item">
                    <img src="<?php echo get_actual_logo_uri('csp.png'); ?>" alt="Clip Studio Paint">
                    <span>Intermediário</span>
                </div>
                <div class="cv-software-item">
                    <img src="<?php echo get_actual_logo_uri('darktable.svg'); ?>" alt="Darktable">
                    <span>Básico</span>
                </div>
                <div class="cv-software-item">
                    <img src="<?php echo get_actual_logo_uri('capcut.svg'); ?>" alt="CapCut">
                    <span>Intermediário</span>
                </div>
                <div class="cv-software-item">
                    <img src="<?php echo get_actual_logo_uri('photopea.svg'); ?>" alt="Photopea">
                    <span>Avançado</span>
                </div>
                <div class="cv-software-item">
                    <img src="<?php echo get_actual_logo_uri('clipchamp.svg'); ?>" alt="Clipchamp">
                    <span>Intermediário</span>
                </div>
            </div>
        </div>

        <div class="cv-software-section">
            <h4 data-key="software-office-title">Softwares de Escritório</h4>
            <div class="cv-software-grid" id="office-software-grid">
                <div class="cv-software-item">
                    <img src="<?php echo get_actual_logo_uri('microsoft_office_word_(2025–present).svg'); ?>"
                        alt="Word">
                    <span>Avançado</span>
                </div>
                <div class="cv-software-item">
                    <img src="<?php echo get_actual_logo_uri('microsoft_office_excel_(2025–present).svg'); ?>"
                        alt="Excel">
                    <span>Básico</span>
                </div>
                <div class="cv-software-item">
                    <img src="<?php echo get_actual_logo_uri('microsoft_office_powerpoint_(2025–present).svg'); ?>"
                        alt="PowerPoint">
                    <span>Avançado</span>
                </div>
                <div class="cv-software-item">
                    <img src="<?php echo get_actual_logo_uri('libreoffice_7.5_writer_icon.svg'); ?>"
                        alt="LibreOffice Writer">
                    <span>Básico</span>
                </div>
                <div class="cv-software-item">
                    <img src="<?php echo get_actual_logo_uri('libreoffice_7.5_impress_icon.svg'); ?>"
                        alt="LibreOffice Impress">
                    <span>Avançado</span>
                </div>
                <div class="cv-software-item">
                    <img src="<?php echo get_actual_logo_uri('libreoffice_7.5_calc_icon.svg'); ?>"
                        alt="LibreOffice Calc">
                    <span>Básico</span>
                </div>
                <div class="cv-software-item">
                    <img src="<?php echo get_actual_logo_uri('libreoffice_7.5_draw_icon.svg'); ?>"
                        alt="LibreOffice Draw">
                    <span>Intermediário</span>
                </div>
            </div>
        </div>

        <div class="cv-software-section">
            <h4 data-key="software-programming-title">Programação e suas linguagens</h4>
            <div class="cv-software-grid" id="programming-software-grid">
                <div class="cv-software-item">
                    <img src="<?php echo get_actual_logo_uri('visual_studio_code.svg'); ?>" alt="VS Code">
                    <span>Básico</span>
                </div>
                <div class="cv-software-item">
                    <img src="<?php echo get_actual_logo_uri('html5_logo_and_wordmark.svg'); ?>" alt="HTML5">
                    <span>Básico</span>
                </div>
                <div class="cv-software-item">
                    <img src="<?php echo get_actual_logo_uri('css3_logo_and_wordmark.svg'); ?>" alt="CSS3">
                    <span>Básico</span>
                </div>
                <div class="cv-software-item">
                    <img src="<?php echo get_actual_logo_uri('javascript-shield.svg'); ?>" alt="JavaScript">
                    <span>Básico</span>
                </div>
            </div>
        </div>

        <div class="cv-software-section">
            <h4 data-key="software-os-title">Sistemas Operacionais</h4>
            <div class="cv-os-grid" id="os-software-grid">
                <div class="cv-os-item">
                    <img src="<?php echo get_actual_logo_uri('windows_11.svg'); ?>" alt="Windows">
                </div>
                <div class="cv-os-item">
                    <img src="<?php echo get_actual_logo_uri('fedora-logo.svg'); ?>" alt="Fedora">
                </div>
                <div class="cv-os-item">
                    <img src="<?php echo get_actual_logo_uri('arch_linux-logo.svg'); ?>" alt="Arch">
                </div>
                <div class="cv-os-item">
                    <img src="<?php echo get_actual_logo_uri('linux_mint.svg'); ?>" alt="Linux Mint">
                </div>
                <div class="cv-os-item">
                    <img src="<?php echo get_actual_logo_uri('steam_os.svg'); ?>" alt="Steam OS">
                </div>
                <div class="cv-os-item">
                    <img src="<?php echo get_actual_logo_uri('bazzite.svg'); ?>" alt="Bazzite">
                </div>
                <div class="cv-os-item">
                    <img src="<?php echo get_actual_logo_uri('zorin_os.svg'); ?>" alt="Zorin">
                </div>
                <div class="cv-os-item">
                    <img src="<?php echo get_actual_logo_uri('pop_os.svg'); ?>" alt="Pop! OS">
                </div>
            </div>
        </div>
    </section>

    <!-- Citação final -->
    <div class="cv-quote">
        <p data-key="cv-quote">"Intelligence is the ability to avoid doing work, yet getting the work done."</p>
        <span data-key="cv-quote-author">~Linus Torvalds</span>
    </div>
</main>

<!-- Script dedicado da pagina de CV (tema/imagem/traducao) -->
<script src="<?php echo esc_url(get_template_directory_uri() . '/assets/js/cv-page.js'); ?>" defer></script>

<!-- Script para carregar dados do painel admin -->
<script src="<?php echo esc_url(get_template_directory_uri() . '/assets/js/cv-page-admin-data.js'); ?>" defer></script>

<?php get_footer(); ?>