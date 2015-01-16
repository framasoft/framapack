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

#ifndef PROGRESSBAR_H_INCLUDED
#define PROGRESSBAR_H_INCLUDED

#include <windows.h>
#include <commctrl.h>
#include <stdexcept>

class ProgressBar
{
    private:
        static HWND   h_bar;
        static bool   cancel;

    public:
                      ProgressBar(HWND hwndbar);
        static  int   setPos(void *p, double dltotal, double dlnow, double ult, double uln);
        static  void  reset(bool needCancel = true);
        static  void  setCancel();
        static  bool  isCancel();
        static  void  all();
};

/**
 * Déclaration des variables statiques
 */
HWND ProgressBar::h_bar  = NULL;
bool ProgressBar::cancel = false;

/**
 * Constructeur de la barre de progression
 * Initialise la variable static gérant la barre
 */
ProgressBar::ProgressBar(HWND hwndbar) //Constructor
{
    h_bar = hwndbar;

    if (h_bar == NULL) {
        throw std::logic_error("Error in init hbar");
    }

    if(SendMessage(h_bar, PBM_SETRANGE, 0, MAKELPARAM(0, 100)) == 0) {
        throw std::logic_error("Error in range");
    }
}


/**
 * Met à jour la barre de progression en fonction
 * du téléchargement effectué et du total à effectuer
 */
int ProgressBar::setPos(void *p, double dltotal, double dlnow, double ult, double uln)
{
    SendMessage(h_bar, PBM_SETPOS, (WPARAM)(dlnow / dltotal * 100), 0);

    if (cancel) {
        return 1;
    } else {
        return 0;
    }
}


/**
 * On initialise la barre de progression à zéro
 */
void ProgressBar::reset(bool needCancel)
{
     if (needCancel) {
         cancel = false;
     }

     SendMessage(h_bar, PBM_SETPOS, (WPARAM)0, 0);
}


/**
 * On indique que l'on doit annuler le transfert
 */
void ProgressBar::setCancel()
{
     cancel = true;
}


/**
 * Retourne la valeur de cancel
 */
bool ProgressBar::isCancel()
{
    return cancel;
}


/**
 * Met la barre a 100%
 */
void ProgressBar::all()
{
    SendMessage(h_bar, PBM_SETPOS, (WPARAM)100, 0);
}
#endif
