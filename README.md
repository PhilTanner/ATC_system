ATC system
=======

System designed to simplify the running of an ATC squadron. Designed and built using my experiences at No 49 (District of Kapiti, New Zealand) Squadron.

All use or forks of the system are welcome, for whatever purposes. Please do let me know if it's of any use to anyone however!

## Requirements

The system was developed to be run on a LAMP (Linux, Apache, MySQL, PHP) stack. It was, however, primarily developed on the [Bit Web Server](http://webserver.bitnet.web.id/) Android app, running on a Samsung Galaxy S2 tablet, so it doesn't require a high-spec machine to run. It was also co-developed and debugged on a Windows laptop running XAMPP, and is running on a t1.micro Amazon EC2 instance for live. So it's very lightweight and should be fairly portable/cross-platform compliant.

That said, you do need the following:

1. Apache (or lighttpd)
2. MySQL
3. PHP version 5.2+
4. These files

## Licences

The code and system itself is released under the GPL 3.0 licence. That said, I have made use of some other libraries, which are:

- jQuery 1.8.3
- jQuery tablesorter plugin v2.0.5b
- jQuery UI 1.9.2
- [jQuery UI Touch Punch 0.2.3](http://touchpunch.furf.com)
- fPDF 1.7

which may (or may not) have their own licences. All required reading/etc is included in this repository, so caveat emptor etc.

## Installation

You'll need to create a database on your system to hold the data used in the system. The `buildscript_atc.sql` file contains the SQL that will need to be run, normally as the MySQL `root` user.  Ideally you should also create a database user with a password to access this database - for the syntax, see the [MySQL CREATE USER](https://dev.mysql.com/doc/refman/5.5/en/create-user.html) syntax.

Once done, update the `Database connection settings` section of the `config.php` file, with the relevant values.

You can also update the other settings available in the `config.php` file in order to adjust the system to your needs.

The `buildscript_atc.sql` file will pre-populate the system with some sample users for you to log in and try out the system as. These are:

| Username | Password |
| -------- | -------- |
| admin@49squadron.org.nz | ADMIN |
| adjutant@49squadron.org.nz | ADJUTANT |
| treasurer@49squadron.org.nz | TREASURER |
| cucdr@49squadron.org.nz | CUCDR  |
| officer@49squadron.org.nz | OFFICER |
| training@49squadron.org.nz | TRAINING |
| stores@49squadron.org.nz | STORES |
| uo@49squadron.org.nz | U/O |
| cadet@49squadron.org.nz | CADET |

For (hopefully obvious reasons) you should rename/change the password for these users ASAP on any public facing systems. All the passwords are all **UPPERCASE** and need to be entered as such. The usernames are **not** case sensitive. Each user has the user level associated with their password assigned to them.

## Version history

### RC 0.8.3
- Enhancements:
  - NZCF16 documentation production
  - Translations of CONSTANTS being implements (later versions will back-fill)
  - Finance report for personnel ATC_Finance->get_account_history()
  - Logic change, only GUI headers now check for login sessions by default
  - Bug fix for activities ICS login
  - Bug fix for activities ICS for Google embed
  - Bug fix for attendance register, shows current term on term end date
  
### RC 0.8.2
- Enhancements:
  - ICS format now include NOK records if the user has ATC_PERMISSION_PERSONNEL_VIEW permission for that record
  
### RC 0.8.1
- Enhancements:
  - Now embed activity calendar in external calendar programs through ICS format

### RC 0.8.0
- Enhancements:
  - New training program setup

### RC 0.7.4
- Enhancements:
  - Update to this README.md file
  - Removal of old/unneeded code
  - Highlighting cadets who haven't paid term invoices by week 6 of the term start date.
  - New tidied-up `buildscripit_atc.sql` vesion  

### RC 0.7.3
- Bug fixes:
  - Include SNCOs in squadron personnel
  - Training officers can now edit activities

- New features:
  - Moved finished activities to the end of the list

### RC 0.7.2 
- Bug fixes:
  - Extra persmission checking when editing activities
  - New user display_name variable set
  - Exclude users who are not enabled from enrolled cadets list in NZCF20
  
- Enhancements:
  - Changing users attending activities no longer removes everyone and re-adds
  - Allow selection of term dates for attendance
  - try/catch around index page lists to nicely handle each user's view permissions
  
- New features:
  - Terms created
  - Term fees implemented
  - Payment list updated to account for activities and term fees
  - Attendance register set to use term dates
  - Creation of Emergeny Contact user type, allowed to be secondary contact point for activities
  - Rank shortname code as CONSTANT to allow easy customisation
  - Cadets risking being signed off (after 4 consecutive AWOL parades)
  - Added version number to the footer
  - New CONSTANTs for term fees etc
  - List cadets missing invoices

### RC 0.7.1 
First "full" release to public

---

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED.