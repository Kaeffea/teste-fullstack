# Estrutura do Banco de Dados

## Diagrama de Relacionamento
```
prestadores (1) ←→ (N) prestadores_servicos (N) ←→ (1) servicos
```

## Tabelas

### prestadores
Armazena informações dos prestadores de serviço.

| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | INT | Chave primária |
| nome | VARCHAR(100) | Primeiro nome |
| sobrenome | VARCHAR(100) | Sobrenome |
| email | VARCHAR(150) | Email (único) |
| telefone | VARCHAR(20) | Telefone de contato |
| foto | VARCHAR(255) | Caminho da foto |
| created | DATETIME | Data de criação |
| modified | DATETIME | Data de modificação |

### servicos
Armazena os tipos de serviços disponíveis.

| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | INT | Chave primária |
| nome | VARCHAR(150) | Nome do serviço |
| descricao | TEXT | Descrição detalhada |
| created | DATETIME | Data de criação |
| modified | DATETIME | Data de modificação |

### prestadores_servicos
Tabela pivô que relaciona prestadores aos serviços que oferecem.
**IMPORTANTE:** Contém o campo `valor` que define o preço específico.

| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | INT | Chave primária |
| prestador_id | INT | FK → prestadores.id |
| servico_id | INT | FK → servicos.id |
| valor | DECIMAL(10,2) | Preço específico |
| created | DATETIME | Data de criação |
| modified | DATETIME | Data de modificação |

## Lógica de Negócio

- Um prestador pode oferecer múltiplos serviços
- Um serviço pode ser oferecido por múltiplos prestadores
- Cada combinação prestador+serviço tem um PREÇO ÚNICO
- Exemplo: João cobra R$ 200 por Pintura, mas Maria cobra R$ 180 pela mesma Pintura