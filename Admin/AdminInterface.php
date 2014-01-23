<?php
/**
 * This file is part of the Zimbra API in PHP library.
 *
 * © Nguyen Van Nguyen <nguyennv1981@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zimbra\Admin;

use Zimbra\Admin\Struct\AttachmentIdAttrib as Attachment;
use Zimbra\Admin\Struct\CalendarResourceSelector as CalendarResource;
use Zimbra\Admin\Struct\CacheSelector as Cache;
use Zimbra\Admin\Struct\CosSelector as Cos;
use Zimbra\Admin\Struct\DataSourceSpecifier as DataSource;
use Zimbra\Admin\Struct\DeviceId as DeviceId;
use Zimbra\Admin\Struct\DistributionListSelector as DistList;
use Zimbra\Admin\Struct\DomainSelector as Domain;
use Zimbra\Admin\Struct\EffectiveRightsTargetSelector as Target;
use Zimbra\Admin\Struct\EntrySearchFilterInfo as SearchFilter;
use Zimbra\Admin\Struct\ExchangeAuthSpec as Exchange;
use Zimbra\Admin\Struct\ExportAndDeleteMailboxSpec as ExportMailbox;
use Zimbra\Admin\Struct\GranteeSelector as Grantee;
use Zimbra\Admin\Struct\HostName;
use Zimbra\Admin\Struct\IdAndAction;
use Zimbra\Admin\Struct\IdStatus;
use Zimbra\Admin\Struct\LoggerInfo as Logger;
use Zimbra\Admin\Struct\LimitedQuery;
use Zimbra\Admin\Struct\MailboxByAccountIdSelector as MailboxId;
use Zimbra\Admin\Struct\Names;
use Zimbra\Admin\Struct\Policy;
use Zimbra\Admin\Struct\PrincipalSelector as Principal;
use Zimbra\Admin\Struct\ReindexMailboxInfo as ReindexMailbox;
use Zimbra\Admin\Struct\RightModifierInfo as RightModifier;
use Zimbra\Admin\Struct\ServerSelector as Server;
use Zimbra\Admin\Struct\ServerMailQueueQuery as ServerMail;
use Zimbra\Admin\Struct\ServerWithQueueAction as ServerQueue;
use Zimbra\Admin\Struct\SyncGalAccountSpec as SyncGalAccount;
use Zimbra\Admin\Struct\StatsSpec;
use Zimbra\Admin\Struct\TargetWithType;
use Zimbra\Admin\Struct\TimeAttr;
use Zimbra\Admin\Struct\UcServiceSelector as UcService;
use Zimbra\Admin\Struct\VolumeInfo as Volume;
use Zimbra\Admin\Struct\XmppComponentSelector as XmppComponent;
use Zimbra\Admin\Struct\XmppComponentSpec as Xmpp;
use Zimbra\Admin\Struct\ZimletAclStatusPri as ZimletAcl;

use Zimbra\Struct\AccountSelector as Account;
use Zimbra\Struct\GranteeChooser;
use Zimbra\Struct\Id;
use Zimbra\Struct\NamedElement;

use Zimbra\Enum\AutoProvTaskAction as TaskAction;
use Zimbra\Enum\AttrMethod;
use Zimbra\Enum\CountObjectsType as ObjType;
use Zimbra\Enum\CompactIndexAction as IndexAction;
use Zimbra\Enum\CertType;
use Zimbra\Enum\CSRType;
use Zimbra\Enum\CSRKeySize;
use Zimbra\Enum\DedupAction;
use Zimbra\Enum\DeployZimletAction as DeployAction;
use Zimbra\Enum\GalConfigAction as ConfigAction;
use Zimbra\Enum\GalMode;
use Zimbra\Enum\GalSearchType;
use Zimbra\Enum\GetSessionsSortBy;
use Zimbra\Enum\IpType;
use Zimbra\Enum\QuotaSortBy;
use Zimbra\Enum\ReIndexAction;
use Zimbra\Enum\RightClass;
use Zimbra\Enum\SessionType;
use Zimbra\Enum\TargetType;
use Zimbra\Enum\VersionCheckAction;
use Zimbra\Enum\VolumeType;
use Zimbra\Enum\ZimletExcludeType as ExcludeType;

/**
 * AdminInterface is a interface which allows to connect Zimbra API administration functions via SOAP
 *
 * @package   Zimbra
 * @category  Admin
 * @author    Nguyen Van Nguyen - nguyennv1981@gmail.com
 * @copyright Copyright © 2013 by Nguyen Van Nguyen.
 */
interface AdminInterface
{
    /**
     * Add an alias for the account.
     * Access: domain admin sufficient.
     * Note: this request is by default proxied to the account's home server.
     *
     * @param  string $id    Value of zimbra identify.
     * @param  string $alias Account alias.
     * @return mix
     */
    function addAccountAlias($id, $alias);

    /**
     * Changes logging settings on a per-account basis.
     * Adds a custom logger for the given account and log category.
     * The logger stays in effect only during the lifetime of the current server instance.
     * If the request is sent to a server other than the one that the account resides on,
     * it is proxied to the correct server.
     * If the category is "all", adds a custom logger for every category or the given user.
     *
     * @param  Logger $logger The Logger infor.
     * @param  Account $account The account.
     * @return mix
     */
    function addAccountLogger(Logger $logger, Account $account = null);

    /**
     * Add an alias for a distribution list.
     * Access: domain admin sufficient.
     *
     * @param  string $id    Value of zimbra identify.
     * @param  string $alias Distribution list alias.
     * @return mix
     */
    function addDistributionListAlias($id, $alias);

    /**
     * Adding members to a distribution list.
     * Access: domain admin sufficient.
     *
     * @param  string $id   Value of zimbra identify.
     * @param  array  $dlms Distribution list members.
     * @return mix
     */
    function addDistributionListMember($id, array $dlms);

    /**
     * Add a GalSync data source.
     * Access: domain admin sufficient.
     *
     * @param  Account $account Account.
     * @param  string $name   Name of the data source.
     * @param  string $domain Name of pre-existing domain.
     * @param  string $type   GalMode type (both|ldap|zimbra).
     * @param  string $folder Contact folder name.
     * @param  array  $attrs  Attributes.
     * @return mix
     */
    function addGalSyncDataSource(
        Account $account,
        $name,
        $domain,
        GalMode $type,
        $folder = null,
        array $attrs = array()
    );

    /**
     * Create a waitset to listen for changes on one or more accounts.
     * Called once to initialize a WaitSet and to set its "default interest types".
     * WaitSet: scalable mechanism for listening for changes to one or more accounts.
     * Interest types:
     *   f. folders
     *   m. messages
     *   c. contacts
     *   a. appointments
     *   t. tasks
     *   d. documents
     *   all. all types (equiv to "f,m,c,a,t,d")
     *
     * @param  array $defTypes Default interest types.
     * @param  array $waitSets The WaitSet add spec array.
     * @param  bool  $allAccounts If all is set, then all mailboxes on the system will be listened to, including any mailboxes which are created on the system while the WaitSet is in existence.
     * @return mix
     */
    function adminCreateWaitSet(array $defTypes, array $waitSets = array(), $allAccounts = null);

    /**
     * Use this to close out the waitset.
     * Note that the server will automatically time out a wait set
     * if there is no reference to it for (default of) 20 minutes.
     * WaitSet: scalable mechanism for listening for changes to one or more accounts.
     *
     * @param  string $waitSet Waitset identify.
     * @return mix
     */
    function adminDestroyWaitSet($waitSet);

    /**
     * AdminWaitSetRequest optionally modifies the wait set and checks for any notifications.
     * If block=1 and there are no notifications, then this API will BLOCK until there is data.
     * Interest types:
     *   f. folders
     *   m. messages
     *   c. contacts
     *   a. appointments
     *   t. tasks
     *   d. documents
     *   all. all types (equiv to "f,m,c,a,t,d")
     *
     * @param string $waitSet Waitset identify.
     * @param string $seq Last known sequence number.
     * @param bool   $block Flag whether or not to block until some account has new data.
     * @param array  $defTypes Default interest types.
     * @param int    $timeout Timeout length.
     * @param array  $addWaitSets The WaitSet add spec array.
     * @param array  $updateWaitSets The WaitSet update spec array.
     * @param array  $removeWaitSets The WaitSet remove spec array.
     * @return mix
     */
    function adminWaitSet(
        $waitSet,
        $seq,
        $block = null,
        array $defTypes = array(),
        $timeout = null,
        array $addWaitSets = array(),
        array $updateWaitSets = array(),
        array $removeWaitSets = array()
    );

    /**
     * Authenticate for an adminstration account.
     *
     * @param string  $name Name. Only one of {auth-name} or <account> can be specified
     * @param string  $password Password - must be present if not using AuthToken
     * @param string  $authToken An authToken can be passed instead of account/password/name to validate an existing auth token.
     * @param Account $account The account
     * @param string  $virtualHost Virtual host
     * @param bool    $persistAuthTokenCookie Controls whether the auth token cookie in the response should be persisted when the browser exits.
     * @return authentication token
     */
    function auth(
        $name = null,
        $password = null,
        $authToken = null,
        Account $account = null,
        $virtualHost = null,
        $persistAuthTokenCookie = null
    );

    /**
     * Authenticate for an adminstration account.
     *
     * @param  string $name     Name. Only one of {auth-name} or <account> can be specified
     * @param  string $password The user password.
     * @param  string $vhost    Virtual-host is used to determine the domain of the account name.
     * @return authentication token
     */
    function authByName($name, $password, $vhost = null);

    /**
     * Authenticate for an adminstration account.
     *
     * @param  Account $account  The user account.
     * @param  string $password The user password.
     * @param  string $vhost    Virtual-host is used to determine the domain of the account name.
     * @return authentication token
     */
    function authByAccount(Account $account, $password, $vhost = null);

