# Onionimb.us
### Dedicated Reverse Proxy for Tor Hidden Services
Derived from the tor2web project, onionimb.us aims to make it possible to assign a clearnet domain name (example.com) or subdomain name (yourpage.example.com) to a Tor Hidden Service (example7dfr3xtum.onion).

The purpose of this software is to promote anonymous publishing.
## Quick setup for Tor Hidden Service providers
1. Have a website operating on a Tor Hidden Service. Duh. (These aren't difficult to set up. Consult the Tor Project website for more information.)
2. Register a domain name. (Finding a registrar and TLD that allows anonymous domain registration is an exercise left up to the reader.)
3. Create an Onionimb.us account. Create an association between your domain name and your hidden service. During this step, you will be given the option of your reverse proxy (options will list IP address and nation of jurisdiction).
  * For example, you can set example.com to point to example7dfr3xtum.onion and be served by 176.56.236.56
4. Find a DNS provider. (Cloudflare's a decent choice, but find one that suits your needs.)
5. Set your domain's A record to the IP address of the reverse proxy you selected (176.56.236.56 in our example above)

## How it Works
There are two components to this project
1. The controller website (onionimbus_site) which manages domain->proxy->hidden_service associations
2. The reverse proxies (onionimbus_node) which actually handles the traffic
The network I set up will consist of one controller website (located at https://onionimb.us) and many reverse proxies (hopefully, in different countries and operated by different people to prevent a single point of failure). The source code for all components is made available so others may operate their own network.

The controller website sends encrypted and signed requests to the proxies in its network to add/remove domains from their internal database, in response to user activity.

When a valid request is received, the database is changed and the nginx configuration is scheduled to be rebuilt and reloaded.

## Roadmap (Arbitrary goals)
These version milestones are subject to change depending on the needs of the community over time. 
* Version 0.1 - Allow users to add, remove, and reassign com->onion associations on the fly
* Version 0.2 - Allow users to upload TLS keys and certificates (whether snakeoil, StartSSL, or other provider)
* Version 0.3 - Allow other protocols to be proxied (IRC, XMPP, email, whatever is requested)?
* Version 0.4 - Support i2psites

## Credit
Based on the work of the tor2web project. RIP Aaron Swartz