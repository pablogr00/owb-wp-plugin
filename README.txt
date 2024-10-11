curl -X POST https://pablo1.odoo.com/jsonrpc \
-H "Authorization: Bearer 46cdaf1281eedbec34aa30c8b6eee59bab5cae21" \
-H "Content-Type: application/json" -d '{
  "jsonrpc": "2.0",
  "method": "call",
  "params": {
    "service": "object",
    "method": "execute_kw",
    "args": [
      "pablo1",
      "product.product",
      "search_read",
      [],
      []
    ]
  },
  "id": 1
}'

curl -X POST https://pablo1.odoo.com/jsonrpc -H "Authorization: Bearer" -H "Content-Type: application/json" -d '{
  "jsonrpc": "2.0",
  "method": "call",
  "params": {
    "service": "object",
    "method": "execute_kw",
    "args": [
      "pablo1",
      2,
      "46cdaf1281eedbec34aa30c8b6eee59bab5cae21",
      "product.product",
      "search_read",
      []
    ],
    "kwargs": {
      "fields": ["id", "name", "qty_available"]
    }
  },
  "id": 1
}'

$payload = [
      'jsonrpc' => '2.0',
      'params' => [
        'db' => $this->db,
        'login' => $this->username,
        'password' => $this->password
      ]
    ];

curl -X POST https://pablo1.odoo.com/web/session/authenticate -H "Authorization: Bearer" -H "Content-Type: application/json" -d '{
  "params": {
    "pablo1",
    2,
    "46cdaf1281eedbec34aa30c8b6eee59bab5cae21"
  },
  "id": 1
}

curl -X POST https://pablo1.odoo.com/jsonrpc -H "Authorization: Bearer" -H "Content-Type: application/json" -d '{
  "jsonrpc": "2.0",
  "method": "call",
  "params": {
    "service": "object",
    "method": "execute_kw",
    "args": [
      "pablo1",
      2,
      "46cdaf1281eedbec34aa30c8b6eee59bab5cae21",
      "product.product",
      "search_read",
      []
    ],
    "kwargs": {
      "fields": ["id", "name", "qty_available"]
    }
  },
  "id": 1
}'