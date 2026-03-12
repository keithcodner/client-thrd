<?php

namespace App\Enums\TypesAndStatus\Circle;

enum CircleStatus: string
{
    case Completed = 'completed';
    case Aborted = 'aborted';
    case Active = 'active';
    case Archived = 'archived';
}

enum CircleSecondStatus: string
{
    case ProspectAccepted = 'prospect_accepted';
    case InitiatorDenied = 'initiator_denied';
    case ProspectIncoming = 'prospect_incoming';
}

enum CircleDisplayStatus: string
{
    case Completed = 'completed';
    case Aborted = 'aborted';
    case Incoming = 'incoming';
    case Negotiate = 'negotiate';
}

enum CircleTimeStatusType: string
{
    case NormalTime = 'normal_time';
}

enum CircleThemeCode: string
{
    case Style1 = 'style1';
    case Style2 = 'style2';
    case Style3 = 'style3';
    case Style4 = 'style4';
    case Style5 = 'style5';
    case Style6 = 'style6';
    case Style7 = 'style7';
    case Style8 = 'style8';
    case Style9 = 'style9';
}

enum TrueFalseStatus: string
{
    case True = 'true';
    case False = 'false';
}