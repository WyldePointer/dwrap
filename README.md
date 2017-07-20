# dwrap
A DNS-like protocol that can work independently and/or on top of the existing DNS infrastructure.


### Examples:

`get_ip_by_name www.google.com`

173.194.32.148

173.194.32.144

173.194.32.147

173.194.32.146

173.194.32.145

<br />
<br />

`get_ip_by_name www.google.com --limit 1`

173.194.32.147

<br />
<br />

`get_ip_by_name www.google.com --json`

["173.194.32.148","173.194.32.144","173.194.32.147","173.194.32.146","173.194.32.145"]

---

### Features
  - JSON output
  - Query result limit
  - Protocol friendly: TCP, UDP, etc.
  - Made for programmers

### Todo
  - RESTful
  - Implement all records.(MX, TXT, etc.)
  - Caching(dwrapd)
  - Reverse lookup
  - Multiple host lookups in one query. (get_ip_of_multiple_names google.com yahoo.com ya.ru)

### Project Goals
  - A platform / language / OS independent protocol.
  - Being implemented only in Application Layer(L7)
  - Support for a query syntax / language.(similar to `sh(1)`)
