<?php
/*
Rage4 DNS PHP5 class

This is a PHP5 wrapper to easily integrate Rage4 DNS service
(www.rage4.com) easily. There is no official PHP SDK at the 
moment so this class can help fill the gap in the meantime.

Note: Number of API calls is not limited at the moment hence
      no mechanism added to track/limit the same.
------------------------------------------------------------
Author                          : Asim Zeeshan (www.asim.pk)
Email                           : asim@techbytes.pk
Twitter                         : @asimzeeshan
Usage instruction & download    : https://github.com/asimzeeshan/php-rage4-dns
------------------------------------------------------------
    */

class rage4 {
    private $username           = "";
    private $password           = "";
    private $valid_record_types = array(1 => "NS", 2 => "A", 3 => "AAAA", 4 => "CNAME", 5 => "MX", 6 => "TXT", 7 => "SRV", 8 => "PTR");

    /*
        THE CONSTRUCTOR
        
        All API calls uses BASIC authentication using user's 
         - email address as username
         - Account Key as password
        
        Note: Account Key is available in User Profile section of 
        Rage4 DNS control panel.
        ------------------------------------------------------------
        Parameters: $user and $pass
        
        */
    public function __construct($user, $pass) {
        if (empty($user) || empty($pass)){
            $this->throwError("Username and Password cannot be empty!");
        } else if (!empty($user) && !empty($pass)){
            $this->username = $this->cleanInput($user);
            $this->password = $this->cleanInput($pass);
        }
    }
    
    // Internal method
    // TODO: Instead of printing, I need to "return" back the error messages
    private function throwError($err) {
        echo "<b><font color='#FF0000'>Error:</font></b> ".$err;
        exit;
    }
    
    // Utility functions
    private function cleanInput($i) {
        return trim($i);
    }
    
    // Internal method to debug code, I will leave it here for now
    public function debug() {
        echo "<br />";
        echo "Username: ".$this->username."<br />";
        echo "Password: ".$this->password."<br />";
        
    }
    
    // Internal method to debug code, I will leave it here for now
    private function dump($obj) {
        echo "<br /><pre>";
        print_r($obj);
        echo "</pre>";
    }
    
    /*
        Core function that queries the API and renders results
        ------------------------------------------------------------
        Parameters: $method (it includes the method and/or querystring)
        
        */
    private function doQuery($method) {
        //echo "Trying ... https://secure.rage4.com/rapi/$method <br />";
        $ch = curl_init("https://secure.rage4.com/rapi/".$method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERPWD, $this->username.":".$this->password);
        //echo $this->username.":".$this->password."<br />";
        //echo "HTTPCODE=".$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE)."<br />";
        $result = curl_exec($ch);
        //$this->dump($result);
        //exit;
        return $result;
    }

    /*
        GET DOMAINS
        Get all domain names in your Rage4.com account
        ------------------------------------------------------------
        Parameters: None
        
        */
    public function getDomains() {
        $response = $this->doQuery("getdomains");
        $response = json_decode($response, true);

        if (isset($response['error']) && $response['error']!="") {
            return $response['error'];
        } else {
            return $response;
        }
    }
    
    /*
        CREATE A DOMAIN NAME
        Create a new domain name (zone) in your Rage4.com account
        ------------------------------------------------------------
        Parameters: (all required)
        $name (string)  = domain name
        $email (string) = regular email address of the domain / NOC manager
        
        */
    public function createDomain($domain_name, $email) {
        if (empty($domain_name) || empty($email)) {
            $this->throwError("(method: createDomain) Domain name and Email address is required");
        }
        
        $response = $this->doQuery("createregulardomain/?name=$domain_name&email=$email");
        $response = json_decode($response, true);
        
        if (isset($response['error']) && $response['error']!="") {
            return $response['error'];
        } else {
            return $response;
        }
    }
    
    /*
        CREATE A REVERSE IPv4 DOMAIN
        Create a reverse IPv4 domain name (zone) in your Rage4.com account
        ------------------------------------------------------------
        Parameters: (all required)
        $name (string)  = domain name (for reverse domains: ip6.arpa or in-addr.arpa)
        $email (string) = owner's email
        $subnet (int)   = valid subnet mask
        
        */
    public function createReverseDomain4($domain_name, $email, $subnet) {
        if (empty($domain_name) || empty($email) || empty($subnet)) {
            $this->throwError("(method: createReverseDomain4) Domain name, Email address and subnet is required");
        }
        
        $response = $this->doQuery("createreversedomain4/?name=$domain_name&email=$email&subnet=$subnet");
        $response = json_decode($response, true);
        
        if (isset($response['error']) && $response['error']!="") {
            return $response['error'];
        } else {
            return $response;
        }
    }
    
