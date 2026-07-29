
npm install -g opencode-ai

rm -rf "$USERPROFILE/.config/opencode"
rm -rf "$USERPROFILE/.opencode"
rm -rf "$USERPROFILE/AppData/Roaming/opencode"

npm uninstall -g opencode-ai


//Deploymnt commands.

ln -s /home/u214605677/domains/ssms-api.custospark.com/public /home/u214605677/domains/custospark.com/public_html/ssms-api
ln -s /home/u214605677/domains/ssms-api.custospark.com/storage/app/public /home/u214605677/domains/ssms-api.custospark.com/public/storage

git clone -b opiyo-oscar/sms-api https://github.com/Powellpay/ssms-php.git ssms-api.custospark.com