    /**
     * Authenticate for an adminstration account.
     *
     * @param  string $token The authentication token.
     * @param  string $vhost Virtual-host is used to determine the domain of the account name.
     * @return authentication token.
     */
    function authByToken($token, $vhost = null);

    /**
     * Perform an autocomplete for a name against the Global Address List
     * Notes: admin verison of mail equiv. Used for testing via zmprov.
     * Type of addresses to auto-complete on:
     *   1. "account" for regular user accounts, aliases and distribution lists
     *   2. "resource" for calendar resources
     *   3. "group" for groups
     *   4. "all" for combination of types
     *
     * @param  string $domain The domain name.
     * @param  string $name The name to test for autocompletion.
     * @param  SearchType $type Type of addresses to auto-complete on.
     * @param  string $galAcctId GAL Account ID.
     * @param  int    $limit An integer specifying the maximum number of results to return
     * @return mix
     */
    function autoCompleteGal(
        $domain,
        $name,
        GalSearchType $type = null,
        $galAcctId = null,
        $limit = null
    );

    /**
     * Auto-provision an account
     *
     * @param  Domain    $domain    The domain name.
     * @param  Principal $principal The name used to identify the principal.
     * @param  string    $password  Password.
     * @return mix
     */
    function autoProvAccount(Domain $domain, Principal $principal, $password = null);

    /**
     * Auto-provision task control.
     * Under normal situations, the EAGER auto provisioning task(thread)
     * should be started/stopped automatically by the server when appropriate.
     * The task should be running when zimbraAutoProvPollingInterval is not 0
     * and zimbraAutoProvScheduledDomains is not empty.
     * The task should be stopped otherwise.
     * This API is to manually force start/stop or query status of the EAGER auto provisioning task.
     * It is only for diagnosis purpose and should not be used under normal situations.
     *
     * @param  TaskAction $action Action to perform - one of start|status|stop
     * @return mix
     */
    function autoProvTaskControl(TaskAction $action);

    /**
     * Check Auth Config.
     *
     * @param  string $name     Name.
     * @param  string $password Password.
     * @param  array  $attrs    Attributes.
     * @return mix
     */
    function checkAuthConfig($name, $password, array $attrs = array());

    /**
     * Checks for items that have no blob, blobs that have no item,
     * and items that have an incorrect blob size stored in their metadata.
     * If no volumes are specified, all volumes are checked.
     * If no mailboxes are specified, all mailboxes are checked.
     * Blob sizes are checked by default.
     * Set checkSize to 0 (false) to * avoid the CPU overhead
     * of uncompressing compressed blobs in order to calculate size.
     *
     * @param array $volumes Volumes.
     * @param array $mboxes Mailboxes.
     * @param bool  $checkSize Check size.
     * @param bool  $reportUsedBlobs If set a complete list of all blobs used by the mailbox(es) is returned.
     * @return mix
     */
    function checkBlobConsistency(
        array $volumes = array(),
        array $mboxes = array(),
        $checkSize = null,
        $reportUsedBlobs = null
    );

    /**
     * Check existence of one or more directories and optionally create them.
     *
     * @param  array $directories Directories.
     * @return mix
     */
    function checkDirectory(array $directories = array());

    /**
     * Check Domain MX record.
     *
     * @param  Domain $domain The name used to identify the domain.
     * @return mix
     */
    function checkDomainMXRecord(Domain $domain);

    /**
     * Check Exchange Authorisation.
     *
     * @param  Exchange $auth Exchange auth details.
     * @return mix
     */
    function checkExchangeAuth(Exchange $auth);

    /**
     * Check Global Addressbook Configuration .
     * Notes:
     *   1. zimbraGalMode must be set to ldap, even if you eventually want to set it to "both".
     *   2. <action> is optional. GAL-action can be autocomplete|search|sync. Default is search.
     *   3. <query> is ignored if <action> is "sync".
     *   4. AuthMech can be none|simple|kerberos5.
     *      - Default is simple if both BindDn/BindPassword are provided.
     *      - Default is none if either BindDn or BindPassword are NOT provided.
     *   5. BindDn/BindPassword are required if AuthMech is "simple".
     *   6. Kerberos5Principal/Kerberos5Keytab are required only if AuthMech is "kerberos5".
     *   7. zimbraGalSyncLdapXXX attributes are for GAL sync. They are ignored if <action> is not sync. 
     *      For GAL sync, if a zimbraGalSyncLdapXXX attribute is not set,
     *      server will fallback to the corresponding zimbraGalLdapXXX attribute.
     *
     * @param  LimitedQuery  $query  Description for element text content.
     * @param  ConfigAction  $action Action (autocomplete|search|sync).
     * @param  array   $attrs  Attributes.
     * @return mix
     */
    function checkGalConfig(
        LimitedQuery $query = null,
        ConfigAction $action = null,
        array $attrs = array()
    );

    /**
     * Check Health.
     *
     * @return mix
     */
    function checkHealth();

    /**
     * Check whether a hostname can be resolved.
     *
     * @param  string $hostname Hostname.
     * @return mix
     */
    function checkHostnameResolve($hostname = '');

    /**
     * Check password strength.
     * Access: domain admin sufficient.
     * Note: this request is by default proxied to the account's home server
     *
     * @param  string $id       Zimbra identify.
     * @param  string $password Passowrd to check.
     * @return mix
     */
    function checkPasswordStrength($id, $password);

    /**
     * Check if a principal has the specified right on target. 
     * A successful return means the principal specified by the <grantee>
     * is allowed for the specified right on the * target object. 
     * Note: this request is by default proxied to the account's home server
     *
     * @param Target  $target  The target
     * @param Grantee $grantee The grantee
     * @param string  $right   Name of right.
     * @param array   $attrs   Attributes.
     * @return mix
     */
    function checkRights(
        Target $target,
        Grantee $grantee,
        $right,
        array $attrs = array()
    );

    /**
     * Clear cookie.
     *
     * @param  array $cookies Specifies cookies to clean.
     * @return mix
     */
    function clearCookie(array $cookies = array());

    /**
     * Compact index.
     * Access: domain admin sufficient.
     * Note: this request is by default proxied to the account's home server.
     *
     * @param  MailboxId $mbox Mailbox.
     * @param  IndexAction $action Action to perform (start|status).
     * @return mix
     */
    function compactIndex(MailboxId $mbox, IndexAction $action = null);

    /**
     * Computes the aggregate quota usage for all domains in the system.
     * The request handler issues GetAggregateQuotaUsageOnServerRequest
     * to all mailbox servers and computes the aggregate quota used by each domain.
     * The request handler updates the zimbraAggregateQuotaLastUsage domain attribute
     * and sends out warning messages for each domain having quota usage greater than a defined percentage threshold.
     *
     * @return mix
     */
    function computeAggregateQuotaUsage();

    /**
     * Configure Zimlet.
     *
     * @param  string $content Attachment identify.
     * @return mix
     */
    function configureZimlet(Attachment $content);

    /**
     * Copy Class of service (COS).
     *
     * @param  string $name Destination name for COS.
     * @param  string $cos  Source COS.
     * @return mix
     */
    function copyCos($name = null, Cos $cos = null);

    /**
     * Count number of accounts by cos in a domain.
     * Note: It doesn't include any account with zimbraIsSystemResource=TRUE,
     *       nor does it include any calendar resources.
     *
     * @param  string $domain The name used to identify the domain.
     * @return mix
     */
    function countAccount(Domain $domain = null);

    /**
     * Count number of objects. 
     * Returns number of objects of requested type.
     * Note: For account/alias/dl, if a domain is specified,
     *       only entries on the specified domain are counted.
     *       If no domain is specified, entries on all domains are counted.
     *       For accountOnUCService/cosOnUCService/domainOnUCService,
     *       UCService is required, and domain cannot be specified.
     *
     * @param ObjType $type  Object type. Valid values: (userAccount|account|alias|dl|domain|cos|server|calresource|accountOnUCService|cosOnUCService|domainOnUCService|internalUserAccount|internalArchivingAccount).
     * @param Domain $domain The name used to identify the domain.
     * @param UcService $ucservice Key for choosing ucservice.
     * @return mix
     */
    function countObjects(ObjType $type, Domain $domain = null, UcService $ucservice = null);

    /**
     * Create account.
     * Notes:
     *   1. accounts without passwords can't be logged into.
     *   2. name must include domain (uid@name), and domain specified in name must exist.
     *   3. default value for zimbraAccountStatus is "active".
     * Access: domain admin sufficient.
     *
     * @param  string $name     New account's name. Must include domain (uid@name), and domain specified in name must exist.
     * @param  string $password New account's password.
     * @param  array  $attrs    Attributes.
     * @return mix
     */
    function createAccount($name, $password, array $attrs = array());

    /**
     * Create a calendar resource.
     * Notes:
     *   1. A calendar resource is a special type of Account. The Create, Delete, Modify, Rename, Get, GetAll, and Search operations are very similar to those of Account.
     *   2. Must specify the displayName and zimbraCalResType attributes
     * Access: domain admin sufficient.
     *
     * @param  string $name     Name or calendar resource. Must include domain (uid@domain), and domain specified after @ must exist.
     * @param  string $password Password for calendar resource.
     * @param  array  $attrs    Attributes.
     * @return mix
     */
    function createCalendarResource($name = null, $password = null, array $attrs = array());

    /**
     * Create a Class of Service (COS).
     * Notes:
     *   1. Extra attrs: description, zimbraNotes.
     *
     * @param  string $name  COS name.
     * @param  array  $attrs Attributes.
     * @return mix
     */
    function createCos($name, array $attrs = array());

