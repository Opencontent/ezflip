## OpenContent eZFlip

Convert your ezbinary pdf files in a flip book.

### Warning
This extension uses an old Open Source version of third part software [MegaZine3](http://www.megazine3.de/demo_packages.html) (version 1.38):
keep in mind that there will be no bug fixes, enhancements or answers on questions any longer.
To make this old MegaZine verision working, you need to create a symbolic link to the folder that contains the flash: no problem eZFlip can do that for you, or, if your webserver user doesn't have the permission, eZFlip will print the command that need.
But - and this is the real warning - could be a privilege escalation in the file pdf reading that you made flippable and you don't want make public.


### Requirements

* eZP >= 4.X or 5.X (when running the Legacy Stack only) with ImageMagick
* [pdftk](http://www.pdflabs.com/tools/pdftk-the-pdf-toolkit/)
* Gosthscript

### Installation

Enable the extension; clear all caches.

### Make a pdf flippable

