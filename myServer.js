var http = require("http");
console.log("Started server");
var myServer = http.createServer(function(request, response){
   console.log(request);
   console.log(response);
   response.setHeader('Content-Type', 'text/html');
   response.write("<p>Hello there</p>");
   response.end("<p>I think I'm done</p>");
});

myServer.listen(8080, '0.0.0.0');