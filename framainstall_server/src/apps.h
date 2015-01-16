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

#ifndef APPS_H_INCLUDED
#define APPS_H_INCLUDED

#include <stdlib.h>
#include <string>

#include "options.h"

class Apps
{
    protected:
        std::string     apps[55];
        std::string     url[55];
        std::string     install[55];
        std::string     name[55];
        int             nb;
        int             current;
        
        void            add(std::string url, std::string apps, std::string install, std::string name);

    public:
                        Apps();
        int             getNb() { return this->nb; };
        bool            nextApp(std::string* application[4]);
};

Apps::Apps()
{
    this->current = 0;
    this->nb = 0;

    std::string temp = "";

    Options::init();

    if (Options::useCache()) {
        if (Options::createCacheFolder() != -1) {
            temp = CACHE_FOLDER;
        } else {
            throw std::string("Error in the creation of the cache directory !");
        }
    } else {
        temp = std::getenv("TEMP");
    }

    
#ifdef ZIP6
    this->add("http://downloads.sourceforge.net/project/sevenzip/7-Zip/9.20/7z920.exe", temp + "\\framapack-zip6.exe", "" + temp + "\\framapack-zip6.exe" + " /S", "7-Zip");
#endif
    
#ifdef ABIWORD18
    this->add("http://www.abisource.com/downloads/abiword/2.8.6/Windows/abiword-setup-2.8.6.exe", temp + "\\framapack-abiword18.exe", "" + temp + "\\framapack-abiword18.exe" + " /S", "AbiWord");
#endif
    
#ifdef ALBUMSHAPER36
    this->add("http://downloads.sourceforge.net/project/albumshaper/albumshaper/2.1/albumshaper_2.1_win.exe", temp + "\\framapack-albumshaper36.exe", "" + temp + "\\framapack-albumshaper36.exe" + " /S", "Album Shaper");
#endif
    
#ifdef AUDACITY3
    this->add("http://sourceforge.net/projects/audacity/files/audacity-win-2.0.6.exe", temp + "\\framapack-audacity3.exe", "" + temp + "\\framapack-audacity3.exe" + " /VERYSILENT", "Audacity");
#endif
    
#ifdef AVIDEMUX30
    this->add("http://sourceforge.net/projects/avidemux/files/avidemux/2.6.8/avidemux_2.6.8_win32_v2.exe", temp + "\\framapack-avidemux30.exe", "" + temp + "\\framapack-avidemux30.exe" + " /S", "Avidemux");
#endif
    
#ifdef BLENDER34
    this->add("http://ftp.halifax.rwth-aachen.de/blender/release/Blender2.72/blender-2.72b-windows32.exe", temp + "\\framapack-blender34.exe", "" + temp + "\\framapack-blender34.exe" + " /S", "Blender");
#endif
    
#ifdef CALIBRE61
    this->add("http://download.calibre-ebook.com/2.12.0/calibre-2.12.0.msi", temp + "\\framapack-calibre61.msi", "msiexec /i " + temp + "\\framapack-calibre61.msi" + " /qn", "Calibre");
#endif
    
#ifdef CARMETAL52
    this->add("http://db-maths.nuxit.net/CaRMetal/download/carmetal_setup.exe", temp + "\\framapack-carmetal52.exe", "" + temp + "\\framapack-carmetal52.exe" + " /SP- /VERYSILENT /NOCANCEL /NORESTART", "CaRMetal");
#endif
    
#ifdef CDEX15
    this->add("http://sourceforge.net/projects/cdexos/files/cdexos/CDex%201.71/CDex-1.71-win32.exe", temp + "\\framapack-cdex15.exe", "" + temp + "\\framapack-cdex15.exe" + " /S", "CDex");
#endif
    
#ifdef CELESTIA51
    this->add("http://downloads.sourceforge.net/project/celestia/Celestia-win32-bin/1.6.1/celestia-win32-1.6.1.exe", temp + "\\framapack-celestia51.exe", "" + temp + "\\framapack-celestia51.exe" + " /SP- /VERYSILENT /NOCANCEL /NORESTART", "Celestia");
#endif
    
#ifdef CLAMWIN45
    this->add("http://downloads.sourceforge.net/clamwin/clamwin-0.98.4.1-setup.exe", temp + "\\framapack-clamwin45.exe", "" + temp + "\\framapack-clamwin45.exe" + " /VERYSILENT /NORESTART /NOERROR /NOTB", "ClamWin");
#endif
    
#ifdef CODEBLOCKS17
    this->add("http://sourceforge.net/projects/codeblocks/files/Binaries/13.12/Windows/codeblocks-13.12-setup.exe", temp + "\\framapack-codeblocks17.exe", "" + temp + "\\framapack-codeblocks17.exe" + " /S", "Code::Blocks");
#endif
    
#ifdef DELUGE44
    this->add("http://download.deluge-torrent.org/windows/deluge-1.3.10-win32-setup.exe", temp + "\\framapack-deluge44.exe", "" + temp + "\\framapack-deluge44.exe" + " /S /NCRC", "Deluge");
#endif
    
#ifdef DIA42
    this->add("http://sourceforge.net/projects/dia-installer/files/dia-win32-installer/0.97.2/dia-setup-0.97.2.exe", temp + "\\framapack-dia42.exe", "" + temp + "\\framapack-dia42.exe" + " /S", "Dia");
#endif
    
#ifdef DITTO32
    this->add("http://sourceforge.net/projects/ditto-cp/files/Ditto/3.19.24.0/DittoSetup_3_19_24_0.exe", temp + "\\framapack-ditto32.exe", "" + temp + "\\framapack-ditto32.exe" + " /VERYSILENT", "Ditto");
#endif
    
#ifdef FILEZILLA9
    this->add("http://sourceforge.net/projects/filezilla/files/FileZilla_Client/3.9.0.6/FileZilla_3.9.0.6_win32-setup.exe/download?nowrap", temp + "\\framapack-filezilla9.exe", "" + temp + "\\framapack-filezilla9.exe" + " /S", "FileZilla");
#endif
    
#ifdef FIREFOX4
    this->add("https://download.mozilla.org/?product=firefox-33.0.1-SSL&os=win&lang=fr", temp + "\\framapack-firefox4.exe", "" + temp + "\\framapack-firefox4.exe" + " -ms", "Firefox");
#endif
    
#ifdef FREECIV39
    this->add("http://prdownloads.sourceforge.net/freeciv/Freeciv-2.4.3-win32-gtk2-setup.exe?download", temp + "\\framapack-freeciv39.exe", "" + temp + "\\framapack-freeciv39.exe" + " /S", "Freeciv");
#endif
    
#ifdef FREEPLANE35
    this->add("http://sourceforge.net/projects/freeplane/files/freeplane%20stable/Freeplane-Setup-1.3.12.exe/download", temp + "\\framapack-freeplane35.exe", "" + temp + "\\framapack-freeplane35.exe" + " /VERYSILENT", "Freeplane");
#endif
    
#ifdef FRETSONFIRE41
    this->add("http://downloads.sourceforge.net/project/fretsonfire/fretsonfire/1.3.110/FretsOnFire-1.3.110-win32.exe", temp + "\\framapack-fretsonfire41.exe", "" + temp + "\\framapack-fretsonfire41.exe" + " /S", "Frets On Fire");
#endif
    
#ifdef GAJIM56
    this->add("http://gajim.org/downloads/gajim-0.16-1.exe", temp + "\\framapack-gajim56.exe", "" + temp + "\\framapack-gajim56.exe" + " /S", "Gajim");
#endif
    
#ifdef GIMP10
    this->add("http://download.gimp.org/pub/gimp/v2.8/windows/gimp-2.8.14-setup-1.exe", temp + "\\framapack-gimp10.exe", "" + temp + "\\framapack-gimp10.exe" + " /VERYSILENT /NORESTART", "GIMP");
#endif
    
#ifdef GNUCASH31
    this->add("http://downloads.sourceforge.net/sourceforge/gnucash/gnucash-2.6.4-2-setup.exe", temp + "\\framapack-gnucash31.exe", "" + temp + "\\framapack-gnucash31.exe" + " /VERYSILENT", "GnuCash");
#endif
    
#ifdef GNUMERIC19
    this->add("http://sourceforge.net/projects/gnumeric.mirror/files/gnumeric-1.12.17-20140610.exe", temp + "\\framapack-gnumeric19.exe", "" + temp + "\\framapack-gnumeric19.exe" + " /S", "Gnumeric");
#endif
    
#ifdef GREENSHOT60
    this->add("http://cznic.dl.sourceforge.net/project/greenshot/Greenshot/Greenshot%201.1/Greenshot-INSTALLER-1.1.9.13.exe", temp + "\\framapack-greenshot60.exe", "" + temp + "\\framapack-greenshot60.exe" + " /VERYSILENT /LANG=French", "Greenshot");
#endif
    
#ifdef INFRARECORDER33
    this->add("http://downloads.sourceforge.net/project/infrarecorder/InfraRecorder/0.53/ir053.exe", temp + "\\framapack-infrarecorder33.exe", "" + temp + "\\framapack-infrarecorder33.exe" + " /S", "InfraRecorder");
#endif
    
#ifdef INKSCAPE11
    this->add("http://sourceforge.net/projects/inkscape/files/inkscape/0.48.5/Inkscape-0.48.5-1-win32.exe", temp + "\\framapack-inkscape11.exe", "" + temp + "\\framapack-inkscape11.exe" + " /S", "Inkscape");
#endif
    
#ifdef KLAVARO55
    this->add("http://downloads.sourceforge.net/klavaro/Klavaro-2.00c-win.exe", temp + "\\framapack-klavaro55.exe", "" + temp + "\\framapack-klavaro55.exe" + " /verysilent", "Klavaro");
#endif
    
#ifdef KOMODOEDIT8
    this->add("http://downloads.activestate.com/Komodo/releases/8.5.4/Komodo-Edit-8.5.4-14424.msi", temp + "\\framapack-komodoedit8.msi", "msiexec.exe /i  " + temp + "\\framapack-komodoedit8.msi" + " /qn", "Komodo Edit");
#endif
    
#ifdef KOMPOZER50
    this->add("http://downloads.sourceforge.net/project/kompozer/current/0.8b3/windows/exe/kompozer-0.8b3.fr.win32.exe", temp + "\\framapack-kompozer50.exe", "" + temp + "\\framapack-kompozer50.exe" + " /SP- /VERYSILENT /NOCANCEL /NORESTART", "KompoZer");
#endif
    
#ifdef LAUNCHY12
    this->add("http://www.launchy.net/downloads/win/Launchy2.5.exe", temp + "\\framapack-launchy12.exe", "" + temp + "\\framapack-launchy12.exe" + " /VERYSILENT", "Launchy");
#endif
    
#ifdef LIBREOFFICE5
    this->add("http://libreoffice.mirrors.irontec.com/libreoffice/stable/4.3.4/win/x86/LibreOffice_4.3.4_Win_x86.msi", temp + "\\framapack-libreoffice5.msi", "msiexec /i " + temp + "\\framapack-libreoffice5.msi" + " /qn", "LibreOffice");
#endif
    
#ifdef NEVERBALL40
    this->add("http://neverball.org/neverball-1.5.4-setup.exe", temp + "\\framapack-neverball40.exe", "" + temp + "\\framapack-neverball40.exe" + " /S", "Neverball");
#endif
    
#ifdef NIGHTINGALE29
    this->add("http://sourceforge.net/projects/ngale/files/1.12.1-Release/Nightingale_1.12.1-2454_windows-i686.exe", temp + "\\framapack-nightingale29.exe", "" + temp + "\\framapack-nightingale29.exe" + " /SILENT /VERYSILENT", "Nightingale");
#endif
    
#ifdef NOTEPAD49
    this->add("http://download.tuxfamily.org/notepadplus/6.6.9/npp.6.6.9.Installer.exe", temp + "\\framapack-notepad49.exe", "" + temp + "\\framapack-notepad49.exe" + " /S", "Notepad++");
#endif
    
#ifdef OPENSANKOR63
    this->add("http://ftp.open-sankore.org/current/Windows%202.5.0/install/Open-Sankore_Setup.exe", temp + "\\framapack-opensankor63.exe", "" + temp + "\\framapack-opensankor63.exe" + " /VERYSILENT", "Open-Sankoré");
#endif
    
#ifdef PDFCREATOR59
    this->add("http://violet.download.pdfforge.org/pdfcreator/1.7.3/PDFCreator-1_7_3_setup.exe", temp + "\\framapack-pdfcreator59.exe", "" + temp + "\\framapack-pdfcreator59.exe" + " /ForceInstall /VERYSILENT /LANG=French /COMPONENTS='program,ghostscript' /NORESTART", "PDF Creator");
#endif
    
#ifdef PIDGIN2
    this->add("http://downloads.sourceforge.net/project/pidgin/Pidgin/2.10.10/pidgin-2.10.10.exe", temp + "\\framapack-pidgin2.exe", "" + temp + "\\framapack-pidgin2.exe" + " /DS=1 /SMS=1 /L=1036 /S", "Pidgin");
#endif
    
#ifdef POKERTH38
    this->add("http://sourceforge.net/projects/pokerth/files/pokerth/1.1/PokerTH-1.1.1-windows-installer.exe/download", temp + "\\framapack-pokerth38.exe", "" + temp + "\\framapack-pokerth38.exe" + " --mode unattended", "PokerTH");
#endif
    
#ifdef PUTTY7
    this->add("http://the.earth.li/~sgtatham/putty/0.63/x86/putty-0.63-installer.exe", temp + "\\framapack-putty7.exe", "" + temp + "\\framapack-putty7.exe" + " /VERYSILENT", "PuTTY");
#endif
    
#ifdef RSSOWL28
    this->add("http://downloads.sourceforge.net/project/rssowl/rssowl%202/2.1.4/RSSOwl%20Setup%202.1.4.exe", temp + "\\framapack-rssowl28.exe", "" + temp + "\\framapack-rssowl28.exe" + " /S", "RSSOwl");
#endif
    
#ifdef SCRATCH46
    this->add("http://download.scratch.mit.edu/ScratchInstaller1.4.exe", temp + "\\framapack-scratch46.exe", "" + temp + "\\framapack-scratch46.exe" + " /SP- /S", "Scratch");
#endif
    
#ifdef SCRIBUS22
    this->add("http://sourceforge.net/projects/scribus/files/scribus/1.4.4/scribus-1.4.4-windows.exe/download", temp + "\\framapack-scribus22.exe", "" + temp + "\\framapack-scribus22.exe" + " /S", "Scribus");
#endif
    
#ifdef SUMATRAPDF24
    this->add("http://kjkpub.s3.amazonaws.com/sumatrapdf/rel/SumatraPDF-3.0-install.exe", temp + "\\framapack-sumatrapdf24.exe", "" + temp + "\\framapack-sumatrapdf24.exe" + " /S", "Sumatra PDF");
#endif
    
#ifdef SWEETHOMED43
    this->add("http://sourceforge.net/projects/sweethome3d/files/SweetHome3D/SweetHome3D-4.5/SweetHome3D-4.5-windows.exe/download", temp + "\\framapack-sweethomed43.exe", "" + temp + "\\framapack-sweethomed43.exe" + " /VERYSILENT /NORESTART", "Sweet Home 3D");
#endif
    
#ifdef THUNDERBIRD13
    this->add("https://download.mozilla.org/?product=thunderbird-31.2.0&os=win&lang=fr", temp + "\\framapack-thunderbird13.exe", "" + temp + "\\framapack-thunderbird13.exe" + " -ms", "Thunderbird");
#endif
    
#ifdef TURTLESPORT62
    this->add("http://cznic.dl.sourceforge.net/project/turtlesport/turtlesport/1.6/turtlesport-win-1.6.exe", temp + "\\framapack-turtlesport62.exe", "" + temp + "\\framapack-turtlesport62.exe" + " /VERYSILENT", "Turtle Sport");
#endif
    
#ifdef TUXPAINT25
    this->add("http://sourceforge.net/projects/tuxpaint/files/tuxpaint/0.9.22/tuxpaint-0.9.22-win32-installer.exe/download", temp + "\\framapack-tuxpaint25.exe", "" + temp + "\\framapack-tuxpaint25.exe" + " /SP- /VERYSILENT /NORESTART /NOCANCEL", "Tux Paint");
#endif
    
#ifdef TUXTYPING37
    this->add("http://netcologne.dl.sourceforge.net/project/tuxtype/tuxtype-win32/TuxType%201.8.1%20-%20Windows/tuxtype-1.8.1-win32-installer.exe", temp + "\\framapack-tuxtyping37.exe", "" + temp + "\\framapack-tuxtyping37.exe" + " /S", "Tux Typing");
#endif
    
#ifdef TUXMATH21
    this->add("http://garr.dl.sourceforge.net/project/tuxmath/tuxmath-win32/TuxMath%202.0.2%20-%20Windows/tuxmath-2.0.2-win32-installer.exe", temp + "\\framapack-tuxmath21.exe", "" + temp + "\\framapack-tuxmath21.exe" + " /S", "TuxMath");
#endif
    
#ifdef VLC14
    this->add("http://get.videolan.org/vlc/2.1.5/win32/vlc-2.1.5-win32.exe", temp + "\\framapack-vlc14.exe", "" + temp + "\\framapack-vlc14.exe" + " /S", "VLC");
#endif
    
#ifdef WINSCP48
    this->add("http://netcologne.dl.sourceforge.net/project/winscp/WinSCP/5.5.6/winscp556setup.exe", temp + "\\framapack-winscp48.exe", "" + temp + "\\framapack-winscp48.exe" + " /S /LANG=French /VERYSILENT", "WinScp");
#endif
    
#ifdef XCAS47
    this->add("http://www-fourier.ujf-grenoble.fr/~parisse/giac/xcasinst.exe", temp + "\\framapack-xcas47.exe", "" + temp + "\\framapack-xcas47.exe" + " /SP- /S", "xcas");
#endif
    
#ifdef CLAMSENTINEL57
    this->add("http://downloads.sourceforge.net/project/clamsentinel/clamsentinel/1.22/ClamSentinel1.22.exe", temp + "\\framapack-clamsentinel57.exe", "" + temp + "\\framapack-clamsentinel57.exe" + " /verysilent", "Clam Sentinel");
#endif
    
#ifdef RUBBERSTAMPS26
    this->add("http://sourceforge.net/projects/tuxpaint/files/tuxpaint-stamps/2014-08-23/tuxpaint-stamps-2014-08-23-win32-installer.exe", temp + "\\framapack-rubberstamps26.exe", "" + temp + "\\framapack-rubberstamps26.exe" + " /SP- /VERYSILENT /NORESTART /NOCANCEL", "Rubber Stamps");
#endif
    
}


bool Apps::nextApp(std::string* application[4])
{
    if (this->current >= this->nb) {
        return false;
    }

    application[0] = &this->url[this->current];
    application[1] = &this->apps[this->current];
    application[2] = &this->install[this->current];
    application[3] = &this->name[this->current];

    this->current++;

    return true;
}

void Apps::add(std::string url, std::string apps, std::string install, std::string name)
{
    this->url[this->nb] = url;
    this->apps[this->nb] = apps;
    this->install[this->nb] = install;
    this->name[this->nb] = name;
    this->nb++;
}

#endif // APPS_H_INCLUDED
