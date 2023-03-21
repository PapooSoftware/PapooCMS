<?php
if (stristr( $_SERVER['PHP_SELF'],'url_syntax.php')) die('You are not allowed to see this page directly');

function check_url ($url2check, $types) {

# Be paranoid about using grouping!

$nz_digit       =  '[1-9]';
$nz_digits      =  "(?:$nz_digit\\d*)";
$digits         =  '(?:\d+)';
$space          =  '(?:%20)';
$nl             =  '(?:%0[Aa])';
$dot            =  '\.';
$plus           =  '\+';
$qm             =  '\?';
$ast            =  '\*';
$hex            =  '[a-fA-F\d]';
$alpha          =  '[a-zA-Z]';     # No, no locale.
$alphas         =  "(?:{$alpha}+)";
$alphanum       =  '[a-zA-Z\d]';   # Letter or digit.
$xalphanum      =  "(?:{$alphanum}|%(?:3\\d|[46]$hex|[57][Aa\\d]))";
                      # Letter or digit, or hex escaped letter/digit.
$alphanums      =  "(?:{$alphanum}+)";
$escape         =  "(?:%$hex\{2})";
$safe           =  '[$\-_.+#]';
$extra          =  "[\!*'(),]";
$national       =  '[{}|\\^~[\]`]';
$punctuation    =  '[<>%"]';
$reserved       =  '[;/?:@&=]';
$uchar          =  "(?:{$alphanum}|{$safe}|{$extra}|{$escape})";
$xchar          =  "(?:{$alphanum}|{$safe}|{$extra}|{$reserved}|{$escape})";
$uchar          =  str_replace (']|[', '', $uchar);  // Make string smaller, and speed up regex.
$uchar          =  str_replace (']|[', '', $xchar);  // Make string smaller, and speed up regex.

# URL schemeparts for ip based protocols:
$user           =  "(?:(?:{$uchar}|[;?&=])*)";
$password       =  "(?:(?:{$uchar}|[;?&=])*)";
$hostnumber     =  "(?:{$digits}(?:{$dot}{$digits}){3})";
$toplabel       =  "(?:{$alpha}(?:(?:{$alphanum}|-)*{$alphanum})?)";
$domainlabel    =  "(?:{$alphanum}(?:(?:{$alphanum}|-)*{$alphanum})?)";
$hostname       =  "(?:(?:{$domainlabel}{$dot})*{$toplabel})";
$host           =  "(?:{$hostname}|{$hostnumber})";
$hostport       =  "(?:{$host}(?::{$digits})?)";
$login          =  "(?:(?:{$user}(?::{$password})?\@)?{$hostport})";

# The predefined schemes:

# FTP (see also RFC959)
$fsegment       =  "(?:(?:{$uchar}|[?:\@&=])*)";
$fpath          =  "(?:{$fsegment}(?:/{$fsegment})*)";
$ftpurl         =  "(?:ftp://{$login}(?:/{$fpath}(?:;type=[AIDaid])?)?)";

# FILE
$fileurl        =  "(?:file://(?:{$host}|localhost)?/{$fpath})";

# HTTP
$hsegment       =  "(?:(?:{$uchar}|[~;:\@&=])*)";
$search         =  "(?:(?:{$uchar}|[;:\@&=])*)";
$hpath          =  "(?:{$hsegment}(?:/{$hsegment})*)";
$httpurl        =  "(?:https?://{$hostport}(?:/{$hpath}(?:{$qm}{$search})?)?)";

# GOPHER (see also RFC1436)
$gopher_plus    =  "(?:{$xchar}*)";
$selector       =  "(?:{$xchar}*)";
$gtype          =      $xchar;     // Omitted parens!
$gopherurl      =  "(?:gopher://{$hostport}(?:/{$gtype}(?:{$selector}" .
                      "(?:%09{$search}(?:%09{$gopher_plus})?)?)?)?)";

# MAILTO (see also RFC822)
$encoded822addr =  "(?:$xchar+)";
$mailtourl      =  "(?:mailto:$encoded822addr)";
$mailtonpurl    =  $encoded822addr;

# NEWS (see also RFC1036)
$article        =  "(?:(?:{$uchar}|[;/?:&=])+\@{$host})";
$group          =  "(?:{$alpha}(?:{$alphanum}|[_.+-])*)";
$grouppart      =  "(?:{$article}|{$group}|{$ast})";
$newsurl        =  "(?:news:{$grouppart})";

# NNTP (see also RFC977)
$nntpurl        =  "(?:nntp://{$hostport}/{$group}(?:/{$digits})?)";

# TELNET
$telneturl      =  "(?:telnet://{$login}/?)";

# WAIS (see also RFC1625)
$wpath          =  "(?:{$uchar}*)";
$wtype          =  "(?:{$uchar}*)";
$database       =  "(?:{$uchar}*)";
$waisdoc        =  "(?:wais://{$hostport}/{$database}/{$wtype}/{$wpath})";
$waisindex      =  "(?:wais://{$hostport}/{$database}{$qm}{$search})";
$waisdatabase   =  "(?:wais://{$hostport}/{$database})";
# $waisurl        =  "(?:${waisdatabase}|${waisindex}|${waisdoc})";
# Speed up: the 3 types share a common prefix.
$waisurl        =  "(?:wais://{$hostport}/{$database}" .
                         "(?:(?:/{$wtype}/{$wpath})|{$qm}{$search})?)";

# PROSPERO
$fieldvalue     =  "(?:(?:{$uchar}|[?:\@&])*)";
$fieldname      =  "(?:(?:{$uchar}|[?:\@&])*)";
$fieldspec      =  "(?:;{$fieldname}={$fieldvalue})";
$psegment       =  "(?:(?:{$uchar}|[?:\@&=])*)";
$ppath          =  "(?:{$psegment}(?:/{$psegment})*)";
$prosperourl    =  "(?:prospero://{$hostport}/{$ppath}(?:{$fieldspec})*)";
$dn_separator        =  "(?:[;,])";
$dn_optional_space   =  "(?:{$nl}?{$space}*)";
$dn_spaced_separator =  "(?:{$dn_optional_space}{$dn_separator}" .
                              "{$dn_optional_space})";
$dn_oid              =  "(?:{$digits}(?:{$dot}{$digits})*)";
$dn_keychar          =  "(?:{$xalphanum}|{$space})";
$dn_key              =  "(?:{$dn_keychar}+|(?:OID|oid){$dot}{$dn_oid})";
$dn_string           =  "(?:{$uchar}*)";
$dn_attribute        =  "(?:(?:{$dn_key}{$dn_optional_space}=" .
                                          "{$dn_optional_space})?{$dn_string})";
$dn_name_component   =  "(?:{$dn_attribute}(?:{$dn_optional_space}" .
                              "{$plus}{$dn_optional_space}{$dn_attribute})*)";
$dn_name             =  "(?:{$dn_name_component}" .
                            "(?:{$dn_spaced_separator}{$dn_name_component})*" .
                               "{$dn_spaced_separator}?)";

# RFC 1558 defines the filter syntax, but that requires a PDA to recognize.
# Since that's too powerful for Perl's REs, we allow any char between the
# parenthesis (which have to be there.)
$ldap_filter         =  "(?:\({$xchar}+\))";

# This is from RFC 1777. It defines an attributetype as an 'OCTET STRING',
# whatever that is.
$ldap_attr_type      =  "(?:{$uchar}+)";  # I'm just guessing here.
                                             # The RFCs aren't clear.

# Now we are at the grammar of RFC 1959.
$ldap_attr_list =  "(?:{$ldap_attr_type}(?:,{$ldap_attr_type})*)";
$ldap_attrs     =  "(?:{$ldap_attr_list}?)";

$ldap_scope     =  "(?:base|one|sub)";
$ldapurl        =  "(?:ldap://(?:{$hostport})?/{$dn_name}" .
                        "(?:{$qm}{$ldap_attrs}" .
                        "(?:{$qm}{$ldap_scope}(?:{$qm}{$ldap_filter})?)?)?)";


# RFC 2056 defines the format of URLs for the Z39.50 protocol.
$z_database     =  "(?:{$uchar}+)";
$z_docid        =  "(?:{$uchar}+)";
$z_elementset   =  "(?:{$uchar}+)";
$z_recordsyntax =  "(?:{$uchar}+)";
$z_scheme       =  "(?:z39{$dot}50[rs])";
$z39_50url      =  "(?:{$z_scheme}://{$hostport}" .
                           "(?:/(?:{$z_database}(?:{$plus}{$z_database})*" .
                                   "(?:{$qm}{$z_docid})?)?" .
                               "(?:;esn={$z_elementset})?" .
                               "(?:;rs={$z_recordsyntax}" .
                                    "(?:{$plus}{$z_recordsyntax})*)?))";


# RFC 2111 defines the format for cid/mid URLs.
$url_addr_spec  =  "(?:(?:{$uchar}|[;?:@&=])*)";
$message_id     =  $url_addr_spec;
$content_id     =  $url_addr_spec;
$cidurl         =  "(?:cid:{$content_id})";
$midurl         =  "(?:mid:{$message_id}(?:/{$content_id})?)";


# RFC 2122 defines the Vemmi URLs.
$vemmi_attr     =  "(?:(?:{$uchar}|[/?:@&])*)";
$vemmi_value    =  "(?:(?:{$uchar}|[/?:@&])*)";
$vemmi_service  =  "(?:(?:{$uchar}|[/?:@&=])*)";
$vemmi_param    =  "(?:;{$vemmi_attr}={$vemmi_value})";
$vemmiurl       =  "(?:vemmi://{$hostport}" .
                          "(?:/{$vemmi_service}(?:{$vemmi_param}*))?)";

# RFC 2192 for IMAP URLs.
# Import from RFC 2060.
# $imap4_astring       =  "";
# $imap4_search_key    =  "";
# $imap4_section_text  =  "";
$imap4_nz_number     =  $nz_digits;
$achar          =  "(?:{$uchar}|[&=~])";
$bchar          =  "(?:{$uchar}|[&=~:\@/])";
$enc_auth_type  =  "(?:{$achar}+)";
$enc_list_mbox  =  "(?:{$bchar}+)";
$enc_mailbox    =  "(?:{$bchar}+)";
$enc_search     =  "(?:{$bchar}+)";
$enc_section    =  "(?:{$bchar}+)";
$enc_user       =  "(?:{$achar}+)";
$i_auth         =  "(?:;[Aa][Uu][Tt][Hh]=(?:{$ast}|{$enc_auth_type}))";
$i_list_type    =  "(?:[Ll](?:[Ii][Ss][Tt]|[Ss][Uu][Bb]))";
$i_mailboxlist  =  "(?:{$enc_list_mbox}?;[Tt][Yy][Pp][Ee]={$i_list_type})";
$i_uidvalidity  =  "(?:;[Uu][Ii][Dd][Vv][Aa][Ll][Ii][Dd][Ii][Tt][Yy]=" .
                          "{$imap4_nz_number})";
$i_messagelist  =  "(?:{$enc_mailbox}(?:{$qm}{$enc_search})?" .
                                       "(?:{$i_uidvalidity})?)";
$i_section      =  "(?:/;[Ss][Ee][Cc][Tt][Ii][Oo][Nn]={$enc_section})";
$i_uid          =  "(?:/;[Uu][Ii][Dd]={$imap4_nz_number})";
$i_messagepart  =  "(?:{$enc_mailbox}(?:{$i_uidvalidity})?{$i_uid}" .
                                       "(?:{$i_section})?)";
$i_command      =  "(?:{$i_mailboxlist}|{$i_messagelist}|{$i_messagepart})";
$i_userauth     =  "(?:(?:{$enc_user}(?:{$i_auth})?)|" .
                         "(?:{$i_auth}(?:{$enc_user})?))";
$i_server       =  "(?:(?:{$i_userauth}\@)?{$hostport})";
$imapurl        =  "(?:imap://{$i_server}/(?:$i_command)?)";

# RFC 2224 for NFS.
$nfs_mark       =  '[\$\-_.\!~*\'(),]';
$nfs_unreserved =  "(?:{$alphanum}|{$nfs_mark})";
$nfs_unreserved =  str_replace (']|[', '', $nfs_unreserved);  // Make string smaller, and speed up regex.
$nfs_pchar      =  "(?:{$nfs_unreserved}|{$escape}|[:\@&=+])";
$nfs_segment    =  "(?:{$nfs_pchar}*)";
$nfs_path_segs  =  "(?:{$nfs_segment}(?:/{$nfs_segment})*)";
$nfs_url_path   =  "(?:/?{$nfs_path_segs})";
$nfs_rel_path   =  "(?:{$nfs_path_segs}?)";
$nfs_abs_path   =  "(?:/{$nfs_rel_path})";
$nfs_net_path   =  "(?://{$hostport}(?:{$nfs_abs_path})?)";
$nfs_rel_url    =  "(?:{$nfs_net_path}|{$nfs_abs_path}|{$nfs_rel_path})";
$nfsurl         =  "(?:nfs:{$nfs_rel_url})";

$valid_types = array (
  'http', 'ftp', 'news', 'nntp', 'telnet', 'gopher', 'wais', 'mailto',
  'mailtonp', 'file', 'prospero', 'ldap', 'z39_50', 'cid', 'mid', 'vemmi',
  'imap', 'nfs'
);

# Combining all the different URL formats into a single regex.

$valid = false;

if (!is_array ($types)) {
	$types = array ($types);
}

foreach ($types as $type) {
	if (!in_array ($type, $valid_types)) {
		continue;
	}
	$re = $type.'url';
	if (preg_match ('!^'.$$re.'$!i', $url2check)) {
		$valid = $type;
		break;
	}
}
return $valid;
}