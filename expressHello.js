var http = require("http");
var express = require('express');
console.log("Started server");

var aRouter = express();
var myServer = http.createServer(aRouter);

aRouter.get('/', function(req, res){
   res.send("Testing Express"); 
});

myServer.listen(8080, '0.0.0.0');