
npm install -g opencode-ai

rm -rf "$USERPROFILE/.config/opencode"
rm -rf "$USERPROFILE/.opencode"
rm -rf "$USERPROFILE/AppData/Roaming/opencode"

npm uninstall -g opencode-ai


//Deploymnt commands.

ln -s /home/u214605677/domains/ssms-api.custospark.com/public /home/u214605677/domains/custospark.com/public_html/ssms-api
ln -s /home/u214605677/domains/ssms-api.custospark.com/storage/app/public /home/u214605677/domains/ssms-api.custospark.com/public/storage

git clone -b opiyo-oscar/sms-api https://github.com/Powellpay/ssms-php.git ssms-api.custospark.com


now we must have a way of uploading assessement records , becase we  can have many learner and we must have a way of uploading them, provide template to download just like we did for students

Two failures in AuthContractTest:
1. me returns user resource — expects data key, but we have withoutWrapping() so the UserResource returns unwrapped data
2. token is invalidated after logout — the token count assertion is wrong

