#!/bin/bash

sudo aptitude install libsdl-dev
wget http://files.poulsander.com/~poul19/public_files/oa/dev088/oa-0.8.8.tar.bz2
tar -jxf oa-0.8.8.tar.bz2
cp g_combat.c oa-0.8.8/code/game/g_combat.c
cd oa-0.8.8
BASEDIR=`pwd`
make
sudo mkdir /usr/lib/games/openarena/baseoa/orig
sudo mv /usr/lib/games/openarena/baseoa/*.so /usr/lib/games/openarena/baseoa/orig/
sudo ln -s $BASEDIR/build/release-linux-x86_64/baseq3/*.so /usr/lib/games/openarena/baseoa
