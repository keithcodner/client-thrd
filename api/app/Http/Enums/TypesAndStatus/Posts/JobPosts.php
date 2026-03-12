<?php

namespace App\Enums\TypesAndStatus\Posts;

enum Type: string
{
    case General = 'general';
}

enum Status: string
{
    case Active = 'active';
    case InActive = 'inactive';
}

enum IsVisible: string
{
    case True = 'true';
    case False = 'false';
}

enum EmployerType: string
{
    case FullTime = 'Full-time';
    case PartTime = 'Part-time';
    case Contractor = 'Contractor';
    case Temporary = 'Temporary';
    case Internship = 'Internship';
    case PerDiem = 'Per Diem';
    case Volunteer = 'Volunteer';
    case Onsite = 'Onsite';
}

enum PrimaryTag: string
{
    case SoftwareDevelopment = 'Software Development';
    case CustomerService = 'Customer Service';
    case Sales = 'Sales';
    case Marketing = 'Marketing';
    case Design = 'Design';
    case Frontend = 'Frontend';
    case Backend = 'Backend';
    case Legal = 'Legal';
    case QualityAssurance = 'Quality Assurance';
    case Testing = 'Testing';
    case NonTech = 'Non-tech';
    case Other = 'Other';
}


enum SecondaryTag: string
{
    case Engineer = 'Engineer';
    case Executive = 'Executive';
    case Senior = 'Senior';
    case Developer = 'Developer';
    case Finance = 'Finance';
    case SystemAdmin = 'System Admin';
    case NetworkAdmin = 'Network Admin';
    case FinTech = 'Fin Tech';
    case GoLang = 'Go Lang';
    case Cloud = 'Cloud';
    case Medical = 'Medical';
    case FrontEnd = 'Front End';
    case Javascript = 'Javascript';
    case FullStack = 'Full Stack';
    case Ops = 'Ops';
    case React = 'React';
    case Infosec = 'Infosec';
    case Marketing = 'Marketing';
    case Mobile = 'Mobile';
    case Recruiter = 'Recruiter';
    case FullTime = 'Full Time';
    case Api = 'API';
    case Sales = 'Sales';
    case Ruby = 'Ruby';
    case Education = 'Education';
    case Stats = 'Stats';
    case Python = 'Python';
    case Node = 'Node';
    case English = 'English';
    case Video = 'Video';
    case Travel = 'Travel';
    case QualityAssurance = 'Quality Assurance';
    case Ecommerce = 'Ecommerce';
    case Wordpress = 'Wordpress';
    case Teaching = 'Teaching';
    case Git = 'Git';
    case Legal = 'Legal';
    case Crypto = 'Crypto';
    case Android = 'Android';
    case Admin = 'Admin';
    case Excel = 'Excel';
    case Php = 'Php';
}

enum Benefit: string
{
    case FourOneK = '401k';
    case DistributedTeam = 'Distrubuted Team';
    case Async = 'Async';
    case VisionInsurance = 'Vision Insurance';
    case DentalInsurance = 'Dental Insurance';
    case MedicalInsurance = 'Medical Insurance';
    case UnlimitedVacation = 'Unlimted Caction';
    case PaidTimeOff = 'Paid time off';
    case FourDayWorkWeek = '4 day work week';
    case FourOneKMatching = '401k matching';
    case RrspMatching = 'RRSP Matching';
    case CompanyRetreat = 'Company Retreat';
    case CoworkingBudget = 'Coworking Budget';
    case LearningBudget = 'Learning Budget';
    case FreeGymMembership = 'Free Gym Membeship';
    case MentalWellnessBudget = 'Mental Wellness Budget';
    case HomeOfficeBudget = 'Home Office Budget';
    case PayInCrypto = 'Pay In Crypto';
    case Pseudonymous = 'Pseudonymous';
    case ProfitSharing = 'Profit Sharing';
    case EquityCompensation = 'Equity Compensation';
    case NoWhiteboardInterview = 'No Whitebaord Interview';
    case NoMonitoringSystem = 'No monitorying System';
    case NoPoliticsAtWork = 'No Politics at Work';
    case WeHireOldAndYoung = 'We Hire Old and Young';
}