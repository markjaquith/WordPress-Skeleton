<?php

class Mailchimp_Error extends Exception {}
class Mailchimp_HttpError extends Mailchimp_Error {}

/**
 * The parameters passed to the API call are invalid or not provided when required
 */
class Mailchimp_ValidationError extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_ServerError_MethodUnknown extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_ServerError_InvalidParameters extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Unknown_Exception extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Request_TimedOut extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Zend_Uri_Exception extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_PDOException extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Avesta_Db_Exception extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_XML_RPC2_Exception extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_XML_RPC2_FaultException extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Too_Many_Connections extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Parse_Exception extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_User_Unknown extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_User_Disabled extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_User_DoesNotExist extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_User_NotApproved extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Invalid_ApiKey extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_User_UnderMaintenance extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Invalid_AppKey extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Invalid_IP extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_User_DoesExist extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_User_InvalidRole extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_User_InvalidAction extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_User_MissingEmail extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_User_CannotSendCampaign extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_User_MissingModuleOutbox extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_User_ModuleAlreadyPurchased extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_User_ModuleNotPurchased extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_User_NotEnoughCredit extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_MC_InvalidPayment extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_List_DoesNotExist extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_List_InvalidInterestFieldType extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_List_InvalidOption extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_List_InvalidUnsubMember extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_List_InvalidBounceMember extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_List_AlreadySubscribed extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_List_NotSubscribed extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_List_InvalidImport extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_MC_PastedList_Duplicate extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_MC_PastedList_InvalidImport extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Email_AlreadySubscribed extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Email_AlreadyUnsubscribed extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Email_NotExists extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Email_NotSubscribed extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_List_MergeFieldRequired extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_List_CannotRemoveEmailMerge extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_List_Merge_InvalidMergeID extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_List_TooManyMergeFields extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_List_InvalidMergeField extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_List_InvalidInterestGroup extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_List_TooManyInterestGroups extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Campaign_DoesNotExist extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Campaign_StatsNotAvailable extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Campaign_InvalidAbsplit extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Campaign_InvalidContent extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Campaign_InvalidOption extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Campaign_InvalidStatus extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Campaign_NotSaved extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Campaign_InvalidSegment extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Campaign_InvalidRss extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Campaign_InvalidAuto extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_MC_ContentImport_InvalidArchive extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Campaign_BounceMissing extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Campaign_InvalidTemplate extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Invalid_EcommOrder extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Absplit_UnknownError extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Absplit_UnknownSplitTest extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Absplit_UnknownTestType extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Absplit_UnknownWaitUnit extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Absplit_UnknownWinnerType extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Absplit_WinnerNotSelected extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Invalid_Analytics extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Invalid_DateTime extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Invalid_Email extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Invalid_SendType extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Invalid_Template extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Invalid_TrackingOptions extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Invalid_Options extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Invalid_Folder extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Invalid_URL extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Module_Unknown extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_MonthlyPlan_Unknown extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Order_TypeUnknown extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Invalid_PagingLimit extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Invalid_PagingStart extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Max_Size_Reached extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_MC_SearchException extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Goal_SaveFailed extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Conversation_DoesNotExist extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Conversation_ReplySaveFailed extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_File_Not_Found_Exception extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Folder_Not_Found_Exception extends Mailchimp_Error {}

/**
 * None
 */
class Mailchimp_Folder_Exists_Exception extends Mailchimp_Error {}


