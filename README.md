# Reloader

## Usage

Tendo como verdade que estamos na pasta root do Magento e colocamos o reloader dentro da pasta shell, podemos executar os comandos da seguinte forma:

### Create

| Tipo | Como usar | Observação |
| ---- | --------- | ---------- |
| Customers |  php -f shell/reloader/create/customers.php 10 | Irá criar 10 usuários |
| Products |  php -f shell/reloader/create/products.php 10 5,color,size | Irá criar 10 produtos simples e 5 produtos configuráveis com 5 produtos simples atrelados a eles combinando os valores dos atributos color e size |
| Orders | php -f shell/reloader/create/orders.php 5 | Irá criar 5 pedidos |

### Reset

| Tipo | Como usar | Observação |
| ---- | --------- | ---------- |
| Customer |  php -f shell/reloader/reset/customers.php | Irá apagar todos os usuários |
| Products |  php -f shell/reloader/reset/products.php | Irá apagar todos os produtos |
| Orders | php -f shell/reloader/reset/orders.php | Irá apagar todos os pedidos |
| Categories | php -f shell/reloader/reset/categories.php | Irá apagar todas as categorias |
| All | php -f shell/reloader/reset/all.php | Irá apagar todos os acima |
