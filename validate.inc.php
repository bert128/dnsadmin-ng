<?php

/* DNS record validation functions go here */

/* is the record being added a valid record type */
function validate_record($domainid, $proc, $name, $type, $priority, $content, $ttl) {

        switch ($type) {
                case "A":
                        return validate_a($domainid, $proc, $name, $type, $priority, $content, $ttl);
                        break;
                case "AAAA":
                        return validate_aaaa($domainid, $proc, $name, $type, $priority, $content, $ttl);
                        break;
                case "CNAME":
                        return validate_cname($domainid, $proc, $name, $type, $priority, $content, $ttl);
                        break;
                case "HINFO":
                        return validate_hinfo($domainid, $proc, $name, $type, $priority, $content, $ttl);
                        break;
                case "MBOXFW":
                        return validate_mboxfw($domainid, $proc, $name, $type, $priority, $content, $ttl);
                        break;
                case "MX":
                        return validate_mx($domainid, $proc, $name, $type, $priority, $content, $ttl);
                        break;
                case "NAPTR":
                        return validate_naptr($domainid, $proc, $name, $type, $priority, $content, $ttl);
                        break;
                case "NS":
                        return validate_ns($domainid, $proc, $name, $type, $priority, $content, $ttl);
                        break;
                case "PTR":
                        return validate_ptr($domainid, $proc, $name, $type, $priority, $content, $ttl);
                        break;
                case "SOA":
                        return validate_soa($domainid, $proc, $name, $type, $priority, $content, $ttl);
                        break;
                case "SRV":
                        return validate_srv($domainid, $proc, $name, $type, $priority, $content, $ttl);
                        break;
                case "TXT":
                        return validate_txt($domainid, $proc, $name, $type, $priority, $content, $ttl);
                        break;
                case "URL":
                        return validate_url($domainid, $proc, $name, $type, $priority, $content, $ttl);
                        break;
                default:
			return FALSE;
        }


$rectypes = array('A', 'AAAA', 'CNAME', 'HINFO', 'MBOXFW', 'MX', 'NAPTR', 'NS', 'PTR', 'SOA', 'SRV', 'TXT', 'URL');


}

function validate_a() {
return TRUE;
}

function validate_aaaa() {
return TRUE;
}

function validate_cname() {
return TRUE;
}

function validate_hinfo() {
return TRUE;
}
function validate_mboxfw() {
return TRUE;
}
function validate_mx() {
return TRUE;
}
function validate_naptr() {
return TRUE;
}
function validate_ns() {
return TRUE;
}
function validate_ptr() {
return TRUE;
}
function validate_soa() {
return TRUE;
}
function validate_srv() {
return TRUE;
}
function validate_txt() {
return TRUE;
}
function validate_url() {
return TRUE;
}

function validate_ip($ip) {
return TRUE;
}

?>
