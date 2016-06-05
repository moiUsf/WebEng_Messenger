# WebEng_Messenger
kryptographischer Messaging-Dienst

Kryptomodell-Spezifikation:


Ausgangslage:
Jeder Benutzer hat eine Identität (Login).
Jeder Benutzer hat ein Passwort.
Jedem Benutzer ist ein RSA-2048 Schlüsselpaar privkey_user, pubkey_user zugeordnet.


Registrierung:
Der Benutzer wählt eine Identität.
Der Benutzer wählt ein Passwort.
Die Anwendung erzeugt einen 64 Byte grossen Salt salt_masterkey aus Zufallszahlen.
Die Anwendung bildet masterkey mithilfe der PBKDF2 Funktion mit folgenden Parametern:
Algorithmus: sha-256 Passwort: Passwort Salt: salt_masterkey Länge: 256 Bit Iterationen: 10000
Die Anwendung erzeugt ein RSA-2048 Schlüsselpaar privkey_user, pubkey_user. Die Anwendung verschlüsselt privkey_user via AES-ECB-128 mit masterkey zu privkey_user_enc.
Die Anwendung persistiert Identität, salt_masterkey, pubkey_user und privkey_user_enc beim Dienstanbieter.


Anmeldung:
Die Anwendung bezieht salt_masterkey, pubkey_user und privkey_user_enc von dem Dienstanbeiter auf Basis der angegebenen Identität.
Die Anwendung bildet masterkey mithilfe der PBKDF2 Funktion mit folgenden Parametern:
Algorithmus: sha-256 Passwort: Passwort Salt: salt_masterkey Länge: 256 Bit Iterationen: 10000
Die Anwendung entschlüsselt privkey_user_enc via AES-ECB-128 mit masterkey zu privkey_user.
Nachrichtenversand:
Die Anwendung bezieht pubkey_recipient auf Basis eines Empfängers vom Diensteanbieter (Vereinfachung, denkbar ist der Einsatz einer CA, oder alternativer Bezug des pubkeys). Der Benutzer erzeugt eine Nachricht an den Empfänger.
Die Anwendung erzeugt einen symmetrischen Schlüssel key_recipient der Länge 128 Bit aus Zufallszahlen.
Die Anwendung erzeugt einen Initialisierungsveltor iv der Länge 128 Bit aus Zufallszahlen. Die Anwendung verschlüsselt Nachricht mit Hilfe von AES-CBC-128 und key_recipient, iv zu Cipher.
Die Anwendung verschlüsselt key_recipient mit Hilfe von RSA und pubkey_recipient zu
key_recipient_enc.
Die Anwendung bildet eine digitale Signatur sig_recipient mit Hilfe von SHA-256 und privkey_user über Identität, Cipher, iv und key_recipient_enc.
Das Nachrichtenpaket aus:
Identität
Cipher
iv key_recipient_enc sig_recipient
ist der innere Umschlag des Nachrichtenversands.
Die Anwendung erzeugt einen timestamp aus der Unix Zeit.
Die Anwendung bildet eine digitale Signatur sig_service mit Hilfe von SHA-256 und privkey_user über innerer Umschlag, timestamp, Empfänger.


Das Nachrichtenpaket aus:
innerer Umschlag timestamp Empfänger sig_service
ist der äußere Umschlag des Nachrichtenversands.
Die Anwendung schickt den äußeren Umschlag an den Dienstanbieter.


Nachrichtenprüfung und Weiterleitung:
Der Dienstanbieter authentifiziert den äußeren Umschlag mit Hilfe von sig_service und dem pubkey_user der angegeben Identität im inneren Umschlag.
Der Dienstanbieter vergleicht die Zeit aus timestamp mit der eigenen Zeit, falls eine Abweichung > 5 Minuten existiert wird die Nachricht verworfen.
Nach erfolgreicher Prüfung sortiert der Dienstanbeiter den innerern Umschlag in das Postfach des Empfängers.
Nachrichtenabruf:
Der Empfänger stellt eine Anfrage zum Nachrichtenabruf an den Dienstanbieter. Die Anfrage beinhaltet:
Identität
timestamp
digitale Signatur über Identität+timestamp
Prüfung des Nachrichtenabrufs
Der Dienstanbieter authentifiziert die Anfrage mit Hilfe der dig. Signatur und der angegeben Identität.
Der Dienstanbieter vergleicht die Zeit aus timestamp mit der eigenen Zeit, falls eine Abweichung > 5 Minuten existiert wird die Nachricht verworfen, ansonsten werden die inneren Umschläge ausgeliefert.


Nachrichtenempfang:
Der Empfänger prüft die digitale Signatur sig_recipient.
    
Der Empfänger entschlüsselt key_recipient_enc. Der Empfänger entschlüsselt. Cipher.
