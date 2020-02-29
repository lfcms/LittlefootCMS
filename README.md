# LittlefootCMS

The Big CMS with a little footprint.

Visit https://github.com/lfcms/LittlefootCMS/wiki for verbose documentation.

## Docksal Setup for Local Development

1. Clone the repo locally
1. Install Docksal
1. Initialize project: `fin init`
1. When prompted with setup, use [Docksal defaults](https://docs.docksal.io/service/db/access/#root-password):
  * Host: db
  * User: user
  * Pass: user
  * Database: default
  * Admin Credentials: (your choice)
1. Click Install
1. To create a new branch: `fin branch MY_NEW_BRANCH_NAME_HERE`

## Manual Installation

1. Download <a href="http://littlefootcms.com/files/download/littlefoot.zip">littlefoot.zip</a> and unzip to your document root (eg, `public_html`)
1. Visit http://yourdomain.com/littlefoot/
1. You will be prompted for MySQL database credentials
 1. If you need to create a database in cPanel first, <a href="https://www.google.com/?gws_rd=ssl#q=create+mysql+database+in+cpanel">follow these instructions</a><br />
1. Once you enter all the database information and provide an admin password, click Install.

You should now be presented with the Littlefoot Admin.

## Submitting Issues

The preferred convention for issue titles is as follows:

`<affected area> - <issue>`

> ie, lf admin - add 'anonymous' to ACL tool

### Screenshots

Please include a screenshot. [GitHub made it really easy](https://help.github.com/articles/issue-attachments/)

## Design thoughts

* I want to be able to access anything from any context.
