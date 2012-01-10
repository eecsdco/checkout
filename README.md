EECS CHECKOUT SYSTEM
====================

Checkout system for the EECS department that keeps track of loaned items and sends automated
reminders. Developed for the Department of Electrical Engineering and Computer Science at the
University of Michigan.

Features
--------

- Basic checkout of a dynamic list of items.
- Flexible loan periods.
- Automated email reminders.
- "Self Service" checkout terminal for end-users.
- Full log of user, admin, and automated actions.
- User history and "Karma" tracking.
- Very basic statistics.

Requirements
------------

- Apache setup and working
- Cosign module setup and working with Apache SSL server
- The document root of the *:80 and *:443 Apache servers is the same.
- Directy structure as outlined below

Directory Structure
-------------------

checkout
	includes				contains all code and classes
	layout					contains layout files
	staff					contains staff management area
	
Permissions
-----------

Apache must have R+W+E permission (7) on the following directories and files.

checkout/checkout.log
checkout/includes (and everything below)

Cron Setup
----------

In order to send automated emails, the following entry must be made in the crontab file.
```
0 * * * * root /usr/bin/wget -q --spider http://server.domain/path/to/checkout/includes/cron.php > /dev/null
```

Legal
-----

Copyright (c) 2011, Matt Colf

Permission to use, copy, modify, and/or distribute this software for any
purpose with or without fee is hereby granted, provided that the above
copyright notice and this permission notice appear in all copies.

THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.