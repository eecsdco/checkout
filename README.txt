####################################################################################################
#
# EECS CHECKOUT SYSTEM
# Checkout system for the EECS department that keeps track of loaned items and sends automated
# reminders.
# 
# Departmental Computing Organization
# Department of Electrical Engineering and Computer Science
# The University of Michigan
#
####################################################################################################

This project is maintained by Matt Colf.

####################################################################################################
# REQUIREMENTS
####################################################################################################

- Apache setup and working
- Cosign module setup and working with Apache SSL server
- The document root of the *:80 and *:443 Apache servers is the same.
- Directy structure as outlined below

####################################################################################################
# DIRECTORY STRUCTURE
####################################################################################################

checkout
	includes				contains all code and classes
	layout					contains layout files
	staff					contains staff management area
	
####################################################################################################
# PERMISSIONS
####################################################################################################

Apache must have R+W+E permission (7) on the following directories and files.

checkout/checkout.log
checkout/includes (and everything below)

####################################################################################################
# CRON SETUP
####################################################################################################

In order to send automated emails, the following entry must be made in the crontab file.

0 * * * * root /usr/bin/wget -q --spider http://server.domain/path/to/checkout/includes/cron.php > /dev/null