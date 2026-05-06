# Security Notes

This repository is a security research portal and should be operated carefully when inspecting live URLs or operational webpage sources.

## Built-In Controls

- CSRF token validation on the inspector form.
- Security headers for framing, MIME sniffing, referrer policy, permissions, and content security policy.
- PDO prepared statements for database operations.
- Environment-based configuration for database credentials.
- URL scheme validation limited to HTTP and HTTPS.
- Blocking for localhost, private network, link-local, multicast, and reserved IP ranges.
- Fetch timeout and maximum source-size controls.
- No JavaScript, plugin, iframe, or browser execution during URL inspection.
- Source hashing for inspection records.

## Production Recommendations

- Add authentication and role-based authorization.
- Enforce HTTPS.
- Restrict database access to the application network.
- Route outbound inspection traffic through dedicated malware-safe egress infrastructure.
- Redact sensitive source before persistence.
- Log security events to centralized monitoring.
- Treat detector output as decision support, not as the only control.

## Secure Browsing Reminder

Static detection is not a substitute for secure browsers, endpoint protection, patch management, content filtering, DNS protection, and safe browsing controls.
