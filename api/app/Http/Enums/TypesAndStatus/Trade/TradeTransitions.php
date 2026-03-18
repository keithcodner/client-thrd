<?php

namespace App\Http\Enums\TypesAndStatus\Circle;

enum CircleTransitions: string
{
    case AcceptIncomingCircle = 'AcceptIncomingCircle';
    case AcceptCircleItemOffer = 'AcceptCircleItemOffer';
    case DenyIncomingCircle = 'DenyIncomingCircle';
    case AbortedCircle = 'AbortedCircle';
    case DenyCircleItemOffer = 'DenyCircleItemOffer';
    case CompleteCircle = 'CompleteCircle';
    case InitiatorCompleteCircle = 'InitiatorCompleteCircle';
    case ProspectCompleteCircle = 'ProspectCompleteCircle';
    case ChangeCircleItemOffer = 'ChangeCircleItemOffer';
    case ArchiveCircle = 'ArchiveCircle';
}