#! /bin/bash
# Script to automate creating new OpenVPN clients
# The client cert and key, along with the CA cert is
# zipped up and placed somewhere to download securely
#
# H Cooper - 05/02/11
#
# Usage: new-openvpn-client.sh <common-name>

# Set where we're working from
OPENVPN_RSA_DIR=/etc/openvpn/easy-rsa/2.0
OPENVPN_KEYS=$OPENVPN_RSA_DIR/keys
KEY_DOWNLOAD_PATH=/var/www/html/openvpn-web/secure

# Either read the CN from $1 or prompt for it
if [ -z "$1" ]
	then echo -n "Enter new client common name (CN): "
	read -e CN
else
	CN=$1
fi

KEY_PATH=$KEY_DOWNLOAD_PATH/$CN.zip

# Ensure CN isn't blank
if [ -z "$CN" ]
	then echo "You must provide a CN."
	exit
fi

# Create the ovpn file
cp /var/www/html/config/client.ovpn /tmp/client.ovpn
sed -i 's/#CN#/$CN/g' /tmp/client.ovpn

# Check the CN doesn't already exist
if [ -f $OPENVPN_KEYS/$CN.crt ]
	then if [ -f $KEY_DOWNLOAD_PATH/$CN.zip ]
		then echo "Error: certificate with the CN $CN alread exists!"
			echo "    $OPENVPN_KEYS/$CN.crt"
	else
		echo "zip -q $KEY_PATH $OPENVPN_KEYS/$CN.crt $OPENVPN_KEYS/$CN.key $OPENVPN_KEYS/ca.crt"
		zip -q $KEY_PATH $OPENVPN_KEYS/$CN.crt $OPENVPN_KEYS/$CN.key $OPENVPN_KEYS/ca.crt
		echo "#############################################################"
		echo "COMPLETE! Download the certificate here:"
		echo "https://10.8.0.1/secure/$CN.zip"
		echo "#############################################################"
	fi
	exit
fi

# Enter the easy-rsa directory and establish the default variables
cd $OPENVPN_RSA_DIR
source ./vars > /dev/null

# Copied from build-key script (to ensure it works!)
export EASY_RSA="${EASY_RSA:-.}"
"$EASY_RSA/pkitool" --batch $CN

# Take the new cert and place it somewhere it can be downloaded securely
zip -q $KEY_PATH $OPENVPN_KEYS/$CN.crt $OPENVPN_KEYS/$CN.key $OPENVPN_KEYS/ca.crt

# Celebrate!
echo ""
echo "#############################################################"
echo "COMPLETE! Download the new certificate here:"
echo "https://10.8.0.1/secure/$CN.zip"
echo "#############################################################"