    /**
     * Creates a data source that imports mail items into the specified folder.
     * Notes:
     *   1. Currently the only type supported is pop3.
     *   2. every attribute value is returned except password.
     *   3. this request is by default proxied to the account's home server.
     *
     * @param  string $id    ID for an existing Account.
     * @param  DataSource $dataSource  Details of data source.
     * @return mix
     */
    function createDataSource($id, DataSource $dataSource);

    /**
     * Create a distribution list.
     * Notes:
     *   1. dynamic - create a dynamic distribution list.
     *   2. Extra attrs: description, zimbraNotes.
     *
     * @param  string $name    Name for distribution list.
     * @param  bool   $dynamic If 1 (true) then create a dynamic distribution list.
     * @param  array  $attrs   Attributes.
     * @return mix
     */
    function createDistributionList($name, $dynamic = null, array $attrs = array());

    /**
     * Create a domain.
     * Note:
     *   1. Extra attrs: description, zimbraNotes.
     *
     * @param  string $name  Name of new domain.
     * @param  array  $attrs Attributes.
     * @return mix
     */
    function createDomain($name, array $attrs = array());

    /**
     * Create a domain.
     * Notes:
     *   1. if the referenced account is not found it will be created.
     *   2. the identifier used in name attr is used for SyncGal and SearchGal.
     *   3. name attribute is for the name of the data source.
     *   4. if folder attr is not present it'll default to Contacts folder.
     *   5. passed in attrs in <a/> are used to initialize the gal data source.
     *   6. server is a required parameter and specifies the mailhost on which this account resides.
     *
     * @param string $name Name of the data source.
     * @param string $domain Domain name.
     * @param GalMode $type GalMode type. Valid values: (both|ldap|zimbra).
     * @param string $server The mailhost on which this account resides.
     * @param Account $account The name used to identify the account.
     * @param string $password Password.
     * @param string $folder Contact folder name.
     * @param array  $attrs Attributes.
     * @return mix
     */
    function createGalSyncAccount(
        $name,
        $domain,
        GalMode $type,
        $server,
        Account $account,
        $password = null,
        $folder = null,
        array $attrs = array()
    );

    /**
     * Create an LDAP entry.
     *
     * @param  string $dn    A valid LDAP DN String (RFC 2253) that describes the new DN to create.
     * @param  array  $attrs Attributes.
     * @return mix
     */
    function createLDAPEntry($dn, array $attrs = array());

    /**
     * Create a Server.
     * Extra attrs: description, zimbraNotes.
     *
     * @param  string $name  New server name.
     * @param  array  $attrs Attributes.
     * @return mix
     */
    function createServer($name, array $attrs = array());

    /**
     * Create a system retention policy.
     * The system retention policy SOAP APIs allow the administrator
     * to edit named system retention policies that users can apply to folders and tags.
     *
     * @param  string $cos   The name used to identify the COS.
     * @param  Policy $keep  Keep policy details.
     * @param  Policy $purge Purge policy details.
     * @return mix
     */
    function createSystemRetentionPolicy(Cos $cos = null, Policy $keep = null, Policy $purge = null);

    /**
     * Create a UC service.
     *
     * @param  string $name  New ucservice name.
     * @param  array  $attrs Attributes.
     * @return mix
     */
    function createUCService($name, array $attrs = array());

    /**
     * Create a volume.
     *
     * @param  Volume $volume Volume information.
     * @return mix
     */
    function createVolume(Volume $volume);

    /**
     * Create an XMPP component.
     *
     * @param  Xmpp $xmpp XMPP Component details.
     * @return mix
     */
    function createXMPPComponent(Xmpp $xmpp);

    /**
     * Create a Zimlet.
     *
     * @param  string $name  Zimlet name.
     * @param  array  $attrs Attributes.
     * @return mix
     */
    function createZimlet($name, array $attrs = array());

    /**
     * Dedupe the blobs having the same digest.
     *
     * @param  DedupAction $action  Action to perform - one of start|status|stop.
     * @param  array  $volumes Volumes.
     * @return mix
     */
    function dedupeBlobs(DedupAction $action, array $volumes = array());

    /**
     * Used to request a new auth token that is valid for the specified account.
     * The id of the auth token will be the id of the target account,
     * and the requesting admin's id will be stored in the auth token for auditing purposes.
     *
     * @param  Account $account  The name used to identify the account.
     * @param  long   $duration Lifetime in seconds of the newly-created authtoken. defaults to 1 hour. Can't be longer then zimbraAuthTokenLifetime.
     * @return mix
     */
    function delegateAuth(Account $account, $duration = null);

    /**
     * Deletes the account with the given id.
     * Notes:
     *   1. If the request is sent to the server on which the mailbox resides,
     *      the mailbox is deleted as well.
     *   1. this request is by default proxied to the account's home server.
     *
     * @param  string $id  Zimbra identify.
     * @return mix
     */
    function deleteAccount($id);

    /**
     * Deletes the calendar resource with the given id.
     * Note: this request is by default proxied to the account's home server .
     * Access: domain admin sufficient.
     *
     * @param  string $id  Zimbra identify.
     * @return mix
     */
    function deleteCalendarResource($id);

    /**
     * Delete a Class of Service (COS).
     *
     * @param  string $id  Zimbra identify.
     * @return mix
     */
    function deleteCos($id);

    /**
     * Deletes the given data source.
     * Note: this request is by default proxied to the account's home server.
     *
     * @param  string $id     ID for an existing Account.
     * @param  Id $dataSource Data source ID.
     * @param  array  $attrs  Attributes.
     * @return mix
     */
    function deleteDataSource($id, Id $dataSource, array $attrs = array());

    /**
     * Delete a distribution list.
     * Access: domain admin sufficient.
     *
     * @param  string $id Zimbra ID for distribution list.
     * @return mix
     */
    function deleteDistributionList($id);

    /**
     * Delete a domain.
     *
     * @param  string $id Zimbra ID for domain.
     * @return mix
     */
    function deleteDomain($id);

    /**
     * Delete a Global Address List (GAL) Synchronisation account.
     * Remove its zimbraGalAccountID from the domain, then deletes the account.
     *
     * @param  Account $account The name used to identify the account.
     * @return mix
     */
    function deleteGalSyncAccount(Account $account);

    /**
     * Delete an LDAP entry.
     *
     * @param  string $dn A valid LDAP DN String (RFC 2253) that describes the DN to delete.
     * @return mix
     */
    function deleteLDAPEntry($dn);

    /**
     * Delete a mailbox.
     * The request includes the account ID (uuid) of the target mailbox on success,
     * the response includes the mailbox ID (numeric) of the deleted mailbox
     * the <mbox> element is left out of the response if no mailbox existed for that account.
     * Note: this request is by default proxied to the account's home server 
     * Access: domain admin sufficient
     *
     * @param  MailboxId $id Account ID.
     * @return mix
     */
    function deleteMailbox(MailboxId $id);

    /**
     * Delete a server.
     * Note: this request is by default proxied to the referenced server.
     *
     * @param  string $id Zimbra ID.
     * @return mix
     */
    function deleteServer($id);

    /**
     * Delete a system retention policy.
     *
     * @param  Policy  $policy Retention policy.
     * @param  Cos $cos The name used to identify the COS.
     * @return mix
     */
    function deleteSystemRetentionPolicy(Policy $policy, Cos $cos = null);

    /**
     * Delete a UC service.
     *
     * @param  string $id Zimbra ID.
     * @return mix
     */
    function deleteUCService($id);

    /**
     * Delete a UC service.
     *
     * @param  string $id Volume ID.
     * @return mix
     */
    function deleteVolume($id);

    /**
     * Delete an XMPP Component.
     *
     * @param  XmppComponent $xmpp The name used to identify the XMPP component.
     * @return mix
     */
    function deleteXMPPComponent(XmppComponent $xmpp);

    /**
     * Delete a Zimlet.
     *
     * @param  NamedElement $zimlet Zimlet name.
     * @return mix
     */
    function deleteZimlet(NamedElement $zimlet);

    /**
     * Deploy a Zimlet.
     *
     * @param  DeployAction $action Action - valid values : deployAll|deployLocal|status.
     * @param  Attachment $aid Attachment ID.
     * @param  bool $flush Flag whether to flush the cache.
     * @param  bool $synchronous Synchronous flag.
     * @return mix
     */
    function deployZimlet(
        DeployAction $action,
        Attachment $content = null,
        $flush = null,
        $synchronous = null
    );

    /**
     * Dump sessions.
     *
     * @param  bool $list List Sessions flag.
     * @param  bool $groupBy Group by account flag.
     * @return mix
     */
    function dumpSessions($list = null, $groupBy = null);

    /**
     * Exports the database data for the given items with SELECT INTO OUTFILE
     * and deletes the items from the mailbox.
     * Exported filenames follow the pattern {prefix}{table_name}.txt.
     * The files are written to sqlExportDir.
     * When sqlExportDir is not specified, data is not exported.
     * Export is only supported for MySQL.
     *
     * @param  Mailbox $mbox     Mailbox.
     * @param  string $exportDir Path for export dir.
     * @param  string $exportFilenamePrefix Export filename prefix.
     * @return mix
     */
    function exportAndDeleteItems(ExportMailbox $mbox, $exportDir = null, $exportFilenamePrefix = null);

    /**
     * Fix Calendar End Times.
     *
     * @param  bool  $sync    Sync flag.
     * @param  array $accounts Account names.
     * @return mix
     */
    function fixCalendarEndTime($sync = null, array $accounts = array());

    /**
     * Fix Calendar priority.
     *
     * @param  bool  $sync    Sync flag.
     * @param  array $account Account names.
     * @return mix
     */
    function fixCalendarPriority($sync = null, array $accounts = array());

