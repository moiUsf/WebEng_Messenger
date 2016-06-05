<?php



echo " <p> hello Word  </p>";

$name = $_GET['username'];
$password = $_GET['password'];

echo '     '.$name;
echo '     '.$password;

echo "<p></p>";



$bytes = openssl_random_pseudo_bytes(64);
$salt_masterkey   = bin2hex($bytes);


 //test salt master key and master key
echo "salt_masterkey  64 bytes   ==>     ".$salt_masterkey;
//$password = "test234";
echo "<p></p>";
$masterKey = hash_pbkdf2 ( 'sha256', $password , $salt_masterkey , 10000);
echo "masterkey   PBKDF2 [password / salt / 256 Bit /  10000] ==>     ". $masterKey;



echo "<p></p>";





$config = array(
    //"digest_alg" => "sha512",
    "private_key_bits" => 2048,
    "private_key_type" => OPENSSL_KEYTYPE_RSA,
);

// Create the private and public key
$res = openssl_pkey_new($config);

// Extract the private key from $res to $privKey
openssl_pkey_export($res, $privkey_user);

// Extract the public key from $res as array to $pubKey as String
$pubkey_user = openssl_pkey_get_details($res);
$pubkey_user = $pubkey_user["key"];




 //test public key and private key

echo $pubkey_user;
echo "<p></p>";
echo $privkey_user;

# --- ENCRYPTION ---

/*
 * string mcrypt_encrypt ( string $cipher , string $key , string $data , string $mode [, string $iv ] )
 * string $cipher ==> MCRYPT_RIJNDAEL_128
 * String $key ==> $masterkey
 * string $data ==> $privkey_user
 *string $mode ==> MCRYPT_MODE_CBC
 * $iv ==> initialization vector
 */




# the key should be random binary, use scrypt, bcrypt or PBKDF2 to
# convert a string into a key
# key is specified using hexadecimal
$masterKey = pack('H*', $masterKey );
$key_size =  strlen($key);

echo "<p></p>";
echo "Key size: " . $key_size . "\n";

echo "<p></p>";



# --- ENCRYPTION ---

$privkey_user_enc = mcrypt_ecb (MCRYPT_RIJNDAEL_128, $masterKey, $privkey_user, MCRYPT_MODE_CBC );



echo "privkey_user encrypted with AES-ECB-128 + masterkey ==>   " ;
echo "<p></p>";
echo $privkey_user_enc;
echo "<p></p>";


# --- DECRYPTION ---

echo "privkey_user decrypted with AES-ECB-128 + masterkey ==>   " ;
echo "<p></p>";

$privkey_user_dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $masterKey,
    $privkey_user_enc, MCRYPT_MODE_ECB);

echo  $privkey_user_dec . "\n";


//strlen — Ermitteln der String-Länge
echo "Ermitteln der String-Länge ";
$key_size2 =  strlen($privkey_user_enc);

echo "<p></p>";
echo "Key size: " . $key_size2 . "\n";

echo "<p></p>";


?>