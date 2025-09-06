# Tarefa PHP — Sistema simples em PHP (Painel + AJAX)

Projeto em PHP para gestão de tarefas/conteúdo com **painel administrativo**, páginas modulares em `paginas/`, e ações via `ajax/`. É direto ao ponto e fácil de hospedar em qualquer servidor com Apache + PHP + MySQL.

> **Objetivo**: entregar uma base funcional, organizada e documentada, sem mudar sua lógica atual.

---

## 🔎 Visão geral

- **Stack**: PHP 8.x, MySQL (5.7+ ou 8), PDO, HTML/CSS/JS.
- **Arquitetura**: páginas carregadas por parâmetro (ex.: `index.php?pagina=...`), ações por `ajax/*.php`, conexão em `conexao.php`.
- **Extras já mapeados no repo**: integrações (ex.: `enviar_whatsapp.php`), tarefas agendadas em `cron/`, `.htaccess` para ajustes finos.

---

## 📁 Estrutura de pastas (resumo)

```
/
├─ ajax/                 # Endpoints AJAX (CRUD, ações do painel)
├─ cron/                 # Scripts para CRON (tarefas agendadas)
├─ css/                  # Estilos
├─ img/                  # Imagens
├─ modal/                # Modais/partials
├─ paginas/              # Páginas do sistema (carregadas dinamicamente)
├─ painel/               # Recursos do painel (se aplicável)
├─ conexao.php           # Conexão PDO com o MySQL
├─ enviar_whatsapp.php   # Integração WhatsApp (ajuste seu provedor/API)
├─ index.php             # Entrada do app
├─ .htaccess             # Regras Apache (opcional)
└─ README.md
```

> Observação: em alguns projetos desse estilo, o **roteamento** usa `index.php` + query `pagina` para carregar `painel/paginas/<arquivo>.php`. Siga a lógica que **já está** no seu repositório para não quebrar nada.

---

## ✅ Requisitos

- **PHP** 8.0 ou superior (extensão `pdo_mysql` habilitada)
- **MySQL** 5.7+ ou 8
- Servidor **Apache** (ou Nginx equivalente) com suporte a PHP
- Acesso para criar banco e usuário

Opcional (recomendado):
- **Composer** para gerenciar dependências (ex.: `vlucas/phpdotenv`)
- **Git** para versionamento e CI/CD

---

## ⚙️ Configuração (sem mudar sua lógica)

### 1) Banco de dados
Crie um banco e um usuário com permissão:

```sql
CREATE DATABASE tarefa_php DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'tarefa_user'@'%' IDENTIFIED BY 'senha_forte';
GRANT ALL PRIVILEGES ON tarefa_php.* TO 'tarefa_user'@'%';
FLUSH PRIVILEGES;
```

> Se você já tem as tabelas no servidor, **pule** a criação abaixo. O script a seguir é **apenas um exemplo mínimo** para testar a página de tarefas.

**Exemplo de schema mínimo (opcional):**

```sql
-- Tabela de usuários (login simples)
CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  senha_hash VARCHAR(255) NOT NULL,
  perfil ENUM('admin','user') DEFAULT 'user',
  status ENUM('ativo','inativo') DEFAULT 'ativo',
  criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de tarefas (modelo básico)
CREATE TABLE IF NOT EXISTS tarefas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(200) NOT NULL,
  descricao TEXT,
  status ENUM('novo','em_andamento','concluido','cancelado') DEFAULT 'novo',
  prioridade ENUM('baixa','media','alta') DEFAULT 'media',
  responsavel_id INT NULL,
  prazo DATE NULL,
  criado_por INT NULL,
  criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
  atualizado_em DATETIME NULL,
  FOREIGN KEY (responsavel_id) REFERENCES usuarios(id) ON DELETE SET NULL,
  FOREIGN KEY (criado_por) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Logs simples (opcional)
CREATE TABLE IF NOT EXISTS logs (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  nivel ENUM('INFO','WARN','ERROR') DEFAULT 'INFO',
  origem VARCHAR(100),
  mensagem TEXT,
  criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 2) Conexão com o banco
A conexão acontece em `conexao.php`. Use as credenciais do seu servidor. Exemplo:

```php
<?php
// conexao.php (exemplo)
$host = 'localhost';
$db   = 'tarefa_php';
$user = 'tarefa_user';
$pass = 'senha_forte';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
  PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
  $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
  http_response_code(500);
  die('Falha na conexão com o banco.');
}
```

> **Dica:** Se quiser esconder credenciais, use `.env` (veja “.env opcional”).

### 3) Configuração do servidor
- **Apache**: certifique-se que o `DocumentRoot` aponta para a pasta onde está o `index.php`.
- **.htaccess** (opcional) — se precisar de URLs amigáveis ou headers:
```apache
# .htaccess (exemplo simples)
Options FollowSymLinks
RewriteEngine On

# Força UTF-8
AddDefaultCharset utf-8

