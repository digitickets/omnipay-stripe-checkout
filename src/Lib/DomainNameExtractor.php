<?php

namespace DigiTickets\Stripe\Lib;

class DomainNameExtractor {

    /**
     * Extract the base domain (strip off 1 sub-domain, e.g. sub.test.com => test.com), to the best of our ability without knowing the live list of TLDs.
     * Won't handle 6 char base domains (including the TLD and dots) like "subdomain.aa.com" properly. This will be returned as "subdomain.aa.com".
     * @param string $url
     *
     * @return string
     */
    public static function extractBaseDomainAndPath(string $url) : string {
        $parsedUrl = parse_url($url);
        $host = $parsedUrl['host'] ?? '';
        $path = $parsedUrl['path'] ?? '';
        $baseDomain = $host;

        // Check if it might have a subdomain
        if(!ip2long($host) && substr_count($host, ".")>=2){
            $baseDomain = substr($host, strpos($host, ".")+1);

            if(strlen($baseDomain)<=6){
                // It's too short, so we can only assume it's a TLD, so not actually a base domain (this is a limitation of this logic)
                $baseDomain = $host;
            }
        }
        return $baseDomain . $path;
    }

}
