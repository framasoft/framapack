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

#define WIN32_LEAN_AND_MEAN

#include <windows.h>
#include <commctrl.h>
#include <cstdio>
#include <stdlib.h>
#include <string>
#include <fstream>
#include "cstdlib"
#include "curl/curl.h"
#include "resource.h"
#include "progress.h"
#include "apps.h"
#include "options.h"
#include "proxy.h"


HINSTANCE hInst;
HANDLE g_event;


/**
 * Permet de cacher le bouton Annuler et d'afficher le bouton Fermer
 * @param  HWND  hwndDlg  Le handler de la fenêtre principale
 */
void showQuit(HWND hwndDlg)
{
    // On cache le bouton Annuler
    ShowWindow(GetDlgItem(hwndDlg, IDC_BTN_CANCEL), false);

    // On affiche le bouton Quitter
    ShowWindow(GetDlgItem(hwndDlg, IDC_BTN_QUIT), true);
}


/**
 * Permet de cacher le bouton Annuler et d'afficher le bouton Fermer
 * @param  HWND  hwndDlg  Le handler de la fenêtre principale
 */
void showProgressBarInstall(HWND hwndDlg)
{
    // On cache la barre de progression de téléchargement
    ShowWindow(GetDlgItem(hwndDlg, IDC_PBD), false);

    // On affiche la barre de progression d'installation
    ShowWindow(GetDlgItem(hwndDlg, IDC_PBI), true);
}


/**
 * Permet de cacher le bouton Annuler et d'afficher le bouton Fermer
 * @param  HWND  hwndDlg  Le handler de la fenêtre principale
 */
void showProgressBarDownload(HWND hwndDlg)
{
    // On cache la barre de progression d'installation
    ShowWindow(GetDlgItem(hwndDlg, IDC_PBI), false);

    // On affiche la barre de progression de téléchargement
    ShowWindow(GetDlgItem(hwndDlg, IDC_PBD), true);
}


/**
 * Annule le téléchargement et permet à l'utilisateur de quitter
 */
void cancelDownload(HWND hwndDlg)
{
    // On indique que l'on annule le téléchargement
    ProgressBar::setCancel();

    // On cache le bouton Annuler et on affiche le bouton Quitter
    showQuit(hwndDlg);
}


/**
 * Permet de griser le bouton Annuler
 * @param  HWND  hwndDlg  Le handler de la fenêtre principale
 */
void inactiveCancel(HWND hwndDlg)
{
    EnableWindow(GetDlgItem(hwndDlg, IDC_BTN_CANCEL), false);
}


/**
 * Permet de rendre actif le bouton Annuler
 * @param  HWND  hwndDlg  Le handler de la fenêtre principale
 */
void activeCancel(HWND hwndDlg)
{
    EnableWindow(GetDlgItem(hwndDlg, IDC_BTN_CANCEL), true);
}


/**
 * Télécharge un fichier et le sauvegarde en local
 * @param  std::string   url        L'url du fichier à télécharger
 * @param  std::string   filename   Le lien du fichier dans lequel sauvegarder le téléchargement
 * @param  bool                     Vrai si le téléchargement s'est bien déroulé, faux sinon
 */
bool getData(std::string url, std::string filename)
{
    // Si on utilise le cache, on vérifie que le fichier n'est pas présent
    if (Options::useCache()) {
        std::ifstream check_file(filename.c_str());
        if (!check_file.fail()) {
            // Le fichier est présent, on l'utilise
            return true;
        }
    }

    // On crée le fichier temporaire
    std::FILE* file = std::fopen(filename.c_str(), "wb");

    // On crée l'instance cURL
    CURL* easyhandle = curl_easy_init();

    // On ajoute les options à cURL
    curl_easy_setopt(easyhandle, CURLOPT_URL, url.c_str()); // l'url a télécharger
    curl_easy_setopt(easyhandle, CURLOPT_WRITEFUNCTION, &fwrite); // la fonction a utiliser pour écrire le fichier
    curl_easy_setopt(easyhandle, CURLOPT_WRITEDATA, file); // le fichier dans lequel écrire
    curl_easy_setopt(easyhandle, CURLOPT_NOPROGRESS, 0); // On veut la progression du téléchargement
    curl_easy_setopt(easyhandle, CURLOPT_PROGRESSFUNCTION, ProgressBar::setPos); // la fonction à utiliser pour la progression
    curl_easy_setopt(easyhandle, CURLOPT_FOLLOWLOCATION, 1); // On veut suivre les redirections
    curl_easy_setopt(easyhandle, CURLOPT_CONNECTTIMEOUT, TIMEOUT); // Si le serveur ne répond pas, on ne continue pas
    curl_easy_setopt(easyhandle, CURLOPT_USERAGENT, USERAGENT); // On indique le user agent

    // On défini le proxy si nécessaire
    Proxy::setProxy(easyhandle);

    // On lance le téléchargement
    curl_easy_perform(easyhandle);

    // On récupère le code de réponse du serveur
    int code;
    curl_easy_getinfo(easyhandle, CURLINFO_HTTP_CODE, &code);

    // Téléchargement fini, on nettoie cURL
    curl_easy_cleanup(easyhandle);

    // On ferme le fichier ouvert
    fclose(file);

    // On renvoie vrai si le téléchargement s'est bien déroulé, faux sinon
    if (code == 200) {
        return true;
    } else {
        return false;
    }
}