    /*
        CREATE A REVERSE IPv6 DOMAIN
        Create a reverse IPv6 domain name (zone) in your Rage4.com account
        ------------------------------------------------------------
        Parameters: (all required)
        $name (string)  = domain name (for reverse domains: ip6.arpa or in-addr.arpa)
        $email (string) = owner's email
        $subnet (int)   = valid subnet mask
        
        */
    public function createReverseDomain6($domain_name, $email, $subnet) {
        if (empty($domain_name) || empty($email) || empty($subnet)) {
            $this->throwError("(method: createReverseDomain6) Domain name, Email address and subnet is required");
        }
        
        $response = $this->doQuery("createreversedomain6/?name=$domain_name&email=$email&subnet=$subnet");
        $response = json_decode($response, true);
        
        if (isset($response['error']) && $response['error']!="") {
            return $response['error'];
        } else {
            return $response;
        }
    }
    
    /*
        DELETE A DOMAIN NAME
        Delete a new domain name using its unique identifier in the
        system. To know the unqiue identifier, GetDomains() must be
        called first
        ------------------------------------------------------------
        Parameters: (all required)
        $domain_id (int) = domain id
        
        */
    public function deleteDomain($domain_id) {
        // explicitly typecast into integer
        $domain_id = (int)$domain_id;
        
        if (empty($domain_id)) {
            $this->throwError("(method: deleteDomain) Domain id must be a number");
        }
        
        $response = $this->doQuery("deletedomain/$domain_id");
        $response = json_decode($response, true);
        
        if (isset($response['error']) && $response['error']!="") {
            return $response['error'];
        } else {
            return (bool)$response['status'];
        }
    }

    /*
        IMPORT A DOMAIN NAME
        Importing a domain name including zone data into the system

        Note! You need to allow AXFR transfers
        Note! Only regular domains are supported
        ------------------------------------------------------------
        Parameters: (all required)
        $domain (string) = domain

        */
    public function importDomain($domain) {
        // explicitly typecast into string
        $domain = (string)$domain;
        
        if (empty($domain)) {
            $this->throwError("(method: importDomain) Domain must be a valid string");
        }
        
        $response = $this->doQuery("importdomain/?name=$domain");
        $response = json_decode($response, true);
        
        if (isset($response['error']) && $response['error']!="") {
            return $response['error'];
        } else {
            return (bool)$response['status'];
        }
    }
    
    /*
        GET RECORDS OF A DOMAIN NAME
        Get all records (A, AAAA etc) of a particular domain name
        ------------------------------------------------------------
        Parameters: (all required)
        $domain_id (int) = domain id
        
        */
    public function getRecords($domain_id) {
        // explicitly typecast into integer
        $domain_id = (int)$domain_id;
        
        if (empty($domain_id)) {
            $this->throwError("(method: getRecords) Domain id must be a number");
        }
        
        $response = $this->doQuery("getrecords/$domain_id");
        $response = json_decode($response, true);
        
        //$this->dump($response);
        
        if (isset($response['error']) && $response['error']!="") {
            return $response['error'];
        } else {
            return $response;
        }
    }
    
