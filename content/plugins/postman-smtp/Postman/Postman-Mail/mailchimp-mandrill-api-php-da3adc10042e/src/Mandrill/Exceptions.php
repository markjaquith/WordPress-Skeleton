<?php

class Postman_Mandrill_Error extends Exception {}
class Postman_Mandrill_HttpError extends Postman_Mandrill_Error {}

/**
 * The parameters passed to the API call are invalid or not provided when required
 */
class Postman_Mandrill_ValidationError extends Postman_Mandrill_Error {}

/**
 * The provided API key is not a valid Mandrill API key
 */
class Postman_Mandrill_Invalid_Key extends Postman_Mandrill_Error {}

/**
 * The requested feature requires payment.
 */
class Postman_Mandrill_PaymentRequired extends Postman_Mandrill_Error {}

/**
 * The provided subaccount id does not exist.
 */
class Postman_Mandrill_Unknown_Subaccount extends Postman_Mandrill_Error {}

/**
 * The requested template does not exist
 */
class Postman_Mandrill_Unknown_Template extends Postman_Mandrill_Error {}

/**
 * The subsystem providing this API call is down for maintenance
 */
class Postman_Mandrill_ServiceUnavailable extends Postman_Mandrill_Error {}

/**
 * The provided message id does not exist.
 */
class Postman_Mandrill_Unknown_Message extends Postman_Mandrill_Error {}

/**
 * The requested tag does not exist or contains invalid characters
 */
class Postman_Mandrill_Invalid_Tag_Name extends Postman_Mandrill_Error {}

/**
 * The requested email is not in the rejection list
 */
class Postman_Mandrill_Invalid_Reject extends Postman_Mandrill_Error {}

/**
 * The requested sender does not exist
 */
class Postman_Mandrill_Unknown_Sender extends Postman_Mandrill_Error {}

/**
 * The requested URL has not been seen in a tracked link
 */
class Postman_Mandrill_Unknown_Url extends Postman_Mandrill_Error {}

/**
 * The provided tracking domain does not exist.
 */
class Postman_Mandrill_Unknown_TrackingDomain extends Postman_Mandrill_Error {}

/**
 * The given template name already exists or contains invalid characters
 */
class Postman_Mandrill_Invalid_Template extends Postman_Mandrill_Error {}

/**
 * The requested webhook does not exist
 */
class Postman_Mandrill_Unknown_Webhook extends Postman_Mandrill_Error {}

/**
 * The requested inbound domain does not exist
 */
class Postman_Mandrill_Unknown_InboundDomain extends Postman_Mandrill_Error {}

/**
 * The provided inbound route does not exist.
 */
class Postman_Mandrill_Unknown_InboundRoute extends Postman_Mandrill_Error {}

/**
 * The requested export job does not exist
 */
class Postman_Mandrill_Unknown_Export extends Postman_Mandrill_Error {}

/**
 * A dedicated IP cannot be provisioned while another request is pending.
 */
class Postman_Mandrill_IP_ProvisionLimit extends Postman_Mandrill_Error {}

/**
 * The provided dedicated IP pool does not exist.
 */
class Postman_Mandrill_Unknown_Pool extends Postman_Mandrill_Error {}

/**
 * The user hasn't started sending yet.
 */
class Postman_Mandrill_NoSendingHistory extends Postman_Mandrill_Error {}

/**
 * The user's reputation is too low to continue.
 */
class Postman_Mandrill_PoorReputation extends Postman_Mandrill_Error {}

/**
 * The provided dedicated IP does not exist.
 */
class Postman_Mandrill_Unknown_IP extends Postman_Mandrill_Error {}

/**
 * You cannot remove the last IP from your default IP pool.
 */
class Postman_Mandrill_Invalid_EmptyDefaultPool extends Postman_Mandrill_Error {}

/**
 * The default pool cannot be deleted.
 */
class Postman_Mandrill_Invalid_DeleteDefaultPool extends Postman_Mandrill_Error {}

/**
 * Non-empty pools cannot be deleted.
 */
class Postman_Mandrill_Invalid_DeleteNonEmptyPool extends Postman_Mandrill_Error {}

/**
 * The domain name is not configured for use as the dedicated IP's custom reverse DNS.
 */
class Postman_Mandrill_Invalid_CustomDNS extends Postman_Mandrill_Error {}

/**
 * A custom DNS change for this dedicated IP is currently pending.
 */
class Postman_Mandrill_Invalid_CustomDNSPending extends Postman_Mandrill_Error {}

/**
 * Custom metadata field limit reached.
 */
class Postman_Mandrill_Metadata_FieldLimit extends Postman_Mandrill_Error {}

/**
 * The provided metadata field name does not exist.
 */
class Postman_Mandrill_Unknown_MetadataField extends Postman_Mandrill_Error {}


