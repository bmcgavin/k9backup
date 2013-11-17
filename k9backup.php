<?php

/**
 * Folder schema : 
 * CREATE TABLE folders (id INTEGER PRIMARY KEY, name TEXT, last_updated INTEGER, unread_count INTEGER, visible_limit INTEGER, status TEXT, push_state TEXT, last_pushed INTEGER, flagged_count INTEGER default 0, integrate INTEGER, top_group INTEGER, poll_class TEXT, push_class TEXT, display_class TEXT);
 *
 * Header schema :
 *
 * CREATE TABLE headers (id INTEGER PRIMARY KEY, message_id INTEGER, name TEXT, value TEXT);
 *
 * Message schema :
 *
 * CREATE TABLE messages (id INTEGER PRIMARY KEY, deleted INTEGER default 0, folder_id INTEGER, uid TEXT, subject TEXT, date INTEGER, flags TEXT, sender_list TEXT, to_list TEXT, cc_list TEXT, bcc_list TEXT, reply_to_list TEXT, html_content TEXT, text_content TEXT, attachment_count INTEGER, internal_date INTEGER, message_id TEXT, preview TEXT, mime_type TEXT, normalized_subject_hash INTEGER, empty INTEGER, read INTEGER default 0, flagged INTEGER default 0, answered INTEGER default 0, forwarded INTEGER default 0);
 *
 */

$args = array(
    'dbname',
);

$optargs = array(
    'prefix'
);

define('FOLDER_PREFIX', 'K9B');

$dbname = $argv[1];
if (!file_exists($dbname)) {
    exit;
}

$db = new SQLite3($dbname);
$results = $db->query('SELECT id, name FROM folders');
while ($row = $results->fetchArray()) {
    mkdir(FOLDER_PREFIX . $row['name']);
    $messages = $db->query('SELECT * FROM messages WHERE folder_id = ' . $row['id'] . ' ORDER BY date ASC');
    $i = 1;
    while ($message = $messages->fetchArray()) {
        $message_content = '';
        $headers = $db->query('SELECT * FROM headers WHERE message_id = ' . $message['id']);
        $boundary = null;
        while ($header = $headers->fetchArray()) {
            if ($header['name'] == 'Content-Type') {
                if (preg_match('|boundary="?([\-a-zA-Z0-9])"?|U', $header['value'], $matches)) {
                    $boundary = $matches[1];
                }
            }
            $header['value'] = str_replace("\t", "\r\n\t", $header['value']);
            $message_content .= $header['name'] . ': ' . $header['value'] . "\r\n";
        }
        $message_content .= "\r\n";
        if ($boundary !== null) {
            $message_content .= "--" . $boundary . "\r\n";
            $message_content .= "Content-Type: text/plain;\r\n";
            $message_content .= "  charset=utf-8\r\n";
            $message_content .= "Content-Transfer-Encoding: quoted-printable\r\n";
            $message_content .= "\r\n";
            $message_content .= $message['text_content'] . "\r\n";
            $message_content .= "--" . $boundary . "\r\n";
            $message_content .= "Content-Type: text/html;\r\n";
            $message_content .= "  charset=utf-8\r\n";
            $message_content .= "Content-Transfer-Encoding: quoted-printable\r\n";
            $message_content .= "\r\n";
            $message_content .= $message['html_content'] . "\r\n";
            $message_content .= "--" . $boundary . "--\r\n";
        } else {
            $message_content .= $message['text_content'];
        }
        file_put_contents(FOLDER_PREFIX . $row['name'] . '/' . $i, $message_content);
        $i++;
    }
}
function open_db() {
}
function get_folders() {
}