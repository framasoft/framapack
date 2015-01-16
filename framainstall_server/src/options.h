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

#ifndef OPTIONS_H_INCLUDED
#define OPTIONS_H_INCLUDED

#include <stdlib.h>
#include <windows.h>
#include <direct.h>

class Options
{
    private:
        static bool         cache;
        static bool         install;

        static void         getCache();
        static void         getInstall();

    public:
        static void         init();
        static bool         createCacheFolder();
        static bool         useCache();
        static bool         useInstall();

};

/**
 * Déclaration des variables statiques
 */
bool Options::cache           = false;
bool Options::install         = true;


/**
 * Récupère le fait d'utiliser le cache
 */
void Options::getCache()
{
    int tmp_cache = GetPrivateProfileInt("options", "cache", 0, INI_FILE);
    if (tmp_cache == 1) {
        Options::cache = true;
    }
}

/**
 * Récupère le fait d'installer lors de l'utilisation du cache
 */
void Options::getInstall()
{
    int tmp_install = GetPrivateProfileInt("options", "install", 1, INI_FILE);
    if (tmp_install == 0) {
        Options::install = false;
    }
}


/**
 * Initialise les variables des options
 */
void Options::init()
{
    Options::getCache();
    Options::getInstall();
}


/**
 * Crée si nécessaire le dossier pour le cache
 *
 * @return  int  -1 si erreur, >= 0 si pas d'erreur
 */
bool Options::createCacheFolder()
{
    int attr = GetFileAttributes(CACHE_FOLDER);

    if (attr == FILE_ATTRIBUTE_DIRECTORY) {
        // Le dossier existe
        return 0;
    } else {
        // Le dossier n'existe pas, on le crée
        return _mkdir(CACHE_FOLDER);
    }
}

/**
 * Indique si l'on doit utiliser le cache ou non
 *
 * @return  bool   Vrai si l'on doit utiliser le cache, Faux sinon
 */
bool Options::useCache()
{
    return Options::cache;
}


/**
 * Indique si l'on doit installer en utilisant le cache ou non
 *
 * @return  bool   Vrai si l'on doit installer en utilisant le cache, Faux sinon
 */
bool Options::useInstall()
{
    return Options::install;
}
#endif
