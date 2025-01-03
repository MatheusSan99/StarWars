## **Descrição das Telas da Aplicação**

A aplicação apresenta uma interface temática e funcional, composta por várias telas interativas, cada uma atendendo a um propósito específico no fluxo do usuário. Abaixo, uma descrição detalhada de cada tela:

---

### **Tela de Login (/pages/login)**  
- **Descrição:**  
  Tela inicial para autenticação de usuários. Permite que os usuários façam login utilizando suas credenciais previamente cadastradas.  

- **Funcionalidades:**  
  - Campo de entrada para e-mail e senha.  
  - Validação de dados inseridos antes de submeter o formulário.  
  - Redirecionamento para o catálogo após autenticação bem-sucedida.  

- **Estilo:**  
  - Layout inspirado no painel de controle de naves de Star Wars.  
  - Transições suaves ao interagir com os campos.  

---

### **Tela de Cadastro (/pages/create-account)**  
- **Descrição:**  
  Permite que novos usuários criem uma conta preenchendo informações básicas como nome, e-mail e senha.  

- **Funcionalidades:**  
  - Validações de formulário (e.g., formato de e-mail, força da senha).  
  - Mensagens de erro e sucesso claras e dinâmicas.  
  - Redirecionamento automático para a tela de login após cadastro bem-sucedido.  

- **Estilo:**  
  - Animações leves que refletem a assinatura visual da franquia Star Wars.  

---

### **Tela do Catálogo (/pages/catalog)**  
- **Descrição:**  
  Apresenta uma lista de filmes do universo Star Wars, ordenados por data de lançamento.  

- **Funcionalidades:**  
  - Cada filme exibe título, data de lançamento.  
  - Botão para acessar detalhes do filme e seus personagens.  

- **Estilo:**  
  - Design que remete ao holocrão Jedi, com animações discretas para hover e seleção.  

---

### **Tela de Documentação (/pages/documentation)**  
- **Descrição:**  
  Exibe a documentação da API local, gerada automaticamente via Swagger.  

- **Funcionalidades:**  
  - Listagem detalhada dos endpoints disponíveis.  
  - Teste de chamadas diretamente pela interface.  
  - Acesso restrito a usuários autenticados.  

- **Estilo:**  
  - Interface limpa com cores e fontes minimalistas, garantindo a clareza da documentação.  

---

### **Tela de Detalhes do Filme (/pages/film)**  
- **Descrição:**  
  Fornece informações detalhadas sobre um filme específico.  

- **Funcionalidades:**  
  - Exibição de título, diretor, produtores, e "opening crawl".  
  - Botão para acessar a lista de personagens do filme.  

- **Estilo:**  
  - Recriação visual das introduções clássicas de Star Wars.  
  - Textos animados para o "opening crawl".  

---

### **Tela de Personagens de um Filme (/pages/film/{filmId}/characters)**  
- **Descrição:**  
  Lista os personagens associados a um filme específico, com detalhes como nome, altura, peso e gênero.  
---

### **Segurança e Middleware**  
- Todas as rotas protegidas utilizam o middleware `AuthMiddleware` para garantir que apenas usuários autenticados possam acessar áreas restritas, como o catálogo e a documentação.  
