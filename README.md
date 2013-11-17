# What?

Script to extract data from a K9Mail DB (stored in /data/data/com.fsck.k9/databases/UUID.db, one per account), and dump it to a format that iu-restore (https://code.google.com/p/imaputils/downloads/detail?name=imaputils-1.0.3.beta1.tar.gz&can=2&q=) can push back to your mail server.

# Why?

Through idiocy and paranoia, I lost managed to delete all emails on my mail server, so the only compy I had was the data downloaded to my phone. 

# How?

Point the script at an SQLite DB, it'll dump all the messages into a folder structure with a prefix added (so you can get them on the IMAP server and move them into the folders using the server's own methods - probably safe). Default prefix is K9B.

