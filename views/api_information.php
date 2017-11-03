<!--[body]-->
<section role="main" class="main-container content-page">
  <h2 itemprop="headline">RedCappi API Information</h2>
  <div class="content key-points" itemscope itemprop="about">
    <div class="api-resources">
      <h3>
        Authentication
      </h3>
      <p>
        Authorization is important to execute each API methods in RedCappi system. Authorization is done after authenticating the credentials as public and private keys. If authorization is successful then only you will be able to request for creation or updating of any list or contact in the system.
      </p>
      <p>
        A custom HTTP header based on keyed-HMAC (Hash Message Authentication Code) is used by RedCappi API for authentication. A combination of HTTP request method, API url and json-encoded string of data to be sent are used to create signature. Query string and Posted-data both are sorted lexicographically before process.
      </p>
      <code>
        Signature = Lower-Case( HTTP-Method&nbsp;&nbsp;&nbsp;+&nbsp;&nbsp;&nbsp;"::"&nbsp;&nbsp;&nbsp;+<br />
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;API-URL? Lexicographically-Sorted-Query-String&nbsp;&nbsp;&nbsp;+&nbsp;&nbsp;&nbsp;"::"&nbsp;&nbsp;&nbsp;+<br />
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;JSON-encoded (SORTED-POSTED-DATA) )
      </code>
      <p>
        This signature along with the user's private-key is used to calculate the HMAC and thus hashed-signature is created.
      </p>
      <code>
        Hashed-Signature = HMAC-SHA256( Signature, YOUR-PRIVATE-KEY )
      </code>
      <p>
        Finally this hashed-signature is used as custom HTTP authorization header in the API request.
      </p>
      <p>
        Format of the authorization HTTP header is like:
      </p>
      <code>
        Authorization: RCWS YOUR_PUBLIC_KEY:HASED_SIGNATURE
      </code>
      <h3>
        API Process
      </h3>
      <p>
        A request is processed after authorization of the request. For this system fetches the private-key associated with the public-key from Authorization header. Using this fetched private-key and signature based on the request-parameters and request-method, hashed-signature is calculated using HMAC. If this hashed signature matches with the hashed-signature from authorization header, then request gets authenticated and further process continues. If comparison fails, "Not Authorized" error message is sent as response.
      </p>
      <h3>
        Sample Project
      </h3>
      <p>
        Download the <a href="<?php echo $this->config->item('webappassets');?>redcappi-sample-project.zip">Sample Project</a> and get started in minutes.
      </p>
      <h3>
        List Management
      </h3>
      <div class="api-block">
        <h4>Get All Lists</h4>
        <strong>API Request:</strong>
        <code>
          GET /v1/lists.json/ HTTP/1.1<br />
          Host: api.redcappi.com <br />
          Content-Length: 20 <br />
          Content-Type: application/json; charset=UTF-8 <br />
          Accept: */* <br />
          Authorization: RCWS YOUR-PUBLIC-KEY:Hashed-Signature
        </code>
        <strong>Here Hashed-Signature is calculated as the following:</strong>
        <code>
          Signature = get + "::"+ /v1/lists.json/ + "::" + {"posted_data":"[]"} <br />
          Hashed-Signature = HMAC-SHA256( Signature, YOUR-PRIVATE-KEY )
        </code>
        <strong>API Response:</strong>
        <code>
          { "1":"List 1",  "2":"List 2" }
        </code>
      </div>
      <div class="api-block">
        <h4>Get a List</h4>
        <strong>API Request:</strong>
        <code>
          GET /v1/lists.json?id=2 HTTP/1.1 <br />
          Host: api.redcappi.com <br />
          Content-Length: 20 <br />
          Content-Type: application/json; charset=UTF-8 <br />
          Accept: */* <br />
          Authorization: RCWS YOUR-PUBLIC-KEY:Hashed-Signature
        </code>
        <strong>Here Hashed-Signature is calculated as the following:</strong>
        <code>
          Signature = get + "::"+ /v1/lists.json?id=2  + "::" + {"posted_data":"[]"} <br />
           Hashed-Signature = HMAC-SHA256( Signature, YOUR-PRIVATE-KEY )
        </code>
        <strong>API Response:</strong>
        <code>
          { "2":"List 2" }
        </code>
      </div>
      <div class="api-block">
        <h4>Add a List</h4>
        <strong>API Request:</strong>
        <code>
          POST /v1/lists/List+3 HTTP/1.1 <br />
          Host: api.redcappi.com <br />
          Content-Length: 20 <br />
          Content-Type: application/json; charset=UTF-8 <br />
          Accept: */* <br />
          Authorization: RCWS YOUR-PUBLIC-KEY:Hashed-Signature
        </code>
        <strong>Here Hashed-Signature is calculated as the following:</strong>
        <code>
          Signature = post + "::"+ /v1/lists./List+3  + "::" + {"posted_data":"[]"} <br />
           Hashed-Signature = HMAC-SHA256( Signature, YOUR-PRIVATE-KEY )
        </code>
        <strong>API Response:</strong>
        <code>
          { "code" : 201, "message" : "List List 3 created with list-id 3" }
        </code>
      </div>
      <div class="api-block">
        <h4>Edit a List</h4>
        <strong>API Request:</strong>
        <code>
          POST /v1/lists.json?id=3&name=Renamed+1 HTTP/1.1 <br />
          Host: api.redcappi.com <br />
          Content-Length: 20 <br />
          Content-Type: application/json; charset=UTF-8 <br />
          Accept: */* <br />
          Authorization: RCWS YOUR-PUBLIC-KEY:Hashed-Signature
        </code>
        <strong>Here Hashed-Signature is calculated as the following:</strong>
        <code>
          Signature = post + "::"+ /v1/lists.json?id=3&name=Renamed+1  + "::" + {"posted_data":"[]"} <br />
           Hashed-Signature = HMAC-SHA256( Signature, YOUR-PRIVATE-KEY )
        </code>
        <strong>API Response:</strong>
        <code>
          { "code" : 201, "message" : "List renamed to Renamed 1" }
        </code>
      </div>
      <div class="api-block">
        <h4>Delete a List</h4>
        <strong>API Request:</strong>
        <code>
          DELETE /v1/lists.json/3    HTTP/1.1 <br />
          Host: api.redcappi.com <br />
          Content-Length: 22 <br />
          Content-Type: application/json; charset=UTF-8 <br />
          Accept: */* <br />
          Authorization: RCWS YOUR-PUBLIC-KEY:Hashed-Signature
        </code>
        <strong>Here Hashed-Signature is calculated as the following:</strong>
        <code>
          Signature = delete + "::"+ /v1/lists.json/3  + "::" + {"posted_data":"[]"} <br />
           Hashed-Signature = HMAC-SHA256( Signature, YOUR-PRIVATE-KEY )
        </code>
        <strong>API Response:</strong>
        <code>
          { "code" : 200, "message" : "List removed" }
        </code>
      </div>
      <h3>
        Contact Management
      </h3>
      <div class="api-block">
        <h4>Get Contacts</h4>
        <strong>API Request:</strong>
        <code>
          GET /v1/contacts.json/1/contacts/?pagesize=5&offset=2 HTTP/1.1 <br />
          Host: api.redcappi.com <br />
          Content-Length: 20 <br />
          Content-Type: application/json; charset=UTF-8 <br />
          Accept: */* <br />
          Authorization: RCWS YOUR-PUBLIC-KEY:Hashed-Signature
        </code>
        <strong>Here Hashed-Signature is calculated as the following:</strong>
        <code>
          Signature = get + "::"+ /v1/contacts.json/1/contacts/?offset=2&pagesize=5+ "::" + {"posted_data":"[]"} <br />
           Hashed-Signature = HMAC-SHA256( Signature, YOUR-PRIVATE-KEY )
        </code>
        <strong>API Response:</strong>
        <code>
          {"1":"contact1@domain.com","2":"contact2@domain.ch","3":"contact3@domain.com","4":"contact4@hotmail.com","5":"contact5@domain.com"}
        </code>
      </div>
      <div class="api-block">
        <h4>Get a Contact</h4>
        <strong>API Request:</strong>
        <code>
          GET /v1/contacts.json/1/contact/2    HTTP/1.1 <br />
          Host: api.redcappi.com <br />
          Content-Length: 20 <br />
          Content-Type: application/json; charset=UTF-8 <br />
          Accept: */* <br />
          Authorization: RCWS YOUR-PUBLIC-KEY:Hashed-Signature
        </code>
        <strong>Here Hashed-Signature is calculated as the following:</strong>
        <code>
          Signature = get + "::"+ /v1/contacts.json/1/contact/2  + "::" + {"posted_data":"[]"} <br />
           Hashed-Signature = HMAC-SHA256( Signature, YOUR-PRIVATE-KEY )
        </code>
        <strong>API Response:</strong>
        <code>
          { "2":"contact2@domain.ch" }
        </code>
      </div>
      <div class="api-block">
        <h4>Add a Contact</h4>
        <strong>API Request:</strong>
        <code>
          POST /v1/contacts.json/1/contacts/  HTTP/1.1 <br />
          Host: api.redcappi.com <br />
          Content-Length: 216 <br />
          Content-Type: application/json; charset=UTF-8 <br />
          Accept: */* <br />
          Authorization: RCWS YOUR-PUBLIC-KEY:Hashed-Signature
        </code>
        <strong>Here Hashed-Signature is calculated as the following:</strong>
        <code>
          Signature = get + "::"+ /v1/contacts.json/1/contacts/    + "::" + {"address":"","city":"","company":"","country":"india","dob":"","email_address":"contact4@domain.com","first_name":"full name","last_name":"seth1","name":"test21_name","phone":"","state":"","zip_code":"4008"} <br />
           Hashed-Signature = HMAC-SHA256( Signature, YOUR-PRIVATE-KEY )
        </code>
        <strong>API Response:</strong>
        <code>
          {"code":201,"message":"Contact added"}
        </code>
      </div>
      <div class="api-block">
        <h4>Edit a Contact</h4>
        <strong>API Request:</strong>
        <code>
          POST /v1/contacts.json/1/contacts/42   HTTP/1.1 <br />
          Host: api.redcappi.com <br />
          Content-Length: 216 <br />
          Content-Type: application/json; charset=UTF-8 <br />
          Accept: */* <br />
          Authorization: RCWS YOUR-PUBLIC-KEY:Hashed-Signature
        </code>
        <strong>Here Hashed-Signature is calculated as the following:</strong>
        <code>
          Signature = get + "::"+ /v1/contacts.json/1/contacts/42   + "::" + {"address":"","city":"","company":"","country":"india","dob":"","email_address":"contact4_new@domain.com","first_name":"full name","last_name":"seth1","name":"test21_name","phone":"","state":"","zip_code":"4008"} <br />
           Hashed-Signature = HMAC-SHA256( Signature, YOUR-PRIVATE-KEY )
        </code>
        <strong>API Response:</strong>
        <code>
          {"code":200,"message":"Contact updated"}
        </code>
      </div>
      <div class="api-block">
        <h4>Delete a Contact</h4>
        <strong>API Request:</strong>
        <code>
          DELETE  /v1/contacts.json/1/contacts/342    HTTP/1.1 <br />
          Host: api.redcappi.com <br />
          Content-Length: 216 <br />
          Content-Type: application/json; charset=UTF-8 <br />
          Accept: */* <br />
          Authorization: RCWS YOUR-PUBLIC-KEY:Hashed-Signature
        </code>
        <strong>Here Hashed-Signature is calculated as the following:</strong>
        <code>
          Signature = get + "::"+ /v1/contacts.json/1/contacts/342 + "::" + {"posted_data":"null"} Hashed-Signature = HMAC-SHA256( Signature, YOUR-PRIVATE-KEY )
        </code>
        <strong>API Response:</strong>
        <code>
          "Contact removed"
        </code>
      </div>
    </div>
  </div>
</section>
<!--[/body]-->
