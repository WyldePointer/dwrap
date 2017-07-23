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
<br />
<br />

`get_mx gmail.com --limit 3`
```
array(3) {
  [40]=>
  string(31) "alt4.gmail-smtp-in.l.google.com"
  [5]=>
  string(26) "gmail-smtp-in.l.google.com"
  [10]=>
  string(31) "alt1.gmail-smtp-in.l.google.com"
}
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
  - Record-specific request. (e.g, MX, A)
  - Shell-like syntax

### To-do
  - C89 implementation. (`cc -pedantic -std=c89 -Wall`)
  - In-memory database for records and caching. (Redis)
  - RESTful.
  - Implementing all records. (TXT, etc.)
  - Caching
  - Reverse lookup
  - Multiple host lookups in one query. (`get_ip_of_multiple_names google.com yahoo.com ya.ru`)

### Project Goals
  - Decentralized system.
  - An OS / language / platform independent protocol.
  - Being implemented only in Application Layer(L7).
  - Support for a query syntax / language. (similar to `sh(1)`)
  - Something that everyone can run. (UPnP of the name resolution world) 


### But.. why?
Thinking of high available computing, we must have a quick and effective 
mechanism for applying changes, and most importantly, the one that works 
the same way across all other implementations and environments.
(Imagine the *exact opposite* of HTML and CSS combination!)
<br />

By having this communication protocol on top of the existing HTTP(s) 
infrastructure, we can increase the overall productivity of our cloud(s).
<br />

Personally I believe that the HTTP(s) is a good candidate that actually 
worked for a long time.(Besides the early cryptography extensions such as SSL) 
<br />

I hope people will use this code in their small environments first 
and later on, after more developments, we can do some real-world benchmark 
and analysis based on the data that we can gather from our web-server related 
equipments.(Be it a physical load balancer or an apache/nginx/tomcat instance) 
<br />

