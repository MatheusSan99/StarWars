## **Logging com Monolog**  
O projeto utiliza **Monolog** para centralizar e gerenciar os logs:  
- Registro de todas as **interações do sistema**, incluindo tanto **sucessos quanto falhas**.  
- Armazenamento detalhado de logs no banco de dados **SQLite** ao inves de usar um arquivo de log, garantindo acessibilidade e organização.  
- Suporte a diferentes níveis de log, como **info**, **warning**, e **error**, para categorizar e priorizar eventos.  

**Benefícios:**  
- **Monitoramento detalhado:** Todas as ações da aplicação são rastreadas, proporcionando uma visão abrangente do funcionamento do sistema.  
- **Diagnóstico rápido:** Falhas podem ser identificadas e resolvidas rapidamente devido à riqueza de informações nos logs.  
- **Persistência em banco de dados:** Uso do SQLite para armazenar logs para que os dados sejam consultados facilmente, além de garantir portabilidade e simplicidade na manutenção.  

---  