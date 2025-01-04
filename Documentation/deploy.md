## **Deploy na AWS com EC2**

Este projeto foi implantado na **AWS** utilizando uma instância EC2, proporcionando alta disponibilidade, escalabilidade e flexibilidade para a aplicação. Abaixo estão as etapas detalhadas do processo de deploy:

## **Deploy na Plataforma Render**
Além do deploy na AWS, a aplicação foi implantada com sucesso na plataforma Render, disponível no endereço https://starwars-x6tc.onrender.com. A flexibilidade do Docker permitiu que o mesmo ambiente fosse replicado facilmente na Render, destacando a portabilidade e a consistência entre os ambientes.

### **Etapas de Deploy**

1. **Criação da Instância EC2**
   - **Escolha da AMI**: A instância foi criada com uma **AMI Ubuntu** para garantir compatibilidade com o ambiente PHP, Nginx, e as dependências utilizadas no projeto.
   - **Configuração de Segurança**: As portas necessárias (80 para HTTP, 443 para HTTPS, 22 para SSH) foram abertas no **Security Group** da EC2 para permitir o acesso ao servidor e ao tráfego da web.
   - **Tipo da Instância**: A instância escolhida foi de tipo **t2.micro** para uma aplicação de baixo tráfego inicial, podendo ser escalada conforme a necessidade.

2. **Configuração de PHP, Nginx e Docker na Instância**
   - **Instalação do Docker**: Docker foi instalado na instância para garantir que o ambiente seja isolado e consistente entre desenvolvimento e produção.
   - **Instalação do Nginx**: O Nginx foi configurado para servir a aplicação, com um arquivo de configuração personalizado para otimizar o desempenho e servir conteúdo estático (imagens, animações).
   - **Instalação do PHP**: A versão **PHP 7.4** foi configurada no servidor, com extensões necessárias para o funcionamento da aplicação.

3. **Clonagem do Repositório**
   - O repositório foi clonado diretamente da **GitHub** na instância EC2, utilizando o comando `git clone`. A instância foi configurada para obter as últimas versões do código-fonte.

4. **Configuração do Docker**
   - **Build da Imagem Docker**: O arquivo `Dockerfile` foi utilizado para construir a imagem Docker da aplicação.
   - **Execução da Aplicação com Docker**: Após a construção da imagem, o Docker foi configurado para rodar o servidor web, com o Nginx e PHP-FPM para servir a aplicação de maneira eficiente.
   - **Armazenamento de Logs**: Logs de interação com a aplicação e erros são gerados e armazenados dentro da instância EC2, utilizando **Monolog**.

5. **Configuração do Banco de Dados SQLite**
   - O banco de dados **SQLite** foi utilizado para armazenar as informações da aplicação. Ele foi configurado diretamente dentro da instância EC2, armazenando dados de autenticação e logs de interações.

6. **Testes e Validação**
   - **Testes de Funcionalidade**: Após o deploy, todos os pontos da aplicação foram testados, incluindo as rotas da API, a interface do usuário, e a interação com o banco de dados.
   - **Testes de Performance**: A performance da instância EC2 foi monitorada, para garantir que a aplicação possa escalar conforme o número de usuários.

### **Benefícios do Deploy na AWS com EC2**
- **Escalabilidade**: O uso do EC2 permite que a aplicação seja escalada facilmente conforme a demanda de tráfego, podendo aumentar a capacidade da instância ou criar instâncias adicionais.
- **Segurança**: A instância EC2 é protegida por regras de segurança personalizadas no Security Group, e a comunicação é criptografada com SSL.
- **Redundância**: A infraestrutura na AWS permite a criação de instâncias múltiplas e balanceamento de carga, garantindo alta disponibilidade.
- **Monitoramento e Logs**: O uso de logs com Monolog garante que todos os eventos da aplicação sejam registrados para monitoramento e análise de falhas.
- **Ambiente de Produção Consistente**: O Docker garante que o ambiente da aplicação seja consistente, tanto na produção quanto no desenvolvimento local.

### **Próximos Passos**
- **Escalabilidade Horizontal**: Implementação de múltiplas instâncias EC2 para garantir que o sistema possa lidar com um maior número de requisições simultâneas.
- **Configuração de CI/CD**: Automação do processo de deploy utilizando pipelines CI/CD, para que a aplicação seja automaticamente implantada sempre que um commit for feito no repositório.
- **Monitoramento e Alertas**: Integração com o **CloudWatch** da AWS para monitoramento da aplicação e configuração de alertas para eventos críticos, como uso excessivo de CPU ou falhas na aplicação.
