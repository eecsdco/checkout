EECS Checkout System
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
```
checkout
	includes				contains all code and classes
	layout					contains layout files
	staff					contains staff management area
```

Permissions
-----------

Apache must have R+W+E permission (7) on the following directories and files.
```
checkout/checkout.log
checkout/includes (and everything below)
```

Cron Setup
----------

In order to send automated emails, the following entry must be made in the crontab file.
```
0 * * * * root /usr/bin/wget -q --spider http://server.domain/path/to/checkout/includes/cron.php > /dev/null
```

Legal
-----

Copyright (c) 2011, Matthew Colf <mattcolf@mattcolf.com> and The Regents of the University of Michigan

All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

- Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
- Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
- Neither the names of the copyright holders nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.