/**
 * Execute l'installation d'une application
 * @param  std::string   install    La commande d'installation silencieuse
 */
void installApp(std::string install)
{
    STARTUPINFO startupInfo;         // Info de démarrage du processus
    PROCESS_INFORMATION processInfo; // Récupération d'infos sur le processu créé
    bool res;                        // Renvoyé par CreateProcess

    // On transforme la commandeen char*
    char* command;
    command = new char[install.length() + 1];
    strcpy(command, install.c_str());

    // Initialisation des infos de démarrage
    ZeroMemory(&startupInfo, sizeof(startupInfo));
    startupInfo.cb = sizeof(startupInfo);

    startupInfo.dwFlags = STARTF_USESHOWWINDOW;
    startupInfo.wShowWindow = SW_HIDE;
    res = CreateProcess(NULL,         // On va se servir de lpCommandLine
                        command,
                        NULL, NULL,   // Pas de sécu
                        FALSE,        // Pas d'héritage des handles
                        0,            // Pas de flags de création
                        NULL,         // Même environement
                        NULL,         // Même répertoire
                        &startupInfo,
                        &processInfo);

    // Attente de la fin du processus si besoin
    if (res) {
        WaitForSingleObject(processInfo.hProcess, INFINITE);
    }

    // On supprime le pointeur
    delete [] command;
}


/**
 * Effectue les différentes étapes de téléchargement et d'installation de l'application
 * @param   std::string   url      L'url du fichier d'installation à télécharger
 * @param   std::string   file     Le lien du fichier d'installation en local
 * @param   std::string   install  La commande d'installation silencieuse
 * @param   HWND          hwndDlg  Le handler de la fenêtre principale
 */
void downloadAndInstall(std::string url, std::string file, std::string install, std::string name, HWND hwndDlg)
{
    std::string label;
    bool res;

    // Modification du label
    label = "Téléchargement de " + name;
    SetDlgItemText(hwndDlg, IDC_LABEL, label.c_str());

    // On initialise la valeur de la barre de progression
    ProgressBar::reset();

    // On affiche la barre de progression pour le téléchargement
    showProgressBarDownload(hwndDlg);

    // On rend actif le bouton Annuler
    activeCancel(hwndDlg);

    // On télécharge l'application
    res = getData(url, file);

    // On rend inactif le bouton Annuler
    inactiveCancel(hwndDlg);

    // Si le transfert n'a pas été annulé ou échoué, on installe l'application
    if (res == false) {
        std::string error = "Le téléchargement de " + name + " a échoué !";
        MessageBox(hwndDlg, error.c_str(), "Erreur", MB_ICONERROR);
    } else if (ProgressBar::isCancel() == false && Options::useInstall()) {
        // Modification du label
        label = "Installation de " + name;
        SetDlgItemText(hwndDlg, IDC_LABEL, label.c_str());

        // On affiche la barre de progression pour l'installation
        showProgressBarInstall(hwndDlg);

        // On installe l'application
        installApp(install);

        Sleep(500); // SUPER CRADE!!!! un petit sleep pour laisser le temps au process de bien se fermer
    }

    if (!Options::useCache() && res != false || (ProgressBar::isCancel() == true)) {
        // Si on n'utilise pas le cache et qu'il n'y a pas d'erreur ou que le téléchargement a été annulé
        // On supprime le fichier temporaire téléchargé
        if (remove(file.c_str()) != 0) {
            perror( "Error deleting file" );
        }
    }
}


/**
 * Fonction utilisée pour le thread d'installation
 */
