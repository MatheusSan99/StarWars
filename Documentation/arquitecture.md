## **Arquitetura e Organização**

### **Clean Architecture Mantendo o MVC**
A aplicação adota uma arquitetura limpa (**Clean Architecture**) para garantir modularidade e facilidade de manutenção, sem abandonar a familiaridade do padrão MVC. Isso inclui:
- **Domínio independente:** Lógica de negócio desacoplada de frameworks ou dependências externas.
- **DTOs e Use Cases:** Estrutura clara para transporte de dados e execução de casos de uso.
- **Repositórios:** Abstração para persistência de dados.