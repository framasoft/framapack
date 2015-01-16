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
 * @param  HWND  hwndDlg  Le handler de la fen�tre principale
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
 * @param  HWND  hwndDlg  Le handler de la fen�tre principale
 */
void showProgressBarInstall(HWND hwndDlg)
{
    // On cache la barre de progression de t�l�chargement
    ShowWindow(GetDlgItem(hwndDlg, IDC_PBD), false);

    // On affiche la barre de progression d'installation
    ShowWindow(GetDlgItem(hwndDlg, IDC_PBI), true);
}


/**
 * Permet de cacher le bouton Annuler et d'afficher le bouton Fermer
 * @param  HWND  hwndDlg  Le handler de la fen�tre principale
 */
void showProgressBarDownload(HWND hwndDlg)
{
    // On cache la barre de progression d'installation
    ShowWindow(GetDlgItem(hwndDlg, IDC_PBI), false);

    // On affiche la barre de progression de t�l�chargement
    ShowWindow(GetDlgItem(hwndDlg, IDC_PBD), true);
}


/**
 * Annule le t�l�chargement et permet � l'utilisateur de quitter
 */
void cancelDownload(HWND hwndDlg)
{
    // On indique que l'on annule le t�l�chargement
    ProgressBar::setCancel();

    // On cache le bouton Annuler et on affiche le bouton Quitter
    showQuit(hwndDlg);
}


/**
 * Permet de griser le bouton Annuler
 * @param  HWND  hwndDlg  Le handler de la fen�tre principale
 */
void inactiveCancel(HWND hwndDlg)
{
    EnableWindow(GetDlgItem(hwndDlg, IDC_BTN_CANCEL), false);
}


/**
 * Permet de rendre actif le bouton Annuler
 * @param  HWND  hwndDlg  Le handler de la fen�tre principale
 */
void activeCancel(HWND hwndDlg)
{
    EnableWindow(GetDlgItem(hwndDlg, IDC_BTN_CANCEL), true);
}


/**
 * T�l�charge un fichier et le sauvegarde en local
 * @param  std::string   url        L'url du fichier � t�l�charger
 * @param  std::string   filename   Le lien du fichier dans lequel sauvegarder le t�l�chargement
 * @param  bool                     Vrai si le t�l�chargement s'est bien d�roul�, faux sinon
 */
