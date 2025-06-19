#!/bin/bash
rsync -avz ./ --exclude='mysql' --exclude='node_modules' --exclude='storage' --exclude='webapp' --exclude='vendor' --exclude='.env' --exclude='composer.lock' --exclude='package-lock.json' --exclude='.git' --exclude='publish-sagara-development' --exclude='.DS_Store' --exclude='.vscode' sagara@195.35.6.227:/srv/htdocs/react-laravel-backend
