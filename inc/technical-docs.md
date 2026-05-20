# Documentação Técnica - Painel CV Admin

## 📁 Arquivos Criados

```
inc/
  ├── cv-admin.php              # Painel admin principal + lógica PHP
  ├── cv-admin.css              # Estilos do painel
  ├── cv-admin.js               # JavaScript do painel (repeaters, uploads)
  ├── CV-ADMIN-README.md        # Documentação completa em português
  
assets/js/
  ├── cv-page.js                # [EXISTENTE] Tradução + tema
  ├── cv-page-admin-data.js     # [NOVO] Carrega dados do admin no frontend
  
page-sobre-mim.php             # [MODIFICADO] Adicionado script cv-page-admin-data.js
functions.php                   # [MODIFICADO] Adicionado require do cv-admin.php
GUIA-PAINEL-CV.md              # Guia de uso rápido do painel
```

---

## 🏗️ Arquitetura

### Backend (WordPress Admin)

1. **cv-admin.php** - Funções PHP do painel:
   - `odyssee_cv_add_admin_menu()` - Registra o menu
   - `odyssee_cv_admin_page()` - Renderiza o painel (HTML com abas)
   - `odyssee_cv_get_data()` - Recupera dados da opção
   - `odyssee_cv_save_data()` - Sanitiza e salva dados
   - `odyssee_cv_sanitize_repeater()` - Valida experiências/cursos
   - `odyssee_cv_sanitize_habilidades()` - Valida habilidades
   - `odyssee_cv_ajax_upload_icon()` - AJAX para upload de imagens
   - `odyssee_cv_localize_frontend_data()` - Injeta dados na página

2. **cv-admin.css** - Estilos:
   - Layout em abas (Tab Navigation)
   - Repeater fields (acordeãos)
   - Media upload widget
   - Responsividade

3. **cv-admin.js** - Interatividade:
   - Gerenciar abas
   - Adicionar/remover repeater items
   - Upload de ícones via media uploader
   - Atualização dinâmica de índices

### Frontend (Página Pública)

1. **page-sobre-mim.php**:
   - Template HTML com placeholders
   - Carrega `cv-page.js` (tradução)
   - Carrega `cv-page-admin-data.js` (dados do admin)

2. **cv-page.js** (existente):
   - Sistema de tradução PT-BR ↔ EN-US
   - Traduz elementos com `data-key`
   - Sincroniza tema/cor da imagem de perfil

3. **cv-page-admin-data.js** (novo):
   - Verifica if `odysseeCVData` está disponível
   - Se sim, popula descrição, experiências, cursos, habilidades
   - Renderiza ícones (SVG/IMG)
   - Atualiza descrição quando idioma muda

---

## 💾 Estrutura de Dados

### Local de Armazenamento
- **Tabela:** `wp_options`
- **Chave:** `odyssee_cv_data`
- **Formato:** JSON serializado

### Schema

```json
{
  "descricao": {
    "pt_br": {
      "intro_1": "string",
      "intro_2": "string"
    },
    "en_us": {
      "intro_1": "string",
      "intro_2": "string"
    }
  },
  
  "experiencias": [
    {
      "titulo": "string",
      "empresa": "string",
      "periodo": "string",
      "descricao_pt": "string (suporta quebras de linha)",
      "descricao_en": "string (suporta quebras de linha)"
    }
  ],
  
  "cursos": [
    {
      "nome": "string",
      "instituicao": "string",
      "data_final": "string",
      "descricao_pt": "string (opcional)",
      "descricao_en": "string (opcional)"
    }
  ],
  
  "habilidades": {
    "softwares": [
      {
        "nome": "string (opcional)",
        "nivel": "iniciante|intermediário|avançado|expert",
        "icon_url": "string (URL da imagem)",
        "icon_id": "integer (ID do attachment)"
      }
    ],
    "criativos": [...],
    "office": [...],
    "os": [...]
  }
}
```

---

## 🔄 Fluxo de Dados

### Salvamento (Backend)

```
User edita painel
    ↓
Submit form @admin.php?page=odyssee-cv-manager
    ↓
Verifica nonce ✓
    ↓
odyssee_cv_save_data()
    ├─ odyssee_cv_sanitize_repeater() (experiências/cursos)
    ├─ odyssee_cv_sanitize_habilidades() (habilidades)
    └─ update_option('odyssee_cv_data', $data)
    ↓
Success notice ✓
```

### Carregamento (Frontend)