    /**
     * Fix timezone definitions in appointments and tasks to reflect changes
     * in daylight savings time rules in various timezones.
     *
     * @param  bool  $sync       Sync flag.
     * @param  int   $after      Fix appts/tasks that have instances after this time, default = January 1, 2008 00:00:00 in GMT+13:00 timezone.
     * @param  array $accounts   Account names.
     * @param  array $fixupRules Fixup rules.
     * @return mix
     */
    function fixCalendarTZ(
        $sync = null,
        $after = null,
        array $accounts = array(),
        array $fixupRules = array()
    );

    /**
     * FixFlush memory cache for specified LDAP or directory scan type/entries.
     * Directory scan caches(source of data is on local disk of the server): skin|locale
     * LDAP caches(source of data is LDAP): account|cos|domain|server|zimlet.
     * 
     * For LDAP caches, one or more optional <entry> can be specified. 
     * If <entry>(s) are specified, only the specified entries will be flushed.
     * If no <entry> is given, all enties of the type will be flushed from cache.
     * Type can contain a combination of skin, locale and zimlet.
     * E.g. type='skin,locale,zimlet' or type='zimletskin'.
     *
     * @param  Cache $cache Cache.
     * @return mix
     */
    function flushCache(Cache $cache = null);

    /**
     * Request a certificate signing request (CSR).
     *
     * @param string $server Server ID
     * @param bool $isNew If value is "1" then force to create a new CSR, the previous one will be overwrited
     * @param CSRType $type Type of CSR
     * @param CSRKeySize $keysize Key size
     * @param string $c Subject attr C
     * @param string $sT Subject attr ST
     * @param string $l Subject attr L
     * @param string $o Subject attr O
     * @param string $oU Subject attr OU
     * @param string $cN Subject attr CN
     * @param array $subjectAltName Used to add the Subject Alt Name extension in the certificate, so multiple hosts can be supported
     * @return mix
     */
    function genCSR($server,
        $isNew,
        CSRType $type,
        CSRKeySize $keysize,
        $c = null,
        $sT = null,
        $l = null,
        $o = null,
        $oU = null,
        $cN = null,
        array $subjectAltName = array()
    );

    /**
     * Get attributes related to an account.
     * {request-attrs} - comma-seperated list of attrs to return 
     * Note: this request is by default proxied to the account's home server 
     * Access: domain admin sufficient
     *
     * @param  Account $account  The name used to identify the account.
     * @param  bool    $applyCos Flag whether or not to apply class of service (COS) rules.
     * @param  string  $attrs    Comma separated list of attributes.
     * @return mix
     */
    function getAccount(Account $account = null, $applyCos = null, $attrs = null);

    /**
     * Get information about an account.
     * Currently only 2 attrs are returned:
     *   zimbraId    the unique UUID of the zimbra account
     *   zimbraMailHost  the server on which this user's mail resides 
     * Access: domain admin sufficient
     *
     * @param  Account $account The name used to identify the account.
     * @return mix
     */
    function getAccountInfo(Account $account);

    /**
     * Returns custom loggers created for the given account since the last server start.
     * If the request is sent to a server other than the one that the account resides on,
     * it is proxied to the correct server.
     *
     * @param  Account $account  The name used to identify the account.
     * @return mix
     */
    function getAccountLoggers(Account $account = null);

    /**
     * Get distribution lists an account is a member of.
     *
     * @param  Account $account The name used to identify the account.
     * @return mix
     */
    function getAccountMembership(Account $account);

    /**
     * Get distribution lists an account is a member of.
     *
     * @param  Account  $account The name used to identify the account.
     * @param  DistList $dl      The name used to identify the distribution list.
     * @return mix
     */
    function getAdminConsoleUIComp(Account $account = null, DistList $dl = null);

    /**
     * Returns the admin extension addon Zimlets.
     *
     * @return mix
     */
    function getAdminExtensionZimlets();

    /**
     * Returns admin saved searches.
     * If no <search> is present server will return all saved searches.
     *
     * @param  NamedElement $search The search name.
     * @return mix
     */
    function getAdminSavedSearches(NamedElement $search);

    /**
     * Gets the aggregate quota usage for all domains on the server.
     *
     * @return mix
     */
    function getAggregateQuotaUsageOnServer();

    /**
     * Returns all account loggers that have been created on the given server
     * since the last server start.
     *
     * @return mix
     */
    function getAllAccountLoggers();

    /**
     * Get All accounts matching the selectin criteria.
     * Access: domain admin sufficient
     *
     * @param  Server $server The server name.
     * @param  Domain $domain The domain name.
     * @return mix
     */
    function getAllAccounts(Server $server = null, Domain $domain = null);

    /**
     * Get all Admin accounts.
     *
     * @param  string $applyCos Apply COS.
     * @return mix
     */
    function getAllAdminAccounts($applyCos = null);

    /**
     * Get all calendar resources that match the selection criteria.
     * Access: domain admin sufficient.
     *
     * @param  Server $server The server name.
     * @param  Domain $domain The domain name.
     * @return mix
     */
    function getAllCalendarResources(Server $server = null, Domain $domain = null);

    /**
     * Get all config.
     *
     * @return mix
     */
    function getAllConfig();

    /**
     * Get all classes of service (COS).
     *
     * @return mix
     */
    function getAllCos();

    /**
     * Get all calendar resources that match the selection criteria.
     * Access: domain admin sufficient.
     *
     * @param  Domain $domain The domain name.
     * @return mix
     */
    function getAllDistributionLists(Domain $domain = null);

    /**
     * Get all domains.
     *
     * @param  bool $applyConfig Apply config flag.
     * @return mix
     */
    function getAllDomains($applyConfig = null);

    /**
     * Get all effective Admin rights.
     *
     * @param  Grantee $grantee The name used to identify the grantee.
     * @param  string $expandAllAttrs Flags whether to include all attribute names if the right is meant for all attributes.
     * @return mix
     */
    function getAllEffectiveRights(Grantee $grantee = null, $expandAllAttrs = null);

    /**
     * Get all free/busy providers.
     *
     * @return mix
     */
    function getAllFreeBusyProviders();

    /**
     * Get all free/busy providers.
     *
     * @return mix
     */
    function getAllLocales();

    /**
     * Return all mailboxes.
     * Returns all data from the mailbox table (in db.sql), except for the "comment" column.
     *
     * @param  integer $limit  The number of mailboxes to return (0 is default and means all).
     * @param  integer $offset The starting offset (0, 25, etc).
     * @return mix
     */
    function getAllMailboxes($limit = null, $offset = null);

    /**
     * Get all effective Admin rights.
     *
     * @param  string $targetType Target type on which a right is grantable.
     * @param  bool $expandAllAttrs Flags whether to include all attribute names in the <attrs> elements in GetRightResponse if the right is meant for all attributes.
     * @param  RightClass $rightClass Right class to return (ADMIN|USER|ALL).
     * @return mix
     */
    function getAllRights(
        $targetType = null, $expandAllAttrs = null, RightClass $rightClass = null
    );

    /**
     * Get all servers defined in the system or all servers that
     * have a particular service enabled (eg, mta, antispam, spell).
     * If {apply} is 1 (true), then certain unset attrs on a server
     * will get their value from the global config. 
     * If {apply} is 0 (false), then only attributes directly set on the server will be returned
     *
     * @param  string $service Service name. e.g. mta, antispam, spell.
     * @param  bool   $apply   Apply config flag.
     * @return mix
     */
    function getAllServers($service = null, $applyConfig = null);

    /**
     * Get all installed skins on the server.
     *
     * @return mix
     */
    function getAllSkins();

    /**
     * Returns all installed UC providers and applicable UC service attributes for each provider.
     *
     * @return mix
     */
    function getAllUCProviders();

    /**
     * Get all ucservices defined in the system.
     *
     * @return mix
     */
    function getAllUCServices();

    /**
     * Get all volumes.
     *
     * @return mix
     */
    function getAllVolumes();

    /**
     * Get all XMPP components.
     *
     * @return mix
     */
    function getAllXMPPComponents();

    /**
     * Get all Zimlets.
     *
     * @param  ExcludeType $exclude Can be "none|extension|mail". extension: return only mail Zimlets. mail: return only admin extensions. none [default]: return both mail and admin zimlets.
     * @return mix
     */
    function getAllZimlets(ExcludeType $exclude = null);

    /**
     * Get attribute information.
     * Valid entry types:
     *   account,alias,distributionList,cos,globalConfig,domain,server,mimeEntry,zimletEntry,
     *   calendarResource,identity,dataSource,pop3DataSource,imapDataSource,rssDataSource,
     *   liveDataSource,galDataSource,signature,xmppComponent,aclTarget
     *
     * @param  string $attrs      Comma separated list of attributes to return.
     * @param  array  $entryTypes Attributes on the specified entry types will be returned.
     * @return mix
     */
    function getAttributeInfo($attrs = null, array $entryTypes = array());

    /**
     * Get a calendar resource.
     * Access: domain admin sufficient.
     *
     * @param  Calendar $calResource Specify calendar resource.
     * @param  bool $applyCos Flag whether to apply Class of Service (COS).
     * @param  string $attrs Comma separated list of attributes.
     * @return mix
     */
    function getCalendarResource(Calendar $calResource = null, $applyCos = null, $attrs = null);

    /**
     * Get Certificate.
     * Currently, GetCertRequest/Response only handle 2 types "staged" and "all".
     * May need to support other options in the future.
     *
     * @param  string $server The server's ID whose cert is to be got.
     * @param  CertType $type Certificate type. Value: staged - view the staged crt. Other options (all, mta, ldap, mailboxd, proxy) are used to view the deployed crt
     * @param  CSRType $option Required only when type is "staged". Could be "self" (self-signed cert) or "comm" (commerical cert).
     * @return mix
     */
    function getCert($server, CertType $type, CSRType $option = null);

    /**
     * Get Config request.
     *
     * @param  array $attrs Array of attributes.
     * @return mix
     */
    function getConfig(array $attrs = array());

