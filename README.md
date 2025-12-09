# Sistema de Gestão de Prestadores - Seu João

Sistema web para gerenciamento de prestadores de serviço, desenvolvido como teste técnico FullStack.

## 🚀 Tecnologias

- **Backend:** PHP 7.4 + CakePHP 2.10.24
- **Banco de Dados:** MySQL 5.7
- **Frontend:** HTML5, CSS3, JavaScript, jQuery
- **Containerização:** Docker + Docker Compose

## 📋 Funcionalidades

### ✅ CRUD de Prestadores
- Cadastro completo (nome, email, telefone, foto)
- Listagem com paginação (6 por página)
- Busca por nome, sobrenome ou email
- Edição e exclusão de prestadores
- Upload de fotos (JPG, PNG, GIF, SVG - máx 2MB)

### ✅ Gestão de Serviços
- Cadastro de serviços via AJAX
- Associação N:N entre prestadores e serviços
- Preço individualizado por prestador/serviço
- Edição e exclusão de serviços
- Seleção múltipla com busca

### ✅ Importação CSV
- Upload de arquivo CSV
- Validação de dados (email, campos obrigatórios)
- Criação automática de serviços inexistentes
- Relatório detalhado (sucessos, erros, avisos)
- Suporte a múltiplos serviços por prestador

### ✅ Interface
- Design moderno baseado no Figma fornecido
- Responsivo (desktop e mobile)
- Modais animados
- Feedback visual (loading, alertas)
- Máscaras de input (telefone, valores)

## 🐳 Instalação com Docker

### Pré-requisitos
- Docker Desktop ou Docker Engine + Docker Compose
- Git

### Passo 1: Clonar o repositório
```bash
git clone https://github.com/Kaeffea/teste-fullstack
cd teste-fullstack
```

### Passo 2: Subir os containers
```bash
docker-compose up -d --build
```

Isso irá criar 3 containers:
- **cakephp_app** (PHP 7.4 + Apache) - Porta 8080
- **cakephp_db** (MySQL 5.7) - Porta 3306
- **cakephp_phpmyadmin** (phpMyAdmin) - Porta 8081

### Passo 3: Configurar o banco de dados

O banco já está configurado automaticamente via Docker com:
- **Host:** db
- **Database:** teste_joao
- **User:** cakephp
- **Password:** cakephp123

**Executar migrations/seed:**
```bash
docker exec cakephp_db sh -c 'until mysql -uroot -proot -e "SELECT 1" >/dev/null 2>&1; do
  echo "Aguardando MySQL iniciar...";
  sleep 2;
done'

docker exec -i cakephp_db mysql -uroot -proot --default-character-set=utf8 teste_joao < app/app/Config/Schema/schema.sql
```

Ou acessar phpMyAdmin (http://localhost:8081) e importar `app/Config/Schema/schema.sql`

### Passo 4: Acessar a aplicação

- **Aplicação:** http://localhost:8080/prestadores
- **phpMyAdmin:** http://localhost:8081 (Login: root / root)

## 📁 Estrutura do Projeto
```
teste-fullstack-joao/
├── app/                        # Aplicação CakePHP
│   ├── Config/
│   │   ├── database.php       # Configuração do banco
│   │   └── Schema/
│   │       ├── schema.sql     # Schema + seed data
│   │       └── README.md      # Documentação do banco
│   ├── Controller/
│   │   └── PrestadoresController.php
│   ├── Model/
│   │   ├── Prestador.php
│   │   ├── Servico.php
│   │   └── PrestadorServico.php
│   ├── View/
│   │   ├── Elements/          # Componentes reutilizáveis
│   │   ├── Layouts/
│   │   └── Prestadores/
│   └── webroot/
│       ├── css/style.css      # Estilos customizados
│       └── files/uploads/     # Upload de fotos
├── docker-compose.yml         # Configuração Docker
├── Dockerfile                 # Imagem customizada PHP
└── README.md                  # Este arquivo
```

## 🗄️ Banco de Dados

### Tabelas

**prestadores**
- Informações dos prestadores (nome, email, telefone, foto)

**servicos**
- Catálogo de serviços (nome, descrição)

**prestadores_servicos** (tabela pivô)
- Relacionamento N:N com campo `valor` (preço específico)

## 📊 Importação CSV

### Formato do arquivo
```csv
nome;sobrenome;email;telefone;servicos;valores
João;Silva;joao@email.com;(82) 99604-9202;Pintura|Elétrica;200.00|150.00
```

- **Separador:** ponto e vírgula (;)
- **Múltiplos serviços:** pipe (|)
- **Valores:** formato decimal (200.00 ou 200,00)

Arquivo de exemplo: `exemplo-importacao.csv`

## 🎥 Vídeo Demonstrativo

[YouTube](https://www.youtube.com/watch?v=emHGU_jr5hk)

Demonstração completa das funcionalidades:
- CRUD de prestadores
- Sistema de busca e paginação
- Cadastro e gestão de serviços
- Importação CSV
- Interface responsiva

## 🔧 Comandos Úteis
```bash
# Ver logs dos containers
docker-compose logs -f app

# Acessar container PHP
docker exec -it cakephp_app bash

# Reiniciar containers
docker-compose restart

# Parar containers
docker-compose down

# Limpar tudo (containers + volumes)
docker-compose down -v
```

## 🐛 Troubleshooting

### Erro de permissão em uploads
```bash
docker exec -it cakephp_app bash
chmod -R 777 /var/www/html/app/webroot/files/uploads
chmod -R 777 /var/www/html/app/tmp
```

### Banco não conecta
Verificar se o container MySQL está rodando:
```bash
docker ps
```

### Cache do CakePP
```bash
docker exec -it cakephp_app bash
rm -rf /var/www/html/app/tmp/cache/*
```

## 👤 Autor

### Kauê Fortaleza Ferreira
- GitHub: [@Kaeffea](https://github.com/Kaeffea/)
- LinkedIn: [Kauê Ferreira](https://www.linkedin.com/in/kau%C3%AA-ferreira-a67546215/)

## 📝 Licença

Este projeto foi desenvolvido como teste técnico.

---

**Desenvolvido com dedicação para o teste FullStack** 🚀
