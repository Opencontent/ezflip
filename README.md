## OpenContent eZFlip

Convert your ezbinary pdf files in a flash-based flip book.

### Requirements

* eZP >= 4.X or 5.X (when running the Legacy Stack only) with ImageMagick
* [pdftk](http://www.pdflabs.com/tools/pdftk-the-pdf-toolkit/)
* Gosthscript

### Installation

Enable the extension; clear all caches.

Add the ezflip cron `php runcronjobs.php -s<siteaccess> ezflip` to your crontab


### How to make a pdf flippable

If the current node has an ezbinarytype attribute and hit content is a pdf file, you will see a small book icon in the website toolbar. From website toolbar press the 'book' icon and follow the instructions. The cronjob will make the file flippable. 

### Warning
This extension uses an old Open Source version of third part software [MegaZine3](http://www.megazine3.de/demo_packages.html) (version 1.38):
keep in mind that there will be no bug fixes, enhancements or faq any longer.
