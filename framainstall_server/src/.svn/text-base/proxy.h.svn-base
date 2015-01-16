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

#ifndef PROXY_H_INCLUDED
#define PROXY_H_INCLUDED

#include <cstdio>
#include <stdlib.h>
#include <string>
#include <windows.h>

#include "curl/curl.h"

class Proxy
{
    private:
        static bool         is_init;
        static bool         use_proxy;
        static std::string  server;
        static std::string  username;
        static std::string  password;
        static int          port;

        static void         getServer();
        static void         getPort();
        static void         getUsername();
        static void         getPassword();
        static bool         useProxy();

    public:
        static void         setProxy(CURL* easyhandle);

};

/**
 * Déclaration des variables statiques
 */
bool Proxy::is_init         = false;
bool Proxy::use_proxy       = false;
std::string Proxy::server   = "";
int Proxy::port             = 0;
std::string Proxy::username = "";
std::string Proxy::password = "";


/**
 * Récupère l'adresse du proxy
 */
void Proxy::getServer()
{
    char tmp_server[256];
    int nb_char = 0;
    nb_char = GetPrivateProfileString("proxy", "server", "", tmp_server, sizeof(tmp_server), INI_FILE);

    if (nb_char > 0) {
        Proxy::server = (std::string) tmp_server;
    } else {
        Proxy::server = "";
    }
}

/**
 * Récupère le port du proxy
 */
void Proxy::getPort()
{
    Proxy::port = GetPrivateProfileInt("proxy", "port", 0, INI_FILE);
}

/**
 * Récupère le nom d'utilisateur du proxy
 */
void Proxy::getUsername()
{
    char tmp_username[256];
    int nb_char = 0;
    nb_char = GetPrivateProfileString("proxy", "username", "", tmp_username, sizeof(tmp_username), INI_FILE);

    if (nb_char > 0) {
        Proxy::username = (std::string) tmp_username;
    } else {
        Proxy::username = "";
    }
}

/**
 * Récupère le mot de passe du proxy
 */
void Proxy::getPassword()
{
    char tmp_password[256];
    int nb_char = 0;
    nb_char = GetPrivateProfileString("proxy", "password", "", tmp_password, sizeof(tmp_password), INI_FILE);

    if (nb_char > 0) {
        Proxy::password = (std::string) tmp_password;
    } else {
        Proxy::password = "";
    }
}


/**
 * Retourne si l'on doit utiliser un proxy, si on utilise
 * un proxy, on récupère les données nécessaire
 */
bool Proxy::useProxy()
{
    if (is_init == true) {
        return use_proxy;
    }

    FILE *file = fopen(INI_FILE, "r");

    if (!file) {
        is_init = true;
        use_proxy = false;
        return use_proxy;
    } else {
        is_init = true;

        getServer();
        getUsername();
        getPassword();
        getPort();

        fclose(file);

        use_proxy = true;
        return use_proxy;
    }
}

/**
 * Initialise la connexion sur un proxy si nécessaire
 */
void Proxy::setProxy(CURL* easyhandle)
{
    if (useProxy()) {
        if (server != "") {
            curl_easy_setopt(easyhandle, CURLOPT_PROXY, server.c_str());

            if (port != 0) {
                curl_easy_setopt(easyhandle, CURLOPT_PROXYPORT, port);
            }

            if (username != "" && password != "") {
                std::string userpwd = username + ":" + password;
                char* userpassword = new char[userpwd.length() + 1];
                strcpy(userpassword, userpwd.c_str());
                curl_easy_setopt(easyhandle, CURLOPT_PROXYUSERPWD, userpassword);
            }
        }
    }
}
#endif
