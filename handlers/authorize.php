<?php

    
    function zfHandle($formData, $formKey='') {
        
        $username = ""; // api key
        $password = ""; // transaction key
        $dev = true; // changes authorize server to testing
        
        $formData['ship_to_billing'] = 1; //forcing this
        
        //billing info
        $b_email = $formData[''];
        $b_fname = $formData[''];
        $b_lname = $formData[''];
        $b_address = $formData[''];
        $b_city = $formData[''];
        $b_state = $formData[''];
        $b_zip = $formData[''];
        $b_country = $formData[''];
        $b_description = $formData[''];
        
        //credit card and amount
        $cc_number = $formData[''];
        $cc_expire_year = $formData[''];
        $cc_expire_month = $formData[''];
        $cvv = $formData[''];
        $amount = $formData[''];
        
        $s_fname = $formData[''];
        $s_lname = $formData[''];
        $s_address = $formData[''];
        $s_city = $formData[''];
        $s_state = $formData[''];
        $s_zip = $formData[''];
        $s_country = $formData[''];
        
        
        //SHOULDNT HAVE TO EDIT BELOW THIS LINE, BUT GO AHEAD
        
        $exp_date_time=strtotime("$cc_exp_year-$cc_exp_month-01");
        $cc_expire_date=date("m-Y", $exp_date_time);
        
        $success = false;
        $error = '';
        
        $auth = new authorize($username, $password, $dev);
        if ($auth->error) {
            $error .= $auth->errorMsg;
        }
        
        $auth->setTransaction($cc_number, $cc_expire_date, $amount, $cvv);

        //Billing information
        if (! $auth->setParameter("x_email", $b_email)) { $hasError=1; $error .= "\n".$auth->errorMsg; }
        if (! $auth->setParameter("x_first_name", $b_fname)) { $hasError=1; $error .= "\n".$auth->errorMsg; }
        if (! $auth->setParameter("x_last_name", $b_lname)) { $hasError=1; $error .= "\n".$auth->errorMsg; }
        if (! $auth->setParameter("x_address", $b_address)) { $hasError=1; $error .= "\n".$auth->errorMsg; }
        if (! $auth->setParameter("x_city", $b_city)) { $hasError=1; $error .= "\n".$auth->errorMsg; }
        if (! $auth->setParameter("x_state", $b_state)) { $hasError=1; $error .= "\n".$auth->errorMsg; }
        if (! $auth->setParameter("x_zip", $b_zip)) { $hasError=1; $error .= "\n".$auth->errorMsg; }
        if (! $auth->setParameter("x_country", $b_country)) { $hasError=1; $error .= "\n".$auth->errorMsg; }
        if (! $auth->setParameter("x_description", $description)) { $hasError=1; $error .= "\n".$auth->errorMsg; } //Purchase description (Order #: 199 from yoursite.com)

        if (isset($formData['ship_to_billing']) && (bool) $formData['ship_to_billing']) {
        
            //copy billing to shipping
            $s_fname = $b_fname;
            $s_lname = $b_lname;
            $s_address = $b_address;
            $s_city = $b_city;
            $s_state = $b_state;
            $s_zip = $b_zip;
            $s_country = $b_country;

        }

        //Shipping information
        if (! $auth->setParameter("x_ship_to_first_name", $s_fname)) { $hasError=1; $error .= "\n".$auth->errorMsg; }
        if (! $auth->setParameter("x_ship_to_last_name", $s_lname)) { $hasError=1; $error .= "\n".$auth->errorMsg; }
        if (! $auth->setParameter("x_ship_to_address", $s_address)) { $hasError=1; $error .= "\n".$auth->errorMsg; }
        if (! $auth->setParameter("x_ship_to_city", $s_city)) { $hasError=1; $error .= "\n".$auth->errorMsg; }
        if (! $auth->setParameter("x_ship_to_state", $s_state)) { $hasError=1; $error .= "\n".$auth->errorMsg; }
        if (! $auth->setParameter("x_ship_to_zip.", $s_zip)) { $hasError=1; $error .= "\n".$auth->errorMsg; }
        if (! $auth->setParameter("x_ship_to_country", $s_country)) { $hasError=1; $error .= "\n".$auth->errorMsg; }

        //don't process if we have errors
        if (!$hasError) {

            $auth->process();

            if ($auth->isApproved()) {
                $success = true;
            } elseif($auth->isDeclined()){
                $error .="\n<p>DECLINED: ".$auth->getResponseText()."</p>";
            } else{
                $error .="\n<p>ERROR: ".$auth->getResponseText()."</p>";
            }
        }
        
        return $success;
    }
    
    function zfError($formData, $formKey = '') {
        
        return true;
    }
    
    function zfSuccess($formData, $formKey = '') {
        
        return true;
    }


