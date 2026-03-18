<?php

namespace App\Http\Enums\TypesAndStatus\Conversation;

enum SeenByRecievedUser: string
{
    case True = 'true';
    case False = 'false';
}

enum SeenByOtherUser: string
{
    case True = 'true';
    case False = 'false';
}
