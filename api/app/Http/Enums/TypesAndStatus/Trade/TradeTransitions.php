<?php

namespace App\Enums\TypesAndStatus\Trade;

enum TradeTransitions: string
{
    case AcceptIncomingTrade = 'AcceptIncomingTrade';
    case AcceptTradeItemOffer = 'AcceptTradeItemOffer';
    case DenyIncomingTrade = 'DenyIncomingTrade';
    case AbortedTrade = 'AbortedTrade';
    case DenyTradeItemOffer = 'DenyTradeItemOffer';
    case CompleteTrade = 'CompleteTrade';
    case InitiatorCompleteTrade = 'InitiatorCompleteTrade';
    case ProspectCompleteTrade = 'ProspectCompleteTrade';
    case ChangeTradeItemOffer = 'ChangeTradeItemOffer';
    case ArchiveTrade = 'ArchiveTrade';
}