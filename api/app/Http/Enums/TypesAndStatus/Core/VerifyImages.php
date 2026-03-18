<?php

namespace App\Http\Enums\TypesAndStatus\Core;

enum ReasonTable: string
{
    case FilesStored = 'files_stored';
}

enum CommStatus: string
{
    case ApprovedContent = 'approved_content';
    case NotAccurateDescription = 'not_accurate_description';
    case ViolentContent = 'violent_content';
}

enum ImageStatus1: string
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
