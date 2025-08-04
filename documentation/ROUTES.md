# Rotas do Sistema

## Rotas Públicas
- `/` : Tela de login (view: auth.login)
- `Auth::routes()` : Rotas de autenticação padrão (login, registro, recuperação de senha, etc)

## Dashboard
- `/home` : Dashboard principal (HomeController@index)

## CRUD de Entidades
- `/emissor` : CRUD de emissores (EmissorController)
- `/grupo` : CRUD de grupos (GrupoController)
- `/assinatura` : CRUD de assinaturas (AssinaturaController)
- `/assunto` : CRUD de assuntos (AssuntoController)
- `/aplicacao` : CRUD de aplicações (AplicacaoController)
- `/usuario_aplicacao` : CRUD de usuários-aplicação (Usuario_AplicacaoController)

## Outras Rotas
- `/image/upload` : Upload de imagem (ImageController@upload)
- `/usage` : View de uso (usage)

## Observações
- As rotas resource implementam os métodos padrão: index, create, store, show, edit, update, destroy.
- Rotas comentadas em `web.php` podem ser ativadas conforme necessidade. 