    /**
     * Get Class Of Service (COS).
     *
     * @param  Cos $cos The name used to identify the COS.
     * @param  string $attrs Comma separated list of attributes.
     * @return mix
     */
    function getCos(Cos $cos = null, $attrs = null);

    /**
     * Returns attributes, with defaults and constraints if any,
     * that can be set by the authed admin when an object is created.
     * Domain name.required if target type is account/calresource/dl/domain, ignored otherwise.
     *   1. if {target-type} is account/calresource/dl:
     *      This is the domain in which the object will be in.
     *      The domain can be speciffied by id or by name.
     *   2. if {target-type} is domain, it is the domain name to be created.
     *      E.g. to create a subdomain named foo.bar.test.com,
     *      should pass in <domain by="name">foo.bar.test.com</domain>.
     *
     * @param  TargetWithType $target Target.
     * @param  Domain $domain The name used to identify the domain.
     * @param  Cos $cos The name used to identify the COS..
     * @return mix
     */
    function getCreateObjectAttrs(TargetWithType $target, Domain $domain = null, Cos $cos = null);

    /**
     * Get a certificate signing request (CSR).
     *
     * @param  string $server Server ID. Can be "--- All Servers ---" or the ID of a server.
     * @param  CSRType $type Type of CSR (required). Value: self mean self-signed certificate; comm mean commercial certificate
     * @return mix
     */
    function getCSR($server = null, CSRType $type = null);

    /**
     * Get current volumes.
     *
     * @return mix
     */
    function getCurrentVolumes();

    /**
     * Returns all data sources defined for the given mailbox.
     * For each data source, every attribute value is returned except password.
     * Note: this request is by default proxied to the account's home server.
     *
     * @param  string $id    Account ID for an existing account.
     * @param  array  $attrs Array of attributes.
     * @return mix
     */
    function getDataSources($id, array $attrs = array());

    /**
     * Get constraints (zimbraConstraint) for delegated admin on global config or a COS
     * none or several attributes can be specified for which constraints are to be returned.
     * If no attribute is specified, all constraints on the global config/cos will be returned.
     * If there is no constraint for a requested attribute,
     * <a> element for the attribute will not appear in the response.
     *
     * @param  TargetType $type  Target type. Valid values: (account|calresource|cos|dl|group|domain|server|ucservice|xmppcomponent|zimlet|config|global).
     * @param  string $id    ID of target.
     * @param  string $name  Name of target.
     * @param  array  $attrs Array of name.
     * @return mix
     */
    function getDelegatedAdminConstraints(
        TargetType $type,
        $id = null,
        $name = null,
        array $attrs = array()
    );

    /**
     * Get devices.
     *
     * @param  Account $account The name used to identify the account.
     * @return mix
     */
    function getDevices(Account $account);

    /**
     * Get a Distribution List.
     *
     * @param  DistList $dl     The name used to identify the distribution list.
     * @param  integer  $limit  The maximum number of accounts to return (0 is default and means all).
     * @param  integer  $offset The starting offset (0, 25 etc).
     * @param  bool     $sortAscending Flag whether to sort in ascending order 1 (true) is the default.
     * @param  array    $attrs  Attributes.
     * @return mix
     */
    function getDistributionList(
        DistList $dl = null,
        $limit = null,
        $offset = null,
        $sortAscending = null,
        array $attrs = array()
    );

    /**
     * Request a list of DLs that a particular DL is a member of.
     *
     * @param  DistList $dl     The name used to identify the distribution list.
     * @param  integer  $limit  The maximum number of DLs to return (0 is default and means all).
     * @param  integer  $offset The starting offset (0, 25 etc).
     * @return mix
     */
    function getDistributionListMembership(
        DistList $dl = null,
        $limit = null,
        $offset = null
    );

    /**
     * Get information about a domain.
     * 
     * @param  Domain $domain The name used to identify the domain.
     * @param  bool   $applyConfig Apply config flag. True, then certain unset attrs on a domain will get their values from the global config. False, then only attributes directly set on the domain will be returned.
     * @param  string $attrs  Attributes.
     * @return mix
     */
    function getDomain(Domain $domain = null, $applyConfig = null, $attrs = null);

    /**
     * Get Domain information.
     * This call does not require an auth token.
     * It returns attributes that are pertinent to domain settings
     * for cases when the user is not authenticated.
     * For example, URL to direct the user to upon logging out or when auth token is expired.
     * 
     * @param  Domain $domain The name used to identify the domain.
     * @param  bool   $applyConfig Apply config flag. True, then certain unset attrs on a domain will get their values from the global config. False, then only attributes directly set on the domain will be returned.
     * @return mix
     */
    function getDomainInfo(Domain $domain = null, $applyConfig = null);

    /**
     * Returns effective ADMIN rights the authenticated admin has on the specified target entry.
     * Effective rights are the rights the admin is actually allowed.
     * It is the net result of applying ACL checking rules given the target and grantee.
     * Specifically denied rights will not be returned.
     * 
     * @param  Target $target  The name used to identify the target.
     * @param  string $type    Target type. Valid values: (account|calresource|cos|dl|group|domain|server|ucservice|xmppcomponent|zimlet|config|global).
     * @param  Grantee  $grantee Grantee.
     * @param  AttrMethod $expandAllAttrs  Whether to include all attribute names in the <getAttrs>/<setAttrs> elements in the response if all attributes of the target are gettable/settable.
     *                         Valid values are:
     *                         1. getAttrs: expand attrs in getAttrs in the response
     *                         2. setAttrs: expand attrs in setAttrs in the response
     *                         3. getAttrs,setAttrs: expand attrs in both getAttrs and setAttrs in the response
     * @return mix
     */
    function getEffectiveRights(
        Target $target,
        Grantee $grantee = null,
        AttrMethod $expandAllAttrs = null
    );

    /**
     * Get Free/Busy provider information.
     * If the optional element <provider/> is present in the request, the response contains the requested provider only.
     * If no provider is supplied in the request, the response contains all the providers.
     * 
     * @param  NamedElement $provider Provider name.
     * @return mix
     */
    function getFreeBusyQueueInfo(NamedElement $provider);

    /**
     * Returns all grants on the specified target entry,
     * or all grants granted to the specified grantee entry. 
     * The authenticated admin must have an effective "viewGrants"
     * (TBD) system right on the specified target/grantee. 
     * At least one of <target> or <grantee> must be specified.
     * If both <target> and <grantee> are specified, only grants that are granted
     * on the target to the grantee are returned.
     * 
     * @param  Target $target The name used to identify the target.
     * @param  Grantee $grantee Grantee.
     * @return mix
     */
    function getGrants(Target $target = null, Grantee $grantee = null);

    /**
     * Get index statistics.
     * 
     * @param  MailboxId $id  Mailbox account ID.
     * @return mix
     */
    function getIndexStats(MailboxId $mbox);

    /**
     * Get index statistics.
     * 
     * @param  string  $query Query string. Should be an LDAP-style filter string (RFC 2254).
     * @param  string  $ldapSearchBase LDAP search base. An LDAP-style filter string that defines an LDAP search base (RFC 2254).
     * @param  string  $sortBy Name of attribute to sort on. default is null.
     * @param  bool    $sortAscending Flag whether to sort in ascending order 1 (true) is default.
     * @param  integer $limit Limit - the maximum number of LDAP objects (records) to return (0 is default and means all).
     * @param  integer $offset The starting offset (0, 25, etc).
     * @return mix
     */
    function getLDAPEntries(
        $query,
        $ldapSearchBase,
        $sortBy = null,
        $sortAscending = null,
        $limit = null,
        $offset = null
    );

    /**
     * Get License information.
     * 
     * @return mix
     */
    function getLicenseInfo();

    /**
     * Query to retrieve Logger statistics in ZCS.
     * Use cases:
     *   1. No elements specified. Result: a listing of reporting host names.
     *   2. Hostname specified. Result: a listing of stat groups for the specified host.
     *   3. Hostname and stats specified, text content of stats non-empty.
     *      Result: a listing of columns for the given host and group
     *   4. Hostname and stats specified, text content empty, startTime/endTime optional.
     *      Result: all of the statistics for the given host/group are returned,
     *      if start and end are specified, limit/expand the timerange to the given setting.
     *      If limit=true is specified, attempt to reduce result set to under 500 records
     * 
     * @param  HostName $hostname Hostname.
     * @param  StatsSpec $stats Stats specification.
     * @param  TimeAttr $startTime Start time.
     * @param  TimeAttr $endTime   End time .
     * @return mix
     */
    function getLoggerStats(
        HostName $hostname = null,
        StatsSpec $stats = null,
        TimeAttr $startTime = null,
        TimeAttr $endTime = null
    );

    /**
     * Get a Mailbox.
     * Note: this request is by default proxied to the account's home server.
     * 
     * @param  MailboxId $id Mailbox account ID.
     * @return mix
     */
    function getMailbox(MailboxId $mbox);

    /**
     * Get MailBox Statistics.
     * 
     * @return mix
     */
    function getMailboxStats();

    /**
     * Summarize and/or search a particular mail queue on a particular server.
     * The admin SOAP server initiates a MTA queue scan (via ssh)
     * and then caches the result of the queue scan.
     * To force a queue scan, specify scan=1 in the request.
     * The response has two parts.
     *   1. <qs> elements summarize queue by various types of data (sender addresses,
     *      recipient domain, etc). Only the deferred queue has error summary type.
     *   2. <qi> elements list the various queue items that match the requested query.
     * The stale-flag in the response means that since the scan,
     * some queue action was done and the data being presented is now stale.
     * This allows us to let the user dictate when to do a queue scan.
     * The scan-flag in the response indicates that the server has not completed scanning
     * the MTA queue, and that this scan is in progress,
     * and the client should ask again in a little while. 
     * The more-flag in the response indicates that more qi's are available past the limit specified in the request.
     * 
     * @param  ServerMail  $server Server Mail Queue Query.
     * @return mix
     */
    function getMailQueue(ServerMail $server);