class authorize {
    
    public $login    = '';
    public $transkey = '';
    public $test     = '';

    public $params   = array();
    public $results  = array();

    public $approved = false;
    public $declined = false;
    public $error    = false;

    public $fields;
    public $response;
    public $url;

    public $errorMsg = '';

    public function __construct($login, $transkey, $dev) {
        //API Login ID , Transaction Key
        $this->login    = $login;
        $this->transkey = $transkey;
        $this->test     = false;//$dev; //true or false

        if (empty($this->login) || empty($this->transkey)){
            $this->errorMsg = "<p>error: You have empty Authorize.net login credentials.</p>";
            $this->error=1;
        }

        $subdomain = ($this->test) ? 'test' : 'secure';
        $this->url = "https://" . $subdomain . ".authorize.net/gateway/transact.dll";

        $this->params['x_delim_data']     = "TRUE";
        $this->params['x_delim_char']     = "|";
        $this->params['x_relay_response'] = "FALSE";
        $this->params['x_url']            = "FALSE";
        $this->params['x_version']        = "3.1";
        $this->params['x_method']         = "CC";
        $this->params['x_type']           = "AUTH_CAPTURE";
        $this->params['x_login']          = $this->login;
        $this->params['x_tran_key']       = $this->transkey;
    }

    public function process($retries = 3) {
        $this->prepareParameters();
        //echo "<p>url: ".$this->url."</p>";
        
        $ch = curl_init($this->url);

        $count = 0;
        //while ($count < $retries){
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, rtrim($this->fields, "& "));
            $this->response = curl_exec($ch);
            $this->parseResults();

            if ($this->getResultResponseFull() == "Approved"){
                $this->approved = true;
                $this->declined = false;
                $this->error    = false;
                //break;
            } elseif ($this->getResultResponseFull() == "Declined"){
                $this->approved = false;
                $this->declined = true;
                $this->error    = false;
                //break;
            } else{
                $this->approved = false;
                $this->declined = false;
                $this->error    = true;
            }

