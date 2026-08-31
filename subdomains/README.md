# Subdomains (by Boy132 & HarlequinSin)

Allows users to create and manage custom subdomains (A/AAAA or SRV) for their game servers using Cloudflare DNS.

## Setup

[Create a Cloudflare API token](https://developers.cloudflare.com/fundamentals/api/get-started/create-token/) and enter it via the plugin settings.  
The token needs to have read permissions for `Zone.Zone` and write for `Zone.Dns`. For better security you can also set the `Zone Resources` to exclude certain domains and add the panel ip to the `Client IP Address Filtering`.

By default every server has a subdomain limit of 0. You can change this limit by editing the server in the admin area.

### Domains

Each domain is composed of a name and an optional prefix. The name must be a valid Cloudflare Zone, while the prefix can be used to specify a subdomain on which the server subdomains will be created.

For example: when creating a subdomain `server1` on a domain with name `example.com` and prefix `abc`, the created record will be `server1.abc.example.com`.

## Configuration

Subdomains support several different DNS Record types. Each type has different requirements before it can be created.

If a DNS Record type is not available, check whether all of it's requirements have been met.

### Valid primary allocation addresses

A and AAAA Subdomains point to the IP address of the server's primary allocation, so they require that IP address to be valid.

The only invalid values are `0.0.0.0` and `::`. They should be changed to proper IP addresses on which your servers can be reached.

### Subdomain targets

CNAME and SRV Subdomains must point to a specific Subdomain target. These can be configured for every node individually in the admin area.

Note: According to [RFC2782](https://www.rfc-editor.org/info/rfc2782/), SRV records must always point to either an A or AAAA record. While some applications may handle SRV records pointing to CNAME records correctly, this can lead to undefined behavior.

### SRV service types

SRV Subdomains require an SRV service type. This must be configured in the egg features section. The format is `srv-` and then the service name, e.g. `srv-minecraft` or `srv-rust`.

You can find the list of currently supported SRV service types [here](https://github.com/pelican/plugins/blob/main/subdomains/src/Enums/SRVServiceType.php#L10-L15).
