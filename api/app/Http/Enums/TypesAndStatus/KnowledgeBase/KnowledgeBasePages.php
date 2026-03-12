<?php

namespace App\Enums\TypesAndStatus\KnowledgeBase;

enum Type: string
{
    case Documentation = 'documentation';
    case Technical_document = 'technical_document';
    case Tutorial = 'tutorial';
    case FAQ = 'faq';
}

enum Status: string
{
    case Active = 'active';
    case InActive = 'inactive';
    case Set = 'set';
}