    /**
     * Get a count of all the mail queues by counting the number of files in the queue directories.
     * Note that the admin server waits for queue counting to complete before responding
     * - client should invoke requests for different servers in parallel.
     * 
     * @param  NamedElement $server MTA server name.
     * @return mix
     */
    function getMailQueueInfo(NamedElement $server);

    /**
     * Returns the memcached client configuration on a mailbox server.
     * 
     * @return mix
     */
    function getMemcachedClientConfig();

    /**
     * Returns the memcached client configuration on a mailbox server.
     * 
     * @param  string  $domain Domain - the domain name to limit the search to.
     * @param  bool    $allServers Whether to fetch quota usage for all domain accounts from across all mailbox servers, default is false, applicable when domain attribute is specified.
     * @param  integer $limit Limit - the number of accounts to return (0 is default and means all).
     * @param  integer $offset Offset - the starting offset (0, 25, etc).
     * @param  QuotaSortBy $sortBy SortBy - valid values: "percentUsed", "totalUsed", "quotaLimit".
     * @param  bool    $sortAscending Whether to sort in ascending order 0 (false) is default, so highest quotas are returned first.
     * @param  bool    $refresh Refresh - whether to always recalculate the data even when cached values are available. 0 (false) is the default..
     * @return mix
     */
    function getQuotaUsage(
        $domain = null,
        $allServers = null,
        $limit = null,
        $offset = null,
        QuotaSortBy $sortBy = null,
        $sortAscending = null,
        $refresh = null
    );

    /**
     * Get definition of a right.
     * 
     * @param  string $right  Right name.
     * @param  bool   $expandAllAttrs Whether to include all attribute names in the <attrs> elements in the response if the right is meant for all attributes.
     *                        0 (false) [default] default, do not include all attribute names in the <attrs> elements.
     *                        1 (true)  include all attribute names in the <attrs> elements.
     * @return mix
     */
    function getRight($right, $expandAllAttrs = null);

    /**
     * Get Rights Document.
     * 
     * @param  array $packages Packages.
     * @return mix
     */
    function getRightsDoc(array $packages = array());

    /**
     * Get Server.
     * 
     * @param  Server $server Server.
     * @param  bool   $applyConfig Apply config flag.
     *                        If {apply} is 1 (true), then certain unset attrs on a server will get their values from the global config. 
     *                        if {apply} is 0 (false), then only attributes directly set on the server will be returned.
     * @param  string  $attrs Comma separated list of attributes.
     * @return mix
     */
    function getServer(Server $server = null, $applyConfig = null, $attrs = null);

    /**
     * Get Network Interface information for a server.
     * Get server's network interfaces. Returns IP addresses and net masks.
     * This call will use zmrcd to call /opt/zimbra/libexec/zmserverips
     * 
     * @param  Server $server Server name.
     * @param  IpType $type   Specifics the ipAddress type (ipV4/ipV6/both). default is ipv4.
     * @return mix
     */
    function getServerNIfs(Server $server, IpType $type = null);

    /**
     * Returns server monitoring stats.
     * These are the same stats that are logged to mailboxd.csv.
     * If no <stat> element is specified, all server stats are returned.
     * If the stat name is invalid, returns a SOAP fault.
     * 
     * @param  array $stats Stats.
     * @return mix
     */
    function getServerStats(array $stats = array());

    /**
     * Get Service Status.
     * 
     * @return mix
     */
    function getServiceStatus();

    /**
     * Get Sessions.
     * Access: domain admin sufficient (though a domain admin can't specify "domains" as a type).
     * 
     * @param  SessionType  $type Type - valid values soap|imap|admin.
     * @param  GetSessionsSortBy $sortBy Sort by - valid values nameAsc|nameDesc|createdAsc|createdDesc|accessedAsc|accessedDesc.
     * @param  integer $limit Limit - the number of sessions to return per page (0 is default and means all).
     * @param  integer $offset Offset - the starting offset (0, 25, etc).
     * @param  bool    $refresh Refresh. If 1 (true), ignore any cached results and start fresh..
     * @return mix
     */
    function getSessions(
        SessionType $type,
        GetSessionsSortBy $sortBy = null,
        $limit = null,
        $offset = null,
        $refresh = null
    );

    /**
     * Iterate through all folders of the owner's mailbox and return shares
     * that match grantees specified by the <grantee> specifier.
     * 
     * @param  string $owner The name used to identify the account.
     * @param  string $type  If specified, filters the result by the specified grantee type.
     * @param  string $name  If specified, filters the result by the specified grantee name.
     * @param  string $id    If specified, filters the result by the specified grantee ID.
     * @return mix
     */
    function getShareInfo(Account $owner, GranteeChooser $grantee = null);

    /**
     * Get System Retention Policy.
     * The system retention policy SOAP APIs allow the administrator
     * to edit named system retention policies that users can apply to folders and tags.
     * 
     * @param  string $cos The name used to identify the COS.
     * @return mix
     */
    function getSystemRetentionPolicy(Cos $cos = null);

    /**
     * Get UC Service.
     * 
     * @param  UcService $ucservice UC Service name.
     * @param  string $attrs Comma separated list of attributes.
     * @return mix
     */
    function getUCService(UcService $ucservice = null, $attrs = null);

    /**
     * Get Version information.
     * 
     * @return mix
     */
    function getVersionInfo();

    /**
     * Get Volume.
     * 
     * @param  int $id ID of volume.
     * @return mix
     */
    function getVolume($id);

    /**
     * Get XMPP Component.
     * XMPP stands for Extensible Messaging and Presence Protocol.
     * 
     * @param  XmppComponent $xmpp XMPP Component selector.
     * @param  string $attrs Comma separated list of attributes.
     * @return mix
     */
    function getXMPPComponent(XmppComponent $xmpp, $attrs = null);

    /**
     * Retreives a list of search tasks running or cached on a server.
     * 
     * @param  NamedElement $name Zimlet name.
     * @param  string $attrs Comma separated list of attributes.
     * @return mix
     */
    function getZimlet(NamedElement $name, $attrs = null);

    /**
     * Get status for Zimlets.
     * Priority is listed in the global list <zimlets> ... </zimlets> only.
     * This is because the priority value is relative to other Zimlets in the list.
     * The same Zimlet may show different priority number depending
     * on what other Zimlets priorities are.
     * The same Zimlet will show priority 0 if all by itself,
     * or priority 3 if there are three other Zimlets with higher priority.
     * 
     * @return mix
     */
    function getZimletStatus();

    /**
     * Grant a right on a target to an individual or group grantee.
     * 
     * @param  Target $target  Target selector. The name used to identify the target.
     * @param  Grantee  $grantee Grantee selector.
     * @param  RightModifier  $right   Right selector.
     * @return mix
     */
    function grantRight(
        Target $target,
        Grantee $grantee,
        RightModifier $right
    );

    /**
     * Command to act on invidual queue files.
     * This proxies through to postsuper.
     * list-of-ids can be ALL.
     * 
     * @param  ServerQueue $server Server Mail Queue Query.
     * @return mix
     */
    function mailQueueAction(ServerQueue $server);

    /**
     * Command to invoke postqueue -f.
     * All queues cached in the server are stale after invoking this because
     * this is a global operation to all the queues in a given server.
     * 
     * @param  NamedElement $server MTA server.
     * @return mix
     */
    function mailQueueFlush(NamedElement $server);

    /**
     * Migrate an account.
     * 
     * @param  IdAndAction $migrate Specification for the migration.
     * @return mix
     */
    function migrateAccount(IdAndAction $migrate);

    /**
     * Modify an account.
     * 
     * @param  string $id    Zimbra ID of account.
     * @param  array  $attrs Attributes.
     * @return mix
     */
    function modifyAccount($id, array $attrs = array());

    /**
     * Modifies admin saved searches.
     * Returns the admin saved searches.
     * If {search-query} is empty => delete the search if it exists.
     * If {search-name} already exists => replace with new {search-query}.
     * If {search-name} does not exist => save as a new search.
     * 
     * @param  array $searchs Array of NamedValue.
     * @return mix
     */
    function modifyAdminSavedSearches(array $searchs = array());

    /**
     * Modify a calendar resource.
     * Notes:
     *   1. an empty attribute value removes the specified attr.
     *   2. this request is by default proxied to the resources's home server.
     * Access: domain admin sufficient. limited set of attributes that can be updated by a domain admin.
     * 
     * @param  string $id    Zimbra ID.
     * @param  array  $attrs Attributes.
     * @return mix
     */
    function modifyCalendarResource($id, array $attrs = array());

    /**
     * Modify Configuration attributes.
     * Note: an empty attribute value removes the specified attr.
     * 
     * @param  array $attrs Attributes.
     * @return mix
     */
    function modifyConfig(array $attrs = array());

    /**
     * Modify Class of Service (COS) attributes.
     * Note: an empty attribute value removes the specified attr.
     * 
     * @param  string $id    Zimbra ID.
     * @param  array  $attrs Attributes.
     * @return mix
     */
    function modifyCos($id, array $attrs = array());

    /**
     * Changes attributes of the given data source.
     * Only the attributes specified in the request are modified.
     * To change the name, specify "zimbraDataSourceName" as an attribute.
     * Note: this request is by default proxied to the account's home server
     * 
     * @param  string $id     Existing account ID.
     * @param  Id $dataSource Data source  ID.
     * @param  array  $attrs  Attributes.
     * @return mix
     */
    function modifyDataSource($id, Id $dataSource, array $attrs = array());

