# API de Checkout — AppAlways

API RESTful de checkout para loja virtual, desenvolvida em Laravel 11. Recebe pedidos de um app mobile, processa pagamentos via gateway simulado e confirma pagamentos via webhook.

---

## Requisitos

- [Docker](https://www.docker.com/products/docker-desktop) instalado e rodando
- [Composer](https://getcomposer.org/) instalado localmente

---

## Instalação

### 1. Clone o repositório

```bash
git clone <url-do-repositorio>
cd AppAlways
```

### 2. Instale as dependências PHP

```bash
composer install
```

### 3. Configure o ambiente

```bash
cp .env.example .env
```

Edite o `.env` e ajuste as variáveis do banco para apontar para o container do Sail:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password
```

### 4. Suba os containers

```bash
./vendor/bin/sail up -d
```

Isso iniciará os containers da aplicação e do MySQL em segundo plano.

> **Dica:** adicione um alias para não precisar digitar o caminho completo:
> ```bash
> alias sail='./vendor/bin/sail'
> ```

### 5. Gere a chave da aplicação

```bash
./vendor/bin/sail artisan key:generate
```

### 6. Execute as migrations e seeds

```bash
./vendor/bin/sail artisan migrate --seed
```

Isso criará as tabelas e populará o banco com:
- **3 clientes** de exemplo (João, Maria, Pedro)
- **4 produtos** de exemplo com estoque inicial de 100 unidades cada

A API estará disponível em `http://localhost`.

---

## Documentação Swagger

A documentação interativa da API está disponível em:

```
http://localhost/api/documentation
```

Para regenerar os docs após alterações:

```bash
./vendor/bin/sail artisan l5-swagger:generate
```

---

## Endpoints

### POST `/api/checkout`

Realiza o checkout de um pedido. Calcula o total, chama o gateway de pagamento e cria o pedido com status `pending`.

**Body:**
```json
{
  "customer_id": 1,
  "items": [
    { "product_id": 1, "quantity": 2 },
    { "product_id": 3, "quantity": 1 }
  ],
  "credit_card": {
    "holder_name": "João Silva",
    "number": "1234567890123456",
    "expiry_month": 12,
    "expiry_year": 2027,
    "cvv": "123"
  }
}
```

**Resposta (200):**
```json
{
  "success": true,
  "message": "Checkout realizado com sucesso",
  "data": {
    "id": 1,
    "status": "pending",
    "total_amount": "35.00",
    "transaction_id": "txn_fake_AbCdEfGh",
    "card_last_digits": "3456",
    "customer": { ... },
    "items": [ ... ]
  }
}
```

> **Regra do gateway simulado:** último dígito do cartão **par** → aprovado | **ímpar** → recusado.

---

### POST `/api/webhook/payment`

Recebe a confirmação de pagamento do gateway. Atualiza o status do pedido para `paid` ou `failed` e deduz o estoque em caso de aprovação.

Implementa **idempotência**: pedidos já finalizados (`paid` ou `failed`) são ignorados.

**Body:**
```json
{
  "order_id": 1,
  "status": "approved"
}
```

**Valores aceitos para `status`:** `approved` | `declined`

**Resposta (200):**
```json
{
  "order_id": 1,
  "status": "paid",
  "transaction_id": "txn_fake_AbCdEfGh"
}
```

---

### GET `/api/orders/{order_id}`

Retorna os dados completos de um pedido, incluindo cliente e itens.

**Resposta (200):**
```json
{
  "success": true,
  "message": "Pedido encontrado.",
  "data": {
    "id": 1,
    "status": "paid",
    "total_amount": "35.00",
    "transaction_id": "txn_fake_AbCdEfGh",
    "card_last_digits": "3456",
    "customer": {
      "id": 1,
      "name": "João",
      "email": "joao@example.com",
      "phone": "123456789",
      "document": "12345678901234"
    },
    "items": [
      {
        "product_id": 1,
        "product_name": "Caixa de Limão",
        "quantity": 2,
        "unit_price": "10.00",
        "subtotal": 20
      }
    ],
    "created_at": "2026-06-05T20:00:00.000000Z"
  }
}
```

---

## Artisan Commands

### Simular confirmações de pagamento

Busca todos os pedidos com status `pending` e processa as confirmações usando a mesma regra do gateway (último dígito do cartão par/ímpar):

```bash
./vendor/bin/sail artisan app:simulate-payment-webhook
```

### Simular um checkout completo

Cria um pedido de forma automática com cliente e produtos aleatórios do banco:

```bash
# Cartão aleatório
./vendor/bin/sail artisan app:simulate-checkout

# Forçar aprovação (último dígito par)
./vendor/bin/sail artisan app:simulate-checkout --approved

# Forçar recusa (último dígito ímpar)
./vendor/bin/sail artisan app:simulate-checkout --declined
```

---

## Agendamento (Scheduler)

O command `app:simulate-payment-webhook` está registrado no scheduler do Laravel para rodar **a cada minuto**.

Para ativar o agendamento, adicione a seguinte entrada ao cron do sistema:

```bash
crontab -e
```

```
* * * * * cd /caminho-absoluto-do-projeto && ./vendor/bin/sail artisan schedule:run >> /dev/null 2>&1
```

Substitua `/caminho-absoluto-do-projeto` pelo caminho real do projeto na máquina, por exemplo `/home/user/appalways`.

> Se preferir rodar o scheduler **dentro do container** diretamente:
> ```bash
> ./vendor/bin/sail artisan schedule:work
> ```

---

## Estrutura do Banco de Dados

| Tabela | Descrição |
|---|---|
| `products` | Produtos disponíveis com preço e estoque |
| `customers` | Clientes cadastrados |
| `orders` | Pedidos com status, total e dados do pagamento |
| `order_items` | Itens de cada pedido com quantidade e preço unitário |

**Status possíveis de um pedido:** `pending` → `paid` ou `failed`

---

## Fluxo Completo

```
App Mobile
    │
    ▼
POST /api/checkout
    │  Valida dados, calcula total, chama gateway simulado
    │  Cria pedido com status "pending"
    ▼
Gateway Simulado (PaymentGatewayService)
    │  Aprova ou recusa com base no último dígito do cartão
    │  Retorna transaction_id
    ▼
POST /api/webhook/payment  (ou php artisan app:simulate-payment-webhook)
    │  Atualiza status para "paid" ou "failed"
    │  Se aprovado: deduz estoque dos produtos
    ▼
GET /api/orders/{id}
    │  Consulta dados completos do pedido
```

---

## Tecnologias

- **Laravel 11** — Framework PHP
- **MySQL 8** — Banco de dados relacional
- **L5-Swagger** — Documentação automática da API
- **Laravel Sail** — Ambiente Docker integrado
