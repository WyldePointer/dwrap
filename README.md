# dwrap
A DNS-like protocol that can work independently and/or on top of the existing DNS infrastructure.


### Examples:

`get_ip_by_name www.google.com`
```
173.194.32.148

173.194.32.144

173.194.32.147

173.194.32.146

173.194.32.145
```
<br />
<br />

`get_ip_by_name www.google.com --limit 1`
```
173.194.32.147
```
<br />
<br />

`get_ip_by_name www.google.com --json`
```
["173.194.32.148","173.194.32.144","173.194.32.147","173.194.32.146","173.194.32.145"]
```

#### /api/
```
GET /api/get_ip_by_name/www.google.com
GET /api/get_ip_by_name/www.google.com/limit/3
GET /api/get_ip_by_name/www.google.com/json
GET /api/get_ip_by_name/www.google.com/json/limit/2
```

---

### Features
  - JSON output
  - Local database(e.g, acting as DNS server)
  - Query result limit
  - Protocol friendly: TCP, UDP, etc.
  - Made for programmers!
  - Supports both FQDN and non-FQDN hosts.
  - API (Think about it as DNS over HTTP)

### To-do
  - C89 implementation. (`cc -pedantic -std=c89 -Wall`)
  - In-memory database for records and caching. (Redis)
  - RESTful.
  - Implementing all records. (MX, TXT, etc.)
  - Caching
  - Reverse lookup
  - Multiple host lookups in one query. (`get_ip_of_multiple_names google.com yahoo.com ya.ru`)

### Project Goals
  - A platform / language / OS independent protocol.
  - Being implemented only in Application Layer(L7).
  - Support for a query syntax / language. (similar to `sh(1)`)