bool getData(std::string url, std::string filename)
{
    // Si on utilise le cache, on v�rifie que le fichier n'est pas pr�sent
    if (Options::useCache()) {
        std::ifstream check_file(filename.c_str());
        if (!check_file.fail()) {
            // Le fichier est pr�sent, on l'utilise
            return true;
        }
    }

    // On cr�e le fichier temporaire
    std::FILE* file = std::fopen(filename.c_str(), "wb");

    // On cr�e l'instance cURL
    CURL* easyhandle = curl_easy_init();

    // On ajoute les options � cURL
    curl_easy_setopt(easyhandle, CURLOPT_URL, url.c_str()); // l'url a t�l�charger
    curl_easy_setopt(easyhandle, CURLOPT_WRITEFUNCTION, &fwrite); // la fonction a utiliser pour �crire le fichier
    curl_easy_setopt(easyhandle, CURLOPT_WRITEDATA, file); // le fichier dans lequel �crire
    curl_easy_setopt(easyhandle, CURLOPT_NOPROGRESS, 0); // On veut la progression du t�l�chargement
    curl_easy_setopt(easyhandle, CURLOPT_PROGRESSFUNCTION, ProgressBar::setPos); // la fonction � utiliser pour la progression
    curl_easy_setopt(easyhandle, CURLOPT_FOLLOWLOCATION, 1); // On veut suivre les redirections
    curl_easy_setopt(easyhandle, CURLOPT_CONNECTTIMEOUT, TIMEOUT); // Si le serveur ne r�pond pas, on ne continue pas
    curl_easy_setopt(easyhandle, CURLOPT_USERAGENT, USERAGENT); // On indique le user agent

    // On d�fini le proxy si n�cessaire
    Proxy::setProxy(easyhandle);

    // On lance le t�l�chargement
    curl_easy_perform(easyhandle);

    // On r�cup�re le code de r�ponse du serveur
    int code;
    curl_easy_getinfo(easyhandle, CURLINFO_HTTP_CODE, &code);

    // T�l�chargement fini, on nettoie cURL
    curl_easy_cleanup(easyhandle);

    // On ferme le fichier ouvert
    fclose(file);

    // On renvoie vrai si le t�l�chargement s'est bien d�roul�, faux sinon
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
    STARTUPINFO startupInfo;         // Info de d�marrage du processus
    PROCESS_INFORMATION processInfo; // R�cup�ration d'infos sur le processu cr��
    bool res;                        // Renvoy� par CreateProcess

    // On transforme la commandeen char*
    char* command;
    command = new char[install.length() + 1];
    strcpy(command, install.c_str());

    // Initialisation des infos de d�marrage
    ZeroMemory(&startupInfo, sizeof(startupInfo));
    startupInfo.cb = sizeof(startupInfo);

    startupInfo.dwFlags = STARTF_USESHOWWINDOW;
    startupInfo.wShowWindow = SW_HIDE;
    res = CreateProcess(NULL,         // On va se servir de lpCommandLine
                        command,
                        NULL, NULL,   // Pas de s�cu
                        FALSE,        // Pas d'h�ritage des handles
                        0,            // Pas de flags de cr�ation
                        NULL,         // M�me environement
                        NULL,         // M�me r�pertoire
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
 * Effectue les diff�rentes �tapes de t�l�chargement et d'installation de l'application
 * @param   std::string   url      L'url du fichier d'installation � t�l�charger
 * @param   std::string   file     Le lien du fichier d'installation en local
 * @param   std::string   install  La commande d'installation silencieuse
 * @param   HWND          hwndDlg  Le handler de la fen�tre principale
 */
void downloadAndInstall(std::string url, std::string file, std::string install, std::string name, HWND hwndDlg)
{
    std::string label;
    bool res;

    // Modification du label
    label = "T�l�chargement de " + name;
    SetDlgItemText(hwndDlg, IDC_LABEL, label.c_str());

    // On initialise la valeur de la barre de progression
    ProgressBar::reset();

    // On affiche la barre de progression pour le t�l�chargement
    showProgressBarDownload(hwndDlg);

    // On rend actif le bouton Annuler
    activeCancel(hwndDlg);

    // On t�l�charge l'application
    res = getData(url, file);

    // On rend inactif le bouton Annuler
    inactiveCancel(hwndDlg);

    // Si le transfert n'a pas �t� annul� ou �chou�, on installe l'application
    if (res == false) {
        std::string error = "Le t�l�chargement de " + name + " a �chou� !";
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
        // Si on n'utilise pas le cache et qu'il n'y a pas d'erreur ou que le t�l�chargement a �t� annul�
        // On supprime le fichier temporaire t�l�charg�
        if (remove(file.c_str()) != 0) {
            perror( "Error deleting file" );
        }
    }
}


/**
 * Fonction utilis�e pour le thread d'installation
 */
DWORD WINAPI installAll(LPVOID lpParameter)
{
    HWND hwndDlg = (HWND)lpParameter;

    // Initialisation de la barre de progression pour le t�l�chargement
    ProgressBar h_bar = ProgressBar(GetDlgItem(hwndDlg, IDC_PBD));


    // On cr�e l'instance pour les applications � t�l�charger/installer
    std::string* applications[4];

    try {
        Apps apps = Apps();

        // pour chaque application, on la t�l�charge et on l'installe
        while (apps.nextApp(applications)) {
            // lancement du t�l�chargement et de l'installation
            downloadAndInstall(*applications[0], *applications[1], *applications[2], *applications[3], hwndDlg);

            // Si l'utilisateur a annul�, on quitte le thread
            if (ProgressBar::isCancel() == true) {
                // On r�-initialise la barre de progression
                ProgressBar::reset(false);

                // Modification du label
                SetDlgItemText(hwndDlg, IDC_LABEL, "L'installation des applications a �t� annul�e.");

                // On indique au programme que le thread est fini
                SetEvent(g_event);
                return (DWORD)0;
            }
        }

        // On affiche la barre de progression � 100% pour signifier que c'est fini
        showProgressBarDownload(hwndDlg);
        ProgressBar::all();

        // On cache le bouton Annuler et on affiche le bouton Quitter
        showQuit(hwndDlg);

        // Modification du label
        if (Options::useInstall()) {
            SetDlgItemText(hwndDlg, IDC_LABEL, "Les applications ont �t� install�es");
        } else {
            SetDlgItemText(hwndDlg, IDC_LABEL, "Les applications ont �t� t�l�charg�es");
        }

        // On indique � l'utilisateur que c'est fini
        if (Options::useInstall()) {
            MessageBox(hwndDlg, "Les applications ont �t� install�es", "Information", MB_ICONINFORMATION);
        } else {
            MessageBox(hwndDlg, "Les applications ont �t� t�l�charg�es", "Information", MB_ICONINFORMATION);
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
 * Affichage de la fen�tre
 */
BOOL CALLBACK DialogProc(HWND hwndDlg, UINT uMsg, WPARAM wParam, LPARAM lParam)
{
    switch(uMsg)
    {
        case WM_INITDIALOG:
            // On d�sactive le bouton Fermer
            SetClassLongPtr(hwndDlg, GCL_STYLE, GetClassLongPtr(hwndDlg, GCL_STYLE) | CS_NOCLOSE | WS_OVERLAPPED | WS_SYSMENU | WS_MINIMIZEBOX);

            // Initialisation des contr�le commun pour
            // la gestion de la barre de progression
            InitCommonControls();

            // Initialisation de la barre de progression pour l'installation
            SendMessage(GetDlgItem(hwndDlg, IDC_PBI), PBM_SETMARQUEE, (WPARAM)true, (LPARAM)(UINT)100);

            // On affiche l'ic�ne de l'application
            char buf[1024];
            GetModuleFileName(0, (LPCH)&buf, 1024);
            //HINSTANCE instance = GetModuleHandle(buf);
            /*HICON hIcon = LoadIcon(instance, MAKEINTRESOURCE(IDC_ICONAPP));
            if (hIcon) {
                SendMessage(hwndDlg, WM_SETICON, 1, (LPARAM)hIcon);
                SendMessage(hwndDlg, WM_SETICON, 0, (LPARAM)hIcon);
            }*/


            // On lance le thread d'installation
            // On lui passe le handler de la fen�tre pour qu'il
            // puisse contr�ler les barres de progression
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


