## Rage4 DNS PHP5 class

This is a PHP5 wrapper to easily integrate Rage4 DNS service (www.rage4.com) easily. There is no official PHP SDK at the moment so this class can help fill the gap in the meantime.

Number of API calls is not limited at the moment hence no mechanism added to track/limit the same.

The methods introduced in this first release are
- getDomains()
- createDomain()
- createReverseDomain4()
- createReverseDomain6()
- deleteDomain()
- importDomain()
- getRecords()
- createRecord()
- updateRecord()
- deleteRecord()

You can also consult the official documentation avilable at the following URL:
http://gbshouse.uservoice.com/knowledgebase/articles/109834-rage4-dns-developers-api

Official set of SDKs by Rage4: http://code.google.com/p/rage4-dns-sdk/ (only dotNET available at the moment)

## Configuration

	include("class.rage4.php");
	// your username (email) and API KEY from Rage4.com
	// you can get your API KEY from this URL
	// https://secure.rage4.com/Secure/ShowProfile
	$r4 = new rage4('you@yourhost.com', 'your-api-key-here');

## Examples

Here are some examples on how to do basic things. Of course you need to configure the class in your app before using the scriptlets below.

### Get all domain names (zones)

	$response = $r4->getDomains();
	print_r($response);
    
### Create a new domain name (zone)

	// createDomain($domain_name, $email)
	$response = $r4->createDomain('my-domain-name-here.com', 'you@yourhost.com');
	print_r($response);

### Create Reverse IPv4 domain

	// createReverseDomain4($domain_name, $email, $subnet)
	$response = $r4->createReverseDomain4('155.39.97.in-addr.arpa', 'you@yourhost.com', '27');
	print_r($response);

### Create Reverse IPv6 domain

	// createReverseDomain6($domain_name, $email, $subnet)
	$response = $r4->createReverseDomain6('0.0.0.0.8.b.d.0.1.0.0.2.ip6.arpa', 'you@yourhost.com', '48');
	print_r($response);

### Delete a new domain name (zone)

In this example, 627 is the ID of the domain zone to be deleted. To know the IDs of the domain zones, do $r4->getDomains(); first

	// deleteDomain($domain_id)
	$response = $r4->deleteDomain(627);
	print_r($response);

### Import a new domain name (zone)

Note! You need to allow AXFR transfers
Note! Only regular domains are supported
        
	// importDomain($domain)
	$response = $r4->importDomain('my-domain-name-here.com');
	print_r($response);

### Get all records of a domain name (zone)

You need to mention the domain ID for which you need to get all records for. Again, if you are unsure please do a $r4->getDomains(); first

	// getRecords($domain_id);
	$response = $r4->getRecords(55);
	print_r($response);

### Create a new record for a particular domain name (zone)

	// createRecord($domain_id, $name, $content, $type="TXT", $priority="", $failover="", $failovercontent="", $ttl=3600)
	$response = $r4->createRecord(55, 'my-domain-name-here.com', 'ns1.4dns.com', "NS", 1500);
	print_r($response);

### Update an existing record

5555 is the record id that was returned from the function $r4->createRecord()

Note! No domain_name/domain_id is required while updating a record
Note! There is no way to update the record-type at the moment, so the easy way is to delete the record first and then recreate with new values (if record-type is changed) e.g. from CNAME to TXT etc

	// updateRecord($record_id, $name, $content, $priority="", $failover="", $failovercontent="", $ttl=3600)
	$response = $r4->updateRecord(5555, 'my-domain-name-here.com', 'ns1.4dns.com', 1500);
	print_r($response);

### Delete an existing record

	// deleteRecord($record_id)
	$response = $r4->deleteRecord(5555);
	print_r($response);
    
## TODO / Wish List

- Return the errors instead of echo'ing them in-file
 
## Credits

- [Asim Zeeshan](https://github.com/asimzeeshan)
- Piotr Ginalski from gbshouse.com for tracking down certain issues in my code

#### Special thanks to

- Rage4.com for providing the free beta run and then the free service to me
- GitHub for providing this amazing free service