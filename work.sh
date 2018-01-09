#!/usr/bin/env bash

echo Which Job ?
echo 1. Start the job
echo 2. Committing
echo 3. End Job
read choice

case $choice in
1)
    git stash
    git pull --rebase
    git stash pop
    echo Ok you ready to go!
    exit 1
    ;;
2)
    echo input commit message
    read msg
    ./autofix.sh
    git add .
    git commit -m "$msg"
    git pull --rebase
    git push origin master
    echo Ok you\'re ready to go!
    ;;
3)
    ./autofix.sh
    git add .
    git commit -m date -u
    git pull --rebase
    git push origin master
    ;;
*)
    echo he choice nothing
    exit 1
esac

#if [ "$choice" == "1" ]
#then
#    git stash
#    git pull --rebase
#    git stash pop
#    echo Ok you ready to go!
#    exit
#else if [ "$choice" == "2" ]
#then
#    read msg
#
#fi