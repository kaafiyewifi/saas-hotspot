\# System Spec



Multi-tenant Hotspot + PPPoE management SaaS.



\## Requirements



\- Super Admin only creates tenants and users

\- No public signup

\- Single domain



\## Customers



Customer can have:

\- Hotspot service

\- PPPoE service

\- Both



Default credentials:

username = phone

password = 123456

must change on first login



\## Billing



\- Monthly subscription

\- Invoice generation

\- Overdue → suspend service

\- Payment → reactivate



\## Routers



\- MikroTik integration

\- RADIUS auth + accounting

\- One-touch provisioning endpoint



Provision script must be idempotent

Must not delete untagged firewall rules



