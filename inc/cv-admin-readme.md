# Painel CV Admin - Documentação

## Visão Geral

O Painel CV Admin é um módulo customizado do WordPress que permite gerenciar todos os dados da página "Sobre Mim" (Currículo/CV) sem precisar editar o HTML ou JavaScript.

## Acessar o Painel

1. Acesse o WordPress Admin (painel administrativo)
2. No menu lateral esquerdo, procure por **"Sobre Mim"** (ícone de ID)
3. Clique para abrir o painel

## Abas e Funcionalidades

### 1. **Descrição** 

Gerenciar textos de introdução bilíngues:

- **Português (PT-BR)**
  - Descrição 1: Primeira parte da introdução em português
  - Descrição 2: Segunda parte da introdução em português

- **English (EN-US)**
  - Descrição 1: First part of introduction in English
  - Descrição 2: Second part of introduction in English

Os textos são exibidos na seção hero da página sobre mim conforme o idioma selecionado pelo usuário.

---

### 2. **Experiências Profissionais**

Adicione, edite ou remova suas experiências de trabalho.

**Campos por Experiência:**
- **Título/Cargo**: ex: "Designer Gráfico"
- **Empresa**: ex: "First Publicidade"
- **Período**: ex: "2023 - 2024" ou "2025 - Presente"
- **Descrição (PT-BR)**: Detalhes sobre o trabalho em português
- **Descrição (EN-US)**: Details about the job in English

**Como adicionar:**
1. Clique no botão "+ Adicionar Experiência"
2. Preencha todos os campos
3. Clique em "Salvar Alterações" no final da página

**Como editar:**
1. Clique pada expandir o item que deseja editar
2. Faça as alterações
3. Clique em "Salvar Alterações"

**Como remover:**
1. Clique no botão "Remover" da experiência desejada
2. Confirme a ação
3. Clique em "Salvar Alterações"

---

### 3. **Cursos e Formação**

Adicione seus cursos, formações acadêmicas e certificações.

**Campos por Curso:**
- **Nome do Curso**: ex: "Designer Gráfico", "Curso de Motion Design"
- **Instituição**: ex: "Universidade X", "Escola de Design"
- **Data Final**: ex: "2024", "2025" ou "Presente" (deixe em branco se ainda está cursando)
- **Descrição (PT-BR)**: Detalhes opcionais em português
- **Descrição (EN-US)**: Optional details in English

**Notas:**
- Os campos de descrição são opcionais
- Se o curso está em andamento, coloque "Presente" ou deixe em branco na data final

---

### 4. **Habilidades**

Gerenciar habilidades organizadas por categorias:

- **Softwares**: Ferramentas e softwares em geral
- **Softwares Criativos**: Adobe Creative Suite, Cinema 4D, Blender, etc
- **Office**: Microsoft Office, Google Suite, etc
- **Sistemas Operacionais**: Windows, macOS, Linux, etc

**Campos por Habilidade:**
- **Nome (opcional)**: ex: "Adobe Photoshop", "Windows 11"
- **Nível de Proficiência**: Escolha entre Iniciante, Intermediário, Avançado ou Expert
- **Ícone (SVG ou PNG)**: Upload da logo/ícone da ferramenta

**Como adicionar um ícone:**
1. Clique no botão "Escolher Arquivo"
2. Selecione uma imagem SVG, PNG ou JPEG
3. A imagem será processada e exibida como preview
4. Clique em "Salvar Alterações"

**Como remover um ícone:**
1. Clique no botão "Remover Imagem"
2. Clique em "Salvar Alterações"

**Dicas para ícones:**
- Use imagens em **formato SVG** quando possível (mais leve e escalável)
- Se usar PNG/JPEG, certifique-se de que têm fundo transparente
- Tamanho recomendado: mínimo 64x64px, máximo 512x512px
- Os ícones são armazenados na mídia do WordPress

---

## Estrutura de Dados

Os dados são salvos na tabela `wp_options` do banco de dados WordPress:
- **Option Name**: `odyssee_cv_data`
- **Formato**: JSON serializado

### Estrutura JSON:
```json
{
  "descricao": {
    "pt_br": {
      "intro_1": "...",
      "intro_2": "..."
    },
    "en_us": {
      "intro_1": "...",
      "intro_2": "..."
    }
  },
  "experiencias": [
    {
      "titulo": "...",
      "empresa": "...",
      "periodo": "...",
      "descricao_pt": "...",
      "descricao_en": "..."
    }
  ],
  "cursos": [
    {
      "nome": "...",
      "instituicao": "...",
      "data_final": "...",
      "descricao_pt": "...",
      "descricao_en": "..."
    }
  ],
  "habilidades": {
    "softwares": [
      {
        "nome": "...",
        "nivel": "...",
        "icon_url": "...",
        "icon_id": 123
      }
    ],
    "criativos": [...],
    "office": [...],
    "os": [...]
  }
}
```

---

## Integração com o Frontend

### Para a página "Sobre Mim"

Os dados são disponibilizados para o JavaScript via `wp_localize_script`:

```javascript
// Acessar dados no cv-page.js
const cvData = odysseeCVData; // Objeto global injetado pelo WordPress
```

### Para recuperar dados programaticamente em PHP:

```php
// No seu código PHP (template, plugin, etc)
$cv_data = odyssee_cv_get_data();

// Acessar descrição
echo $cv_data['descricao']['pt_br']['intro_1'];

// Iterar experiências
foreach ( $cv_data['experiencias'] as $exp ) {
    echo $exp['titulo'];
    echo $exp['empresa'];
}
```

---

## Capabilidades e Segurança

- **Permitido para**: Admin do WordPress (só usuários com `manage_options`)
- **Proteção**: Todos os dados são sanitizados e validados
- **Nonce**: Usa WordPress nonce field para prevenção de CSRF
- **Upload**: Apenas SVG, PNG e JPEG são aceitos para ícones

---

## Troubleshooting

### O painel não aparece no menu
- Certifique-se de estar logado como Admin do WordPress
- Verifique se o arquivo `/inc/cv-admin.php` existe
- Verifique se o require está correto em `functions.php`

### Os dados não salvam
- Verifi que se você está clicando no botão "Salvar Alterações"
- Verifique as permissões de escrita no banco de dados
- Abra o console do navegador (F12) para ver se há erros

### Imagens não carregam
- Verifique o tamanho do arquivo (máximo 10MB)
- Certifique-se de que o formato é SVG, PNG ou JPEG
- Limpe o cache do navegador

---

## Dicas de Uso

1. **Sempre salve antes de sair**: Clique em "Salvar Alterações" antes de fechar a página

2. **Use tabelas para dados estruturados**: Se tiver uma lista de responsabilidades, separe por quebras de linha

3. **Mantenha descricao bilíngue**: Sempre preencha tanto PT-BR quanto EN-US para uma melhor experiência do usuário

4. **Organize por data**: Ordene experiências e cursos do mais recente para o mais antigo

5. **Teste no frontend**: Após salvar, acesse a página sobre mim e verifique se os dados aparecem corretamente

---

## Arquivos Relacionados

- **Painel Admin**: `/inc/cv-admin.php`
- **Estilos**: `/inc/cv-admin.css`
- **JavaScript**: `/inc/cv-admin.js`
- **Template Frontend**: `/page-sobre-mim.php`
- **JavaScript Frontend**: `/assets/js/cv-page.js`

---

## Suporte

Se encontrar problemas ou tiver sugestões de melhoria, consulte o desenvolvedor do tema.
