## **Dependências do Projeto**

O projeto utiliza as seguintes dependências no **`composer.json`**:

### **Dependências de Produção**
- **`psr/http-message`** e **`nyholm/psr7`**: Implementações PSR para manipulação de requisições e respostas HTTP de forma padrão e eficiente.
- **`php-di/php-di`**: Gerenciador de injeção de dependências para facilitar o desacoplamento e a testabilidade do código.
- **`slim/slim`** e **`slim/psr7`**: Microframework para aplicações web em PHP, com suporte nativo ao padrão PSR-7.
- **`monolog/monolog`**: Ferramenta poderosa para registro de logs com suporte a vários handlers.
- **`doctrine/migrations`**: Gerenciamento de migrações do banco de dados, simplificando a evolução do esquema.
- **`firebase/php-jwt`**: Biblioteca para criação e validação de tokens JWT para autenticação segura.
- **`zircote/swagger-php`**: Geração automática de documentação para APIs no formato OpenAPI (Swagger).

### **Dependências de Desenvolvimento**
- **`phpunit/phpunit`**: Framework de testes unitários amplamente utilizado em PHP.
- **`squizlabs/php_codesniffer`**: Análise de código para aderir a padrões de codificação.

## **Melhorias com Docker**

### **1. Docker e Docker Compose**
O projeto utiliza um **Dockerfile otimizado**, com base na imagem **Alpine Linux**, dividindo o processo em três estágios:

- **Build:** 
  - Compila extensões PHP necessárias (como `pdo_sqlite`, `yaml`, `soap`, etc.).
  - Remove dependências de compilação ao final, reduzindo o tamanho da imagem.
- **Composer:**
  - Realiza a instalação das dependências PHP sem incluir as ferramentas de desenvolvimento no ambiente final.
  - Otimiza o autoloader para desempenho superior.
- **Final:**
  - Configura o ambiente com **PHP-FPM** e um servidor **Nginx** minimalista.
  - A imagem final é extremamente leve (aproximadamente **130 MB**) e eficiente.

**Benefícios:**
- Ambientes consistentes entre desenvolvimento e produção.
- Configuração modular que facilita manutenção e escalabilidade.
- Desempenho otimizado com uso apenas de componentes essenciais.

---

## **Servidor Nginx Configurado do Zero**
A configuração do Nginx foi feita do zero, atendendo aos requisitos da aplicação:

- **Rotas personalizadas**:
  - Servem as páginas do frontend e APIs do backend de forma otimizada.
  - Gerenciam arquivos estáticos (como imagens e animações temáticas).
- **Segurança e desempenho**:
  - Configuração simplificada para suporte eficiente a cache.
  - Flexibilidade para adicionar cabeçalhos de segurança no futuro.

### **Benefícios:**

### **1. Desempenho Superior**
- **Eficiência na Manipulação de Requisições:** O Nginx é projetado para lidar com um grande número de conexões simultâneas graças ao seu modelo de eventos assíncronos e não bloqueantes, diferente do servidor embutido do PHP, que segue um modelo síncrono.
- **Entrega de Conteúdo Estático:** O Nginx é altamente otimizado para servir arquivos estáticos (imagens, CSS, JavaScript, etc.), aliviando a carga do PHP-FPM.

### **2. Proxy Reverso e Balanceamento de Carga**
- **Proxy Reverso:** O Nginx pode atuar como proxy reverso, roteando as requisições para diferentes backends (como PHP-FPM ou outros serviços).
- **Balanceamento de Carga:** Ele pode distribuir requisições entre múltiplos servidores, facilitando a escalabilidade horizontal.

### **3. Cache Integrado**
- O Nginx pode ser configurado para armazenar respostas em cache, reduzindo a carga no backend e melhorando a velocidade de entrega para os usuários.

### **4. Segurança**
- **Suporte a HTTPS:** Configuração simples e eficiente para habilitar TLS/SSL, garantindo a criptografia das comunicações.
- **Proteção contra Ataques:** Com módulos adicionais, o Nginx pode mitigar ataques DDoS e filtrar requisições maliciosas.
- **Cabeçalhos de Segurança:** Permite configurar headers como `Content-Security-Policy`, `X-Frame-Options` e outros, melhorando a segurança do frontend.

### **5. Modularidade e Flexibilidade**
- **Configurações Avançadas:** O Nginx oferece grande flexibilidade para configurar redirecionamentos, reescritas de URL, limites de requisição e mais.
- **Compatibilidade com PHP-FPM:** Trabalha de forma eficiente com PHP-FPM para processar requisições dinâmicas, separando o processamento PHP do servidor web.

### **6. Robustez em Produção**
- O servidor embutido do PHP é recomendado apenas para desenvolvimento, pois:
  - Não é otimizado para alto tráfego ou cargas simultâneas.
  - Carece de recursos avançados de configuração e segurança.
  - É menos confiável em termos de estabilidade.

### **7. Logs e Monitoramento**
- O Nginx oferece logs detalhados de acesso e erros, facilitando a análise de tráfego e a identificação de problemas.

## **Banco de Dados: SQLite**
O projeto utiliza o banco de dados **SQLite** devido à sua simplicidade e eficiência:

- Banco de dados leve e embutido, ideal para APIs de pequeno a médio porte.
- Integração simples com PHP usando extensões nativas.
- Elimina a necessidade de configurar um servidor de banco de dados separado.

**Benefícios:**
- Reduz a complexidade da configuração inicial.
- Excelente desempenho para consultas locais.
- Portabilidade total, já que o banco é armazenado em um único arquivo.

---

## **Funcionalidades Adicionais**
- **Testes Automatizados:**
  - Cobertura completa de unidades de negócio e integração.
  - Utiliza PHPUnit para validar a funcionalidade e a robustez da aplicação.

- **Documentação Completa:**
  - Gerada automaticamente com **Swagger**, proporcionando uma visão clara e interativa dos endpoints da API.
  - Disponível em `/api-docs` no ambiente local.

- **Frontend com Animações:**
  - Desenvolvido para capturar a essência de **Star Wars**.
  - Utiliza **Bootstrap** e **jQuery** para criar uma interface amigável e visualmente atraente.

- **Logs com Monolog:**
  - Registra todas as interações com o sistema e a API.
  - Configuração modular que permite fácil adaptação para novos tipos de logs (e.g., logs em arquivos ou serviços remotos).