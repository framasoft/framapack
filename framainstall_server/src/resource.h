/*
Framapack is an utils for download and install free software
Copyright (C) 2009  Simon Leblanc <contact@leblanc-simon.eu>

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

#include <windows.h>

// ID of Main Dialog
#define DLG_MAIN       101

// ID of Button Controls
#define IDC_BTN_CANCEL 1001
#define IDC_BTN_QUIT   1002

// ID of progress bar
#define IDC_PBD        1003
#define IDC_PBI        1004

// ID of label
#define IDC_LABEL      1005

// ID of icon
#define IDC_ICONAPP    1006

// Options for progress bar
#define PBS_MARQUEE  0x08
#define PBM_SETMARQUEE WM_USER + 10

// Options for cURL
#define TIMEOUT     30
#define USERAGENT   "framapack"

// Options pour les proxys et les options de framapack
#define INI_FILE ".\\framapack.ini"
#define CACHE_FOLDER ".\\framapack"

// Options pour les informations de version
#define __FILEVERSION__         1,2,0,0
#define __PRODUCTVERSION__      1,2,0,0
#define __SZFILEVERSION__      "1, 2, 0, 0\0"
#define __SZPRODUCTVERSION__   "1, 2, 0, 0\0"

#define __COMMENTS__         "Gestionnaire de téléchargement et d'installation d'applications libres. Logiciel sous licence GNU/GPL v2."
#define __COMPANYNAME__      "Framasoft.net\0"
#define __FILEDESCRIPTION__  "Installation de logiciels libres\0"
#define __INTERNALNAME__     "Framapack"
#define __LEGALCOPYRIGHT__   "Copyright Simon Leblanc © 2009\0"
#define __ORIGINALFILENAME__ "Framapack.exe\0"
#define __PRODUCTNAME__      "Framapack\0"
