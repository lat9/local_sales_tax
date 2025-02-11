===========================================================
Local Sales Taxes for Zen Cart 1.5.7 
===========================================================
This plugin adds the support for local sales tax zones. In
the United States a local sales tax is a additional tax
levied by the county or city in addition to the tax levied
by a state.

You should consult your local tax authority for tax advice
as it relates to your business.

As this plug-in handles additional tax zones some features
of "Modules/Shipping Modules" do not work in the same way
after installation of this module.

===========================================================
KNOWN ISSUES
===========================================================
Zen Cart and other order total modules have no way to know
the order total line added by this module is applying
additional tax rates.

Why is this important? You cannot rely on order total
modules such as "coupons" or "gv" to adjust the local sales
tax calculations. This also means payment modules such as
Paypal will see an additional charge (added to subtotal)
as they do not know the order total line is for taxes.

This mod does not work correctly with the "Better Together"
mod from That Software Guy (swguy). It also does not play
well with most software which modifies pricing during
during checkout.

Currently this module will ignore the +4 in a shipping,
billing, or store zip code when matching against rules.

===========================================================
INSTALLATION INSTRUCTIONS
===========================================================
0. Pre - Installation. Backup your Zen Cart installation
  and database. Best practice is to keep regular backups
  and perform a manual backup prior to the installation or
  modification of any portion of Zen Cart. If upgrading,
  remove all files added by previous versions of the module

1. Copy files from "1_new_files/includes" to your Zen Cart
  installation under "/includes". Copy the files from
  "1_new_files/your_admin_folder" to your Zen Cart
  installation under your admin folder.
  If upgrading overwrite any existing files.

2. If you are installing to Zen Cart 1.3.9h, Copy the
  files from "2_only_v139/your_admin_folder" to your
  Zen Cart installation under your admin folder.
  If upgrading overwrite any existing files.

3. Log into the admin side of your Zen Cart installation
  using a web browser. Yes, log into your admin interface
  now before continuing. Failing to first login can
  result in you missing vital error messages. Copy over
  the file from "3_install/your_admin_folder" to your
  Zen Cart installation under your admin folder. 

4. Click on any link in the admin interface. I typically
  use the "Admin Home" link. Note any error or success
  messages which appear on the top of the site. If
  all goes well it should report success. If additional
  steps need to be performed, follow the instructions
  and go back to step 3.

5. Install any of the desired optional sql files located
  in "5_optional_sql" using phpMyAdmin or a similiar tool.

===========================================================
REMOVAL INSTRUCTIONS
===========================================================
1. Log into the admin side of your Zen Cart installation
  using a web browser. Disable the "Local Sales Tax" module
  from "Admin" -> "Modules" -> "Order Totals".

2. Log into the admin side of your Zen Cart installation
  using a web browser. Yes, log into your admin interface
  now before continuing. Failing to first login can
  result in you missing vital error messages. Copy over
  the file from "5_uninstall/your_admin_folder" to your
  Zen Cart installation under your admin folder.

3. Click on any link in the admin interface. I typically
  use the "Admin Home" link. Note any error or success
  messages which appear on the top of the site. If all goes
  well it should report success. If additional steps need
  to be performed, follow the instructions and go back to
  step 2.

4. Revert all changes made to the database during step 5 of
  the installation. The uninstallation script does attempt
  to remove the new files added during step 1 & 2 of the
  installation, but it would be a good idea at this time to
  verify all changes have been removed.

===========================================================
SOME THINGS WORTH NOTING
===========================================================
Only english language files have been provided. If your
Zen Cart is not set to use english you will need to either
translate the english files to your desired language(s) or
copy the english files into your desired language(s).

As the language files are used during the installation,
you need to address the language files (or switch to the
english language) before installing to avoid issues.

This module is open to contributions by others. If you find
and fix a bug, please notify the support thread and think
about repackaging your changes and release the updates!
