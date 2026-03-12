<?php

namespace App\Enums\TypesAndStatus\File;

enum Type: string
{
    case NewsFeedPostImage = 'news_feed_post_image';
    case File = 'file';
    case Video = 'video';
}

enum Status: string
{
    case Active = 'active';
    case InActive = 'inactive';
}

enum VerifyStatus: string
{
    case Active = 'active';
    case InActive = 'inactive';
}

