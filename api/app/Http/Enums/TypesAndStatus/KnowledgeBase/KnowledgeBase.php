<?php

namespace App\Enums\TypesAndStatus\KnowledgeBase;

enum KBType1: string
{
    case Paragraph = 'paragraph';
}

enum KBStatus: string
{
    case Active = 'active';
    case InActive = 'inactive';
}

