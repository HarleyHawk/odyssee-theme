/**
 * CV Page Admin Data Module
 * Carrega dados do painel admin ao invés de dados hardcoded
 * Funciona em conjunto com cv-page.js
 */

(function() {
    'use strict';

    // Log de inicialização
    console.log('[CV-PAGE-ADMIN] Script iniciado');
    console.log('[CV-PAGE-ADMIN] odysseyCVData disponível:', typeof window.odysseyCVData !== 'undefined');

    // Verificar se dados do admin estão disponíveis
    if (typeof odysseyCVData === 'undefined') {
        console.warn('[CV-PAGE-ADMIN] AVISO: Dados do painel admin não encontrados (odysseyCVData é undefined)');
        console.warn('[CV-PAGE-ADMIN] Verificar se a função odyssee_cv_localize_frontend_data() está sendo chamada');
        return;
    }

    console.log('[CV-PAGE-ADMIN] ✓ Dados do painel admin encontrados');
    console.log('[CV-PAGE-ADMIN] Dados:', odysseyCVData);

    // =====================================================
    // 1. PREENCHER DESCRIÇÃO (INTRO)
    // =====================================================
    function loadDescription() {
        const currentLang = (window.safeStorage && window.safeStorage.getItem('userLang')) || localStorage.getItem('userLang') || 'pt';
        const langKey = currentLang === 'en' ? 'en_us' : 'pt_br';
        
        const descricao = odysseeCVData.descricao?.[langKey] || {};
        
        const intro1 = document.querySelector('[data-key="cv-intro-1"]');
        const intro2 = document.querySelector('[data-key="cv-intro-2"]');
        
        if (intro1 && descricao.intro_1) {
            intro1.textContent = descricao.intro_1;
        }
        
        if (intro2 && descricao.intro_2) {
            intro2.textContent = descricao.intro_2;
        }
    }

    // =====================================================
    // 2. PREENCHER EXPERIÊNCIAS PROFISSIONAIS
    // =====================================================
    function loadExperiences() {
        const currentLang = (window.safeStorage && window.safeStorage.getItem('userLang')) || localStorage.getItem('userLang') || 'pt';
        const descKey = currentLang === 'en' ? 'descricao_en' : 'descricao_pt';
        
        const experiencias = odysseyCVData.experiencias || [];
        const timelineContainer = document.querySelector('.cv-timeline');
        
        if (!timelineContainer || experiencias.length === 0) {
            return;
        }

        // Limpar timeline existente (dados hardcoded)
        // Comentado para manter compatibilidade com dados existentes
        // timelineContainer.innerHTML = '';

        // Se temos dados do admin, substituir
        if (experiencias.length > 0) {
            // Só preencher se houver dados de admin
            const existingItems = timelineContainer.querySelectorAll('.cv-timeline-item');
            existingItems.forEach(item => item.remove());

            experiencias.forEach((exp) => {
                const item = document.createElement('div');
                item.className = 'cv-timeline-item';
                
                let descHTML = '';
                if (exp[descKey]) {
                    const items = exp[descKey].split('\n').filter(line => line.trim());
                    descHTML = '<ul>' + 
                        items.map(line => `<li>${escapeHtml(line.trim())}</li>`).join('') + 
                        '</ul>';
                }

                item.innerHTML = `
                    <div class="cv-year">${escapeHtml(exp.periodo || '')}</div>
                    <div class="cv-content">
                        <h3>${escapeHtml(exp.titulo || '')} - ${escapeHtml(exp.empresa || '')}</h3>
                        ${descHTML}
                    </div>
                `;
                
                timelineContainer.appendChild(item);
            });
        }
    }

    // =====================================================
    // 3. PREENCHER CURSOS
    // =====================================================
    function loadCourses() {
        const currentLang = (window.safeStorage && window.safeStorage.getItem('userLang')) || localStorage.getItem('userLang') || 'pt';
        const descKey = currentLang === 'en' ? 'descricao_en' : 'descricao_pt';
        
        const cursos = odysseyCVData.cursos || [];
        const educationSection = document.querySelector('.cv-education');
        
        if (!educationSection || cursos.length === 0) {
            return;
        }

        const educationList = educationSection.querySelector('.cv-education-list');
        
        if (!educationList) {
            return;
        }

        // Se temos dados do admin, substituir
        if (cursos.length > 0) {
            const existingItems = educationList.querySelectorAll('.cv-education-item');
            existingItems.forEach(item => item.remove());

            cursos.forEach((curso) => {
                const item = document.createElement('div');
                item.className = 'cv-education-item';
                
                let descHTML = '';
                if (curso[descKey]) {
                    descHTML = `<p>${escapeHtml(curso[descKey])}</p>`;
                }

                item.innerHTML = `
                    <div class="cv-education-period">${escapeHtml(curso.data_final || '')}</div>
                    <div class="cv-education-content">
                        <h4>${escapeHtml(curso.nome || '')}</h4>
                        <p class="cv-education-institution">${escapeHtml(curso.instituicao || '')}</p>
                        ${descHTML}
                    </div>
                `;
                
                educationList.appendChild(item);
            });
        }
    }

    // =====================================================
    // 4. PREENCHER HABILIDADES COM ÍCONES
    // =====================================================
    function loadSkills() {
        // Certificar que temos dados
        if (!odysseyCVData || !odysseyCVData.habilidades) {
            console.log('[CV-PAGE-ADMIN] Sem dados de habilidades');
            return;
        }

        const habilidades = odysseyCVData.habilidades;
        
        // Mapear seletores - com compatibilidade para dados retroativos
        const categoriasMapeadas = {
            'criativos': '#creative-software-grid',
            'office': '#office-software-grid',
            'programacao': '#programming-software-grid',
            'softwares': '#programming-software-grid', // compatibilidade retroativa
            'os': '#os-software-grid',
        };

        console.log('[CV-PAGE-ADMIN] Carregando habilidades:', Object.keys(habilidades));

        Object.entries(categoriasMapeadas).forEach(([categoria, selector]) => {
            const container = document.querySelector(selector);
            if (!container) {
                console.log(`[CV-PAGE-ADMIN] Seletor não encontrado: ${selector}`);
                return;
            }

            const items = habilidades[categoria] || [];
            
            console.log(`[CV-PAGE-ADMIN] ${categoria}: ${items.length} itens`);

            if (items.length > 0) {
                // Limpar apenas os itens de software, mantendo a estrutura
                const existingItems = container.querySelectorAll('.cv-software-item, .cv-os-item');
                console.log(`[CV-PAGE-ADMIN] Removendo ${existingItems.length} itens existentes de ${categoria}`);
                existingItems.forEach(item => item.remove());

                items.forEach((skill, idx) => {
                    const itemClass = categoria === 'os' || categoria === 'softwares' ? 'cv-os-item' : 'cv-software-item';
                    const item = document.createElement('div');
                    item.className = itemClass;
                    
                    let iconHTML = '';
                    if (skill.icon_url) {
                        const isSVG = skill.icon_url.toLowerCase().endsWith('.svg');
                        if (isSVG) {
                            iconHTML = `<object data="${escapeHtml(skill.icon_url)}" type="image/svg+xml" class="cv-software-icon"></object>`;
                        } else {
                            iconHTML = `<img src="${escapeHtml(skill.icon_url)}" alt="${escapeHtml(skill.nome || '')}" class="cv-software-icon" />`;
                        }
                    }

                    // Para OS, não mostra o nível; para outros softwares, mostra
                    const nivel = (categoria !== 'os' && categoria !== 'softwares' && skill.nivel) ? ` <span>${escapeHtml(skill.nivel)}</span>` : '';
                    const nome = skill.nome ? escapeHtml(skill.nome) : '';

                    if (categoria === 'os' || categoria === 'softwares') {
                        item.innerHTML = iconHTML;
                    } else {
                        item.innerHTML = `
                            ${iconHTML}
                            <div class="cv-software-info">
                                ${nome ? `<strong>${nome}</strong>` : ''}
                                ${nivel}
                            </div>
                        `;
                    }
                    
                    container.appendChild(item);
                    console.log(`[CV-PAGE-ADMIN] Adicionado ${skill.nome || 'item'} em ${categoria}`);
                });
            }
        });
    }

    // =====================================================
    // 5. UTILIDADES
    // =====================================================

    /**
     * Escapar HTML para evitar XSS
     */
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    /**
     * Função principal para carregar todos os dados
     */
    function loadAllData() {
        console.log('[CV-PAGE-ADMIN] Carregando todos os dados...');
        loadDescription();
        loadExperiences();
        loadCourses();
        loadSkills();
    }

    // =====================================================
    // 6. INICIALIZAÇÃO
    // =====================================================

    // Carregar dados ao DOM estar pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadAllData);
    } else {
        loadAllData();
    }

    // Recarregar descrição quando o idioma muda
    window.addEventListener('storage', (e) => {
        if (e.key === 'userLang') {
            console.log('[CV-PAGE-ADMIN] Idioma mudou, atualizando descrição...');
            loadDescription();
        }
    });

    window.addEventListener('odyssee-storage', (e) => {
        if (e.detail && e.detail.key === 'userLang') {
            console.log('[CV-PAGE-ADMIN] Idioma mudou, atualizando descrição...');
            loadDescription();
        }
    });

})();
