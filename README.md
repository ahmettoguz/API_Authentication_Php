# API_Authentication_Php

STATUS CODES:

200  (Success/OK) 

301,302 (Redirect)

400 (Bad Request)
    When the client requests a page and the server is not able to understand anything, it displays a 400 HTTP status code. The client SHOULD NOT repeat the request without any changes. The request can be a malformed, deceptive request routing, or invalid request.

401 (Unauthorized Error)
    This HTTP status code requires user authentication

403 (Forbidden)
    The HTTP status code 403 implies that the request is understood by the server, but still refuses to fulfill it. If the request method was not HEAD and also the server wants to make it public when the request is not completed, it SHOULD tell the reason for the refusal in the entity. 

404 (Not Found)

500 (Internal Server Error)
    500 HTTP status code means requesting a URL is not fulfilled because the server encounters an unexpected condition. It gives information about the request made if it is successful, and throws an error. When there’s an error during a connection to the server, and the requested page cannot be accessed then this message is displayed. 