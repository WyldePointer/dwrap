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

### Project Goals
  - Decentralized system.
  - An OS / language / platform independent protocol.
  - Being implemented only in Application Layer(L7).
  - Support for a query syntax / language. (similar to `sh(1)`)
  - Something that everyone can run. (UPnP of the name resolution world)

### Features
  - JSON output.
  - Local database. (e.g, acting as DNS server)
  - Query result limit.
  - Protocol friendly: TCP, UDP, etc.
  - Made for programmers.
  - Resolving both FQDN and non-FQDN hosts.
  - Web API. (Think about it as DNS over HTTPS)
  - Record-specific request. (e.g, MX, A)
  - Shell-like syntax.

### TODO
  - Implementing `dwrapd` in C. (`cc -pedantic -std=c89 -Wall`)
  - Python prototype of the `dwrapd` and having a separate `dwrapd-python` repository. (As well as `dwrapd-rust` and so on)
  - Implementation of a DNS-server that uses dwrap as its back-end and communication protocol.
  - More language binding. (PHP library is available here: https://github.com/WyldePointer/libdwrap-php)
  - Input validation / sanitization. (As well as response values)
  - Logging.
  - In-memory database for records and caching. (Redis)
  - RESTful.
  - Implementing all records. (Currently supporting A, MX, and TXT.)
  - Caching.
  - Reverse lookup.
  - Multiple host lookups in one query. (`get_ip_of_multiple_names google.com yahoo.com ya.ru`)

### But.. why?
Thinking of high available computing, we must have a quick and effective 
mechanism for applying changes, and most importantly, the one that works 
the same way across all other implementations and environments.
(Imagine the *exact opposite* of HTML and CSS combination!)
<br />

By having this communication protocol on top of the existing HTTP(S) 
infrastructure, we can increase the overall productivity of our cloud(s).
<br />

Thinking of security, one way or another we've suffered from DNS problems at 
least once in our lives! In the most simplest form, a lot of issues can be 
addressed much easier and faster, and of course cheaper!

Some of these problems are existing in HTTP(S) as well but if you do a basic 
comparison between number of websites that are using TLS(HTTPS) with those 
which are using DNSSEC or DANE, you'll get the picture. It also does not need 
your TLD to support anything special. They're built-in and anyone can just 
enable them on demand!

Personally I believe that the HTTP(S) is a good candidate which has actually 
worked for a long time.(Besides the early cryptography extensions such as SSL) 
<br />

I hope people will use this code in their small environments first 
and later on, after more developments, we can do some real-world benchmark 
and analysis based on the data that we can gather from our web-server related 
equipments.(Be it a physical load balancer or an apache/nginx/tomcat instance) 
<br />
<br />

### Links
https://news.ycombinator.com/item?id=12609752

https://developers.google.com/speed/public-dns/docs/dns-over-https