    /*
        CREATE NEW RECORD
        Create new record for a specific domain name
        ------------------------------------------------------------
        Parameters: (all required except where mentioned)
        $domain_id (int)            = domain id
        $name (string)              = name of the record
        $content (string)           = content of the record
        $type (string)              = type, should be one of the following 
                                        1 = NS
                                        2 = A
                                        3 = AAAA
                                        4 = CNAME
                                        5 = MX
                                        6 = TXT
                                        7 = SRV
                                        8 = PTR
        $priority (int)             = priority of the record being created [OPTIONAL!!]
        $failover (bool)            = Failure support? Yes/No
        $failovercontent (string)   = Failure IP / content
        $ttl (int)                  = TTL
        
        */
    public function createRecord($domain_id, $name, $content, $type="TXT", $priority="", $failover="", $failovercontent="", $ttl=3600) {
        // explicitly typecast into required types
        $domain_id          = (int)$domain_id;
        $name               = (string)$this->cleanInput($name);
        $content            = (string)$this->cleanInput($content);
        $type               = $this->cleanInput($type);
        $priority           = $this->cleanInput($priority);
        //$failover           = (bool)$this->cleanInput($failover);
        $failovercontent    = (string)$this->cleanInput($failovercontent);
        $ttl				= (int)$ttl;
        
        if (empty($domain_id)) {
            $this->throwError("(method: createRecord) Domain id must be a number");
        }
        if (empty($name)) {
            $this->throwError("(method: createRecord) Name cannot be empty");
        }
        if (empty($content)) {
            $this->throwError("(method: createRecord) Content cannot be empty");
        }
        if ($failover=="") {
            $failover = "false";
        } else {
            $failover = "true";
        }
        
        $query_string = "$domain_id?";
        if (!empty($name)) {
            $query_string .= "name=".$name;
        }
        if (!empty($content)) {
            $query_string .= "&content=".$content;
        }
        if (in_array($type, $this->valid_record_types)) {
            $type_id = array_search($type, $this->valid_record_types);
            $query_string .= "&type=".$type_id;
        } else {
            $this->throwError("(method: createRecord) Type must be a valid option from the following NS, MX, A, AAAA, CNAME, TXT, SRV");        
        }
        if (!empty($priority)) {
            $priority = (int)$priority;
        }
        $query_string .= "&priority=$priority&failover=$failover&failovercontent=$failovercontent&ttl=$ttl";
        
        $response = $this->doQuery("createrecord/$query_string");
        $response = json_decode($response, true);
        
        if (isset($response['error']) && $response['error']!="") {
            return $response['error'];
        } else if (isset($response['status']) && $response['status']!="") {
            return "Record added with id ".$response['id'];
        } else {
            return $response;
        }
    }
    
    /*
        UPDATE EXISTING RECORD
        Update an existing record for a specific domain name (no need to mention domain name)
        ------------------------------------------------------------
        Parameters: (all required except where mentioned)
        $record_id (int)            = record id that you wish to update
        $name (string)              = name of the record
        $content (string)           = content of the record
        $priority (int)             = priority of the record being created [OPTIONAL!!]
        $failover (bool)            = Failure support? Yes/No
        $failovercontent (string)   = Failure IP / content
        $ttl (int)                  = TTL
        
        */
    public function updateRecord($record_id, $name, $content, $priority="", $failover="", $failovercontent="", $ttl=3600) {
        // explicitly typecast into required types
        $record_id          = (int)$record_id;
        $name               = (string)$this->cleanInput($name);
        $content            = (string)$this->cleanInput($content);
        $priority           = $this->cleanInput($priority);
        //$failover           = (bool)$this->cleanInput($failover);
        $failovercontent    = (string)$this->cleanInput($failovercontent);
        $ttl				= (int)$ttl;
        
        
        if (empty($record_id)) {
            $this->throwError("(method: updateRecord) Record id must be a number");
        }
        if (empty($name)) {
            $this->throwError("(method: updateRecord) Name cannot be empty");
        }
        if (empty($content)) {
            $this->throwError("(method: updateRecord) Content cannot be empty");
        }
        if ($failover=="") {
            $failover = "false";
        } else {
            $failover = "true";
        }
        
        $query_string = "$record_id?";
        if (!empty($name)) {
            $query_string .= "name=".$name;
        }
        if (!empty($content)) {
            $query_string .= "&content=".$content;
        }
        if (!empty($priority)) {
            $priority = (int)$priority;
        }
        $query_string .= "&priority=$priority&failover=$failover&failovercontent=$failovercontent&ttl=$ttl";
        
        $response = $this->doQuery("updaterecord/$query_string");
        $response = json_decode($response, true);
        
        if (isset($response['error']) && $response['error']!="") {
            return $response['error'];
        } else if (isset($response['status']) && $response['status']!="") {
            return "Record updated with id ".$response['id'];
        } else {
            return $response;
        }
    }

    /*
        DELETE A RECORD
        Delete a record in an existing domain name (zone) in your 
        account
        ------------------------------------------------------------
        Parameters: (all required)
        $record_id (int) = record id
        
        */
    public function deleteRecord($record_id) {
        // explicitly typecast into integer
        $record_id = (int)$record_id;
        
        if (empty($record_id)) {
            $this->throwError("(method: deleteRecord) Record id must be a number");
        }
        
        $response = $this->doQuery("deleterecord/$record_id");
        $response = json_decode($response, true);
        
        if (isset($response['error']) && $response['error']!="") {
            return $response['error'];
        } else {
            return $response['status'];
        }
    }
    
}