# Se usar URL amigável, adapte as regras ao seu roteamento.
# RewriteRule ^painel/?$ index.php?pagina=painel [L,QSA]
```

---

## ▶️ Como rodar (local e produção)

1. Clone o repositório:
   ```bash
   git clone https://github.com/jcordeiro1/tarefa_php.git
   cd tarefa_php
   ```
2. Configure `conexao.php` (ou `.env`, se usar a opção abaixo).
3. Importe o SQL (se precisar) e confirme que o usuário do banco tem acesso.
4. Suba no seu Apache/Nginx ou use o PHP embutido (dev only):
   ```bash
   php -S 127.0.0.1:8080 -t .
   ```
5. Acesse `http://127.0.0.1:8080` (ou seu domínio).

---

## 🧰 .env (opcional, recomendado)

Se quiser centralizar credenciais fora do código, use **Composer** + **vlucas/phpdotenv**:

```bash
composer require vlucas/phpdotenv
```

Crie um arquivo `.env` na raiz:
```dotenv
APP_ENV=local
APP_DEBUG=true

DB_HOST=localhost
DB_NAME=tarefa_php
DB_USER=tarefa_user
DB_PASS=senha_forte
DB_CHARSET=utf8mb4
```

No início do `index.php` (ou no seu bootstrap), carregue o `.env`:

```php
<?php
require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();
```

E em `conexao.php`, leia as variáveis:
```php
$host    = $_ENV['DB_HOST']    ?? 'localhost';
$db      = $_ENV['DB_NAME']    ?? 'tarefa_php';
$user    = $_ENV['DB_USER']    ?? 'root';
$pass    = $_ENV['DB_PASS']    ?? '';
$charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';
```

> Se não quiser Composer, mantenha a conexão fixa em `conexao.php` — funciona igual.

---

## ⏰ CRON (tarefas agendadas)

Se houver scripts em `cron/`, agende no servidor (exemplo diário às 02:30):

```bash
crontab -e
# Adicione:
30 2 * * * /usr/bin/php -d detect_unicode=0 /var/www/html/cron/limpeza.php >> /var/log/cron_tarefa.log 2>&1
```

---

## 🔐 Boas práticas de segurança

- **Nunca** versione segredos (tokens, chaves, `.env`).  
- Garanta permissões mínimas em pastas de upload (`img/`, `uploads/` etc.).  
- Valide e saneie **toda** entrada de usuário (use prepared statements e `htmlspecialchars`).  
- Ative HTTPS no domínio.  
- Proteja rotas do painel com sessão e checagem de perfil/CSRF se necessário.

---

## 🧪 CI simples (GitHub Actions)

Crie `.github/workflows/php.yml` para um lint básico:

```yaml
name: PHP Lint

on:
  push:
  pull_request:

jobs:
  php-lint:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: pdo_mysql
      - name: Lint PHP
        run: |
          find . -type f -name "*.php" -print0 | xargs -0 -n1 php -l
```

---

## 🗑️ .gitignore recomendado

Crie um `.gitignore` na raiz:

```
# Sistema
/vendor/
/node_modules/
/.env
/.env.*
/storage/
/uploads/
/cache/
/tmp/
.idea/
.vscode/
*.log
*.cache

# Logs do app
log_*.txt
logs/*.log

# SO
.DS_Store
Thumbs.db
```

Se existir `log_menuia.txt` ou similar, mantenha fora do versionamento.

---

## 🧭 Fluxo de navegação (padrão)

- **index.php**: roteia para páginas do painel/site conforme `?pagina=`.
- **paginas/**: telas do sistema (ex.: `tarefas/listar.php`, `tarefas/novo.php`).
- **ajax/**: endpoints de ação (ex.: `tarefas/salvar.php`, `tarefas/excluir.php`).
- **conexao.php**: conexão PDO global (incluída nos scripts que acessam DB).

> Siga o padrão que **já existe** no seu projeto para não quebrar rotas.

---

## 🚀 Deploy checklist

- [ ] PHP 8.x com `pdo_mysql` habilitado  
- [ ] Banco criado + usuário com permissão  
- [ ] `conexao.php` (ou `.env`) com credenciais corretas  
- [ ] Permissões de upload/logs configuradas  
- [ ] Domínio com HTTPS e timezone do PHP correto (`date.timezone = America/Sao_Paulo`)  
- [ ] CRON configurado (se usar)  
- [ ] `.gitignore` com segredos e logs

---

## 🧯 Troubleshooting rápido

- **HTTP 500 logo de cara** → verifique `pdo_mysql` e credenciais no `conexao.php`.  
- **Caracteres estranhos/acentos** → garanta `utf8mb4` no banco e no DSN do PDO.  
- **Página em branco** → habilite `display_errors` no ambiente local e consulte o log do Apache/PHP.  
- **Push rejeitado (non-fast-forward)** → `git pull --rebase origin main && git push`.  
- **WhatsApp/API não funciona** → cheque tokens/URLs em `enviar_whatsapp.php` (não versione chaves).

---

## 📄 Licença

Sugestão: **MIT**. Crie um arquivo `LICENSE` com o texto da licença desejada.

---

## 💬 Contribuindo

- Branch principal: `main`  
- Faça PRs pequenos e objetivos. Se possível, adicione prints das telas alteradas.

---

## ✍️ Autor

**Jacy Cordeiro** — Marketing Digital + Engenharia de Software.  
Foco em soluções diretas, funcionais e com resultado.

