## **Como Rodar o Projeto**

### Requisitos:
- Docker
- Docker Compose

### Passos:
1. Clone o repositório:
   ```bash
   git clone https://github.com/MatheusSan99/StarWars.git
   ```

2. Suba o ambiente:
   ```bash
   docker build --no-cache -t starwars .
   ```

3. Acesse o container:
   ```bash
   docker exec -it starwars bash
   ```

4. No container, navegue até a pasta `database` e ajuste as permissões do arquivo de banco de dados:
   ```bash
   cd database
   chmod 777 database.sqlite
   ```

5. Acesse a aplicação no navegador:
   ```
   http://localhost:8080
   ```