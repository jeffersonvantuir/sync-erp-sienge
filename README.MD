# Configuração
1. Copiar o arquivo `app/helpers/app.example.php` para um arquivo `app/helpers/app.php`.
2. Preencher as informações do arquivo `app.php` com as credenciais do banco de dados e dados do ERP

# Ambiente
Para subir o ambiente, podemos utilizar o Docker. No terminal/promp de comando dentro do projeto clonado, execute o comando abaixo:

```bash
$ docker compose build
```

```bash
$ docker compose up -d
```

```bash
$ docker compose exec php bash
```

Se todos os passos derem certo, você terá entrado no container docker php, no qual você poderá executar as importações dos dados do ERP.

Para realizar a importação, foram criados 4 arquivos separados, um para cada contexto (Centro de Custo, Clientes, Contratos de Vernda e Unidades de Imóveis).

Para executar uma das importações, basta executar o comando abaixo dentro do container docker php
```bash
# php app/cost-centers.php
```