    /**
     * Modify constraint (zimbraConstraint) for delegated admin on global config or a COS.
     * If constraints for an attribute already exists, it will be replaced by the new constraints.
     * I <constraint> is an empty element, constraints for the attribute will be removed.
     * 
     * @param  TargetType $type  Target type. Valid values: (account|calresource|cos|dl|group|domain|server|ucservice|xmppcomponent|zimlet|config|global).
     * @param  string $id    ID.
     * @param  string $name  Name.
     * @param  array  $attrs Constaint attributes.
     * @return mix
     */
    function modifyDelegatedAdminConstraints(
        TargetType $type,
        $id = null,
        $name = null,
        array $attrs = array()
    );

    /**
     * Modify attributes for a Distribution List.
     * Notes: an empty attribute value removes the specified attr.
     * Access: domain admin sufficient.
     * 
     * @param  string $id    Zimbra ID.
     * @param  array  $attrs Attributes.
     * @return mix
     */
    function modifyDistributionList($id, array $attrs = array());

    /**
     * Modify attributes for a domain.
     * Note: an empty attribute value removes the specified attr.
     * 
     * @param  string $id    Zimbra ID.
     * @param  array  $attrs Attributes.
     * @return mix
     */
    function modifyDomain($id, array $attrs = array());

    /**
     * Modify an LDAP Entry.
     * 
     * @param  string $dn    A valid LDAP DN String (RFC 2253) that identifies the LDAP object.
     * @param  array  $attrs Attributes.
     * @return mix
     */
    function modifyLDAPEntry($dn, array $attrs = array());

    /**
     * Modify attributes for a server.
     * Notes:
     *   1. An empty attribute value removes the specified attr.
     *   2. His request is by default proxied to the referenced server.
     * 
     * @param  string $id    Zimbra ID.
     * @param  array  $attrs Attributes.
     * @return mix
     */
    function modifyServer($id, array $attrs = array());

    /**
     * Modify system retention policy.
     * 
     * @param  Policy $policy New policy.
     * @param  Cos $cos The name used to identify the COS.
     * @return mix
     */
    function modifySystemRetentionPolicy(Policy $policy, Cos $cos = null);

    /**
     * Modify attributes for a UC service.
     * Notes: An empty attribute value removes the specified attr
     * 
     * @param  string $id    Zimbra ID.
     * @param  array  $attrs Attributes.
     * @return mix
     */
    function modifyUCService($id, array $attrs = array());

    /**
     * Modify volume.
     * 
     * @param  string $id     Zimbra ID.
     * @param  Volume $volume Volume information.
     * @return mix
     */
    function modifyVolume($id, Volume $volume);

    /**
     * Modify Zimlet.
     * 
     * @param  ZimletAcl $zimlet Zimlet information.
     * @return mix
     */
    function modifyZimlet(ZimletAcl $zimlet);

    /**
     * A request that does nothing and always returns nothing.
     * Used to keep an admin session alive.
     * 
     * @return mix
     */
    function noOp();

    /**
     * Ping.
     * 
     * @return mix
     */
    function ping();

    /**
     * Purge the calendar cache for an account.
     * Access: domain admin sufficient.
     * 
     * @param  string $id Zimbra ID.
     * @return mix
     */
    function purgeAccountCalendarCache($id);

    /**
     * Purges the queue for the given freebusy provider on the current host.
     * 
     * @param  NamedElement $provider Provider name.
     * @return mix
     */
    function purgeFreeBusyQueue(NamedElement $provider = null);

    /**
     * Purges aged messages out of trash, spam, and entire mailbox.
     * (if <mbox> element is omitted, purges all mailboxes on server).
     * 
     * @param  MailboxId $mbox Mailbox Account ID.
     * @return mix
     */
    function purgeMessages(MailboxId $mbox);

    /**
     * Push Free/Busy.
     * The request must include either <domain/> or <account/>.
     * When <domain/> is specified in the request, the server will push
     * the free/busy for all the accounts in the domain to the configured free/busy providers.
     * When <account/> list is specified, the server will push the free/busy for
     * the listed accounts to the providers.
     * 
     * @param  Names $domains Domain names specification.
     * @param  Id $account Account ID.
     * @return mix
     */
    function pushFreeBusy(Names $domain = null, Id $account = null);

    /**
     * Query WaitSet.
     * This API dumps the internal state of all active waitsets.
     * It is intended for debugging use only and should not be used for production uses.
     * This API is not guaranteed to be stable between releases in any way
     * and might be removed without warning.
     * 
     * @param  string $waitSet WaitSet ID.
     * @return mix
     */
    function queryWaitSet($waitSet = null);

    /**
     * ReIndex.
     * Access: domain admin sufficient.
     * Note: This request is by default proxied to the account's home server.
     * Note: Only one of {ids} and {types} may be specified.
     * 
     * @param  ReindexMailbox $mbox  Specify reindexing to perform.
     * @param  ReIndexAction $action Action to perform.
     * @return mix
     */
    function reIndex(ReindexMailbox $mbox, ReIndexAction $action = null);

    /**
     * Recalculate Mailbox counts.
     * Forces immediate recalculation of total mailbox quota usage and all folder unread
     * and size counts.
     * Access: domain admin sufficient.
     * Note: this request is by default proxied to the account's home server.
     * 
     * @param  MailboxId $mbox Specify reindexing to perform.
     * @return mix
     */
    function recalculateMailboxCounts(MailboxId $mbox);

    /**
     * Reload LocalConfig.
     * 
     * @return mix
     */
    function reloadLocalConfig();

    /**
     * Reloads the memcached client configuration on this server.
     * Memcached client layer is reinitialized accordingly.
     * Call this command after updating the memcached server list, for example.
     * 
     * @return mix
     */
    function reloadMemcachedClientConfig();

    /**
     * Remove Account Alias.
     * Access: domain admin sufficient.
     * Note: this request is by default proxied to the account's home server.
     * 
     * @param  string $alias Account alias.
     * @param  string $id    Zimbra ID.
     * @return mix
     */
    function removeAccountAlias($alias, $id = null);

    /**
     * Removes one or more custom loggers.
     * If both the account and logger are specified, removes the given account logger if it exists.
     * If only the account is specified or the category is "all",
     * removes all custom loggers from that account.
     * If only the logger is specified, removes that custom logger from all accounts.
     * If neither element is specified, removes all custom loggers from all accounts
     * on the server that receives the request.
     * 
     * @param  Account $account Use to select account.
     * @param  Logger  $logger  Logger category.
     * @return mix
     */
    function removeAccountLogger(Account $account = null, Logger $logger = null);

    /**
     * Remove a device or remove all devices attached to an account.
     * This will not cause a reset of sync data, but will cause a reset of policies on the next sync.
     * 
     * @param  Account  $account  Use to select account.
     * @param  DeviceId $deviceId Device specification - Note - if not supplied ALL devices will be removed.
     * @return mix
     */
    function removeDevice(Account $account, DeviceId $device = null);

    /**
     * Remove Distribution List Alias.
     * Access: domain admin sufficient.
     * 
     * @param  string $id    Zimbra ID
     * @param  string $alias Distribution list alias.
     * @return mix
     */
    function removeDistributionListAlias($id, $alias);

    /**
     * Remove Distribution List Member.
     * Unlike add, remove of a non-existent member causes an exception and no modification to the list. 
     * Access: domain admin sufficient.
     * 
     * @param  string $id   Zimbra ID
     * @param  array  $dlms Members.
     * @return mix
     */
    function removeDistributionListMember($id, array $dlms);

    /**
     * Rename Account.
     * Access: domain admin sufficient.
     * Note: this request is by default proxied to the account's home server. 
     * 
     * @param  string $id      Zimbra ID
     * @param  array  $newName New account name.
     * @return mix
     */
    function renameAccount($id, $newName);

    /**
     * Rename Calendar Resource.
     * Access: domain admin sufficient.
     * Note: this request is by default proxied to the account's home server. 
     * 
     * @param  string $id      Zimbra ID
     * @param  array  $newName New Calendar Resource name.
     * @return mix
     */
    function renameCalendarResource($id, $newName);

    /**
     * Rename Class of Service (COS).
     * 
     * @param  string $id      Zimbra ID
     * @param  array  $newName New COS name.
     * @return mix
     */
    function renameCos($id, $newName);

    /**
     * Rename Distribution List.
     * Access: domain admin sufficient.
     * 
     * @param  string $id      Zimbra ID
     * @param  array  $newName New Distribution List name.
     * @return mix
     */
    function renameDistributionList($id, $newName);

    /**
     * Rename LDAP Entry.
     * 
     * @param  string $dn     A valid LDAP DN String (RFC 2253) that identifies the LDAP object
     * @param  array  $new_dn New DN - a valid LDAP DN String (RFC 2253) that describes the new DN to be given to the LDAP object.
     * @return mix
     */
    function renameLDAPEntry($dn, $new_dn);

    /**
     * Rename Unified Communication Service.
     * 
     * @param  string $id      Zimbra ID
     * @param  array  $newName New UC Service name.
     * @return mix
     */
    function renameUCService($id, $newName);

    /**
     * Removes all account loggers and reloads /opt/zimbra/conf/log4j.properties.
     * 
     * @return mix
     */
    function resetAllLoggers();

    /**
     * Resume sync with a device or all devices attached to an account if currently suspended.
     * This will cause a policy reset, but will not reset sync data.
     * 
     * @param  Account  $account The name used to identify the account.
     * @param  DeviceId $device  Device ID.
     * @return mix
     */
    function resumeDevice(Account $account, DeviceId $device = null);

    /**
     * Revoke a right from a target that was previously granted to an individual or group grantee.
     * 
     * @param  Target $target  Target selector. The name used to identify the target.
     * @param  Grantee  $grantee Grantee selector.
     * @param  RightModifier  $right   Right selector.
     * @return mix
     */
    function revokeRight(
        Target $target,
        Grantee $grantee,
        RightModifier $right
    );