```
GET /page-sobre-mim/
    ↓
page-sobre-mim.php
    ├─ odyssee_cv_localize_frontend_data() (em wp_head)
    │   └─ Injeta window.odysseeCVData = {...}
    │
    ├─ load cv-page.js (defer)
    │   └─ Inicializa tradução + tema
    │
    └─ load cv-page-admin-data.js (defer)
        ├─ Verifica odysseeCVData
        ├─ loadDescription()
        ├─ loadExperiences()
        ├─ loadCourses()
        └─ loadSkills()
```

---

## 🔐 Segurança Implementada

| Aspecto | Implementação |
|---------|---|
| **Acesso** | Apenas `manage_options` |
| **CSRF** | WordPress nonce field |
| **Sanitização** | `sanitize_text_field()`, `sanitize_textarea_field()` |
| **URLs** | `esc_url_raw()`, `esc_url()` |
| **HTML** | `wp_kses_post()` em templates |
| **Upload** | Whitelist de tipos (SVG, PNG, JPEG) |
| **AJAX** | `check_ajax_referer()` obrigatório |

---

## 📞 Funções PHP Públicas

### Para usar em templates ou plugins:

```php
// Obter todos os dados do CV
$cv_data = odyssee_cv_get_data();

// Acessar descrição PT-BR
echo $cv_data['descricao']['pt_br']['intro_1'];

// Iterar experiências
foreach ($cv_data['experiencias'] as $exp) {
    echo $exp['titulo'];
    echo $exp['empresa'];
    echo $exp['periodo'];
}

// Acessar habilidades
$softwares = $cv_data['habilidades']['softwares'];
foreach ($softwares as $skill) {
    echo $skill['nome'];
    echo $skill['nivel'];
    echo '<img src="' . esc_url($skill['icon_url']) . '" />';
}
```

---

## 🎯 Requisitos Atendidos

✅ **Descrição bilíngue** (PT-BR / EN-US)  
✅ **Experiências profissionais** com datas  
✅ **Cursos** com datas (com opção "Presente")  
✅ **Habilidades por categoria**:
   - Softwares
   - Softwares Criativos
   - Office
   - Sistemas Operacionais

✅ **Para cada habilidade**:
   - Upload de SVG ou PNG
   - Nome (opcional)
   - Nível de proficiência (Iniciante/Intermediário/Avançado/Expert)

✅ **Painel admin intuitivo** com:
   - Abas navegáveis
   - Repeater fields (adicionar/remover)
   - Media uploader integrado
   - Validação de dados
   - Proteção de segurança

---

## 🚀 Próximas Melhorias Sugeridas

1. **Drag & drop** para reordenar experiências/cursos
2. **Preview em tempo real** no painel admin
3. **Backup automático** de dados
4. **Histórico** de alterações
5. **Edição inline** na página pública (frontend)
6. **API REST** para acesso via aplicações externas
7. **Importação en lote** (CSV/JSON)
8. **Cache** inteligente dos dados

---

## 🔧 Troubleshooting Técnico

### Dados não aparecem na página
```php
// Verificar se dados existem no banco
$data = get_option('odyssee_cv_data');
var_dump($data);

// Verificar se odysseeCVData está injetado
// (abra DevTools > Console)
console.log(window.odysseeCVData);
```

### Upload de imagens não funciona
- Verificar permissões de upload (`/wp-content/uploads/`)
- Verificar limite de tamanho de arquivo (php.ini)
- Verificar WhiteList de tipos MIME

### Dados não salvam
- Verificar nonce no POST
- Verificar permissões do usuário
- Verificar status do banco de dados

---

## 📊 Performance

- **Tamanho do meta option:** Tipicamente < 10KB
- **Queries ao banco:** 1 por page load (get_option)
- **JS bundle:** ~15KB (cv-page-admin-data.js minificado)
- **Sem impacto** em outras páginas (carregado apenas em page-sobre-mim.php)

---

## 📝 Implementação no Blog

Se desejar exibir dados do CV em posts/artigos:

```php
<?php
// Exemplo: Mostrar experiências em um widget
$cv_data = odyssee_cv_get_data();
$lang = (get_locale() === 'en_US') ? 'en' : 'pt';
$desc_key = ($lang === 'en') ? 'descricao_en' : 'descricao_pt';

foreach ($cv_data['experiencias'] as $exp) {
    echo '<h3>' . esc_html($exp['titulo']) . '</h3>';
    echo '<p>' . esc_html($exp[$desc_key]) . '</p>';
}
?>
```

---

## 📜 Changelog

### v1.0.0 - 16 de Abril de 2026
- ✨ Release inicial
- ✅ Painel completo com 4 abas
- ✅ Suporte bilíngue
- ✅ Upload de ícones
- ✅ Integração com frontend

---

**Desenvolvido para:** Odyssee — Creative Experience  
**Autor:** GitHub Copilot  
**Data:** April 16, 2026