DWORD WINAPI installAll(LPVOID lpParameter)
{
    HWND hwndDlg = (HWND)lpParameter;

    // Initialisation de la barre de progression pour le téléchargement
    ProgressBar h_bar = ProgressBar(GetDlgItem(hwndDlg, IDC_PBD));


    // On crée l'instance pour les applications à télécharger/installer
    std::string* applications[4];

    try {
        Apps apps = Apps();

        // pour chaque application, on la télécharge et on l'installe
        while (apps.nextApp(applications)) {
            // lancement du téléchargement et de l'installation
            downloadAndInstall(*applications[0], *applications[1], *applications[2], *applications[3], hwndDlg);

            // Si l'utilisateur a annulé, on quitte le thread
            if (ProgressBar::isCancel() == true) {
                // On ré-initialise la barre de progression
                ProgressBar::reset(false);

                // Modification du label
                SetDlgItemText(hwndDlg, IDC_LABEL, "L'installation des applications a été annulée.");

                // On indique au programme que le thread est fini
                SetEvent(g_event);
                return (DWORD)0;
            }
        }

        // On affiche la barre de progression à 100% pour signifier que c'est fini
        showProgressBarDownload(hwndDlg);
        ProgressBar::all();

        // On cache le bouton Annuler et on affiche le bouton Quitter
        showQuit(hwndDlg);

        // Modification du label
        if (Options::useInstall()) {
            SetDlgItemText(hwndDlg, IDC_LABEL, "Les applications ont été installées");
        } else {
            SetDlgItemText(hwndDlg, IDC_LABEL, "Les applications ont été téléchargées");
        }

        // On indique à l'utilisateur que c'est fini
        if (Options::useInstall()) {
            MessageBox(hwndDlg, "Les applications ont été installées", "Information", MB_ICONINFORMATION);
        } else {
            MessageBox(hwndDlg, "Les applications ont été téléchargées", "Information", MB_ICONINFORMATION);
        }

        // On indique au programme que le thread est fini
        SetEvent(g_event);

    } catch (std::string e) {
        MessageBox(hwndDlg, e.c_str(), "Error", MB_ICONERROR);

        // On cache le bouton Annuler et on affiche le bouton Quitter
        showQuit(hwndDlg);

        // Modification du label
        SetDlgItemText(hwndDlg, IDC_LABEL, "Impossible d'installer les applications !");

        // On indique au programme que le thread est fini
        SetEvent(g_event);
    }

    return (DWORD)0;
}


/**
 * Affichage de la fenêtre
 */
BOOL CALLBACK DialogProc(HWND hwndDlg, UINT uMsg, WPARAM wParam, LPARAM lParam)
{
    switch(uMsg)
    {
        case WM_INITDIALOG:
            // On désactive le bouton Fermer
            SetClassLongPtr(hwndDlg, GCL_STYLE, GetClassLongPtr(hwndDlg, GCL_STYLE) | CS_NOCLOSE | WS_OVERLAPPED | WS_SYSMENU | WS_MINIMIZEBOX);

            // Initialisation des contrôle commun pour
            // la gestion de la barre de progression
            InitCommonControls();

            // Initialisation de la barre de progression pour l'installation
            SendMessage(GetDlgItem(hwndDlg, IDC_PBI), PBM_SETMARQUEE, (WPARAM)true, (LPARAM)(UINT)100);

            // On affiche l'icône de l'application
            char buf[1024];
            GetModuleFileName(0, (LPCH)&buf, 1024);
            //HINSTANCE instance = GetModuleHandle(buf);
            /*HICON hIcon = LoadIcon(instance, MAKEINTRESOURCE(IDC_ICONAPP));
            if (hIcon) {
                SendMessage(hwndDlg, WM_SETICON, 1, (LPARAM)hIcon);
                SendMessage(hwndDlg, WM_SETICON, 0, (LPARAM)hIcon);
            }*/


            // On lance le thread d'installation
            // On lui passe le handler de la fenêtre pour qu'il
            // puisse contrôler les barres de progression
            DWORD threadID;
            g_event = CreateThread(NULL, 0, installAll, hwndDlg, 0, &threadID);

            return TRUE;

        case WM_CLOSE:
            EndDialog(hwndDlg, 0);
            return TRUE;

        case WM_COMMAND:
            switch(LOWORD(wParam))
            {
                case IDC_BTN_QUIT:
                    EndDialog(hwndDlg, 0);
                    return TRUE;

                case IDC_BTN_CANCEL:
                    cancelDownload(hwndDlg);
                    return TRUE;
            }
    }

    return FALSE;
}


/**
 * Lancement du programme principal
 */
int APIENTRY WinMain(HINSTANCE hInstance, HINSTANCE hPrevInstance, LPSTR lpCmdLine, int nShowCmd)
{
    hInst = hInstance;

    // The user interface is a modal dialog box
    return DialogBox(hInstance, MAKEINTRESOURCE(DLG_MAIN), NULL, DialogProc);
}


