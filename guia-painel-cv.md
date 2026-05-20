# Guia Rápido - Painel de Administração da Página Sobre Mim

## 🚀 Como Acessar

1. Acesse o painel do WordPress admin
2. No menu lateral, clique em **"Sobre Mim"** (novo item com ícone de ID)
3. Você verá 4 abas: **Descrição**, **Experiências**, **Cursos** e **Habilidades**

---

## 📝 Aba 1: Descrição

**O que faz:** Edita o texto de introdução que aparece no hero da página.

- **PT-BR (Português):** Preencha as duas caixas de texto
- **EN-US (English):** Preencha as traduções em inglês

Os textos mudam automaticamente conforme o idioma selecionado na página.

✅ **Salvar:** Clique em "Salvar Alterações" no final

---

## 💼 Aba 2: Experiências Profissionais

**O que faz:** Gerencia suas experiências de trabalho (aparece em ordem cronológica invertida).

### Para cada experiência, preencha:
- **Título/Cargo:** ex: "Designer Gráfico", "Gerente de Projetos"
- **Empresa:** ex: "First Publicidade", "Agência XYZ"
- **Período:** ex: "2023 - 2024" ou "2024 - Presente"
- **Descrição (PT-BR):** Atividades realiza (em português)
- **Descrição (EN-US):** Activities in English

### Ações:
- ➕ **Adicionar:** Clique no botão "+ Adicionar Experiência"
- ✏️ **Editar:** Clique no item para expandir e fazer alterações
- 🗑️ **Remover:** Clique em "Remover" na experiência desejada

✅ **Salvar:** Clique em "Salvar Alterações"

---

## 🎓 Aba 3: Cursos e Formação

**O que faz:** Adiciona cursos, graduações e certificações.

### Para cada curso, preencha:
- **Nome do Curso:** ex: "Designer Gráfico", "Especialização em UX/UI"
- **Instituição:** ex: "UNICID", "Curso Online XYZ"
- **Data Final:** ex: "2024", "2025" ou "Presente"
- **Descrição (PT-BR):** Detalhes opcionais (ex: duração, modalidade)
- **Descrição (EN-US):** Details in English (optional)

### Ações:
- ➕ **Adicionar:** Clique em "+ Adicionar Curso"
- ✏️ **Editar:** Expanda o item
- 🗑️ **Remover:** Clique em "Remover"

✅ **Salvar:** Clique em "Salvar Alterações"

---

## 🎯 Aba 4: Habilidades

**O que faz:** Organiza suas habilidades em 4 categorias com ícones personalizados.

### Categorias:
1. **Softwares:** Ferramentas em geral
2. **Softwares Criativos:** Adobe, Cinema 4D, Blender, etc
3. **Office:** Microsoft Office, Google Suite
4. **Sistemas Operacionais:** Windows, macOS, Linux

### Para cada habilidade, preencha:
- **Nome (opcional):** ex: "Adobe Photoshop", "Windows 11"
- **Nível de Proficiência:** Escolha entre:
  - Iniciante
  - Intermediário
  - Avançado
  - Expert
- **Ícone (SVG ou PNG):** Upload da logo

### 🖼️ Como adicionar um ícone:
1. Clique em "Escolher Arquivo"
2. Selecione uma imagem (SVG, PNG ou JPEG)
3. A imagem aparece como preview
4. Clique em "Salvar Alterações"

### ❌ Como remover um ícone:
1. Clique em "Remover Imagem"
2. Clique em "Salvar Alterações"

### 💡 Dicas:
- Use **SVG** quando possível (mais leve)
- Certifique-se de ter **fundo transparente**
- Tamanho recomendado: mínimo 64x64px

✅ **Salvar:** Clique em "Salvar Alterações"

---

## 🔐 Segurança

- ✅ Apenas administradores do WordPress podem acessar
- ✅ Todos os dados são validados e sanitizados
- ✅ Proteção contra CSRF com WordPress nonce
- ✅ Apenas SVG, PNG e JPEG são aceitos para imagens

---

## 🎨 Visualizar Alterações

Após salvar, acesse a página **"Sobre Mim"** no seu site para ver as mudanças ao vivo:

1. Vá para a página "Sobre Mim" do seu site
2. As alterações aparecem inmediatamente
3. Teste em ambos os idiomas (PT-BR e EN-US)
4. Verifique se as imagens estão carregando corretamente

---

## ⚡ Dicas Importantes

✅ **Sempre salve antes de sair da página**  
✅ **Use quebras de linha nas descrições para listas**  
✅ **Mantenha ambos PT-BR e EN-US preenchidos**  
✅ **Ordene experiências do mais recente para o mais antigo**  
✅ **Teste no frontend após cada grande alteração**

---

## 📱 Compatibilidade

- ✅ Desktop (recomendado para edição)
- ✅ Tablet (com cuidado)
- ⚠️ Mobile (não recomendado para grandes edições)

---

## 🆘 Problemas?

**O painel não aparece?**
- Verifique se está logado como Admin
- Limpe o cache do navegador
- Recarregue a página

**Os dados não salvam?**
- Clique em "Salvar Alterações" (não é automático)
- Verifique se há erros em vermelho
- Tente novamente ou contate o suporte

**As imagens não carregam?**
- Verifique o tamanho do arquivo (máx. 10MB)
- Certifique-se do formato (SVG, PNG, JPEG)
- Limpe cache do navegador

---

## 📞 Suporte

Dados salvos na: `wp_options` → `odyssee_cv_data`

Para recuperar dados manualmente em PHP:
```php
$cv_data = odyssee_cv_get_data();
```

---

**Última atualização:** April 16, 2026  
**Versão do painel:** 1.0.0
