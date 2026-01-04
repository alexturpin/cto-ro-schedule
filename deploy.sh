#!/bin/bash

# Deploy to Bluehost via FTP
# Usage: ./deploy.sh

FTP_HOST="ftp.rub.zmj.mybluehost.me"
FTP_USER="rubzmjmy"

# Load password from .ftppasswd
if [ ! -f ".ftppasswd" ]; then
    echo "Error: .ftppasswd file not found"
    exit 1
fi
FTP_PASS=$(cat .ftppasswd)

REMOTE_PATH="/public_html"

echo "Deploying to $FTP_HOST..."

lftp -c "
set ssl:verify-certificate no;
open -u $FTP_USER,$FTP_PASS $FTP_HOST;
lcd $PWD;
cd $REMOTE_PATH;
mirror --reverse --verbose \
       --exclude-glob .git/ \
       --exclude-glob .* \
       --exclude-glob deploy.sh \
       --exclude-glob README.md \
       --exclude-glob '*.sql';
bye
"

echo "Deployment complete!"
