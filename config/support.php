<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Support ticket notifications (Zoho / inbound)
    |--------------------------------------------------------------------------
    |
    | Inbound ticket email uses SUPPORT_INBOX as To, SUPPORT_MAIL_FROM as From,
    | Reply-To set to the submitter. SUPPORT_INBOX_CC is comma-separated CC list.
    |
    */

    /*
    | Display phone for Messaging Center "Need Help?" and similar UI (not SMS).
    */
    'phone' => env('SUPPORT_PHONE', '242 806 6275'),

    'inbox' => env('SUPPORT_INBOX', 'support@caymark.com'),

    'mail_from' => env('SUPPORT_MAIL_FROM', 'support@caymark.com'),

    'mail_from_name' => env('SUPPORT_MAIL_FROM_NAME', 'CayMark Support'),

    'inbox_cc' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('SUPPORT_INBOX_CC', 'muhammadkabeerxhb@gmail.com'))
    ))),

];
