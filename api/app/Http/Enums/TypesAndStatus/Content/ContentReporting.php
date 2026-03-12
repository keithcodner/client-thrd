<?php

namespace App\Enums\TypesAndStatus\Content;

enum ReportingType: string
{
    case HarassBully     = 'harass_bully';
    case HateAbuse       = 'hate_abuse';
    case HarmDanger      = 'harm_danger';
    case SexualContent   = 'sexual_content';
    case ViolenceGore    = 'violence_gore';
    case SpamMisleading  = 'spam_misleading';
    case MyRights        = 'my_rights';
    case ChildAbuse      = 'child_abuse';
    case Terrorism       = 'terrorism';
    case CaptionIssue    = 'caption_issue';
    case OtherReport     = 'other_report';

    /**
     * Optional: Human-readable labels (for dropdowns, UI, etc.)
     */
    public function label(): string
    {
        return match ($this) {
            self::HarassBully    => 'Harassment or bullying',
            self::HateAbuse      => 'Hateful or Abusive Content',
            self::HarmDanger     => 'Harmful or dangerous acts',
            self::SexualContent  => 'Sexual Content',
            self::ViolenceGore   => 'Violence or Gore Content',
            self::SpamMisleading => 'Spam or misleading',
            self::MyRights       => 'Infringes my rights',
            self::ChildAbuse     => 'Child abuse',
            self::Terrorism      => 'Promotes terrorism',
            self::CaptionIssue   => 'Captions issue',
            self::OtherReport    => 'Other',
        };
    }
}

enum ReportingStatus: string
{
    case Active = 'active';
    case InActive = 'inactive';
}

enum ReportingReasonTable: string
{
    case Post = 'post';
    case Circle = 'circle';
    case Item = 'item';
}

enum ReportingIsAppealed: string
{
    case Yes = 'yes';
    case No = 'no';
}
