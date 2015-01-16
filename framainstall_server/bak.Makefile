# A simple makefile generator by KiSoft, 2008. mailto: kisoft@rambler.ru
# version: 0.3.12.3

# Project Variables start
CPP="i586-mingw32msvc-g++"
CC="i586-mingw32msvc-gcc"
LD="i586-mingw32msvc-g++"
LIB="i586-mingw32msvc-ar"
WINDRES="i586-mingw32msvc-windres"
# Project Variables end

# Target: Release

OBJS_RELEASE=./obj/Release/resource.res ./obj/Release/main.o

Release: ./bin/Release/FramaInstall.exe

./bin/Release/FramaInstall.exe: $(OBJS_RELEASE)
	@echo Building executable ./bin/Release/FramaInstall.exe
	@$(CPP) ./obj/Release/resource.res ./obj/Release/main.o -o ./bin/Release/framapack.exe -L./lib/windows -lcomctl32 -L./lib/curl_static -lcurl -lws2_32 -lwinmm -L./lib/zlib -static -lz
#	@$(CPP) -L./lib/devcpp -L./lib/zlib -L/home/framapack/mingw/lib  -o ./bin/Release/FramaInstall.exe $(OBJS_RELEASE)  -s -lcurl -lws2_32 -lwinmm  -lgdi32 -luser32 -lkernel32 ./lib/curl/libcurl.a ./lib/curl/libcurldll.a ./lib/curl/libeay32.a ./lib/curl/libidn.a ./lib/curl/libidn.dll.a ./lib/curl/libssh2.a ./lib/curl/libssh2dll.a ./lib/curl/libssl32.a ./lib/curl/libssl.a ./lib/curl/libz.a ./lib/curl/libzdll.a ./lib/curl/libcrypto.a ./lib/windows/libcomctl32.a ./lib/curl_static/libcurl.a ./lib/zlib2/zdll.lib ./lib/windows/libcomctl32.a ./lib/curl_static/libcurl.a ./lib/zlib2/zdll.lib  -mwindows

./obj/Release/resource.res: ./src/resource.rc
	@echo Compiling: ./src/resource.rc
	@$(WINDRES) -i ./src/resource.rc -J rc -o ./obj/Release/resource.res -O coff
#	@$(WINDRES) -i ./src/resource.rc -J rc -o ./obj/Release/resource.res -O coff 

./obj/Release/main.o: ./src/main.cpp
	@echo Compiling: ./src/main.cpp
	@$(CPP) -c src/main.cpp -o obj/Release/main.o -DCURL_STATICLIB -Wall
#	@$(CPP) -Wall  -O2 -DCURL_STATICLIB  -DFIREFOX   -c "./src/main.cpp" -o ./obj/Release/main.o

"./src/resource.rc": resource.h

"./src/main.cpp": curl/curl.h resource.h progress.h apps.h

.PHONY: clean_Release

clean_Release: 
	@echo Delete $(OBJS_RELEASE) bin/Release/FramaInstall.exe
	-@del /f $(OBJS_RELEASE) bin/Release/FramaInstall.exe