    /**
     * Runs the server-side unit test suite.
     * If <test>'s are specified, then run the requested tests (instead of the standard test suite).
     * Otherwise the standard test suite is run.
     * 
     * @param  string $tests Array test name.
     * @return mix
     */
    function runUnitTests(array $tests = array());

    /**
     * Search Accounts.
     * Access: domain admin sufficient (a domain admin can't specify "domains" as a type).
     * 
     * @param  string  $query  Query string - should be an LDAP-style filter string (RFC 2254).
     * @param  integer $limit  The maximum number of accounts to return (0 is default and means all).
     * @param  integer $offset The starting offset (0, 25, etc).
     * @param  string  $domain The domain name to limit the search to.
     * @param  bool    $applyCos Flag whether or not to apply the COS policy to account. Specify 0 (false) if only requesting attrs that aren't inherited from COS.
     * @param  array   $attrs Array of attrs to return ("displayName", "zimbraId", "zimbraAccountStatus").
     * @param  string  $sortBy Name of attribute to sort on. Default is the account name.
     * @param  array   $types Array of types to return. Legal values are: accounts|resources (default is accounts).
     * @param  bool    $sortAscending Whether to sort in ascending order. Default is 1 (true).
     * @return mix
     */
    function searchAccounts(
        $query,
        $limit = null,
        $offset = null,
        $domain = null,
        $applyCos = null,
        $attrs = null,
        $sortBy = null,
        $types = null,
        $sortAscending = null
    );

    /**
     * Search Auto Prov Directory.
     * Only one of <name> or <query> can be provided.
     * If neither is provided, the configured search filter for auto provision will be used.
     * 
     * @param  Domain  $domain  Domain name to limit the search to (do not use if searching for domains).
     * @param  string  $keyAttr Name of attribute for the key.
     * @param  string  $query   Query string - should be an LDAP-style filter string (RFC 2254).
     * @param  string  $name    Name to fill the auto provisioning search template configured on the domain.
     * @param  integer $maxResults Maximum results that the backend will attempt to fetch from the directory before returning an account.TOO_MANY_SEARCH_RESULTS error.
     * @param  integer $limit   The number of accounts to return per page (0 is default and means all).
     * @param  integer $offset  The starting offset (0, 25, etc).
     * @param  bool    $refresh Refresh - whether to always re-search in LDAP even when cached entries are available. 0 (false) is the default.
     * @param  array   $attrs   Array of attributes.
     * @return mix
     */
    function searchAutoProvDirectory(
        Domain $domain,
        $keyAttr,
        $query = null,
        $name = null,
        $maxResults = null,
        $limit = null,
        $offset = null,
        $refresh = null,
        $attrs = null
    );

    /**
     * Search for Calendar Resources.
     * Access: domain admin sufficient.
     * 
     * @param  SearchFilter  $searchFilter Search filter condition.
     * @param  integer $limit  The maximum number of calendar resources to return (0 is default and means all).
     * @param  integer $offset The starting offset (0, 25, etc).
     * @param  string  $domain The domain name to limit the search to.
     * @param  bool    $applyCos  Flag whether or not to apply the COS policy to calendar resource. Specify 0 (false) if only requesting attrs that aren't inherited from COS.
     * @param  string  $sortBy   Name of attribute to sort on. default is the calendar resource name.
     * @param  bool    $sortAscending    Whether to sort in ascending order. Default is 1 (true).
     * @param  string  $attrs  Comma separated list of attributes.
     * @return mix
     */
    function searchCalendarResources(
        SearchFilter $searchFilter = null,
        $limit = null,
        $offset = null,
        $domain = null,
        $applyCos = null,
        $sortBy = null,
        $sortAscending = null,
        $attrs = null
    );

    /**
     * Search directory.
     * Access: domain admin sufficient (though a domain admin can't specify "domains" as a type).
     * 
     * @param  string  $query       Query string - should be an LDAP-style filter string (RFC 2254).
     * @param  integer $maxResults         Maximum results that the backend will attempt to fetch from the directory before returning an account.TOO_MANY_SEARCH_RESULTS error.
     * @param  integer $limit       The maximum number of accounts to return (0 is default and means all).
     * @param  integer $offset      The starting offset (0, 25, etc).
     * @param  string  $domain      The domain name to limit the search to.
     * @param  bool    $applyCos    Flag whether or not to apply the COS policy to account. Specify 0 (false) if only requesting attrs that aren't inherited from COS.
     * @param  bool    $applyConfig Whether or not to apply the global config attrs to account. specify 0 (false) if only requesting attrs that aren't inherited from global config.
     * @param  bool    $countOnly   Whether response should be count only. Default is 0 (false).
     * @param  array   $attrs       Array of attributes.
     * @param  array   $types       Array of types to return. Legal values are: accounts|distributionlists|aliases|resources|domains|coses. (default is accounts)
     * @param  string  $sortBy      Name of attribute to sort on. Default is the account name.
     * @param  bool    $sortAscending Whether to sort in ascending order. Default is 1 (true).
     * @return mix
     */
    function searchDirectory(
        $query = null,
        $maxResults = null,
        $limit = null,
        $offset = null,
        $domain = null,
        $applyCos = null,
        $applyConfig = null,
        array $types = array(),
        $sortBy = null,
        $sortAscending = null,
        $countOnly = null,
        $attrs = null
    );

    /**
     * Search Global Address Book (GAL).
     * Notes: admin verison of mail equiv. Used for testing via zmprov.
     * 
     * @param  string  $domain    Domain name.
     * @param  string  $name      Name.
     * @param  integer $limit     The maximum number of entries to return (0 is default and means all).
     * @param  GalSearchType $type Type of addresses to search. Valid values: all|account|resource|group.
     * @param  string  $galAcctId GAL account ID.
     * @return mix
     */
    function searchGal(
        $domain,
        $name = null,
        $limit = null,
        GalSearchType $type = null,
        $galAcctId = null
    );

    /**
     * Set current volume.
     * Notes: Each SetCurrentVolumeRequest can set only one current volume type.
     * 
     * @param  integer $id   ID.
     * @param  VolumeType $type Volume type: 1 (primary message), 2 (secondary message) or 10 (index).
     * @return mix
     */
    function setCurrentVolume($id, VolumeType $type);

    /**
     * Set Password.
     * Access: domain admin sufficient.
     * Note: this request is by default proxied to the account's home server.
     * 
     * @param  string $id          Zimbra ID.
     * @param  string $newPassword New password.
     * @return mix
     */
    function setPassword($id, $newPassword);

    /**
     * Suspend a device or all devices attached to an account from further sync actions.
     * 
     * @param  Account  $account The name used to identify the account.
     * @param  DeviceId $device  Device ID.
     * @return mix
     */
    function suspendDevice(Account $account, DeviceId $device = null);

    /**
     * Sync GalAccount.
     * Notes:
     *   1. If fullSync is set to false (or unset) the default behavior
     *      is trickle sync which will pull in any new contacts or modified contacts since last sync.
     *   2. If fullSync is set to true, then the server will go through all the contacts
     *      that appear in GAL, and resolve deleted contacts in addition to new or modified ones.
     *   3. If reset attribute is set, then all the contacts will be populated again,
     *      regardless of the status since last sync.
     *      Reset needs to be done when there is a significant change in the configuration,
     *      such as filter, attribute map, or search base.
     * 
     * @param  SyncGalAccount  $galAccounts SyncGalAccount data source specifications.
     * @return mix
     */
    function syncGalAccount(SyncGalAccount $account = null);

    /**
     * Undeploy Zimlet.
     * 
     * @param  string $name   Zimlet name.
     * @param  string $action Action.
     * @return mix
     */
    function undeployZimlet($name, $action = null);

    /**
     * Update device status.
     * 
     * @param  Account $account  Account selector.
     * @param  IdStatus $device  Information on new device status.
     * @return mix
     */
    function updateDeviceStatus(Account $account, IdStatus $device);

    /**
     * Generate a new Cisco Presence server session ID and persist the newly generated session id
     * in zimbraUCCiscoPresenceSessionId attribute for the specified UC service..
     * 
     * @param  UcService $ucservice The UC service.
     * @param  string $username  App username.
     * @param  string $password  App password.
     * @param  array  $attrs     Attributes.
     * @return mix
     */
    function updatePresenceSessionId(
        UcService $ucservice,
        $username,
        $password,
        array $attrs = array()
    );

    /**
     * Upload domain certificate.
     * 
     * @param  string $certAid      Certificate attach ID.
     * @param  string $certFilename Certificate name.
     * @param  string $keyAid       Key attach ID.
     * @param  string $keyFilename  Key name.
     * @return mix
     */
    function uploadDomCert(
        $certAid,
        $certFilename,
        $keyAid,
        $keyFilename
    );

    /**
     * Upload proxy CA.
     * 
     * @param  string $certAid      Certificate attach ID.
     * @param  string $certFilename Certificate name.
     * @return mix
     */
    function uploadProxyCA($certAid, $certFilename);

    /**
     * Verify Certificate Key.
     * 
     * @param  string $cert    Certificate.
     * @param  string $privkey Private key.
     * @return mix
     */
    function verifyCertKey($cert = null, $privkey = null);

    /**
     * Mailbox selector.
     * 
     * @param  MailboxId $id Account ID.
     * @return mix
     */
    function verifyIndex(MailboxId $id);

    /**
     * Verify Store Manager.
     * 
     * @param  int  $fileSize.
     * @param  int  $num.
     * @param  bool $checkBlobs.
     * @return mix
     */
    function verifyStoreManager($fileSize = null, $num = null, $checkBlobs = null);

    /**
     * Version Check.
     * 
     * @param  VersionCheckAction $action Action. Either check or status.
     * @return mix
     */
    function versionCheck(VersionCheckAction $action);
}
