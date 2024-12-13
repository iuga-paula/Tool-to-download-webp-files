## PHP Tool for downloading .web images from WordPress websites
This tool gets .web image names and locations from a txt file or, searches for them in a static website directory. \
It downloads the images and adds them under the given location in the current directory.

### Installation
1. Clone this repository
2. Run `composer install`

### Usage
```getWebpFiles.php``` receives 2 arguments:
* ```-b``` *required*, path to the remote website from where to download images
* ```-d``` optional, the directory from which to scrap .webp files. If it is not given, the tool gets image names to download from _text.txt_ file

### Examples
To get .webp files from _text.txt_ file run:
```bash
 php getWebpFiles.php -bhttps://2020.page-annual-report.org
 ```

To get .webp files from HTML from a static website directory run:
```bash
 php getWebpFiles.php -bhttps://2020.page-annual-report.org -d/home/user/Work/2021.page-annual-report.org
 ```