            $count++;
        //}
        curl_close($ch);
    }

    public function prepareParameters(){
        foreach ($this->params as $key => $value){
            $this->fields .= "$key=" . urlencode($value) . "&";
        }
    }

    public function parseResults(){
        $this->results = explode("|", $this->response);
    }

    public function setTransaction($cardnum, $expiration, $amount, $cvv = null){
        $this->params['x_card_num']  = trim($cardnum);
        $this->params['x_exp_date']  = $expiration;
        $this->params['x_amount']    = $amount;
        $this->params['x_card_code'] = $cvv;

        if (empty($this->params['x_card_num']) || empty($this->params['x_exp_date']) || empty($this->params['x_amount'])){
            $this->errorMsg="<p>error: Required information for transaction processing omitted.</p>";
            echo $this->errorMsg;
            return false;
        }
        return true;
    }

    public function setParameter($field = "", $value = null){
        $field = (is_string($field)) ? trim($field) : $field;
        $value = (is_string($value)) ? trim($value) : $value;
        if (!is_string($field)){
            $this->errorMsg="<p>error: setParameter() arg 1 must be a string or integer: " . gettype($field) . " given.</p>";
            return false;
        }

        if (!is_string($value) && !is_numeric($value) && !is_bool($value)){
            $this->errorMsg="<p>error: setParameter() arg 2 must be a string, integer, or boolean value: " . gettype($value) . " given.</p>";
            return false;
        }

        if (empty($field)){
            $this->errorMsg="<p>error: setParameter() requires a parameter field to be named.</p>";
            return false;
        }

        if ($value === "") {
            $this->errorMsg="<p>error: setParameter() requires a parameter value to be assigned: $field .</p>";
            return false;
        }

        $this->params[$field] = $value;
        return true;
    }

    public function setTransactionType($type = ""){
        $type      = strtoupper(trim($type));
        $typeArray = array("AUTH_CAPTURE", "AUTH_ONLY", "PRIOR_AUTH_CAPTURE", "CREDIT", "CAPTURE_ONLY", "VOID");

        if (!in_array($type, $typeArray)){
            $this->errorMsg="<p>error: setTransactionType() requires a valid value to be assigned.</p>";
            echo $this->errorMsg;
            return false;
        }
        $this->params['x_type'] = $type;
        return true;
    }

    public function getResultResponse(){
        return $this->results[0];
    }

    public function getResultResponseFull() {
        $response = array("", "Approved", "Declined", "Error");
        return $response[$this->results[0]];
    }

    public function isApproved() {
        return $this->approved;
    }

    public function isDeclined(){
        return $this->declined;
    }

    public function isError(){
        return $this->error;
    }

    public function getResponseSubcode(){
        return $this->results[1];
    }

    public function getResponseCode() {
        return $this->results[2];
    }

    public function getResponseText() {
        return $this->results[3];
    }

    public function getAuthCode() {
        return $this->results[4];
    }

    public function getAVSResponse() {
        return $this->results[5];
    }

    public function getTransactionID() {
        return $this->results[6];
    }

    public function getInvoiceNumber(){
        return $this->results[7];
    }

    public function getDescription() {
        return $this->results[8];
    }

    public function getAmount(){
        return $this->results[9];
    }

    public function getPaymentMethod(){
        return $this->results[10];
    }

    public function getTransactionType(){
        return $this->results[11];
    }

    public function getCustomerID(){
        return $this->results[12];
    }

    public function getCHFirstName(){
        return $this->results[13];
    }

    public function getCHLastName(){
        return $this->results[14];
    }

    public function getCompany(){
        return $this->results[15];
    }

    public function getBillingAddress(){
        return $this->results[16];
    }

    public function getBillingCity(){
        return $this->results[17];
    }

    public function getBillingState(){
        return $this->results[18];
    }

    public function getBillingZip(){
        return $this->results[19];
    }

    public function getBillingCountry() {
        return $this->results[20];
    }

    public function getPhone(){
        return $this->results[21];
    }

    public function getFax(){
        return $this->results[22];
    }

    public function getEmail(){
        return $this->results[23];
    }

    public function getShippingFirstName(){
        return $this->results[24];
    }

    public function getShippingLastName() {
        return $this->results[25];
    }

    public function getShippingCompany(){
        return $this->results[26];
    }

    public function getShippingAddress(){
        return $this->results[27];
    }

    public function getShippingCity(){
        return $this->results[28];
    }

    public function getShippingState(){
        return $this->results[29];
    }

    public function getShippingZip(){
        return $this->results[30];
    }

    public function getShippingCountry(){
        return $this->results[31];
    }

    public function getTaxAmount(){
        return $this->results[32];
    }

    public function getDutyAmount(){
        return $this->results[33];
    }

    public function getFreightAmount(){
        return $this->results[34];
    }

    public function getTaxExemptFlag(){
        return $this->results[35];
    }

    public function getPONumber(){
        return $this->results[36];
    }

    public function getMD5Hash(){
        return $this->results[37];
    }

    public function getCVVResponse(){
        return $this->results[38];
    }

    public function getCAVVResponse(){
        return $this->results[39];
    }

}