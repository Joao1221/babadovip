# BabadoVip Portal (PHP 8.3 + MySQL/MariaDB)

Portal completo com:
- Área pública: Home por seções, categoria, matéria com galeria/lightbox
- Área admin: login, CRUD de matérias, Home Builder, moderação de envios
- Área do leitor: envio de sugestão/matéria com até 20 fotos

## Stack e decisões
- PHP 8.3 (sem framework pesado), arquitetura MVC simples
- PDO + prepared statements
- Front-controller em `public/index.php`
- CSS próprio (dark premium) para acelerar MVP robusto
- Segurança: CSRF no admin e envio público, validação server-side, upload seguro, escaping de saída

## Estrutura
- `app/Config`: configuração
- `app/Core`: infraestrutura (router, sessão, auth, csrf, db)
- `app/Controllers`: controllers público/admin
- `app/Models`: acesso a dados
- `app/Services`: upload/thumbnail/auditoria
- `app/Views`: templates
- `public/assets`: CSS/JS
- `public/uploads`: uploads de posts e submissions
- `database/schema.sql`: DDL completo
- `database/seed.sql`: dados iniciais (com imagens de `/img`)

## Requisitos
- PHP 8.3+ com extensões:
  - `pdo_mysql`
  - `fileinfo`
  - `gd` (thumbnails)
- MySQL 8+ ou MariaDB 10.6+
- Apache com `mod_rewrite` (ou servidor PHP embutido)

## Instalação
1. Copie `.env.example` para `.env`:
```bash
copy .env.example .env
```
2. Ajuste credenciais de banco no `.env`.
3. Crie o banco e rode:
```sql
SOURCE database/schema.sql;
SOURCE database/seed.sql;
```
Se o banco já existe de versão anterior, rode também:
```sql
SOURCE database/migrations/20260301_add_overlay_titulo_cor.sql;
SOURCE database/migrations/20260301_create_contact_messages.sql;
SOURCE database/migrations/20260304_add_posts_subchamadas_home.sql;
SOURCE database/migrations/20260304_expand_posts_titulo_length.sql;
```

## Executar
### Opção A: Apache (XAMPP)
- Aponte DocumentRoot para `public/` ou mantenha projeto em `htdocs/babadovip/public`.
- Garanta que `mod_rewrite` esteja ativo.

### Opção B: PHP built-in server
```bash
php -S localhost:8000 -t public
```
Acesse: `http://localhost:8000`

## Login inicial do admin
- URL: `/admin/login`
- E-mail: `admin@babadovip.local`
- Senha: `Admin@123`

## Contato do leitor
- Botão `Contato` no rodapé abre `/contato`.
- Mensagens ficam no Admin em `/admin/messages`.

## Fluxo de moderação de envios
1. Leitor envia em `/enviar` (protocolo gerado, status `pendente`)
2. Admin acessa `/admin/submissions`
3. Ações:
   - Aprovar e publicar
   - Aprovar como rascunho
   - Rejeitar (motivo interno opcional)
   - Excluir envio (apaga arquivos)

## Uploads e paths
- Posts: `public/uploads/posts/AAAA/MM/post-{id}/{capa|galeria}`
- Submissions: `public/uploads/submissions/AAAA/MM/sub-{id}`
- Seed utiliza imagens existentes em `/img` para conteúdo inicial.

## Segurança aplicada
- CSRF token em formulários sensíveis
- Sessão segura e `session_regenerate_id` no login
- `password_hash`/`password_verify`
- Rate limit de login e de envio público por IP hash
- MIME/extensão/tamanho validados em uploads
- Sanitização de HTML de conteúdo com whitelist e escaping de saída

## Observações de produção
- Em produção, defina:
  - `APP_ENV=prod`
  - `APP_DEBUG=false`
  - `APP_URL=https://www.babadovip.com.br`
  - `APP_KEY` forte e único
- Idealmente mover uploads para storage externo (S3/objeto) no próximo passo.

