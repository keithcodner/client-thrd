<?php

namespace App\Http\Enums\TypesAndStatus\Core;

enum ComIsReply: string
{
    case Yes = 'Yes';
    case No = 'No';
}

enum CommStatus: string
{
    case Active = 'Active';
    case InActive = 'In-Active';
}

enum CommSStatus: string
{
    case Unread = 'unread';
    case Read = 'read';
}

enum CommType: string
{
    case PostReplyComment = 'post_reply_comment';
    case PostComment = 'post_comment';
    case IncidentMessage = 'incident_message';
}
