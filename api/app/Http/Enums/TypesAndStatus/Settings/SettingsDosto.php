<?php

namespace App\Enums\TypesAndStatus\Settings;

enum SettingsSite: string
{
    // Reporting-related settings
    case ReportingPostThresholdForPoster = 'reporting_post_threshold_for_poster';
    case ReportingUserImageThresholdForPoster = 'reporting_user_image_threshold_for_poster';
    case ReportingAdminImageThreshold = 'reporting_admin_image_threshold';
    case ReportingPowerUserImageThreshold = 'reporting_power_user_image_threshold';
    case ReportingThresholdPerDayForUsers = 'reporting_threshold_per_day_for_users';
    case DiscriminatePostReportsByType = 'discriminate_post_reports_by_type';

    // Content approval & validation
    case ContentApprovalMain = 'content_approval_main';
    case ContentApprovalCredentials = 'content_approval_credentials';
    case RequireContentValidation = 'require_content_validation';
    case ContentValidationThreshold = 'content_validation_threshold';
    case AllowUsersToValidateContent = 'allow_users_to_validate_content';

    // User verification
    case RequireValidUserIdentification = 'require_valid_user_identification';
    case RequireValidUserEmail = 'require_valid_user_email';

    // Ranking system
    case UseRankingPermissions = 'use_ranking_permissions';

    // Login security
    case LoginTryAttempts = 'login_try_attempts';

    // Trade system
    case EnableSimultaneousTradeLimit = 'enable_simultaneous_trade_limit';
    case SimultaneousTradeLimitTier1 = 'simultaneous_trade_limit_tier_1';
    case SimultaneousTradeLimitTier2 = 'simultaneous_trade_limit_tier_2';
    case EnableTradeTimeLimit = 'enable_trade_time_limit';
    case TradeTimeLimitTier1 = 'trade_time_limit_tier_1';
    case TradeTimeLimitTier2 = 'trade_time_limit_tier_2';
    case TradeWhoInitiatesOffer = 'trade_who_initiates_offer';
    case EnableTradeTransactionInsightCollection = 'enable_trade_transaction_insight_collection';

    // Favorite item limits
    case MaxMyFaveItemsTier1 = 'max_my_fave_items_tier_1';
    case MaxMyFaveItemsTier2 = 'max_my_fave_items_tier_2';

}

enum Type2: string
{
    case Threshold = 'threshold';
    case SwitchOnOff = 'switch_on_off';
    case SwitchTrueFalse = 'switch_true_false';
    case SwitchYesNo = 'switch_yes_no';
}

enum Option4: string
{
    case Unit = 'unit';
}

enum Option5: string
{
    case Count = 'count';
    case Days = 'days';
}

